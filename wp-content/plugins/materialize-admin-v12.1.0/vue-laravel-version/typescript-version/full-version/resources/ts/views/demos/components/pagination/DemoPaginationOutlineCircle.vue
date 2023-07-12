<script setup lang="ts">
const currentPage = ref(1)
</script>

<template>
  <VPagination
    v-model="currentPage"
    variant="outlined"
    :length="5"
    rounded="circle"
  />
</template>
