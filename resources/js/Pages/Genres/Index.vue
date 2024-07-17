<template>
  <Box>
    <div class="flex justify-end">
      <Button @click="showForm = true">Add New Genre</Button>
    </div>

    <DataTable v-model:selection="selectedRow" :value="genres" rowHover>
      <template #header>
        <div class="flex flex-wrap gap-2 items-center justify-between">
          <h4 class="text-xl font-medium">Manage Genres</h4>

          <IconField>
            <InputIcon class="pi pi-search" />
            <InputText :value="search" @input="onSearchChange" class="w-full" placeholder="Search" />
          </IconField>
        </div>
      </template>

      <Column selectionMode="single" headerStyle="width: 3rem"></Column>
      <Column field="name" header="Name"></Column>
      <Column field="created_at" header="Created At">
        <template #body="slotProps">
          {{ getDate(slotProps.data.created_at, 'DD-MM-YYYY') }}
        </template>
      </Column>
      <Column header="Actions">
        <template #body="slotProps">
          <div class="flex items-center gap-x-3">
            <button class="h-8 w-8 text-blue-400 bg-white hover:bg-blue-100 transition-colors ease-linear rounded-full"
              @click="openEditModal(slotProps.data.id)">
              <i class="pi pi-pencil"></i>
            </button>
            <button @click="confirmDelete(slotProps.data.id)"
              class="h-8 w-8 text-red-400 bg-white hover:bg-red-100 transition-colors ease-linear rounded-full">
              <i class="pi pi-trash"></i>
            </button>
          </div>
        </template>
      </Column>
    </DataTable>

    <Dialog v-model:visible="showForm" modal :header="`${selectedGenre ? 'Edit Genre' : 'Add New Genre'}`"
      :style="{ width: '25rem' }">
      <form @submit="onSubmit">
        <div class="flex flex-col gap-4 mb-4">
          <label for="name" class="font-semibold w-24">Name</label>
          <InputText v-model="name" :invalid="!!errors.name" class="flex-auto" autocomplete="off" />
          <small class="text-red-500">{{ errors.name }}</small>
        </div>
        <div class="flex justify-end gap-2">
          <Button type="button" label="Cancel" severity="secondary" @click="showForm = false"></Button>
          <Button type="submit" label="Save"></Button>
        </div>
      </form>
    </Dialog>
  </Box>
</template>

<script setup>
import Button from 'primevue/button';
import Dialog from 'primevue/dialog';
import InputText from 'primevue/inputtext';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { ref, onMounted, defineProps, toRefs } from 'vue'
import { router, usePage } from '@inertiajs/vue3';
import { getDate, getQuery } from '../../utils/utils'
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';
import { debounce } from 'lodash';
import { useConfirm } from "primevue/useconfirm";

const props = defineProps(['genres'])
const { genres } = toRefs(props);

const showForm = ref(false);
const selectedRow = ref();
const search = ref();
const selectedGenre = ref();

const schema = yup.object().shape({
  name: yup.string().required()
})

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
})

const [name] = defineField('name');

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

const confirm = useConfirm();

const confirmDelete = (id) => {
  confirm.require({
    message: 'Are you sure you want to delete it?',
    header: 'Warning',
    icon: 'pi pi-info-circle',
    rejectProps: {
      label: "Cancel",
      severity: 'secondary',
      outlined: true
    },
    acceptProps: {
      label: 'Delete',
      severity: 'danger'
    },
    accept: () => {
      router.delete(route('genres.destroy', id));
    }
  })
}

const openEditModal = (id) => {
  selectedGenre.value = genres.value.find((item) => item.id === id);
  resetForm({
    values: {
      name: selectedGenre.value?.name
    }
  })
  showForm.value = true;
}

onMounted(() => {
  const query = getQuery();
  search.value = query.search || null;

  router.on('finish', (event) => {
    const page = usePage();
    if (page.props.success) {
      showForm.value = false;
    }
  })
})

</script>

<style lang="scss" scoped></style>