<template>
  <Link :href="route(href)"
    class='flex items-center gap-x-2 px-3 py-2 rounded-md cursor-pointer transition-all ease-linear' :class="{
      'bg-[#5E5FEF] text-white': isActive,
      'bg-white text-black hover:bg-[#5E5FEF]/20': !isActive
    }">
  <div class="h-6 w-6">
    <Icon :name="isActive ? activeIcon : inactiveIcon" />
  </div>

  <span :class="{ 'font-medium': isActive }">{{ title }}</span>
  </Link>
</template>

<script>
import { router } from '@inertiajs/vue3'
export default {
  name: "SidebarItem",
  data() {
    return {
      isActive: route().current(this.href)
    }
  },
  props: {
    href: {
      type: String,
      required: true
    },
    title: {
      type: String,
      required: true
    },
    activeIcon: {
      type: String,
      required: true
    },
    inactiveIcon: {
      type: String,
      required: true
    }
  },
  mounted() {
    router.on('finish', (event) => {
      const hrefSplit = this.href.split('.');
      this.isActive = route().current(hrefSplit[0] + '.*');
    })
  },
}
</script>

<style lang="scss" scoped></style>