<script lang="ts" setup>
const isAlertVisible = ref(true)
</script>

<template>
  <VAlert
    v-model="isAlertVisible"
    closable
    close-label="Close Alert"
    color="primary"
  >
    Pudding wafer I love chocolate bar wafer chupa chups wafer. Cake gummies pudding gummies cake.
  </VAlert>

  <!-- Button -->
  <div class="text-center">
    <VBtn
      v-if="!isAlertVisible"
      @click="isAlertVisible = true"
    >
      Reset
    </VBtn>
  </div>
</template>
