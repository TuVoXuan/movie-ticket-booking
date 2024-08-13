<template>
  <Breadcrumb class="mb-4" :breadcrumb-items="breadcrumbItems" />
  <Box>
    This is home page
  </Box>
</template>

<script>
import Breadcrumb from '../components/Breadcrumb/Breadcrumb.vue';
export default {
  name: "HomePage",
  components: {
    Breadcrumb
  },
  data() {
    return {
      breadcrumbItems: [{ label: 'Dashboard', href: null }]
    }
  }
}
</script>