<template>
  <component :is="dynamicComponent" :class="class" />
</template>

<script>
import { defineAsyncComponent } from "vue";
import * as path from 'path'

export default {
  props: {
    name: {
      type: String,
      required: true,
    },
    class: {
      type: String
    }
  },
  computed: {
    dynamicComponent() {
      // const name = this.name.charAt(0).toUpperCase() + this.name.slice(1);
      const iconPath = `./${this.name}.vue`;
      return defineAsyncComponent(() => import(iconPath));
    },
  },
};
</script>