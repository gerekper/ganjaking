export const autoGrow = { ts: `<script setup lang="ts">
const textareaValue = ref('The Woodman set to work at once, and so sharp was his axe that the tree was soon chopped nearly through.')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    label="Auto Grow"
    placeholder="Placeholder Text"
    auto-grow
  />
</template>
`, js: `<script setup>
const textareaValue = ref('The Woodman set to work at once, and so sharp was his axe that the tree was soon chopped nearly through.')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    label="Auto Grow"
    placeholder="Placeholder Text"
    auto-grow
  />
</template>
` }

export const basic = { ts: `<template>
  <VTextarea
    label="Default"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<template>
  <VTextarea
    label="Default"
    placeholder="Placeholder Text"
  />
</template>
` }

export const browserAutocomplete = { ts: `<template>
  <VTextarea
    autocomplete="email"
    label="Email"
    placeholder="johndoe@email.com"
  />
</template>
`, js: `<template>
  <VTextarea
    autocomplete="email"
    label="Email"
    placeholder="johndoe@email.com"
  />
</template>
` }

export const clearable = { ts: `<script setup lang="ts">
const textareaValue = ref('This is clearable text.')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    clearable
    clear-icon="mdi-close-circle-outline"
    label="Text"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<script setup>
const textareaValue = ref('This is clearable text.')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    clearable
    clear-icon="mdi-close-circle-outline"
    label="Text"
    placeholder="Placeholder Text"
  />
</template>
` }

export const counter = { ts: `<script lang="ts" setup>
const textareaValue = ref('Hello!')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    counter
    label="Text"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<script setup>
const textareaValue = ref('Hello!')
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    counter
    label="Text"
    placeholder="Placeholder Text"
  />
</template>
` }

export const icons = { ts: `<template>
  <VRow>
    <VCol cols="12">
      <VTextarea
        label="prepend-icon"
        rows="1"
        placeholder="Placeholder Text"
        prepend-icon="mdi-comment-outline"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        append-icon="mdi-comment-outline"
        placeholder="Placeholder Text"
        label="append-icon"
        rows="1"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        prepend-inner-icon="mdi-comment-outline"
        label="prepend-inner-icon"
        placeholder="Placeholder Text"
        rows="1"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        append-inner-icon="mdi-comment-outline"
        label="append-inner-icon"
        placeholder="Placeholder Text"
        rows="1"
      />
    </VCol>
  </VRow>
</template>
`, js: `<template>
  <VRow>
    <VCol cols="12">
      <VTextarea
        label="prepend-icon"
        rows="1"
        placeholder="Placeholder Text"
        prepend-icon="mdi-comment-outline"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        append-icon="mdi-comment-outline"
        placeholder="Placeholder Text"
        label="append-icon"
        rows="1"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        prepend-inner-icon="mdi-comment-outline"
        label="prepend-inner-icon"
        placeholder="Placeholder Text"
        rows="1"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        append-inner-icon="mdi-comment-outline"
        label="append-inner-icon"
        placeholder="Placeholder Text"
        rows="1"
      />
    </VCol>
  </VRow>
</template>
` }

export const noResize = { ts: `<script lang="ts" setup>
const value = ref('Marshmallow tiramisu pie dessert gingerbread tart caramels marzipan oat cake. Muffin sesame snaps cupcake bonbon cookie tiramisu. Pudding biscuit gingerbread halvah lollipop jelly-o cookie.')
</script>

<template>
  <VTextarea
    v-model="value"
    label="Text"
    no-resize
    rows="2"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<script setup>
const value = ref('Marshmallow tiramisu pie dessert gingerbread tart caramels marzipan oat cake. Muffin sesame snaps cupcake bonbon cookie tiramisu. Pudding biscuit gingerbread halvah lollipop jelly-o cookie.')
</script>

<template>
  <VTextarea
    v-model="value"
    label="Text"
    no-resize
    rows="2"
    placeholder="Placeholder Text"
  />
</template>
` }

export const rows = { ts: `<template>
  <VRow>
    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="One row"
        auto-grow
        rows="1"
        row-height="15"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        auto-grow
        label="Two rows"
        rows="2"
        placeholder="Placeholder Text"
        row-height="20"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Three rows"
        auto-grow
        rows="3"
        placeholder="Placeholder Text"
        row-height="25"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        auto-grow
        label="Four rows"
        placeholder="Placeholder Text"
        rows="4"
        row-height="30"
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
      <VTextarea
        label="One row"
        auto-grow
        rows="1"
        row-height="15"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        auto-grow
        label="Two rows"
        rows="2"
        placeholder="Placeholder Text"
        row-height="20"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Three rows"
        auto-grow
        rows="3"
        placeholder="Placeholder Text"
        row-height="25"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        auto-grow
        label="Four rows"
        placeholder="Placeholder Text"
        rows="4"
        row-height="30"
      />
    </VCol>
  </VRow>
</template>
` }

export const states = { ts: `<template>
  <VRow>
    <VCol cols="12">
      <VTextarea
        disabled
        label="Disabled"
        hint="Hint text"
        placeholder="Placeholder Text"
        rows="2"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        readonly
        rows="2"
        label="Readonly"
        placeholder="Placeholder Text"
        hint="Hint text"
      />
    </VCol>
  </VRow>
</template>
`, js: `<template>
  <VRow>
    <VCol cols="12">
      <VTextarea
        disabled
        label="Disabled"
        hint="Hint text"
        placeholder="Placeholder Text"
        rows="2"
      />
    </VCol>

    <VCol cols="12">
      <VTextarea
        readonly
        rows="2"
        label="Readonly"
        placeholder="Placeholder Text"
        hint="Hint text"
      />
    </VCol>
  </VRow>
</template>
` }

export const validation = { ts: `<script lang="ts" setup>
const textareaValue = ref('Hello!')
const rules = [(v: string) => v.length <= 25 || 'Max 25 characters']
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    label="Validation"
    :rules="rules"
    rows="2"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<script setup>
const textareaValue = ref('Hello!')
const rules = [v => v.length <= 25 || 'Max 25 characters']
</script>

<template>
  <VTextarea
    v-model="textareaValue"
    label="Validation"
    :rules="rules"
    rows="2"
    placeholder="Placeholder Text"
  />
</template>
` }

export const variant = { ts: `<template>
  <VRow>
    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Default"
        rows="2"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Solo"
        placeholder="Placeholder Text"
        rows="2"
        variant="solo"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Filled"
        rows="2"
        placeholder="Placeholder Text"
        variant="filled"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Outlined"
        rows="2"
        placeholder="Placeholder Text"
        variant="outlined"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Underlined"
        rows="2"
        placeholder="Placeholder Text"
        variant="underlined"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Plain"
        rows="2"
        placeholder="Placeholder Text"
        variant="plain"
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
      <VTextarea
        label="Default"
        rows="2"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Solo"
        placeholder="Placeholder Text"
        rows="2"
        variant="solo"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Filled"
        rows="2"
        placeholder="Placeholder Text"
        variant="filled"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Outlined"
        rows="2"
        placeholder="Placeholder Text"
        variant="outlined"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Underlined"
        rows="2"
        placeholder="Placeholder Text"
        variant="underlined"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextarea
        label="Plain"
        rows="2"
        placeholder="Placeholder Text"
        variant="plain"
      />
    </VCol>
  </VRow>
</template>
` }

