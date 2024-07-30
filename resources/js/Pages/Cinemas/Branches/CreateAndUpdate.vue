<template>
  <Box class="px-5">
    <h1 class="text-2xl text-center font-medium mb-4">Create Cinema Branch</h1>
    <a-form layout="vertical" @submit.prevent="onSubmit" class="grid grid-cols-2 gap-3">
      <a-form-item class="mb-0" label="Name" v-bind="nameProps">
        <a-input :disabled="isSubmitting" size="large" v-model:value="name" />
      </a-form-item>
      <a-form-item class="mb-0" label="Address" v-bind="addressProps">
        <a-input :disabled="isSubmitting" size="large" v-model:value="address" />
      </a-form-item>
      <a-form-item class="mb-0" label="Region" v-bind="regionProps">
        <a-select :disabled="isSubmitting" size="large" label-in-value v-model:value="region" show-search
          placeholder="Select region" :options="regionsOptions.data" @search="handleSearch" :filter-option="false">
          <template v-if="regionsOptions.isFetching" #notFoundContent>
            <a-spin size="small" />
          </template>
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Cinema" v-bind="companyProps">
        <a-select :disabled="isSubmitting" size="large" label-in-value v-model:value="company"
          placeholder="Select cinema" :options="cinemaCompanyOptions.data" :filter-option="false">
          <template #option="{ value: val, label, logo }">
            <div class="flex items-center gap-x-3">
              <div class="h-10 w-10 rounded-full overflow-hidden bg-white">
                <img :src="logo" alt="logo-cinema-company" />
              </div>
              <span>{{ label }}</span>
            </div>
          </template>
        </a-select>
      </a-form-item>
      <a-button :loading="isSubmitting" class="w-fit" type="primary" html-type="submit">Save</a-button>
    </a-form>
  </Box>
</template>

<script setup>
import { Button, Input, Select, Form, FormItem } from 'ant-design-vue';
import * as yup from 'yup';
import { useForm } from 'vee-validate';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { debounce } from 'lodash';
import { reactive, onMounted, ref } from 'vue';

const isSubmitting = ref(false);

const schema = yup.object().shape({
  'name': yup.string().min(10).required(),
  'address': yup.string().min(10).required(),
  'company': yup.object().required(),
  'region': yup.object().required()
});

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const { defineField, handleSubmit, errors, resetForm } = useForm({
  validationSchema: schema
})

const [name, nameProps] = defineField('name', antConfig);
const [address, addressProps] = defineField('address', antConfig);
const [company, companyProps] = defineField('company', antConfig);
const [region, regionProps] = defineField('region', antConfig);

const onSubmit = handleSubmit((values) => {
  isSubmitting.value = true;
  const body = {
    name: values.name,
    address: values.address,
    region: values.region.value,
    company: values.company.value
  }
  router.post(route('cinemas.branches.store'), body);
})

const regionsOptions = reactive({
  data: [],
  isFetching: false
});

async function handleGetRegions(search) {
  try {
    regionsOptions.isFetching = true;
    const response = await axios.get(route('apiRegions.index'), {
      params: {
        search
      }
    });
    regionsOptions.data = response.data.data.data.map((item) => ({ value: item.id, label: item.name }));

    setTimeout(() => {
      regionsOptions.isFetching = false;
    }, 500)
  } catch (error) {
    console.log("error: ", error);
  }
}

const handleSearch = debounce((search) => {
  handleGetRegions(search)
}, 500)

const cinemaCompanyOptions = reactive({
  data: []
});

async function handleGetCinemaCompanies() {
  try {
    const response = await axios.get(route('apiCinemas.companies.index'));
    cinemaCompanyOptions.data = response.data.data.map((item) => ({ value: item.id, label: item.name, logo: item.logo.url }));
  } catch (error) {
    console.log("error: ", error);
  }
}

onMounted(() => {
  handleGetRegions();
  handleGetCinemaCompanies();
})

</script>

<style lang="scss" scoped></style>