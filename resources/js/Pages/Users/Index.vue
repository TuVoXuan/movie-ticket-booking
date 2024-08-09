<template>
  <Box>
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
          <label>Role</label>
          <a-select class="mt-2" size="large" allow-clear :options="roleOptions" v-model:value="role"
            placeholder="Select role" @change="handleFilter"></a-select>
        </div>
      </div>

      <div class="self-end">
        <a-button type="primary">
          <Link :href="route('users.create')">Add new user</Link>
        </a-button>
      </div>
    </div>

    <a-table :data-source="users.data" :columns="columns" :pagination="pagination" @change="handleTableChange">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'role'">
          {{ record.roles[0].name }}
        </div>
        <div v-if="column.key === 'created_at'">
          {{ dayjs(record.created_at).format('DD/MM/YYYY') }}
        </div>
        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
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
  </Box>
</template>

<script>
import { Button, Table, Input, Select } from 'ant-design-vue';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { debounce } from 'lodash';
import { convertSortOrder, getQuery, removeEmptyFields } from '../../utils/utils';
import { createVNode } from 'vue';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';

export default {
  name: 'UsersPage',
  components: {
    Button,
    Table,
    Input,
    Select
  },
  props: ['users', 'roles'],
  data() {
    return {
      search: null,
      sortInfo: {},
      dayjs,
      roleOptions: [],
      role: null
    }
  },
  methods: {
    handleTableChange(pagination, filters, sorter) {
      this.sortInfo = sorter;
      this.fetchUsers({
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
      this.fetchUsers({ search: searchText, page: 0, page_size: 10 });
    },
    handleFilter() {
      this.fetchUsers({ role: this.role });
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
    fetchUsers(params) {
      const query = getQuery();
      const newQuery = removeEmptyFields({ ...query, ...params })
      router.get(route('users.index'), newQuery);
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
          dataIndex: 'name',
          key: 'name',
          maxWidth: 400,
        },
        {
          title: 'Email',
          dataIndex: 'email',
          key: 'email',
        },
        {
          title: 'Account',
          dataIndex: 'account',
          key: 'account',
        },
        {
          title: 'Role',
          dataIndex: 'role',
          key: 'role',
        },
        {
          title: 'Created At',
          dataIndex: 'created_at',
          key: 'created_at',
          align: 'center',
        },
        {
          title: 'Action',
          key: 'action',
        },
      ]
    },
    pagination() {
      return {
        total: this.users.total,
        current: this.users.current_page,
        pageSize: this.users.per_page,
        showSizeChanger: true,
        pageSizeOptions: ['5', '10', '20', '30'],
        showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} items`,
      }
    }
  },
  mounted() {
    this.roleOptions = this.roles.map((item) => ({ value: item.code, label: item.name }));

    const query = getQuery();
    this.search = query.search || '';
    this.pagination.current = query.page ? parseInt(query.page) : 0;
    this.pagination.pageSize = query.page_size ? parseInt(query.page_size) : 0

    if (query.role) {
      this.role = this.roles.find((role) => role.code === query.role)?.code;
    }
  }
}
</script>

<style lang="scss" scoped></style>