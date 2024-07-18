<template>
  <Box>
    <div class="flex justify-between mb-4">
      <div>
        <a-input v-model:value="search" placeholder="Search ..." @input="onSearchChange">
          <template #prefix>
            <Icon name="search_outline" class="h-5 w-5" />
          </template>
        </a-input>
      </div>

      <a-button type="primary" @click="openModal = true">Add New Genre</a-button>
    </div>

    <a-table :data-source="genres" :columns="columns">
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
  </Box>
  <a-modal v-model:open="openModal" :title="selectedGenre ? 'Update Genre' : 'Create Genre'"
    :ok-button-props="{ form: 'genreForm', htmlType: 'submit' }">
    <a-form id="genreForm" layout="vertical" @submit="onSubmit">
      <a-form-item class="mb-0" label="Name" v-bind="nameProps">
        <a-input v-model:value="name" />
      </a-form-item>
    </a-form>
  </a-modal>
</template>

<script setup>
import { Table, Button, Modal, Form, FormItem } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { ExclamationCircleOutlined } from '@ant-design/icons-vue';
import { ref, onMounted, defineProps, toRefs, createVNode } from 'vue'
import { router, usePage } from '@inertiajs/vue3';
import { getQuery } from '../../utils/utils'
import { debounce } from 'lodash';
import dayjs from 'dayjs';

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

const props = defineProps(['genres'])
const { genres } = toRefs(props);
const search = ref();
const openModal = ref(false);
const selectedGenre = ref();

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
  if (selectedGenre.value) {
    router.put(route('genres.update', selectedGenre.value.id), values);
  } else {
    router.post(route('genres.store'), values)
  }
})

const onSearchChange = debounce((event) => {
  router.get(route('genres.index'), {
    search: event.target.value
  });
}, 1000)

const showConfirmDelete = (id) => {
  Modal.confirm({
    centered: true,
    title: "Do you want to delete this item?",
    icon: createVNode(ExclamationCircleOutlined),
    content: createVNode('div', { class: 'text-red-500' }, "This action can't be undo."),
    onOk() {
      router.delete(route('genres.destroy', id));
    }
  })
}

const handleClickEdit = (id) => {
  selectedGenre.value = genres.value.find((item) => item.id === id);
  resetForm({
    values: {
      name: selectedGenre.value?.name
    }
  })
  openModal.value = true;
}

onMounted(() => {
  const query = getQuery();
  search.value = query.search || null;

  router.on('finish', (event) => {
    const page = usePage();
    if (page.props.success) {
      openModal.value = false;
      resetForm();
    }
  })
})

</script>

<style lang="scss" scoped></style>