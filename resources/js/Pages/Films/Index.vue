<template>
  <Box class="p-4">
    <div class="flex justify-between mb-4">
      <div class="flex gap-x-3">
        <div>
          <label>Search</label>
          <a-input class="mt-2" size="large" v-model:value="search" placeholder="Search ..." @input="onSearchChange">
            <template #prefix>
              <Icon name="search_outline" class="h-5 w-5" />
            </template>
          </a-input>
        </div>

        <div class="flex flex-col">
          <label>Release Date</label>
          <a-range-picker size="large" class="mt-2" :presets="rangePresets" format="DD/MM/YYYY"
            v-model:value="dateRange" />
        </div>
        <a-button @click="handleFilter" class="self-end" type="primary">Search</a-button>
      </div>
      <div class="self-end">
        <a-button type="primary">
          <Link :href="route('films.create')">Add New Film</Link>
        </a-button>
      </div>
    </div>

    <a-table :data-source="films.data" :columns="columns" :pagination="pagination" @change="handleTableChange">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'title'">
          <Link :href="route('films.edit', record.id)">{{ record.title }}</Link>
        </div>
        <div v-if="column.key === 'release_date'">
          {{ dayjs(record.release_date).format('DD/MM/YYYY') }}
        </div>
        <div v-if="column.key === 'created_at'">
          {{ dayjs(record.created_at).format('DD/MM/YYYY') }}
        </div>
        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-tooltip>
            <template #title>Preview</template>
            <a-button @click="getFilmDetails(record.id)" shape="circle"
              class="relative hover:!border-green-200 hover:bg-green-50">
              <Icon name="earth_outline"
                class="absolute text-green-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
            </a-button>
          </a-tooltip>

          <a-tooltip>
            <template #title>Edit</template>
            <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50">
              <Link :href="route('films.edit', record.id)">
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
    <a-modal v-model:open="showModal" title="Preview Film Details" :footer="null" :width="1000">
      <ShowFilm :film="filmDetails" />
    </a-modal>
  </Box>
</template>

<script>
import { Button, Table, Input, Modal, message, RangePicker, Tooltip } from 'ant-design-vue';
import { convertSortOrder, getQuery } from '../../utils/utils';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import dayjs from 'dayjs';
import axios from 'axios';
import ShowFilm from '../../components/Modal/ShowFilm.vue';

export default {
  name: 'FilmPage',
  props: ['films'],
  components: {
    Button,
    Table,
    Input,
    ShowFilm,
    RangePicker,
    Tooltip
  },
  data() {
    const rangePresets = [
      { label: 'Last 7 Days', value: [dayjs().add(-7, 'd'), dayjs()] },
      { label: 'Last 14 Days', value: [dayjs().add(-14, 'd'), dayjs()] },
      { label: 'Last 30 Days', value: [dayjs().add(-30, 'd'), dayjs()] },
      { label: 'Last Month', value: [dayjs().subtract(1, 'month').startOf('month'), dayjs().subtract(1, 'month').endOf('month')] },
      { label: 'This Month', value: [dayjs().startOf('month'), dayjs().endOf('month')] },
      { label: 'Next Month', value: [dayjs().add(1, 'month').startOf('month'), dayjs().add(1, 'month').endOf('month')] },
    ];

    return {
      dateRange: null,
      search: null,
      sortInfo: {},
      dayjs,
      filmDetails: {},
      showModal: false,
      rangePresets
    }
  },
  methods: {
    handleTableChange(pagination, filters, sorter) {
      this.sortInfo = sorter;
      this.fetchFilms({
        page: pagination.current,
        page_size: pagination.pageSize,
        sort: sorter.field,
        sort_order: convertSortOrder(sorter.order)
      });
    },
    onSearchChange: debounce(function (event) {
      this.handleSearch(event.target.value);
    }, 1000),
    handleSearch(searchText) {
      this.search = searchText;
      this.fetchFilms({ search: searchText, page: 0, page_size: 10 });
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
    async getFilmDetails(id) {
      try {
        message.loading('Getting film details for preview...', 0);
        const response = await axios.get(route('apiFilms.show', id));
        this.filmDetails = response.data.data;
        this.showModal = true;
      } catch (error) {
        message.error('An error occurred during get film details', 2500);
      } finally {
        message.destroy();
      }
    },
    handleFilter() {
      const [released_from, released_to] = this.dateRange.map(date => date ? date.toISOString() : null);
      this.fetchFilms({ released_from, released_to });
    },
    fetchFilms(params) {
      const query = getQuery();
      router.get(route('films.index'), { ...query, ...params });
    },
    getSortOrder(key) {
      return this.sortInfo?.columnKey === key && this.sortInfo.order;
    },
  },
  computed: {
    columns() {
      return [
        {
          title: 'Name',
          dataIndex: 'title',
          key: 'title',
          maxWidth: 400,
          sorter: true,
          sortOrder: this.getSortOrder('title')
        },
        {
          title: 'Release Date',
          dataIndex: 'release_date',
          key: 'release_date',
          align: 'center',
          sorter: true,
          sortOrder: this.getSortOrder('release_date')
        },
        {
          title: 'Duration',
          dataIndex: 'duration',
          key: 'duration',
          align: 'center',
        },
        {
          title: 'Age Restricted',
          dataIndex: 'age_restricted',
          key: 'age_restricted',
          align: 'center',
        },
        {
          title: 'Created At',
          dataIndex: 'created_at',
          key: 'created_at',
          align: 'center',
          sorter: true,
          sortOrder: this.getSortOrder('created_at')
        },
        {
          title: 'Action',
          key: 'action',
        },
      ]
    },
    pagination() {
      return {
        total: this.films.total,
        current: this.films.current_page,
        pageSize: this.films.per_page,
        showSizeChanger: true,
        pageSizeOptions: ['5', '10', '20', '30'],
        showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
      }
    }
  },
  mounted() {
    const query = getQuery();
    this.search = query.search || '';
    this.pagination.current = query.page ? parseInt(query.page) : 0;
    this.pagination.pageSize = query.page_size ? parseInt(query.page_size) : 0
    if (query.sort) {
      this.sortInfo.columnKey = query.sort;
      this.sortInfo.order = convertSortOrder(query.sort_order);
    }
    if (query.released_from && query.released_to) {
      this.dateRange = [new dayjs(query.released_from), new dayjs(query.released_to)];
    }
  },
}
</script>