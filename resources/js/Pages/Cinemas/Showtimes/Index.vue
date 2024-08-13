<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />
  <Box>
    <div class="flex justify-between items-end mb-4">
      <div class="flex gap-3">
        <div class="flex flex-col gap-y-2">
          <label>Screening Date</label>
          <a-date-picker class="w-[200px]" v-model:value="screeningDate" size="large" @change="onChangeScreeningDate"
            format="DD/MM/YYYY" />
        </div>
        <div class="flex flex-col gap-y-2">
          <label>Auditorium</label>
          <a-select class="w-[200px]" size="large" v-model:value="auditorium" label-in-value
            placeholder="Select auditorium" :options="auditoriaOptions" :filter-option="false" allow-clear
            @change="handleChangeAuditorium">
          </a-select>
        </div>
      </div>


      <a-button type="primary">
        <Link :href="route('cinemas.branches.showtimes.create', route().params)">Add new screening</Link>
      </a-button>
    </div>

    <a-table :data-source="showtimes.data" :columns="columns" :pagination="pagination" @change="handleTableChange">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'film'">
          {{ record.film.title }}
        </div>
        <div v-if="column.key === 'screening_time'">
          {{ dayjs(record.screening_time).format('HH:mm DD/MM/YYYY') }}
        </div>
        <div v-if="column.key === 'auditorium'">
          {{ record.auditorium.name }}
        </div>

        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-tooltip>
            <template #title>Edit</template>
            <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50">
              <Link
                :href="route('cinemas.branches.showtimes.edit', { branch: route().params.branch, showtime: record.id })">
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
import { Button, Table, DatePicker, Tooltip, Select } from 'ant-design-vue';
import { getQuery, removeEmptyFields, convertSortOrder } from '../../../utils/utils';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { breadcrumbItemProps } from 'ant-design-vue/es/breadcrumb/BreadcrumbItem';

export default {
  name: 'ShowtimesPage',
  components: {
    Button,
    Table,
    DatePicker,
    Tooltip,
    Select,
    Breadcrumb
  },
  props: ['showtimes', 'cinemaBranchName'],
  data() {
    const breadcrumbItems = [
      {
        label: 'Cinemas',
        href: null
      },
      {
        label: 'Branches',
        href: route('cinemas.branches.index')
      },
      {
        label: this.cinemaBranchName,
        href: route('cinemas.branches.edit', { branch: route().params.branch })
      },
      {
        label: 'Showtimes',
        href: null
      }
    ];

    return {
      screeningDate: null,
      search: null,
      sortInfo: {},
      dayjs,
      auditoriaOptions: [],
      auditorium: null,
      breadcrumbItems
    }
  },
  methods: {
    handleTableChange(pagination, filters, sorter) {
      this.sortInfo = sorter;
      this.fetchShowtimes({
        page: pagination.current,
        page_size: pagination.pageSize,
        sort: sorter.field,
        sort_order: convertSortOrder(sorter.order)
      });
    },
    showConfirmDelete: (id) => {
      Modal.confirm({
        centered: true,
        title: "Do you want to delete this item?",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode('div', { class: 'text-red-500' }, "This action can't be undo."),
        // onOk: () => router.delete(route('films.destroy', id))
      })
    },
    onChangeScreeningDate(value, dateString) {
      this.fetchShowtimes({
        screening_date: value?.format('YYYY/MM/DD')
      })
    },
    handleChangeAuditorium(value, option) {
      this.fetchShowtimes({ auditorium: option?.code || '' })
    },
    fetchShowtimes(params) {
      const query = getQuery();
      const newQuery = removeEmptyFields({ ...query, ...params })
      const branch = route().params.branch;
      router.get(route('cinemas.branches.showtimes.index', { branch }), newQuery);
    },
    async fetchAuditoriaOption() {
      try {
        const response = await axios.get(route('apiCinemas.branches.auditoria.getAll', {
          branch: route().params.branch
        }));
        this.auditoriaOptions = response.data.data.map((item) => (
          {
            value: item.id,
            label: item.name,
            code: item.code
          }));
      } catch (error) {
        console.log("error: ", error);
      }
    },
    getSortOrder(key) {
      return this.sortInfo?.columnKey === key && this.sortInfo.order;
    },
  },
  computed: {
    columns() {
      return [
        {
          title: 'Film',
          dataIndex: 'film',
          key: 'film',
          maxWidth: 400,
        },
        {
          title: 'Screening time',
          dataIndex: 'screening_time',
          key: 'screening_time',
          align: 'center',
          sorter: true,
          sortOrder: this.getSortOrder('screening_time')
        },
        {
          title: 'Auditorium',
          dataIndex: 'auditorium',
          key: 'auditorium',
        },
        {
          title: 'Film translation',
          dataIndex: 'film_translation',
          key: 'film_translation',
        },
        {
          title: 'Action',
          key: 'action',
        },
      ]
    },
    pagination() {
      return {
        total: this.showtimes.total,
        current: this.showtimes.current_page,
        pageSize: this.showtimes.per_page,
        showSizeChanger: true,
        pageSizeOptions: ['5', '10', '20', '30'],
        showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
      }
    }
  },
  async mounted() {
    this.fetchAuditoriaOption();

    const query = getQuery();
    this.pagination.current = query.page ? parseInt(query.page) : 0;
    this.pagination.pageSize = query.page_size ? parseInt(query.page_size) : 0
    if (query.sort) {
      this.sortInfo.columnKey = query.sort;
      this.sortInfo.order = convertSortOrder(query.sort_order);
    }
    if (query.screening_date) {
      this.screeningDate = new dayjs(query.screening_date);
    }
    if (query.auditorium) {
      const branch = route().params.branch;
      const response = await axios.get(route('apiCinemas.branches.auditoria.getByCode', {
        branch,
        auditorium: query.auditorium
      }));
      this.auditorium = {
        value: response.data.data.id,
        label: response.data.data.name,
        code: response.data.data.code
      }
    }
  },
}
</script>

<style lang="scss" scoped></style>