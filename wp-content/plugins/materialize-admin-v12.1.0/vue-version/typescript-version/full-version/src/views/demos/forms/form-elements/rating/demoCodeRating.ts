export const basic = {
  ts: `<template>
  <VRating />
</template>
`,
  js: `<template>
  <VRating />
</template>
`,
}

export const clearable = {
  ts: `<template>
  <VRating clearable />
</template>
`,
  js: `<template>
  <VRating clearable />
</template>
`,
}

export const colors = {
  ts: `<script lang="ts" setup>
const rating = ref(4)
const ratingColors = ['primary', 'secondary', 'success', 'info', 'warning', 'error']
</script>

<template>
  <div class="d-flex flex-column">
    <VRating
      v-for="color in ratingColors"
      :key="color"
      v-model="rating"
      :active-color="color"
    />
  </div>
</template>
`,
  js: `<script setup>
const rating = ref(4)

const ratingColors = [
  'primary',
  'secondary',
  'success',
  'info',
  'warning',
  'error',
]
</script>

<template>
  <div class="d-flex flex-column">
    <VRating
      v-for="color in ratingColors"
      :key="color"
      v-model="rating"
      :active-color="color"
    />
  </div>
</template>
`,
}

export const density = {
  ts: `<template>
  <VRating density="default" />
</template>
`,
  js: `<template>
  <VRating density="default" />
</template>
`,
}

export const hover = {
  ts: `<template>
  <VRating hover />
</template>
`,
  js: `<template>
  <VRating hover />
</template>
`,
}

export const incremented = {
  ts: `<script lang="ts" setup>
const rating = ref(4.5)
</script>

<template>
  <VRating
    v-model="rating"
    half-increments
    hover
  />
</template>
`,
  js: `<script setup>
const rating = ref(4.5)
</script>

<template>
  <VRating
    v-model="rating"
    half-increments
    hover
  />
</template>
`,
}

export const itemSlot = {
  ts: `<script lang="ts" setup>
const rating = ref(4.5)
</script>

<template>
  <VRating v-model="rating">
    <template #item="props">
      <VIcon
        v-bind="props"
        :size="25"
        :color="props.isFilled ? 'success' : 'secondary'"
        class="me-3"
        :icon="props.isFilled ? 'mdi-emoticon-excited-outline' : 'mdi-emoticon-sad-outline'"
      />
    </template>
  </VRating>
</template>
`,
  js: `<script setup>
const rating = ref(4.5)
</script>

<template>
  <VRating v-model="rating">
    <template #item="props">
      <VIcon
        v-bind="props"
        :size="25"
        :color="props.isFilled ? 'success' : 'secondary'"
        class="me-3"
        :icon="props.isFilled ? 'mdi-emoticon-excited-outline' : 'mdi-emoticon-sad-outline'"
      />
    </template>
  </VRating>
</template>
`,
}

export const length = {
  ts: `<script lang="ts" setup>
const length = ref(5)
const rating = ref(2)
</script>

<template>
  <div class="text-caption">
    Custom length
  </div>

  <VSlider
    v-model="length"
    :min="1"
    :max="7"
  />

  <VRating
    v-model="rating"
    :length="length"
  />
  <p class="font-weight-medium mb-0">
    Model: {{ rating }}
  </p>
</template>
`,
  js: `<script setup>
const length = ref(5)
const rating = ref(2)
</script>

<template>
  <div class="text-caption">
    Custom length
  </div>

  <VSlider
    v-model="length"
    :min="1"
    :max="7"
  />

  <VRating
    v-model="rating"
    :length="length"
  />
  <p class="font-weight-medium mb-0">
    Model: {{ rating }}
  </p>
</template>
`,
}

export const readonly = {
  ts: `<template>
  <VRating
    readonly
    :model-value="4"
  />
</template>
`,
  js: `<template>
  <VRating
    readonly
    :model-value="4"
  />
</template>
`,
}

export const size = {
  ts: `<script lang="ts" setup>
const rating = ref(4)
</script>

<template>
  <div class="d-flex flex-column">
    <VRating
      v-model="rating"
      size="x-small"
    />

    <VRating
      v-model="rating"
      size="small"
    />

    <VRating v-model="rating" />

    <VRating
      v-model="rating"
      size="large"
    />

    <VRating
      v-model="rating"
      size="x-large"
    />
  </div>
</template>
`,
  js: `<script setup>
const rating = ref(4)
</script>

<template>
  <div class="d-flex flex-column">
    <VRating
      v-model="rating"
      size="x-small"
    />

    <VRating
      v-model="rating"
      size="small"
    />

    <VRating v-model="rating" />

    <VRating
      v-model="rating"
      size="large"
    />

    <VRating
      v-model="rating"
      size="x-large"
    />
  </div>
</template>
`,
}
