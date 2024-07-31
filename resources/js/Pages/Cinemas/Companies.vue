<template>
  <div>
    <Box>
      <div class="flex justify-end gap-x-3 mb-4">
        <a-button type="primary" @click="handleOpenModal">Add New Cinema</a-button>
      </div>

      <a-table :data-source="cinemas" :columns="columns">
        <template #bodyCell="{ column, record }">
          <div v-if="column.key === 'name'" class="flex items-center gap-x-3">
            <div class="h-[50px] w-[50px]">
              <img :src="record.logo.url" alt="logo-cinema-company" />
            </div>
            <span>{{ record.name }}</span>
          </div>
          <div v-if="column.key === 'created_at'">
            {{ dayjs(record.created_at).format('DD/MM/YYYY') }}
          </div>
          <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
            <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50"
              @click="handleClickEdit(record)">
              <Icon name="pen_outline"
                class="absolute text-blue-500 left-1/2 translate-x-[-50%] translate-y-[-50%] h-5 w-5" />
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
    <a-modal v-model:open="isOpen" :title="selectedCinemaCompany ? 'Edit Cinema' : 'Create Cinema'" :footer="null"
      @cancel="onCloseModal" :force-render="true">
      <CinemaCompany :cinemaCompany="selectedCinemaCompany" ref="CinemaCompanyForm" />
    </a-modal>
  </div>
</template>

<script>
import { Button, Modal, Table } from 'ant-design-vue';
import CinemaCompany from '../../components/Modal/CinemaCompany.vue';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { createVNode } from 'vue'
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';

export default {
  name: 'CinemaCompaniesPage',
  components: {
    Button,
    CinemaCompany,
    Modal,
    Table
  },
  props: ['cinemas'],
  data() {
    const columns = [
      {
        title: 'Name',
        dataIndex: 'name',
        key: 'name',
      },
      {
        title: 'Created At',
        dataIndex: 'created_at',
        key: 'created_at',
        align: 'center'
      },
      {
        title: 'Action',
        key: 'action'
      }
    ]
    return {
      isOpen: false,
      selectedCinemaCompany: null,
      columns,
      dayjs
    }
  },
  methods: {
    handleOpenModal() {
      this.isOpen = true;
    },
    onCloseModal() {
      this.$refs.CinemaCompanyForm.handleResetForm();
      this.selectedCinemaCompany = null;
    },
    handleClickEdit(record) {
      this.selectedCinemaCompany = record;
      this.isOpen = true;
    },
    showConfirmDelete(id) {
      Modal.confirm({
        centered: true,
        title: "Do you want to delete this item?",
        icon: createVNode(ExclamationCircleOutlined),
        content: createVNode('div', { class: 'text-red-500' }, "This action can't be undo."),
        onOk() {

          router.delete(route('cinemas.companies.destroy', id));
        },
        okButtonProps: {
          loading: this.loadingDelete
        }
      })
    }
  },
  mounted() {
    router.on('finish', (event) => {
      if (this.$page.props?.success) {
        this.$refs.CinemaCompanyForm.handleResetForm();
        this.isOpen = false;
      }
    })
  }
}
</script>

<style lang="scss" scoped></style>