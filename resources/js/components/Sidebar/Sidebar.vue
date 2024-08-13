<template>
  <a-menu v-model:openKeys="openKeys" v-model:selectedKeys="selectedKeys" mode="inline" :items="items"
    style="border-inline-end: none" @select="handleSelectNav" :inline-collapsed="collapsed" />
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
  props: ['collapsed'],
  data() {
    return {
      selectedKeys: [],
      openKeys: [],
      items: [
        {
          key: 'dashboard',
          icon: () => h(ChartOutline, { class: 'w-4 h-4' }),
          label: 'Dashboard',
          title: 'Dashboard',
        },
        {
          key: 'films.index',
          icon: () => h(FilmOutline, { class: 'w-4 h-4' }),
          label: 'Films',
          title: 'Films',
        },
        {
          key: 'artists.index',
          icon: () => h(ArtistOutline, { class: 'w-4 h-4' }),
          label: 'Artists',
          title: 'Artists',
        },
        {
          key: 'genres.index',
          icon: () => h(CategoryOutline, { class: 'w-4 h-4' }),
          label: 'Genres',
          title: 'Genres',
        },
        {
          key: 'cinemas',
          icon: () => h(BuildingOutline, { class: 'w-4 h-4' }),
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
          icon: () => h(RoleOutline, { class: 'w-4 h-4' }),
          label: 'Roles',
          title: 'Roles',
        },
        {
          key: 'permissions.index',
          icon: () => h(PermissionOutline, { class: 'w-4 h-4' }),
          label: 'Permissions',
          title: 'Permissions',
        },
        {
          key: 'users.index',
          icon: () => h(UserOutline, { class: 'w-4 h-4' }),
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

    router.on('finish', (event) => {
      this.items.forEach((item) => {
        if (item.children) {
          item.children.forEach((childItem) => {
            this.checkCurrentActiveRoute(childItem.key)
          })
        } else {
          this.checkCurrentActiveRoute(item.key)
        }
      })
    })
  }
}
</script>

<style lang="css">
.ant-menu-inline-collapsed {
  width: unset;
}
</style>