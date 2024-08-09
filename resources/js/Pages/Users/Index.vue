<template>
  <Box>
    This is user page
    <a-button type="primary">
      <Link :href="route('users.create')">Add new user</Link>
    </a-button>
  </Box>
</template>

<script>
import { Button } from 'ant-design-vue';
export default {
  name: 'UsersPage',
  components: {
    Button
  }
}
</script>

<style lang="scss" scoped></style>