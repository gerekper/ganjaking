export const basic = { ts: `<script setup lang="ts">
const sliderValues = ref([10, 60])
</script>

<template>
  <VRangeSlider v-model="sliderValues" />
</template>
`, js: `<script setup>
const sliderValues = ref([
  10,
  60,
])
</script>

<template>
  <VRangeSlider v-model="sliderValues" />
</template>
` }

export const color = { ts: `<script lang="ts" setup>
const sliderValues = ref([10, 60])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    color="success"
    track-color="secondary"
  />
</template>
`, js: `<script setup>
const sliderValues = ref([
  10,
  60,
])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    color="success"
    track-color="secondary"
  />
</template>
` }

export const disabled = { ts: `<script lang="ts" setup>
const slidersValues = ref([30, 60])
</script>

<template>
  <VRangeSlider
    v-model="slidersValues"
    disabled
    label="Disabled"
  />
</template>
`, js: `<script setup>
const slidersValues = ref([
  30,
  60,
])
</script>

<template>
  <VRangeSlider
    v-model="slidersValues"
    disabled
    label="Disabled"
  />
</template>
` }

export const step = { ts: `<script lang="ts" setup>
const sliderValues = ref([20, 40])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    step="10"
  />
</template>
`, js: `<script setup>
const sliderValues = ref([
  20,
  40,
])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    step="10"
  />
</template>
` }

export const thumbLabel = { ts: `<script lang="ts" setup>
const seasons = ['Winter', 'Spring', 'Summer', 'Fall']
const icons = ['mdi-snowflake', 'mdi-leaf', 'mdi-fire', 'mdi-water']
const sliderValues = ref([1, 2])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    :tick="seasons"
    min="0"
    max="3"
    :step="1"
    show-ticks="always"
    thumb-label
    tick-size="2"
  >
    <template #thumb-label="{ modelValue }">
      <VIcon :icon="icons[modelValue]" />
    </template>
  </VRangeSlider>
</template>
`, js: `<script setup>
const seasons = [
  'Winter',
  'Spring',
  'Summer',
  'Fall',
]

const icons = [
  'mdi-snowflake',
  'mdi-leaf',
  'mdi-fire',
  'mdi-water',
]

const sliderValues = ref([
  1,
  2,
])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    :tick="seasons"
    min="0"
    max="3"
    :step="1"
    show-ticks="always"
    thumb-label
    tick-size="2"
  >
    <template #thumb-label="{ modelValue }">
      <VIcon :icon="icons[modelValue]" />
    </template>
  </VRangeSlider>
</template>
` }

export const vertical = { ts: `<script lang="ts" setup>
const sliderValues = ref([20, 40])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    direction="vertical"
  />
</template>
`, js: `<script setup>
const sliderValues = ref([
  20,
  40,
])
</script>

<template>
  <VRangeSlider
    v-model="sliderValues"
    direction="vertical"
  />
</template>
` }

