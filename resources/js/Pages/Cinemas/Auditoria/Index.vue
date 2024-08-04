<template>
  <Box>
    <div class="flex justify-end mb-4">
      <a-button type="primary">
        <Link :href="route('cinemas.branches.auditoria.create', route().params)">Add New Auditoria</Link>
      </a-button>
    </div>

    <a-table :data-source="auditoria" :columns="columns">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-tooltip>
            <template #title>Edit</template>
            <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50">
              <!-- <Link :href="route('films.edit', record.id)"> -->
              <Icon name="pen_outline"
                class="absolute text-blue-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
              <!-- </Link> -->
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
import { Button, Table, Tooltip } from 'ant-design-vue';
export default {
  name: 'AuditoriaPage',
  components: {
    Button,
    Table
  },
  props: ['auditoria'],
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

    return {
      columns
    }
  }
}
</script>

<style lang="scss" scoped></style>