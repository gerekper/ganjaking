<script setup>
import trophy from '@images/misc/trophy.png'
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle class="text-wrap">
        <span class="me-1">Congratulations </span><strong class="me-1">Norris!</strong><span>🎉</span>
      </VCardTitle>
      <span class="text-sm">Best seller of the month</span>
    </VCardItem>

    <VCardText>
      <h5 class="text-2xl font-weight-medium text-primary">
        $42.8k
      </h5>
      <p class="mb-3">
        78% of target 🤟🏻
      </p>

      <VBtn size="small">
        View Sales
      </VBtn>
    </VCardText>

    <!-- Trophy -->
    <VImg
      :src="trophy"
      class="trophy flip-in-rtl"
    />
  </VCard>
</template>

<style lang="scss">
.v-card .trophy {
  position: absolute;
  inline-size: 6.625rem;
  inset-block-end: 0;
  inset-inline-end: 1rem;
}
</style>
