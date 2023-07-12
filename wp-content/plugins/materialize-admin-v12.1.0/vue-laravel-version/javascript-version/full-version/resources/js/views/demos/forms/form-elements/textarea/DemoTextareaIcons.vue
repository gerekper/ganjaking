<template>
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
