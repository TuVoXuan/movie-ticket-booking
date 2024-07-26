<template>
  <a-upload :disabled="disabled" :file-list="fileList" :max-count="1" list-type="picture-card" :show-upload-list="false"
    :before-upload="handleBeforeUpload" accept="image/*">
    <img v-if="fileURL" :src="fileURL" class="h-full object-contain" alt="image" />
    <div v-else>
      <plus-outlined></plus-outlined>
      <div>Upload</div>
    </div>
  </a-upload>
</template>

<script setup>
import { Upload } from 'ant-design-vue';
import { PlusOutlined } from '@ant-design/icons-vue';
import { ref, defineExpose } from 'vue';

const props = defineProps(['url', 'disabled']);

const fileList = ref([]);
const fileURL = ref(props.url || '');
const error = ref('');

const handleBeforeUpload = (file) => {
  fileURL.value = URL.createObjectURL(file);
  fileList.value = [file];
  return false;
}

defineExpose({
  fileList, fileURL
})

</script>

<style lang="scss" scoped></style>