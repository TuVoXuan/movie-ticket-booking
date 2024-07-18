<template>
  <Box>
    <div class="flex justify-end">
      <Button>
        <Link :href="route('films.create')">Add new Film</Link>
      </Button>
    </div>
  </Box>
</template>

<script>
import Button from 'primevue/button';
export default {
  name: 'Index',
  components: {
    Button
  }
}
</script>

<style lang="scss" scoped></style>