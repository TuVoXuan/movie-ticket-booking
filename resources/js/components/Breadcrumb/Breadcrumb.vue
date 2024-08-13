<template>
  <div :class="class">
    <a-breadcrumb>
      <a-breadcrumb-item class="flex items-center">
        <Icon name="home_outline" class="h-5 w-5" />
      </a-breadcrumb-item>
      <a-breadcrumb-item v-for="item in breadcrumbItems">
        <template v-if="item.href">
          <Link :href="item.href">{{ item.label }}</Link>
        </template>
        <template v-else>
          {{ item.label }}
        </template>
      </a-breadcrumb-item>
    </a-breadcrumb>
  </div>
</template>

<script>
import { Breadcrumb, BreadcrumbItem } from 'ant-design-vue';
export default {
  name: 'Breadcrumb',
  props: ['breadcrumbItems', 'class'],
  components: {
    Breadcrumb, BreadcrumbItem
  }
}
</script>