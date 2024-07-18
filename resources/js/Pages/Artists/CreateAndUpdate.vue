<template>
  <Box>
    <h1 class="text-2xl font-medium text-center mb-4">{{ artist ? 'Update' : 'Create' }} New Artist</h1>
    <a-form layout="vertical" @submit.prevent="onSubmit" :validation-schema="schema" class="grid grid-cols-2 gap-3">
      <a-form-item class="mb-0" label="Name" v-bind="nameProps">
        <a-input v-model:value="name" />
      </a-form-item>
      <a-form-item class="mb-0" label="Birthday" v-bind="birthdayProps">
        <a-date-picker class="w-full" v-model:value="birthday" format="DD/MM/YYYY" />
      </a-form-item>
      <a-form-item class="col-span-2 mb-0" label="Biography" v-bind="biographyProps">
        <a-textarea v-model:value="biography" :rows="10" />
      </a-form-item>

      <a-button class="col-start-1 w-fit" type="primary" htmlType="submit">{{ artist ? 'Update' : 'Create' }}</a-button>
    </a-form>
  </Box>
</template>

<script setup>
import { Input, Button, DatePicker, Textarea, Form, FormItem } from 'ant-design-vue';
import * as yup from 'yup'
import { useForm } from 'vee-validate';
import { router } from '@inertiajs/vue3';
import { toRefs, ref } from 'vue'
import dayjs from 'dayjs';

const props = defineProps(['artist']);
const { artist } = toRefs(props);

const schema = yup.object().shape({
  name: yup.string().required().min(3),
  biography: yup.string().required(),
  birthday: yup.date().required()
})

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
  initialValues: {
    name: artist.value?.name,
    biography: artist.value?.biography,
    birthday: artist.value?.birthday ? new dayjs(artist.value.birthday) : null
  }
})

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const [name, nameProps] = defineField('name', antConfig);
const [biography, biographyProps] = defineField('biography', antConfig);
const [birthday, birthdayProps] = defineField('birthday', antConfig);

const onSubmit = handleSubmit((values) => {
  if (artist.value) {
    router.put(route('artists.update', artist.value.id), values);
  } else {
    router.post(route('artists.store'), values);
  }
})
</script>
