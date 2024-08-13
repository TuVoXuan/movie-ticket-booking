<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />
  <Box>
    <div class="flex justify-between mb-3">
      <div class="flex gap-x-3">
        <div class="flex-2">
          <label class="text-sm">Search</label>
          <a-input class="mt-2" size="large" v-model:value="search" placeholder="Search ..." @input="onSearchChange">
            <template #prefix>
              <Icon name="search_outline" class="h-5 w-5" />
            </template>
          </a-input>
        </div>
        <div class="flex flex-col gap-y-2 w-[200px]">
          <label class="text-sm">Region</label>
          <a-select size="large" v-model:value="region" label-in-value show-search placeholder="Select region"
            :options="regionOptions.data" :filter-option="false" allow-clear @search="handleSearchRegion"
            @change="handleChangeRegion">
            <template v-if="regionOptions.isFetching" #notFoundContent>
              <a-spin size="small" />
            </template>
          </a-select>
        </div>
        <div class="flex flex-col gap-y-2 w-[200px]">
          <label class="text-sm">Company</label>
          <a-select size="large" v-model:value="company" label-in-value placeholder="Select cinema"
            :options="cinemaCompanyOptions.data" :filter-option="false" allow-clear @change="handleChangeCompany">
            <template #option="{ value: val, label, logo }">
              <div class="flex items-center gap-x-3">
                <div class="h-10 w-10 rounded-full overflow-hidden bg-white">
                  <img :src="logo" alt="logo-cinema-company" />
                </div>
                <span>{{ label }}</span>
              </div>
            </template>
          </a-select>
        </div>
      </div>

      <a-button type="primary" class="self-end">
        <Link :href="route('cinemas.branches.create')">Add New Cinema Branch</Link>
      </a-button>
    </div>

    <a-table :data-source="cinemaBranches.data" :columns="columns" :pagination="pagination" @change="handleTableChange">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'name'">
          <Link :href="route('cinemas.branches.auditoria.index', { branch: record.code })">{{ record.name }}</Link>
        </div>

        <div v-if="column.key === 'region'">
          {{ record.region.name }}
        </div>

        <div v-if="column.key === 'cinema_company'" class="flex items-center gap-x-3">
          <div class="h-10 w-10 rounded-full overflow-hidden bg-white shrink-0">
            <img :src="record.cinema_company.logo.url" />
          </div>
          <span>{{ record.cinema_company.name }}</span>
        </div>

        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-tooltip>
            <template #title>Showtimes</template>
            <a-button shape="circle" class="relative hover:!border-purple-200 hover:bg-purple-50">
              <Link :href="route('cinemas.branches.showtimes.index', record.code)">
              <Icon name="calendar_outline"
                class="absolute text-purple-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
              </Link>
            </a-button>
          </a-tooltip>
          <a-tooltip>
            <template #title>Edit</template>
            <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50">
              <Link :href="route('cinemas.branches.edit', record.code)">
              <Icon name="pen_outline"
                class="absolute text-blue-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
              </Link>
            </a-button>
          </a-tooltip>

          <a-tooltip>
            <template #title>Delete</template>
            <a-button shape="circle" class="relative hover:!border-red-200 hover:bg-red-50"
              @click="showConfirmDelete(record.id)">
              <Icon name="trash_outline"
                class="absolute text-red-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
            </a-button>
          </a-tooltip>
        </div>
      </template>
    </a-table>
  </Box>
</template>

<script>
import Breadcrumb from '../../../components/Breadcrumb/Breadcrumb.vue';
import { Button, Table, Tooltip, Select } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { getQuery, convertSortOrder, removeEmptyFields } from '../../../utils/utils';
import { router } from '@inertiajs/vue3';
import { createVNode } from 'vue';
import { debounce } from 'lodash';
import dayjs from 'dayjs';
import axios from 'axios';
import { breadcrumbItemProps } from 'ant-design-vue/es/breadcrumb/BreadcrumbItem';

