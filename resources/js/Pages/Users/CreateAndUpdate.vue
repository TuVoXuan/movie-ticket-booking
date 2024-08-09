<template>
  <Box>
    <h1 class="text-2xl font-medium text-center mb-4">Create New User</h1>
    <a-form layout="vertical" @submit.prevent="onSubmit" class="grid grid-cols-2 gap-3">
      <a-form-item class="mb-0" label="Name" v-bind="nameProps">
        <a-input :disabled="isSubmitting" size="large" v-model:value="name" placeholder="Enter name" />
      </a-form-item>
      <a-form-item class="mb-0" label="Email" v-bind="emailProps">
        <a-input :disabled="isSubmitting || !!user" size="large" v-model:value="email" placeholder="Enter email" />
      </a-form-item>
      <a-form-item class="mb-0" label="Account" v-bind="accountProps">
        <a-input :disabled="isSubmitting || !!user" size="large" v-model:value="account" placeholder="Enter account" />
      </a-form-item>
      <a-form-item class="mb-0" label="Role" v-bind="roleProps">
        <a-select :disabled="isSubmitting" :options="roleOptions" v-model:value="role" placeholder="Select role"
          size="large"></a-select>
      </a-form-item>
      <a-form-item class="mb-0" v-bind="isActiveProps">
        <a-checkbox v-model:checked="isActive">Active</a-checkbox>
      </a-form-item>
      <a-button :loading="isSubmitting" class="col-start-1 w-fit" type="primary" html-type="submit">Save</a-button>
    </a-form>
  </Box>
</template>

<script setup>
import { Button, Form, FormItem, Input, Select, Checkbox } from 'ant-design-vue';
import * as yup from 'yup';
import { useForm } from 'vee-validate';
import { router, usePage } from '@inertiajs/vue3';
import { defineProps, toRefs, ref, onMounted } from 'vue';
import { EmailRegex } from '../../constant';

const props = defineProps(['roles', 'user']);
const { roles, user } = toRefs(props);
const roleOptions = ref([]);
const isSubmitting = ref(false);

const schema = yup.object().shape({
  name: yup.string().required(),
  email: yup.string().matches(EmailRegex, 'Invalid email address.').required(),
  account: yup.string().required(),
  role: yup.string().required(),
  isActive: yup.boolean(),
})

const { defineField, handleSubmit, errors, resetForm } = useForm({
  validationSchema: schema,
  initialValues: {
    name: user.value && user.value.name,
    email: user.value && user.value.email,
    account: user.value && user.value.account,
    isActive: user.value ? !!user.value.is_active : false,
    role: user.value && user.value.roles[0].id
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
const [email, emailProps] = defineField('email', antConfig);
const [account, accountProps] = defineField('account', antConfig);
const [role, roleProps] = defineField('role', antConfig);
const [isActive, isActiveProps] = defineField('isActive', antConfig);

const onSubmit = handleSubmit((values) => {
  isSubmitting.value = true;
  let body = {
    name: values.name,
    is_active: values.isActive,
    role: values.role
  }

  if (user.value) {
    router.put(route('users.update', { user: user.value.id }), body);
  } else {
    body.email = values.email;
    body.account = values.account;
    router.post(route('users.store'), body);
  }
})

onMounted(() => {
  roleOptions.value = roles.value.map((item) => ({ value: item.id, label: item.name }));

  router.on('finish', (event) => {
    const page = usePage();
    if (page.props.error) {
      isSubmitting.value = false;
    }
  })
})
</script>

<style lang="scss" scoped></style>