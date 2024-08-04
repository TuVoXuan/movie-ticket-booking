<template>
  <Box>
    <h1 class="text-2xl font-medium text-center mb-4">Create New Auditorium</h1>
    <a-form id="auditoriumForm" layout="vertical" @submit.prevent="onSubmit" class="grid grid-cols-2 gap-3 mb-4">
      <a-form-item class="mb-0" label="Name" v-bind="nameProps">
        <a-input size="large" v-model:value="name" />
      </a-form-item>
      <a-form-item class="mb-0" label="Capacity" v-bind="capacityProps">
        <a-input-number class="w-full" size="large" v-model:value="capacity" :min="1" />
      </a-form-item>
      <a-form-item class="mb-0" label="Seat direction" v-bind="seatDirectionProps">
        <a-select :disabled="isSubmitting" size="large" v-model:value="seatDirection"
          placeholder="Select seat direction" :options="seatDirectionOptions">
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Rows" v-bind="rowsProps">
        <a-input-number class="w-full" size="large" v-model:value="rows" :min="1" />
      </a-form-item>
      <a-form-item class="mb-0" label="Columns" v-bind="columnsProps">
        <a-input-number class="w-full" size="large" v-model:value="columns" :min="1" />
      </a-form-item>
    </a-form>

    <div v-show="isEmpty(errors) && columns && rows && capacity && seatDirection">
      <h2 class="text-xl font-medium text-center mb-4">Auditorium chair layout</h2>
      <auditorium-layout ref="auditoriumLayout" :rows="rows" :columns="columns" :seat-direction="seatDirection"
        :capacity="capacity" />
    </div>

    <a-button class="col-start-1 w-fit" form="auditoriumForm" type="primary" html-type="submit">Save</a-button>
  </Box>
</template>

<script setup>
import { Button, Input, Select, Form, FormItem, InputNumber, SelectOption } from 'ant-design-vue';
import * as yup from 'yup';
import { useForm } from 'vee-validate';
import { router } from '@inertiajs/vue3';
import { ref, } from 'vue';
import AuditoriumLayout from '../../../components/AuditoriumLayout/AuditoriumLayout.vue';
import { isEmpty } from 'lodash';

const auditoriumLayout = ref(null);

const seatDirectionOptions = ref([
  {
    label: 'Trái sang phải',
    value: 'LRT'
  },
  {
    label: 'Phải sang trái',
    value: 'RTL'
  }
]);
const isSubmitting = ref(false);

const schema = yup.object().shape({
  name: yup.string().min(1).required(),
  capacity: yup.number().min(0).required()
    .when(['rows', 'columns'], ([rows, columns], schema) => {
      if (rows && columns) {
        const max = rows * columns;
        return yup.number().max(max).required();
      }
      return yup.number().nullable();
    }),
  seatDirection: yup.string().required(),
  rows: yup.number().min(1).max(26).required(),
  columns: yup.number().required().when(['capacity', 'rows'], ([capacity, rows], schema) => {
    if (capacity && rows) {
      const maxColumns = capacity / rows + 2;
      return yup.number().max(maxColumns).required();
    }
    return yup.number().required();
  })
},
  [
    ['capacity', 'columns']
  ]
)

const { defineField, handleSubmit, errors } = useForm({
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
const [capacity, capacityProps] = defineField('capacity', antConfig);
const [seatDirection, seatDirectionProps] = defineField('seatDirection', antConfig);
const [rows, rowsProps] = defineField('rows', antConfig);
const [columns, columnsProps] = defineField('columns', antConfig);

const onSubmit = handleSubmit((values) => {
  const body = {
    name: values.name,
    capacity: values.capacity,
    seat_direction: values.seatDirection,
    rows: values.rows,
    columns: values.columns,
    grid_layout: auditoriumLayout.value.gridLayout
  }

  router.post(route('cinemas.branches.auditoria.store', route().params), body);
})


</script>

<style lang="scss" scoped></style>