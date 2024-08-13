<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />

  <Box class="p-4">
    <div class="flex justify-between mb-4">
      <div>
        <a-input size="large" v-model:value="search" placeholder="Search ..." @input="onSearchChange">
          <template #prefix>
            <Icon name="search_outline" class="h-5 w-5" />
          </template>
        </a-input>
      </div>
      <a-button type="primary">
        <Link :href="route('artists.create')">Add New Artist</Link>
      </a-button>
    </div>

    <a-table :data-source="artists.data" :columns="columns" :pagination="pagination" @change="handleTableChange"
      :scroll="{ x: 'max-content' }">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'birthday'">
          {{ record.birthday ? dayjs(record.birthday).format('DD/MM/YYYY') : '-' }}
        </div>
        <div v-if="column.key === 'created_at'">
          {{ dayjs(record.created_at).format('DD/MM/YYYY') }}
        </div>
        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50">
            <Link :href="route('artists.edit', record.id)">
            <Icon name="pen_outline"
              class="absolute text-blue-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
            </Link>

          </a-button>
          <a-button shape="circle" class="relative hover:!border-red-200 hover:bg-red-50"
            @click="showConfirmDelete(record.id)">
            <Icon name="trash_outline"
              class="absolute text-red-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
          </a-button>
        </div>
      </template>
    </a-table>
  </Box>
</template>

<script>
import Breadcrumb from '../../components/Breadcrumb/Breadcrumb.vue';
import { h } from 'vue';
import { Button, Table, Input, Modal } from 'ant-design-vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { createVNode } from 'vue';
import { getQuery, convertSortOrder } from '../../utils/utils';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';

export default {
  name: "ArtistsPage",
  props: ['artists'],
  components: {
    Button,
    Table,
    Input,
    Breadcrumb
  },
  data() {
    const breadcrumbItems = [
      {
        label: 'Artists',
        href: null
      }
    ];

    return {
      search: null,
      sortInfo: {},
      dayjs,
      breadcrumbItems
    }
  },
  methods: {
    h,
    handleTableChange(pagination, filters, sorter) {
      this.sortInfo = sorter;
      const query = getQuery();
      router.get(route('artists.index'), {
        ...query,
        page: pagination.current,
        page_size: pagination.pageSize,
        sort: sorter.field,
        sort_order: convertSortOrder(sorter.order)
      })
    },
    onSearchChange: debounce(function (event) {
      this.handleSearch(event.target.value);
    }, 1000),
    handleSearch(searchText) {
      this.search = searchText;
      const query = getQuery();
      router.get(route('artists.index'), {
        ...query,
        search: searchText,
        page: 0,
        page_size: 10
      });
    },
    showConfirmDelete: (id) => {
      Modal.confirm({
        centered: true,
        title: "Do you want to delete this item?",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode('div', { class: 'text-red-500' }, "This action can't be undo."),
        onOk() {
          router.delete(route('artists.destroy', id));
        }
      })
    }
  },
  computed: {
    columns() {
      return [
        {
          title: 'Name',
          dataIndex: 'name',
          key: 'name',
          sorter: true,
          sortOrder: this.sortInfo?.columnKey === 'name' && this.sortInfo.order
        },
        {
          title: 'Birthday',
          dataIndex: 'birthday',
          key: 'birthday',
          align: 'center',
          sorter: true,
          sortOrder: this.sortInfo?.columnKey === 'birthday' && this.sortInfo.order
        },
        {
          title: 'Created At',
          dataIndex: 'created_at',
          key: 'created_at',
          align: 'center',
          sorter: true,
          sortOrder: this.sortInfo?.columnKey === 'created_at' && this.sortInfo.order
        },
        {
          title: 'Action',
          key: 'action',
        },
      ]
    },
    pagination() {
      return {
        total: this.artists.total,
        current: this.artists.current_page,
        pageSize: this.artists.per_page,
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
  },
}

</script>
