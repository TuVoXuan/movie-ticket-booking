<template>
  <div class="bg-white rounded-md p-3 shadow-md overflow-x-auto" :class="class">
    <slot />
  </div>
</template>

<script>
export default {
  name: "Box",
  props: ['class']
}
</script>