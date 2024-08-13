<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />
  <Box>
    <div class="flex justify-end mb-4">
      <a-button type="primary">
        <Link :href="route('cinemas.branches.auditoria.create', route().params)">Add New Auditoria</Link>
      </a-button>
    </div>

    <a-table :data-source="auditoria" :columns="columns" :scroll="{ x: 'max-content' }">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-tooltip>
            <template #title>Edit</template>
            <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50">
              <Link
                :href="route('cinemas.branches.auditoria.edit', { branch: route().params.branch, auditorium: record.code })">
              <Icon name="pen_outline"
                class="absolute text-blue-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
              </Link>
            </a-button>
          </a-tooltip>

          <a-tooltip>
            <template #title>Delete</template>
            <a-button shape="circle" class="relative hover:!border-red-200 hover:bg-red-50">
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
import Breadcrumb from '../../../components/Breadcrumb/Breadcrumb.vue'
import { Button, Table, Tooltip } from 'ant-design-vue';
export default {
  name: 'AuditoriaPage',
  components: {
    Button,
    Table,
    Breadcrumb
  },
  props: ['auditoria', 'cinemaBranchName'],
  data() {
    const columns = [
      {
        title: 'Name',
        dataIndex: 'name',
        key: 'name',
      },
      {
        title: 'Capacity',
        dataIndex: 'capacity',
        key: 'capacity',
        align: 'right'
      },
      {
        title: 'Seat Direction',
        dataIndex: 'seat_direction',
        key: 'seat_direction',
        align: 'center'
      },
      {
        title: 'Action',
        key: 'action',
      }
    ]
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
        label: 'Auditoriums',
        href: null
      }
    ];
    return {
      columns,
      breadcrumbItems
    }
  }
}
</script>

<style lang="scss" scoped></style>