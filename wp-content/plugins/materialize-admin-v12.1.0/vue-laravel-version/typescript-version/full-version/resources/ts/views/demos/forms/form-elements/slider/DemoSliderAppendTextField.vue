<script lang="ts" setup>
const redColorValue = ref(161)
const greenColorValue = ref(105)
const blueColorValue = ref(225)
</script>

<template>
  <VResponsive
    :style="{ background: `rgb(${redColorValue}, ${greenColorValue}, ${blueColorValue})` }"
    height="150px"
  />

  <VRow class="mt-5">
    <VCol cols="12">
      <!-- R -->
      <div class="d-flex justify-space-between">
        <VSlider
          v-model="redColorValue"
          :max="255"
          :step="1"
          prepend-icon="mdi-alpha-r"
        />

        <VTextField
          v-model="redColorValue"
          type="number"
          :max="255"
          style="max-inline-size: 5rem;"
        />
      </div>
    </VCol>

    <VCol cols="12">
      <!-- G -->
      <div class="d-flex justify-space-between">
        <VSlider
          v-model="greenColorValue"
          :max="255"
          :step="1"
          prepend-icon="mdi-alpha-g"
        />

        <VTextField
          v-model="greenColorValue"
          type="number"
          :max="255"
          style="max-inline-size: 5rem;"
        />
      </div>
    </VCol>

    <VCol cols="12">
      <!-- B -->
      <div class="d-flex justify-space-between">
        <VSlider
          v-model="blueColorValue"
          :max="255"
          :step="1"
          prepend-icon="mdi-alpha-b"
        />
        <VTextField
          v-model="blueColorValue"
          type="number"
          :max="255"
          style="max-inline-size: 5rem;"
        />
      </div>
    </VCol>
  </VRow>
</template>
