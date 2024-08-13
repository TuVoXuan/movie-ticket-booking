<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />
  <Box>
    <div class="flex justify-end mb-4">
      <a-button type="primary" @click="openModal = true">Add New Role</a-button>
    </div>

    <a-table :data-source="roles" :columns="columns" :scroll="{ x: 'max-content' }">
      <template #bodyCell="{ column, record }">
        <div v-if="column.key === 'created_at'">
          {{ dayjs(record.created_at).format('DD/MM/YYYY') }}
        </div>
        <div v-if="column.key === 'action'" class="flex items-center gap-x-3">
          <a-button shape="circle" class="relative hover:!border-blue-200 hover:bg-blue-50"
            @click="handleClickEdit(record.id)">
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

    <a-modal v-model:open="openModal" :title="selectedRole ? 'Update Role' : 'Create Role'"
      :ok-button-props="{ form: 'roleForm', htmlType: 'submit' }" @cancel="handleCloseModal" :force-render="true">
      <a-form id="roleForm" layout="vertical" @submit="onSubmit">
        <a-form-item class="mb-0" label="Name" v-bind="nameProps">
          <a-input v-model:value="name" />
        </a-form-item>
      </a-form>
    </a-modal>
  </Box>
</template>

<script setup>
import Breadcrumb from '../../components/Breadcrumb/Breadcrumb.vue';
import { Table, Button, Modal, Form, FormItem } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { ref, onMounted, defineProps, toRefs, createVNode } from 'vue'
import { router, usePage } from '@inertiajs/vue3';
import dayjs from 'dayjs';

const props = defineProps(['roles'])
const { roles } = toRefs(props);

const breadcrumbItems = ref([
  {
    label: 'Roles',
    href: null
  }
])

const columns = ref([
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
])

const openModal = ref(false);
const selectedRole = ref();

const schema = yup.object().shape({
  name: yup.string().required()
})

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
})

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const [name, nameProps] = defineField('name', antConfig);

const onSubmit = handleSubmit((values) => {
  if (selectedRole.value) {
    router.put(route('roles.update', selectedRole.value.id), values);
  } else {
    router.post(route('roles.store'), values)
  }
})

const showConfirmDelete = (id) => {
  Modal.confirm({
    centered: true,
    title: "Do you want to delete this item?",
    icon: createVNode(ExclamationCircleOutlined),
    content: createVNode('div', { class: 'text-red-500' }, "This action can't be undo."),
    onOk() {
      router.delete(route('roles.destroy', id));
    }
  })
}

const handleClickEdit = (id) => {
  selectedRole.value = roles.value.find((item) => item.id === id);
  resetForm({
    values: {
      name: selectedRole.value?.name
    }
  })
  openModal.value = true;
}

const handleCloseModal = () => {
  resetForm({ values: { name: '' } });
  selectedRole.value = null;
}

onMounted(() => {
  router.on('finish', (event) => {
    const page = usePage();
    if (page.props.success) {
      openModal.value = false;
      resetForm();
    }
  })
})
</script>