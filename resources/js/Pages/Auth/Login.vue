<template>
  <Box class="p-10 w-[500px]">
    <h1 class="font-bold text-2xl text-center">Login</h1>
    <a-form layout="vertical" @submit.prevent="onSubmit" class="grid grid-cols-1 gap-3">
      <a-form-item class="mb-0" label="Account" v-bind="accountProps">
        <a-input :disabled="isSubmitting" size="large" v-model:value="account" />
      </a-form-item>
      <a-form-item class="mb-0" label="Password" v-bind="passwordProps">
        <a-input-password :disabled="isSubmitting" size="large" v-model:value="password" />
      </a-form-item>
      <a-button :loading="isSubmitting" class="mt-2" type="primary" html-type="submit">Login</a-button>
    </a-form>
  </Box>
</template>

<script setup>
import AuthLayout from '../../layouts/AuthLayout.vue';
import { Button, Form, Input, InputPassword, FormItem } from 'ant-design-vue';
import * as yup from 'yup';
import { useForm } from 'vee-validate';
import { PasswordRegex, AccountRegex } from '../../constant';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const isSubmitting = ref(false);

const schema = yup.object().shape({
  account: yup.string().min(3).matches(AccountRegex, 'Account must only have letters and numbers and start with letter').required(),
  password: yup.string().matches(PasswordRegex, 'Password must have at least 8 characters, at least one letter, one number and one special character.').required()
});

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema
});

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const [account, accountProps] = defineField('account', antConfig);
const [password, passwordProps] = defineField('password', antConfig);

const onSubmit = handleSubmit((values) => {
  isSubmitting.value = true;
  router.post(route('auth.login'), values);
})

defineOptions({ layout: AuthLayout })
</script>
