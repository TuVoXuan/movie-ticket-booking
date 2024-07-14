<template>
  <div class="flex flex-col gap-y-3">
    <SidebarItem v-for="routeItem in routers" :href="routeItem.href" :title="routeItem.title"
      :activeIcon="routeItem.activeIcon" :inactiveIcon="routeItem.inactiveIcon" />
  </div>
</template>

<script>
import SidebarItem from './SidebarItem.vue';
export default {
  name: "Sidebar",
  components: {
    SidebarItem,
  },
  data() {
    return {
      routers: [
        {
          href: 'dashboard',
          activeIcon: 'chart_solid',
          inactiveIcon: 'chart_outline',
          title: 'Dashboard',
        },
        {
          href: 'films',
          activeIcon: 'film_solid',
          inactiveIcon: 'film_outline',
          title: 'Films',
        },
        {
          href: 'artists',
          activeIcon: 'artist_solid',
          inactiveIcon: 'artist_outline',
          title: 'Artist',
        },
        {
          href: 'genres',
          activeIcon: 'category_solid',
          inactiveIcon: 'category_outline',
          title: 'Genres',
        },
        {
          href: 'cinemas',
          activeIcon: 'building_solid',
          inactiveIcon: 'building_outline',
          title: 'Cinemas',
        },
        {
          href: 'roles',
          activeIcon: 'role_solid',
          inactiveIcon: 'role_outline',
          title: 'Roles',
        },
        {
          href: 'permissions',
          activeIcon: 'permission_solid',
          inactiveIcon: 'permission_outline',
          title: 'Permissions',
        },
        {
          href: 'users',
          activeIcon: 'user_solid',
          inactiveIcon: 'user_outline',
          title: 'Users',
        },
      ]
    }
  },
  methods: {
    isActiveRoute(routeName) {
      return route().current(routeName);
    }
  }
}
</script>

<style lang="scss" scoped></style>