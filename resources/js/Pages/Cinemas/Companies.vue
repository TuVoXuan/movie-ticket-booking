<template>
  <div>
    <Box>
      <div class="flex justify-end gap-x-3">
        <a-button type="primary" @click="handleOpenModal">Add New Cinema</a-button>
      </div>
    </Box>
    <a-modal v-model:open="isOpen" title="Create Cinema" :footer="null" @cancel="onCloseModal">
      <CinemaCompany ref="CinemaCompanyForm" />
    </a-modal>
  </div>
</template>

<script>
import { Button, Modal } from 'ant-design-vue';
import CinemaCompany from '../../components/Modal/CinemaCompany.vue';
export default {
  name: 'CinemaCompaniesPage',
  components: {
    Button,
    CinemaCompany,
    Modal
  },
  data() {
    return {
      isOpen: false
    }
  },
  methods: {
    handleOpenModal() {
      this.isOpen = true;
    },
    onCloseModal() {
      this.$refs.CinemaCompanyForm.handleResetForm();
    }
  }
}
</script>

<style lang="scss" scoped></style>