<template>
  <Box>
    <div class="flex justify-between mb-4">
      <a-form id="rolePermissionForm" layout="vertical" @submit="onSubmit">
        <a-form-item class="mb-0 w-fit" label="Role" v-bind="roleProps">
          <a-select :options="roleOptions" v-model:value="role" placeholder="Select role"
            @change="handleChangeRole"></a-select>
        </a-form-item>
      </a-form>
      <a-button class="self-end" type="primary" form="rolePermissionForm" html-type="submit">Save</a-button>
    </div>

    <div class="flex gap-3 items-center mb-4">
      <span class="font-medium">Permissions</span>
      <div class="h-[1px] border-b-[1px] border-b-gray-200 flex-1"></div>
    </div>
    <div class="grid grid-cols-3 gap-3">
      <div v-for="group in treeData">
        <a-tree :tree-data="[group]" checkable :selectable="false" v-model:expandedKeys="expandedKeys"
          v-model:checkedKeys="checkedKeys[group.key]">
        </a-tree>
      </div>
    </div>
  </Box>
</template>

<script setup>
import { Tree, Select, Button, Form, FormItem } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { defineProps, onMounted, reactive, toRefs, ref } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps(['permissions', 'roles']);
const { permissions, roles } = toRefs(props);

let treeData = ref([]);
let checkedKeys = ref({});
let originCheckedKeys = ref({});
let expandedKeys = ref([]);
let roleOptions = ref([]);

const schema = yup.object().shape({
  role: yup.string().required()
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

const [role, roleProps] = defineField('role', antConfig);

function groupPermissions(permissions) {
  const grouped = {};

  permissions.forEach(permission => {
    const [group, action] = permission.name.split('.');

    if (!grouped[group]) {
      grouped[group] = [];
    }

    grouped[group].push({
      id: permission.id,
      action: action,
      code: permission.code
    });
  });

  let treeOptions = [];

  for (const property in grouped) {
    treeOptions.push({
      title: property,
      key: property,
      children: grouped[property].map((item) => ({
        title: item.action,
        key: `${item.code}-${item.id}`,
      }))
    })
  }
  return treeOptions;
}

function handleChangeRole(value, option) {
  for (const key in checkedKeys.value) {
    checkedKeys.value[key] = []
  }

  const foundRole = roles.value.find((role) => role.id === value);
  foundRole.permissions.forEach((permission) => {
    const permissionSplit = permission.name.split('.');
    checkedKeys.value[permissionSplit[0]].push(`${permission.name}-${permission.id}`);
    originCheckedKeys.value[permissionSplit[0]].push(`${permission.name}-${permission.id}`);
  })
}

const onSubmit = handleSubmit((values) => {
  const addedPermissions = [];
  const deletedPermissions = [];

  for (const key in originCheckedKeys.value) {
    const deleted = originCheckedKeys.value[key].filter((item) => !checkedKeys.value[key].includes(item));
    const added = checkedKeys.value[key].filter((item) => !originCheckedKeys.value[key].includes(item));

    deleted.forEach((item) => {
      const splitItem = item.split('-');
      if (splitItem.length === 2) {
        deletedPermissions.push(splitItem[1]);
      }
    })

    added.forEach((item) => {
      const splitItem = item.split('-');
      if (splitItem.length === 2) {
        addedPermissions.push(splitItem[1]);
      }
    })
  }

  const body = {
    role: values.role,
    added_permissions: addedPermissions,
    deleted_permissions: deletedPermissions
  }
  console.log("body: ", body);

  router.post(route('permissions.updateRolePermissions'), body);
})

onMounted(() => {
  treeData.value = groupPermissions(permissions.value);
  expandedKeys.value = treeData.value.map(item => item.key);

  treeData.value.forEach(element => {
    checkedKeys.value[element.key] = [];
    originCheckedKeys.value[element.key] = [];
  });

  roleOptions.value = roles.value.map((role) => ({
    value: role.id,
    label: role.name
  }))

  resetForm({ values: { role: roleOptions.value[0].value } })
  handleChangeRole(role.value);
})
</script>
