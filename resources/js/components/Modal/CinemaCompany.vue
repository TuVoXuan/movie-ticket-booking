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
import { ref, defineExpose } from 'vue'

const isSubmitting = ref(false);
const logoURL = ref();

const schema = yup.object().shape({
  logo: yup.array().required(),
  name: yup.string().required()
})

const { defineField, handleSubmit, errors, resetForm } = useForm({
  validationSchema: schema
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
  console.log("values: ", values);
  isSubmitting.value = true;
})

function handleResetForm() {
  resetForm();
  isSubmitting.value = false;
  logoURL.value = '';
}

defineExpose({
  handleResetForm
})
</script>

<style lang="scss" scoped></style>