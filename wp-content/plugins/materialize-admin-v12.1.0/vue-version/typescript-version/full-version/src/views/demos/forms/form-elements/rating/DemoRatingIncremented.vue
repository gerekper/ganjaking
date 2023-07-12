<script lang="ts" setup>
const rating = ref(4.5)
</script>

<template>
  <VRating
    v-model="rating"
    half-increments
    hover
  />
</template>
