<template>
  <div class="bg-white h-12 flex justify-between items-center p-4">
    <div class="hidden lg:block">
      <div class="border-[1px] border-gray-300 p-1 rounded-md" @click="toggleCollapseMenu">
        <Icon name="menu_outline" class="h-5 w-5" />
      </div>
    </div>

    <div class="lg:hidden flex gap-x-3 items-center">
      <div class="border-[1px] border-gray-300 p-1 rounded-md" @click="showDrawer">
        <Icon name="menu_outline" class="h-5 w-5" />
      </div>

      <div class="py-1 h-12 flex items-center justify-center">
        <Link :href="route('dashboard')" class="font-bold text-2xl text-blue-400">
        Cineverse
        </Link>
      </div>
    </div>

    <a-popover placement="bottomRight">
      <template #content>
        <a-button type="text" class="flex items-center gap-x-3" @click="handleLogout">
          <Icon name="logout_outline" class="h-5 w-5" />
          <span>Logout</span>
        </a-button>
      </template>
      <span v-if="$page.props.curUser">Hi, {{ $page.props.curUser.name }} !</span>
    </a-popover>

    <a-drawer width="250px" placement="left" :closable="false" :open="openDrawer" @close="onCloseDrawer">
      <div class="py-1 h-12 flex items-center justify-center">
        <Link :href="route('dashboard')" class="font-bold text-2xl text-blue-400">
        Cineverse
        </Link>
      </div>
      <Sidebar />
    </a-drawer>
  </div>
</template>

<script>
import Sidebar from '../Sidebar/Sidebar.vue';
import { Popover, Button, Drawer } from 'ant-design-vue';
import { router } from '@inertiajs/vue3';
export default {
  name: "Topbar",
  components: {
    Popover, Button, Drawer, Sidebar
  },
  data() {
    return {
      openDrawer: false,
    }
  },
  methods: {
    handleLogout() {
      router.post(route('auth.logout'));
    },
    onCloseDrawer() {
      this.openDrawer = false;
    },
    showDrawer() {
      this.openDrawer = true;
    },
    toggleCollapseMenu() {
      this.$emit('toggleCollapseMenu');
    }
  },
  mounted() {
    router.on('finish', (event) => {
      this.openDrawer = false;
    })
  }
}
</script>

<style lang="scss" scoped></style>