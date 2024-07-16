<template>
  <div class="m-0 flex h-screen overflow-hidden">
    <div class="w-[200px] shrink-0 h-screen bg-white overflow-y-auto px-3">
      <div class="py-1 h-12 mb-10">
        <a href="/">
          <img class="object-contain h-full mx-auto" src="../../../public/assets/images/logo.png" alt="logo">
        </a>
      </div>

      <Sidebar />
    </div>
    <div class="min-h-screen w-[calc(100%-200px)]">
      <Topbar />
      <div id="main-section" class="shadow-custom px-8 py-4 bg-[#FAFBFC] h-[calc(100%-48px)] overflow-y-auto">
        <div>
          <slot />
        </div>
      </div>

    </div>
  </div>
  <Toast />
  <ConfirmDialog></ConfirmDialog>
</template>

<script>
import Sidebar from '../components/Sidebar/Sidebar.vue'
import Topbar from '../components/Topbar/Topbar.vue';
import { router } from '@inertiajs/vue3';
import Toast from 'primevue/toast';
import ConfirmDialog from 'primevue/confirmdialog';

export default {
  name: "PageLayout",
  components: {
    Sidebar,
    Topbar,
    Toast,
    ConfirmDialog
  },
  mounted() {
    router.on('finish', (event) => {
      if (this.$page.props?.success) {
        this.$toast.add({ severity: "success", summary: "Success", detail: this.$page.props.success, life: 3000 })
      }
      if (this.$page.props?.error) {
        this.$toast.add({ severity: "error", summary: "Error", detail: this.$page.props.error, life: 3000 })
      }
    })
  },
  unmounted() {
    router.on('finish', (event) => {

    })
  }

}
</script>

<style lang="scss" scoped></style>