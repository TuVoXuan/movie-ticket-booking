<template>
  <Box class="px-10 py-8">
    <h1 class="text-2xl text-center font-medium mb-4">Create New Film</h1>
    <a-form @submit.prevent="onSubmit" layout="vertical" class="grid grid-cols-2 gap-x-6 gap-y-3">
      <a-form-item class="mb-0" label="Title" v-bind="titleProps">
        <a-input size="large" v-model:value="title" placeholder="Enter title" />
      </a-form-item>
      <a-form-item class="mb-0" label="Release Date" v-bind="releaseDateProps">
        <a-date-picker size="large" class="w-full" v-model:value="releaseDate" format="DD/MM/YYYY" />
      </a-form-item>
      <a-form-item class="mb-0" label="Duration" v-bind="durationProps">
        <a-input-number size="large" class="w-full" v-model:value="duration" :min="0" placeholder="Enter duration" />
      </a-form-item>
      <a-form-item class="mb-0" label="Age restricted" v-bind="ageRestrictedProps">
        <a-input-number size="large" class="w-full" v-model:value="ageRestricted" :min="0"
          placeholder="Enter age restricted" />
      </a-form-item>
      <a-form-item class="mb-0" label="Trailer" v-bind="trailerProps">
        <a-input size="large" v-model:value="trailer" placeholder="Enter trailer link" />
      </a-form-item>
      <a-form-item class="mb-0" label="Directors" v-bind="directorsProps">
        <a-select size="large" label-in-value v-model:value="directors" mode="multiple" placeholder="Select directors"
          :options="artistsOptions.data" @search="handleSearch" :filter-option="false">
          <template v-if="artistsOptions.isFetching" #notFoundContent>
            <a-spin size="small" />
          </template>
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Producers" v-bind="producersProps">
        <a-select size="large" label-in-value v-model:value="producers" mode="multiple" placeholder="Select producers"
          :options="artistsOptions.data" @search="handleSearch" :filter-option="false">
          <template v-if="artistsOptions.isFetching" #notFoundContent>
            <a-spin size="small" />
          </template>
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Actors" v-bind="actorsProps">
        <a-select size="large" label-in-value v-model:value="actors" mode="multiple" placeholder="Select actors"
          :options="artistsOptions.data" @search="handleSearch" :filter-option="false">
          <template v-if="artistsOptions.isFetching" #notFoundContent>
            <a-spin size="small" />
          </template>
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Genres" v-bind="genresProps">
        <a-select size="large" label-in-value v-model:value="genres" mode="multiple" placeholder="Select genres"
          :options="genresOptions.data" :filter-option="filterOption"></a-select>
      </a-form-item>
      <a-form-item class="col-start-1" label="Thumbnail" v-bind="thumbnailProps">
        <Upload v-model:fileList="thumbnail" :url="thumbnailURL" />
      </a-form-item>
      <a-form-item label="Thumbnail Background" v-bind="thumbnailBgProps">
        <Upload v-model:fileList="thumbnailBg" :url="thumbnailBgURL" />
      </a-form-item>
      <a-form-item class="col-span-2 mb-0" label="Description" v-bind="descriptionProps">
        <a-textarea size="large" v-model:value="description" :rows="8" placeholder="Enter description" />
      </a-form-item>
      <a-button type="primary" htmlType="submit" class="w-fit">Save</a-button>
    </a-form>
  </Box>
</template>

<script setup>
import Upload from '../../components/Upload/Upload.vue';
import { PlusOutlined, LoadingOutlined } from '@ant-design/icons-vue';
import { Input, Textarea, DatePicker, InputNumber, Select, Form, FormItem, Spin } from 'ant-design-vue';
import { useForm } from 'vee-validate';
import * as yup from 'yup';
import { reactive, onMounted, defineProps, toRefs } from 'vue';
import axios from 'axios';
import { debounce } from 'lodash';
import { router } from '@inertiajs/vue3';
import dayjs from 'dayjs';
import { getRemovedItems, getNewItems } from '../../utils/utils'

const props = defineProps(['film']);
const { film } = toRefs(props);
const thumbnailURL = film.value?.thumbnail.url;
const thumbnailBgURL = film.value?.thumbnail_bg.url;

const schema = yup.object().shape({
  releaseDate: yup.date().nullable(),
  duration: yup.number().nullable(),
  ageRestricted: yup.number().nullable(),
  trailer: yup.string().nullable(),
  title: yup.string().nullable(),
  description: yup.string().nullable(),
  directors: yup.array().nullable(),
  producers: yup.array().nullable(),
  actors: yup.array().nullable(),
  genres: yup.array().nullable(),
  thumbnail: yup.array().required(),
  thumbnailBg: yup.array().required()
});

