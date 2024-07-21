<template>
  <a-upload :file-list="fileList" list-type="picture-card" :show-upload-list="false" :before-upload="handleBeforeUpload"
    accept="image/*">
    <img v-if="fileURL" :src="fileURL" alt="image" />
    <div v-else>
      <plus-outlined></plus-outlined>
      <div>Upload</div>
    </div>
  </a-upload>
</template>

<script setup>
import { Upload } from 'ant-design-vue';
import { PlusOutlined, LoadingOutlined } from '@ant-design/icons-vue';
import { ref } from 'vue';

const fileList = ref([]);
const fileURL = ref('');
const error = ref('');

const handleBeforeUpload = (file) => {
  fileURL.value = URL.createObjectURL(file);
  fileList.value = [...(fileList.value), file];
  return false;
}

defineExpose({
  fileList
})

</script>

<style lang="scss" scoped></style>