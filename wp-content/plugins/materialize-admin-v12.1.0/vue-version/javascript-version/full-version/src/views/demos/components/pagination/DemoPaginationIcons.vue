<script setup>
const currentPage = ref(1)
</script>

<template>
  <VPagination
    v-model="currentPage"
    :length="5"
    prev-icon="mdi-menu-left"
    next-icon="mdi-menu-right"
  />
</template>
