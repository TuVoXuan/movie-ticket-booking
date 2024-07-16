<template>
  <Box>
    <h1 class="text-2xl font-medium text-center mb-4">{{ artist ? 'Update' : 'Create' }} New Artist</h1>
    <form @submit.prevent="onSubmit" :validation-schema="schema" class="grid grid-cols-2 gap-3">
      <div>
        <label class="block mb-2" for="name">Name</label>
        <InputText class="w-full" name="name" placeholder="Enter name" v-model="name" :invalid="errors.name" />
        <small id="name-help" class="text-red-500">{{ errors.name }}</small>
      </div>
      <div>
        <label class="block mb-2" for="birthday">Birthday</label>
        <DatePicker name="birthday" v-model="birthday" placeholder="Select date" dateFormat="dd/mm/yy" showIcon fluid
          :invalid="errors.birthday" />
        <small id="name-help" class="text-red-500">{{ errors.birthday }}</small>
      </div>
      <div class="col-span-2">
        <label class="block mb-2" for="biography">Biography</label>
        <Textarea class="w-full" name="biography" placeholder="Enter biography" rows="8" v-model="biography"
          :invalid="errors.biography" />
        <small id="name-help" class="text-red-500">{{ errors.biography }}</small>
      </div>

      <Button class="col-start-1 w-fit" type="submit">{{ artist ? 'Update' : 'Create' }}</Button>
    </form>
  </Box>
</template>

<script setup>
import InputText from 'primevue/inputtext';
import Textarea from 'primevue/textarea';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import * as yup from 'yup'
import { useForm } from 'vee-validate';
import { router } from '@inertiajs/vue3';
import { toRefs, ref } from 'vue'

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
    birthday: artist.value?.birthday ? new Date(artist.value.birthday) : null
  }
})

const [name] = defineField('name');
const [biography] = defineField('biography');
const [birthday] = defineField('birthday');

const onSubmit = handleSubmit((values) => {
  if (artist.value) {
    router.put(route('artists.update', artist.value.id), values);
  } else {
    router.post(route('artists.store'), values);
  }
})

const date = ref()
</script>
