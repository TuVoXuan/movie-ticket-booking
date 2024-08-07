<template>
  <Box>
    This is showtimes page
    <a-button type="primary">
      <Link :href="route('cinemas.branches.showtimes.create', route().params)">Add new screening</Link>
    </a-button>
  </Box>
</template>

<script>
import { Button } from 'ant-design-vue';
export default {
  name: 'ShowtimesPage'
}
</script>

<style lang="scss" scoped></style>