<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />
  <Box class="px-5">
    <h1 class="text-2xl text-center font-medium mb-4">Create New Screening</h1>

    <a-form layout="vertical" @submit.prevent="onSubmit" class="grid grid-cols-2 gap-3">
      <a-form-item class="mb-0" label="Film" v-bind="filmProps">
        <a-select :disabled="isSubmitting" size="large" label-in-value v-model:value="film" show-search
          placeholder="Select film" :options="filmsOptions.data" @search="handleSearchFilm" :filter-option="false">
          <template v-if="filmsOptions.isFetching" #notFoundContent>
            <a-spin size="small" />
          </template>
          <template #option="{ value: val, label, thumbnail }">
            <div class="flex items-center gap-x-3">
              <div class="shrink-0">
                <img :src="thumbnail" class="h-12 w-auto" />
              </div>
              <span>{{ label }}</span>
            </div>
          </template>
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Auditorium" v-bind="auditoriumProps">
        <a-select :disabled="isSubmitting" size="large" label-in-value v-model:value="auditorium" show-search
          placeholder="Select auditorium" :options="auditoriaOptions.data" :filter-option="false"
          @change="onChangeAuditorium">
          <template v-if="auditoriaOptions.isFetching" #notFoundContent>
            <a-spin size="small" />
          </template>
        </a-select>
      </a-form-item>
      <a-form-item class="mb-0" label="Screening time" v-bind="screeningTimeProps">
        <a-date-picker :disabled="isSubmitting" class="w-full" size="large" :show-time="{ format: 'HH:mm' }"
          placeholder="Select screening time" v-model:value="screeningTime" format="HH:mm DD/MM/YYYY"
          :disabled-date="disabledDate" />
      </a-form-item>
      <a-form-item class="mb-0" label="Film translation" v-bind="filmTranslationProps">
        <a-select :disabled="isSubmitting" size="large" label-in-value v-model:value="filmTranslation"
          placeholder="Select film translation" :options="filmTranslationOptions" :filter-option="false">
        </a-select>
      </a-form-item>
      <a-form-item v-if="showSeatPriceInput(CellType.SeatNormal)" class="mb-0" label="Normal seat price"
        v-bind="normalSeatPriceProps">
        <a-input-number class="w-full" :disabled="isSubmitting" size="large" v-model:value="normalSeatPrice"
          placeholder="Enter normal seat price">
        </a-input-number>
      </a-form-item>
      <a-form-item v-if="showSeatPriceInput(CellType.SeatVIP)" class="mb-0" label="VIP seat price"
        v-bind="vipSeatPriceProps">
        <a-input-number class="w-full" :disabled="isSubmitting" size="large" v-model:value="vipSeatPrice"
          placeholder="Enter VIP seat price">
        </a-input-number>
      </a-form-item>

      <a-button :loading="isSubmitting" type="primary" html-type="submit" class="w-fit col-start-1">Save</a-button>
    </a-form>
  </Box>
</template>

<script setup>
import Breadcrumb from '../../../components/Breadcrumb/Breadcrumb.vue';
import { Button, Form, FormItem, Select, DatePicker, InputNumber } from 'ant-design-vue';
import * as yup from 'yup';
import { useForm } from 'vee-validate';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { debounce } from 'lodash';
import { ref, reactive, onMounted, defineProps, toRefs } from 'vue';
import dayjs from 'dayjs';
import { CellType } from '../../../constant/enum';

const props = defineProps(['screening', 'cinemaBranchName']);
const { screening, cinemaBranchName } = toRefs(props);

const breadcrumbItems = ref([
  {
    label: 'Cinemas',
    href: null
  },
  {
    label: 'Branches',
    href: route('cinemas.branches.index')
  },
  {
    label: cinemaBranchName.value,
    href: route('cinemas.branches.edit', { branch: route().params.branch })
  },
  {
    label: 'Showtimes',
    href: route('cinemas.branches.showtimes.index', { branch: route().params.branch })
  },
  {
    label: screening.value ? 'Edit' : 'Create New',
    href: null
  }
]);

const filmTranslationOptions = ref([
  {
    value: 'voiceover',
    label: 'Voiceover'
  },
  {
    value: 'vietsub',
    label: 'Vietsub'
  }
])
const isSubmitting = ref(false);
const seatTypes = ref(null);

const schema = yup.object().shape({
  film: yup.object().required(),
  auditorium: yup.object().required(),
  screeningTime: yup.date().required(),
  filmTranslation: yup.object().required(),
  normalSeatPrice: yup.number().nullable(),
  vipSeatPrice: yup.number().nullable()
});

