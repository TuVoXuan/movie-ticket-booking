<template>
  <div class="bg-white h-12 flex justify-between items-center p-4">
    <h1 class="font-bold text-2xl">
      <!-- {{ $getTitle }} -->
    </h1>

    <a-popover placement="bottomRight">
      <template #content>
        <a-button type="text" class="flex items-center gap-x-3" @click="handleLogout">
          <Icon name="logout_outline" class="h-5 w-5" />
          <span>Logout</span>
        </a-button>
      </template>
      <span v-if="$page.props.user">Hi, {{ $page.props.user.name }} !</span>
    </a-popover>
  </div>
</template>

<script>
import { Popover, Button } from 'ant-design-vue';
import { router } from '@inertiajs/vue3';
export default {
  name: "Topbar",
  components: {
    Popover, Button
  },
  methods: {
    handleLogout() {
      router.post(route('auth.logout'));
    }
  }
}
</script>

<style lang="scss" scoped></style>