const { defineField, handleSubmit, resetForm, errors } = useForm({
  validationSchema: schema,
  initialValues: {
    releaseDate: dayjs(film.value?.release_date),
    duration: film.value?.duration,
    ageRestricted: film.value?.age_restricted,
    trailer: film.value?.trailer,
    title: film.value?.title,
    description: film.value?.description,
    genres: film.value?.genres.map((item) => ({ value: item.id, label: item.name })),
    directors: film.value?.directors.map((item) => ({ value: item.id, label: item.name })),
    producers: film.value?.producers.map((item) => ({ value: item.id, label: item.name })),
    actors: film.value?.actors.map((item) => ({ value: item.id, label: item.name })),
    thumbnail: [{ uid: film.value?.thumbnail.id, url: film.value?.thumbnail.url, }],
    thumbnailBg: [{ uid: film.value?.thumbnail_bg.id, url: film.value?.thumbnail_bg.url }],
  }
});

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const [releaseDate, releaseDateProps] = defineField('releaseDate', antConfig);
const [duration, durationProps] = defineField('duration', antConfig);
const [ageRestricted, ageRestrictedProps] = defineField('ageRestricted', antConfig);
const [trailer, trailerProps] = defineField('trailer', antConfig);
const [title, titleProps] = defineField('title', antConfig);
const [description, descriptionProps] = defineField('description', antConfig);
const [directors, directorsProps] = defineField('directors', antConfig);
const [producers, producersProps] = defineField('producers', antConfig);
const [actors, actorsProps] = defineField('actors', antConfig);
const [genres, genresProps] = defineField('genres', antConfig);
const [thumbnail, thumbnailProps] = defineField('thumbnail', antConfig);
const [thumbnailBg, thumbnailBgProps] = defineField('thumbnailBg', antConfig);

const artistsOptions = reactive({
  data: [],
  isFetching: false
});

const genresOptions = reactive({
  data: []
});

const getDeletedItems = (filmItems, valueItems) => {
  return getRemovedItems(
    filmItems.map(item => item.id),
    valueItems.map(item => item.value)
  );
};

const getAddNewItems = (filmItems, valueItems) => {
  return getNewItems(
    filmItems.map(item => item.id),
    valueItems.map(item => item.value)
  )
}

const onSubmit = handleSubmit((values) => {
  console.log("values: ", values);
  const formData = new FormData();
  formData.append('title', values.title);
  formData.append('release_date', values.releaseDate.toISOString());
  formData.append('duration', values.duration);
  formData.append('age_restricted', values.ageRestricted);
  formData.append('trailer', values.trailer);
  formData.append('description', values.description);

  if (film.value) {
    const addNewDirectors = getAddNewItems(film.value.directors, values.directors);
    console.log("addNewDirectors: ", addNewDirectors);
    const addNewProducers = getAddNewItems(film.value.producers, values.producers);
    console.log("addNewProducers: ", addNewProducers);
    const addNewActors = getAddNewItems(film.value.actors, values.actors);
    console.log("addNewActors: ", addNewActors);
    const addNewGenres = getAddNewItems(film.value.genres, values.genres);
    console.log("addNewGenres: ", addNewGenres);

    const deletedDirectors = getDeletedItems(film.value.directors, values.directors);
    const deletedProducers = getDeletedItems(film.value.producers, values.producers);
    const deletedActors = getDeletedItems(film.value.actors, values.actors);
    const deletedGenres = getDeletedItems(film.value.genres, values.genres);

    const arrayFields = [
      {
        key: 'directors',
        value: addNewDirectors
      },
      {
        key: 'producers',
        value: addNewProducers
      },
      {
        key: 'actors',
        value: addNewActors
      },
      {
        key: 'genres',
        value: addNewGenres
      },
      {
        key: 'deleted_directors',
        value: deletedDirectors
      },
      {
        key: 'deleted_producers',
        value: deletedProducers
      },
      {
        key: 'deleted_actors',
        value: deletedActors
      },
      {
        key: 'deleted_genres',
        value: deletedGenres
      }
    ]

    arrayFields.forEach(field => {
      field.value.forEach(id => {
        formData.append(`${field.key}[]`, id);
      });
    });

    if (!values.thumbnail[0].url) {
      formData.append('thumbnail', values.thumbnail[0].originFileObj);
    }

    if (!values.thumbnailBg[0].url) {
      formData.append('thumbnail_bg', values.thumbnailBg[0].originFileObj);
    }

    router.post(route('films.update', {
      film: film.value.id,
      _query: {
        _method: 'PUT'
      }
    }), formData);
  } else {
    formData.append('thumbnail', values.thumbnail[0].originFileObj);
    formData.append('thumbnail_bg', values.thumbnailBg[0].originFileObj);

    const arrayFields = ['directors', 'producers', 'actors', 'genres'];
    arrayFields.forEach(field => {
      for (const item of values[field]) {
        formData.append(`${field}[]`, item.value)
      }
    });

    router.post(route('films.store'), formData);
  }
});

async function handleGetArtists(search) {
  try {
    artistsOptions.isFetching = true;
    const response = await axios.get(route('apiArtists.index'), {
      params: {
        search
      }
    });
    artistsOptions.data = response.data.data.data.map((item) => ({ value: item.id, label: item.name }));

    setTimeout(() => {
      artistsOptions.isFetching = false;
    }, 500)
  } catch (error) {
    console.log("error: ", error);
  }
}

const handleSearch = debounce((search) => {
  handleGetArtists(search)
}, 500)

async function handleGetGenres() {
  try {
    const response = await axios.get(route('apiGenres.index'));
    genresOptions.data = response.data.data.map((item) => ({ value: item.id, label: item.name }));
  } catch (error) {
    console.log("error: ", error);
  }
}

const filterOption = (input, option) => {
  return option.label.toLowerCase().indexOf(input.toLowerCase()) >= 0;
};

onMounted(() => {
  handleGetArtists();
  handleGetGenres();
})
</script>

<style lang="scss" scoped></style>