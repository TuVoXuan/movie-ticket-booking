<template>
  <a-form layout="vertical" @submit.prevent="onSubmit">
    <a-form-item class="mb-0" label="Logo" v-bind="logoProps">
      <Upload :disabled="isSubmitting" v-model:fileList="logo" :url="logoURL" />
    </a-form-item>
    <a-form-item class="mb-0" label="Name" v-bind="nameProps">
      <a-input :disabled="isSubmitting" size="large" v-model:value="name" placeholder="Enter cinema name" />
    </a-form-item>

    <div class="mt-4 flex justify-end">
      <a-button type="primary" :loading="isSubmitting" htmlType="submit">Save</a-button>
    </div>
  </a-form>
</template>

<script setup>
import { Input, Form, FormItem, Button } from 'ant-design-vue';
import * as yup from 'yup'
import { useForm } from 'vee-validate';
import Upload from '../Upload/Upload.vue';
import { ref, defineExpose, defineProps, toRefs, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps(['cinemaCompany']);
const { cinemaCompany } = toRefs(props);

const isSubmitting = ref(false);
const logoURL = ref(cinemaCompany.value?.logo.url || '');

const schema = yup.object().shape({
  logo: yup.array().required(),
  name: yup.string().required()
})

const { defineField, handleSubmit, errors, resetForm } = useForm({
  validationSchema: schema,
});

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const [logo, logoProps] = defineField('logo', antConfig);
const [name, nameProps] = defineField('name', antConfig);

const onSubmit = handleSubmit((values) => {
  try {
    isSubmitting.value = true;
    const formData = new FormData();

    formData.append('name', values.name);


    if (cinemaCompany.value) {
      if (!values.logo[0].url) {
        formData.append('logo', values.logo[0].originFileObj);
      }

      router.post(route('cinemas.companies.update', {
        company: cinemaCompany.value.id,
        _query: {
          _method: 'PUT'
        }
      }), formData)
    } else {
      formData.append('logo', values.logo[0].originFileObj);
      router.post(route('cinemas.companies.store'), formData);
    }

  } catch (error) {
    console.log("error: ", error);
  }
})

function handleResetForm() {
  resetForm({
    values: {
      name: '',
      logo: []
    }
  });
  isSubmitting.value = false;
  logoURL.value = '';
}

watch(cinemaCompany, (newVal, oldVal) => {
  logoURL.value = newVal?.logo.url || '';
  if (newVal) {
    resetForm({
      values: {
        name: cinemaCompany.value?.name,
        logo: [{ uid: cinemaCompany.value?.logo.id, url: cinemaCompany.value?.logo.url, }]
      },
    })
  }
})

defineExpose({
  handleResetForm
})
</script>

<style lang="scss" scoped></style>