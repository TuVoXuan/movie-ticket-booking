<template>
  <Box class="px-10 py-8">
    <h1 class="text-2xl text-center font-medium mb-4">Create New Film</h1>
    <form class="grid grid-cols-2 gap-x-6 gap-y-3">
      <div>
        <label class="block mb-2" for="title">Title</label>
        <InputText class="w-full" name="title" placeholder="Enter age restricted" v-model="title"
          :invalid="!!errors.title" />
        <small id="title-help" class="text-red-500">{{ errors.title }}</small>
      </div>
      <div>
        <label class="block mb-2" for="release_date">Release Date</label>
        <DatePicker name="release_date" v-model="release_date" placeholder="Select date" dateFormat="dd/mm/yy" showIcon
          fluid :invalid="!!errors.release_date" />
        <small id="release_date-help" class="text-red-500">{{ errors.release_date }}</small>
      </div>
      <div>
        <label class="block mb-2" for="duration">Duration</label>
        <InputNumber name="duration" placeholder="Enter duration" v-model="duration" :invalid="!!errors.duration" fluid
          suffix=" minutes" />
        <small id="duration-help" class="text-red-500">{{ errors.duration }}</small>
      </div>
      <div>
        <label class="block mb-2" for="age_restricted">Age Restricted</label>
        <InputNumber name="age_restricted" placeholder="Enter age restricted" v-model="age_restricted"
          :invalid="!!errors.age_restricted" fluid />
        <small id="age_restricted-help" class="text-red-500">{{ errors.age_restricted }}</small>
      </div>
      <div>
        <label class="block mb-2" for="trailer">Trailer</label>
        <InputText class="w-full" name="trailer" placeholder="Enter age restricted" v-model="trailer"
          :invalid="!!errors.trailer" />
        <small id="trailer-help" class="text-red-500">{{ errors.trailer }}</small>
      </div>
      <div>
        <label class="block mb-2" for="trailer">Directors</label>
        <MultiSelect v-model="directors" display="chip" :options="[]" optionLabel="name" filter
          placeholder="Select directors" :maxSelectedLabels="3" fluid :invalid="!!errors.directors" />
        <small id="trailer-help" class="text-red-500">{{ errors.trailer }}</small>
      </div>
      <div>
        <label class="block mb-2" for="trailer">Producers</label>
        <MultiSelect v-model="producers" display="chip" :options="[]" optionLabel="name" filter
          placeholder="Select producers" :maxSelectedLabels="3" fluid :invalid="!!errors.producers" />
        <small id="trailer-help" class="text-red-500">{{ errors.trailer }}</small>
      </div>
      <div>
        <label class="block mb-2" for="trailer">Artists</label>
        <MultiSelect v-model="artists" display="chip" :options="[]" optionLabel="name" filter
          placeholder="Select artists" :maxSelectedLabels="3" fluid :invalid="!!errors.artists" />
        <small id="trailer-help" class="text-red-500">{{ errors.trailer }}</small>
      </div>
      <div>
        <label class="block mb-2" for="trailer">Genres</label>
        <MultiSelect v-model="genres" display="chip" :options="[]" optionLabel="name" filter placeholder="Select genres"
          :maxSelectedLabels="3" fluid :invalid="!!errors.genres" />
        <small id="trailer-help" class="text-red-500">{{ errors.trailer }}</small>
      </div>
      <div>
        <label class="block mb-2" for="thumbnail">Thumbnail</label>
        <FileUpload showCancelButton cancelIcon="pi pi-times" ref="fileupload" mode="basic" name="thumbnail[]"
          accept="image/*" :maxFileSize="1000000" />
        <small id="age_restricted-help" class="text-red-500">{{ errors.age_restricted }}</small>
      </div>
      <div>
        <label class="block mb-2" for="thumbnail">Thumbnail Background</label>
        <FileUpload ref="fileUpload" mode="basic" name="thumbnail_bg[]" accept="image/*" :maxFileSize="1000000" />
        <small id="age_restricted-help" class="text-red-500">{{ errors.age_restricted }}</small>
      </div>
      <div class="col-span-2">
        <label class="block mb-2" for="description">Description</label>
        <Textarea class="w-full" name="description" placeholder="Enter description" rows="8" v-model="description"
          :invalid="errors.description" />
        <small id="description-help" class="text-red-500">{{ errors.description }}</small>
      </div>
      <Button class="w-fit">Save</Button>
    </form>
    <input type="file" />
  </Box>
</template>

<script setup>
import FileUpload from 'primevue/fileupload';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import InputNumber from 'primevue/inputnumber';
import Textarea from 'primevue/textarea';
import MultiSelect from 'primevue/multiselect';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { ref } from 'vue';

const fileUpload = ref();

const schema = yup.object().shape({
  release_date: yup.date().required(),
  duration: yup.number().required(),
  age_restricted: yup.number().required(),
  trailer: yup.string().required(),
  thumbnail: yup.mixed().required(),
  thumbnail_bg: yup.mixed().required(),
  title: yup.string().required(),
  description: yup.string().required(),
  directors: yup.array().required(),
  producers: yup.array().required(),
  artists: yup.array().required(),
  genres: yup.array().required()
});

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema
});

const [release_date] = defineField('release_date');
const [duration] = defineField('duration');
const [age_restricted] = defineField('age_restricted');
const [trailer] = defineField('trailer');
const [thumbnail] = defineField('thumbnail');
const [thumbnail_bg] = defineField('thumbnail_bg');
const [title] = defineField('title');
const [description] = defineField('description');
const [directors] = defineField('directors');
const [producers] = defineField('producers');
const [artists] = defineField('artists');
const [genres] = defineField('genres');

</script>

<style lang="scss" scoped></style>