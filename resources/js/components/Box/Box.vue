<template>
  <div class="bg-white rounded-md p-3 shadow-md" :class="class">
    <slot />
  </div>
</template>

<script>
export default {
  name: "Box",
  props: ['class']
}
</script>