const antConfig = (state) => ({
  props: {
    hasFeedback: !!state.errors[0],
    help: state.errors[0],
    validateStatus: state.errors[0] ? 'error' : undefined,
  },
});

const { defineField, handleSubmit, errors, resetForm } = useForm({
  validationSchema: schema,
  initialValues: {
    film: screening.value?.film && {
      value: screening.value.film.id,
      label: screening.value.film.title,
      code: screening.value.film.code,
      thumbnail: screening.value.film.thumbnail.url
    },
    auditorium: screening.value?.auditorium && {
      label: screening.value.auditorium.name,
      value: screening.value.auditorium.id
    },
    screeningTime: screening.value?.screening_time && dayjs(screening.value.screening_time),
    filmTranslation: screening.value?.film_translation &&
      filmTranslationOptions.value.find((option) => option.value === screening.value?.film_translation),
    normalSeatPrice: screening.value?.ticket_prices && screening.value.ticket_prices.find((item) => item.seat_type === CellType.SeatNormal)?.price,
    vipSeatPrice: screening.value?.ticket_prices && screening.value.ticket_prices.find((item) => item.seat_type === CellType.SeatVIP)?.price
  }
});

const [film, filmProps] = defineField('film', antConfig);
const [auditorium, auditoriumProps] = defineField('auditorium', antConfig);
const [screeningTime, screeningTimeProps] = defineField('screeningTime', antConfig);
const [filmTranslation, filmTranslationProps] = defineField('filmTranslation', antConfig);
const [normalSeatPrice, normalSeatPriceProps] = defineField('normalSeatPrice', antConfig);
const [vipSeatPrice, vipSeatPriceProps] = defineField('vipSeatPrice', antConfig);

const onSubmit = handleSubmit((values) => {
  isSubmitting.value = true;
  const body = {
    film: values.film.value,
    auditorium: values.auditorium.value,
    film_translation: values.filmTranslation.value,
    screening_time: values.screeningTime.format('YYYY-MM-DD HH:mm'),
    normal_seat_price: showSeatPriceInput(CellType.SeatNormal) ? values.normalSeatPrice : undefined,
    vip_seat_price: showSeatPriceInput(CellType.SeatVIP) ? values.vipSeatPrice : undefined
  }
  const branch = route().params.branch;
  if (screening.value) {
    router.put(route('cinemas.branches.showtimes.update', { branch: branch, showtime: screening.value.id }), body);
  } else {
    router.post(route('cinemas.branches.showtimes.store', { branch }), body);
  }
})

const filmsOptions = reactive({
  data: [],
  isFetching: false
});

async function handleGetFilms(search) {
  try {
    filmsOptions.isFetching = true;
    const response = await axios.get(route('apiFilms.options'), {
      params: {
        search
      }
    });
    filmsOptions.data = response.data.data.data.map((item) => (
      {
        value: item.id,
        label: item.title,
        code: item.code,
        thumbnail: item.thumbnail.url
      }));

    setTimeout(() => {
      filmsOptions.isFetching = false;
    }, 500)
  } catch (error) {
    console.log("error: ", error);
  }
}

const handleSearchFilm = debounce((search) => {
  handleGetFilms(search)
}, 500)

const auditoriaOptions = reactive({
  data: [],
  isFetching: false
});

async function handleGetAuditoria() {
  try {
    auditoriaOptions.isFetching = true;
    const response = await axios.get(route('apiCinemas.branches.auditoria.getAll', {
      branch: route().params.branch
    }));
    auditoriaOptions.data = response.data.data.map((item) => (
      {
        value: item.id,
        label: item.name,
      }));

    setTimeout(() => {
      auditoriaOptions.isFetching = false;
    }, 500)

  } catch (error) {
    console.log("error: ", error);
  }
}

async function handleGetSeatType(auditorium) {
  try {
    if (auditorium) {
      const response = await axios.get(route('apiAuditoria.getSeatTypes', auditorium));
      seatTypes.value = response.data.data;
    }
  } catch (error) {
    console.log("error: ", error);
  }
}

function onChangeAuditorium(value, option) {
  handleGetSeatType(value.value);
}

const disabledDate = (current) => {
  // Can not select days before today and today
  return current < dayjs().subtract(1, 'day');
};

const showSeatPriceInput = (seatType) => {
  return (seatTypes.value && seatTypes.value.find((item) => item.seat_type === seatType))
  // || (screening.value?.ticket_prices && screening.value.ticket_prices.find((item) => item.seat_type === seatType))
}

onMounted(() => {
  handleGetFilms();
  handleGetAuditoria();
  if (screening.value) {
    handleGetSeatType(screening.value.auditorium_id);
  }
})

</script>