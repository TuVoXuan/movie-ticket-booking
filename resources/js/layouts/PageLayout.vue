<template>
  <div class="m-0 flex h-screen overflow-hidden">
    <div class="w-[200px] shrink-0 h-screen bg-white overflow-y-auto px-3">
      <div class="py-1 h-12 mb-3 flex items-center justify-center">
        <Link :href="route('dashboard')" class="font-bold text-2xl text-blue-400">
        Cineverse
        </Link>
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
</template>

<script>
import Sidebar from '../components/Sidebar/Sidebar.vue'
import Topbar from '../components/Topbar/Topbar.vue';
import { router } from '@inertiajs/vue3';
import { notification } from 'ant-design-vue';

export default {
  name: "PageLayout",
  components: {
    Topbar,
    Sidebar
  },
  mounted() {
    router.on('finish', (event) => {
      if (this.$page.props?.success) {
        notification['success']({
          message: 'Success',
          description: this.$page.props.success,
        });
      }
      if (this.$page.props?.error) {
        notification['error']({
          message: 'Error',
          description: this.$page.props.error,
        });
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