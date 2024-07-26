<template>
  <div class="relative z-[1]">
    <div class="absolute top-0 left-0 right-0 bottom-0 z-[-1] overflow-hidden bg-cover bg-center bg-no-repeat"
      :style="{ backgroundImage: 'url(' + film.thumbnail_bg.url + ')' }">
    </div>
    <div class="grid grid-cols-12 px-10 py-4 gap-3 bg-black/70">
      <div class="col-span-2">
        <div class="border-white border-[1px] rounded-md overflow-hidden">
          <img :src="film.thumbnail.url" class="h-auto max-w-full " />
        </div>
      </div>
      <div class="col-span-10 text-white">
        <h2 class="text-xl font-medium">{{ film.title }}</h2>
        <p class="text-gray-400 font-medium">{{ film.genres.map((genre) => genre.name).join(', ') }}</p>
        <div class="grid grid-cols-12 gap-3">
          <div class="col-span-7">
            <p>{{ film.description }}</p>
            <div class="grid grid-cols-3">
              <div>
                <div class="flex items-center gap-x-2 mb-2">
                  <Icon name="calendar_outline" class="shrink-0 h-4 w-4" />
                  <p class="font-semibold mb-0">Khởi chiếu</p>
                </div>
                <p>{{ dayjs(film.release_date).format('DD/MM/YYYY') }}</p>
              </div>
              <div>
                <div class="flex items-center gap-x-2 mb-2">
                  <Icon name="alarm_outline" class="shrink-0 h-4 w-4" />
                  <p class="font-semibold mb-0">Thời lượng</p>
                </div>
                <p>{{ film.duration }} phút</p>
              </div>
              <div>
                <div class="flex items-center gap-x-2 mb-2">
                  <Icon name="children_outline" class="shrink-0 h-4 w-4" />
                  <p class="font-semibold mb-0">Giới hạn tuổi</p>
                </div>
                <p>T{{ film.age_restricted }}</p>
              </div>
            </div>
          </div>
          <div class="col-span-5">
            <div class="mb-3">
              <h3>Diễn viên</h3>
              <span class="text-red-500" v-for="(actor, index) in film.actors" :key="index">
                {{ actor.name }}<span class="text-white" v-if="index < film.actors.length - 1">, </span>
              </span>
            </div>
            <div class="mb-3">
              <h3>Đạo diễn</h3>
              <span class="text-red-500" v-for="(director, index) in film.directors" :key="index">
                {{ director.name }}<span class="text-white" v-if="index < film.directors.length - 1">, </span>
              </span>
            </div>
            <div>
              <h3>Nhà sản xuất</h3>
              <span class="text-red-500" v-for="(producer, index) in film.producers" :key="index">
                {{ producer.name }}<span class="text-white" v-if="index < film.producers.length - 1">, </span>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import dayjs from 'dayjs';

export default {
  name: 'ShowFilm',
  props: ['film'],
  data() {
    return {
      dayjs
    }
  }
}
</script>
