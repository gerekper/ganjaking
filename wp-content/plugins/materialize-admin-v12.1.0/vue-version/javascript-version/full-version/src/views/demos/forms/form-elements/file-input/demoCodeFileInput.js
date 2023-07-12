export const accept = { ts: `<template>
  <VFileInput
    accept="image/*"
    label="File input"
  />
</template>
`, js: `<template>
  <VFileInput
    accept="image/*"
    label="File input"
  />
</template>
` }

export const basic = { ts: `<template>
  <VFileInput label="File input" />
</template>
`, js: `<template>
  <VFileInput label="File input" />
</template>
` }

export const chips = { ts: `<template>
  <VFileInput
    chips
    label="File input w/ chips"
  />
</template>
`, js: `<template>
  <VFileInput
    chips
    label="File input w/ chips"
  />
</template>
` }

export const counter = { ts: `<template>
  <VFileInput
    show-size
    counter
    multiple
    label="File input"
  />
</template>
`, js: `<template>
  <VFileInput
    show-size
    counter
    multiple
    label="File input"
  />
</template>
` }

export const density = { ts: `<template>
  <VFileInput
    label="File input"
    density="compact"
  />
</template>
`, js: `<template>
  <VFileInput
    label="File input"
    density="compact"
  />
</template>
` }

export const loading = { ts: `<script setup lang="ts">
const file = ref()
const loading = ref(true)

watch(file, () => {
  loading.value = !file.value[0]
})
</script>

<template>
  <VFileInput
    v-model="file"
    :loading="loading"
    color="primary"
    label="File input"
  />
</template>
`, js: `<script setup>
const file = ref()
const loading = ref(true)

watch(file, () => {
  loading.value = !file.value[0]
})
</script>

<template>
  <VFileInput
    v-model="file"
    :loading="loading"
    color="primary"
    label="File input"
  />
</template>
` }

export const multiple = { ts: `<template>
  <VFileInput
    multiple
    label="File input"
  />
</template>
`, js: `<template>
  <VFileInput
    multiple
    label="File input"
  />
</template>
` }

export const prependIcon = { ts: `<template>
  <VFileInput
    label="File input"
    prepend-icon="mdi-camera-outline"
  />
</template>
`, js: `<template>
  <VFileInput
    label="File input"
    prepend-icon="mdi-camera-outline"
  />
</template>
` }

export const selectionSlot = { ts: `<script lang="ts" setup>
const files = ref<File[]>([])
</script>

<template>
  <VFileInput
    v-model="files"
    multiple
    placeholder="Upload your documents"
    label="File input"
    prepend-icon="mdi-paperclip"
  >
    <template #selection="{ fileNames }">
      <template
        v-for="fileName in fileNames"
        :key="fileName"
      >
        <VChip
          label
          size="small"
          variant="outlined"
          color="primary"
          class="me-2"
        >
          {{ fileName }}
        </VChip>
      </template>
    </template>
  </VFileInput>
</template>
`, js: `<script setup>
const files = ref([])
</script>

<template>
  <VFileInput
    v-model="files"
    multiple
    placeholder="Upload your documents"
    label="File input"
    prepend-icon="mdi-paperclip"
  >
    <template #selection="{ fileNames }">
      <template
        v-for="fileName in fileNames"
        :key="fileName"
      >
        <VChip
          label
          size="small"
          variant="outlined"
          color="primary"
          class="me-2"
        >
          {{ fileName }}
        </VChip>
      </template>
    </template>
  </VFileInput>
</template>
` }

export const showSize = { ts: `<template>
  <VFileInput
    show-size
    label="File input"
  />
</template>
`, js: `<template>
  <VFileInput
    show-size
    label="File input"
  />
</template>
` }

export const validation = { ts: `<script lang="ts" setup>
const rules = [
  (fileList: FileList) => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Avatar size should be less than 1 MB!',
]
</script>

<template>
  <VFileInput
    :rules="rules"
    label="Avatar"
    accept="image/png, image/jpeg, image/bmp"
    placeholder="Pick an avatar"
    prepend-icon="mdi-camera-outline"
  />
</template>
`, js: `<script setup>
const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 1000000 || 'Avatar size should be less than 1 MB!']
</script>

<template>
  <VFileInput
    :rules="rules"
    label="Avatar"
    accept="image/png, image/jpeg, image/bmp"
    placeholder="Pick an avatar"
    prepend-icon="mdi-camera-outline"
  />
</template>
` }

export const variant = { ts: `<template>
  <VRow>
    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput label="Outlined" />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Filled"
        variant="filled"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Solo"
        variant="solo"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Plain"
        variant="plain"
      />
    </VCol>
    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Underlined"
        variant="underlined"
        density="default"
      />
    </VCol>
  </VRow>
</template>
`, js: `<template>
  <VRow>
    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput label="Outlined" />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Filled"
        variant="filled"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Solo"
        variant="solo"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Plain"
        variant="plain"
      />
    </VCol>
    <VCol
      cols="12"
      sm="6"
    >
      <VFileInput
        label="Underlined"
        variant="underlined"
        density="default"
      />
    </VCol>
  </VRow>
</template>
` }