export default {
  name: 'CinemaBranchesPage',
  components: {
    Button,
    Table,
    Tooltip,
    Select,
    Breadcrumb
  },
  props: ['cinemaBranches'],
  computed: {
    columns() {
      return [
        {
          title: 'Name',
          dataIndex: 'name',
          key: 'name',
          maxWidth: 400,
          sorter: true,
          sortOrder: this.getSortOrder('name')
        },
        {
          title: 'Region',
          dataIndex: 'region',
          key: 'region',
        },
        {
          title: 'Company',
          dataIndex: 'cinema_company',
          key: 'cinema_company',
          width: 250
        },
        {
          title: 'Address',
          dataIndex: 'address',
          key: 'address',
          ellipsis: true,
        },
        {
          title: 'Action',
          key: 'action',
        },
      ]
    },
    pagination() {
      return {
        total: this.cinemaBranches.total,
        current: this.cinemaBranches.current_page,
        pageSize: this.cinemaBranches.per_page,
        showSizeChanger: true,
        pageSizeOptions: ['5', '10', '20', '30'],
        showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
      }
    }
  },
  data() {
    const breadcrumbItems = [
      {
        label: 'Cinemas',
        href: null
      },
      {
        label: 'Branches',
        href: null
      },
    ]
    return {
      search: null,
      sortInfo: {},
      dayjs,
      cinemaCompanyOptions: {
        data: []
      },
      regionOptions: {
        data: [],
        isFetching: false
      },
      region: null,
      company: null,
      breadcrumbItems
    }
  },
  methods: {
    onSearchChange: debounce(function (event) {
      this.handleSearch(event.target.value);
    }, 1000),
    handleSearch(searchText) {
      this.search = searchText;
      this.fetchCinemaBranches({ search: searchText, page: 0, page_size: 10 });
    },
    handleTableChange(pagination, filters, sorter) {
      this.sortInfo = sorter;
      this.fetchCinemaBranches({
        page: pagination.current,
        page_size: pagination.pageSize,
        sort: sorter.order ? sorter.field : '',
        sort_order: convertSortOrder(sorter.order)
      });
    },
    fetchCinemaBranches(params) {
      const query = getQuery();
      const newQuery = removeEmptyFields({ ...query, ...params });
      router.get(route('cinemas.branches.index'), newQuery);
    },
    getSortOrder(key) {
      return this.sortInfo?.columnKey === key && this.sortInfo.order;
    },
    showConfirmDelete: (id) => {
      Modal.confirm({
        centered: true,
        title: "Do you want to delete this item?",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode('div', { class: 'text-red-500' }, "This action can't be undo."),
        onOk: () => router.delete(route('films.destroy', id))
      })
    },
    async handleGetCinemaCompanies() {
      try {
        const response = await axios.get(route('apiCinemas.companies.index'));
        this.cinemaCompanyOptions.data = response.data.data.map((item) => ({ value: item.id, label: item.name, logo: item.logo.url, code: item.code }));
      } catch (error) {
        console.log("error: ", error);
      }
    },
    async handleGetRegions(search) {
      try {
        this.regionOptions.isFetching = true;
        const response = await axios.get(route('apiRegions.index'), {
          params: {
            search
          }
        });
        this.regionOptions.data = response.data.data.data.map((item) => ({ value: item.id, label: item.name, code: item.code }));

        setTimeout(() => {
          this.regionOptions.isFetching = false;
        }, 500)
      } catch (error) {
        console.log("error: ", error);
      }
    },
    handleSearchRegion: debounce(function (search) {
      this.handleGetRegions(search)
    }, 500),
    handleChangeRegion(value, option) {
      this.fetchCinemaBranches({ region: option?.code || '' })
    },
    handleChangeCompany(value, option) {
      this.fetchCinemaBranches({ company: option?.code || '' })
    }
  },
  async mounted() {
    this.handleGetCinemaCompanies();
    this.handleGetRegions();

    const query = getQuery();
    this.search = query.search || '';
    this.pagination.current = query.page ? parseInt(query.page) : 0;
    this.pagination.pageSize = query.page_size ? parseInt(query.page_size) : 0
    if (query.sort) {
      this.sortInfo.columnKey = query.sort;
      this.sortInfo.order = convertSortOrder(query.sort_order);
    }
    if (query.region) {
      const response = await axios.get(route('apiRegions.findByCode', query.region));
      this.region = {
        value: response.data.data.id,
        label: response.data.data.name,
        code: response.data.data.code
      }
    }
    if (query.company) {
      const response = await axios.get(route('apiCinemas.companies.findByCode', query.company));
      this.company = {
        value: response.data.data.id,
        label: response.data.data.name,
        code: response.data.data.code
      }
    }
  }
}
</script>

<style lang="scss" scoped></style>