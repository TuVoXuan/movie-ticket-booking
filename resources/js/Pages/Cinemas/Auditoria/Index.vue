<template>
  <Box>
    This is auditoria page
    <a-button type="primary">
      <Link :href="route('cinemas.branches.auditoria.create', route().params)">Add New Auditoria</Link>
    </a-button>
  </Box>
</template>

<script>
import { Button } from 'ant-design-vue';
export default {
  name: 'AuditoriaPage',
  components: {
    Button
  }
}
</script>

<style lang="scss" scoped></style>