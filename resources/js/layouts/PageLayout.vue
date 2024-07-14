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
</template>

<script>
import Sidebar from '../components/Sidebar/Sidebar.vue'
import Topbar from '../components/Topbar/Topbar.vue';
export default {
  name: "PageLayout",
  components: {
    Sidebar,
    Topbar
  }
}
</script>

<style lang="scss" scoped></style>