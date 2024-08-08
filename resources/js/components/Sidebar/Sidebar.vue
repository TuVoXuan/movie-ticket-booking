<template>
  <a-menu v-model:openKeys="openKeys" v-model:selectedKeys="selectedKeys" mode="inline" :items="items"
    style="border-inline-end: none" @select="handleSelectNav" />
</template>

<script>
import { Menu } from 'ant-design-vue';
import ChartOutline from '../icons/chart_outline.vue';
import FilmOutline from '../icons/film_outline.vue';
import ArtistOutline from '../icons/artist_outline.vue';
import CategoryOutline from '../icons/category_outline.vue';
import RoleOutline from '../icons/role_outline.vue';
import BuildingOutline from '../icons/building_outline.vue';
import PermissionOutline from '../icons/permission_outline.vue';
import UserOutline from '../icons/user_outline.vue';
import { h } from 'vue';
import { router } from '@inertiajs/vue3';

export default {
  name: "Sidebar",
  components: {
    Menu
  },
  data() {
    return {
      selectedKeys: [],
      openKeys: [],
      items: [
        {
          key: 'dashboard',
          icon: () => h(ChartOutline),
          label: 'Dashboard',
          title: 'Dashboard',
        },
        {
          key: 'films.index',
          icon: () => h(FilmOutline),
          label: 'Films',
          title: 'Films',
        },
        {
          key: 'artists.index',
          icon: () => h(ArtistOutline),
          label: 'Artists',
          title: 'Artists',
        },
        {
          key: 'genres.index',
          icon: () => h(CategoryOutline),
          label: 'Genres',
          title: 'Genres',
        },
        {
          key: 'cinemas',
          icon: () => h(BuildingOutline),
          label: 'Cinemas',
          title: 'Cinemas',
          children: [
            {
              key: 'cinemas.companies.index',
              label: 'Companies',
              title: 'Companies',
            },
            {
              key: 'cinemas.branches.index',
              label: 'Branches',
              title: 'Branches',
            },
          ]
        },
        {
          key: 'roles.index',
          icon: () => h(RoleOutline),
          label: 'Roles',
          title: 'Roles',
        },
        {
          key: 'permissions',
          icon: () => h(PermissionOutline),
          label: 'Permissions',
          title: 'Permissions',
        },
        {
          key: 'users',
          icon: () => h(UserOutline),
          label: 'Users',
          title: 'Users',
        },
      ],
    }
  },
  methods: {
    handleSelectNav({ item, key, selectedKeys }) {
      router.get(route(key));
    },
    checkCurrentActiveRoute(routeName) {
      const hrefSplit = routeName.split('.index');

      if (route().current(hrefSplit[0] + '.*')) {
        this.selectedKeys = [routeName]
      }
    }
  },
  mounted() {
    this.items.forEach((item) => {
      if (item.children) {
        item.children.forEach((childItem) => {
          this.checkCurrentActiveRoute(childItem.key)
        })
      } else {
        this.checkCurrentActiveRoute(item.key)
      }
    })
  }
}
</script>

<style lang="scss" scoped></style>