<script setup lang="ts">
import illustrationJohnDark from '@images/cards/illustration-john-dark.png'
import illustrationJohnLight from '@images/cards/illustration-john-light.png'

import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'

const johnImage = useGenerateImageVariant(illustrationJohnLight, illustrationJohnDark)
</script>

<template>
  <VCard class="pa-2">
    <VRow no-gutters>
      <VCol
        cols="12"
        sm="8"
        order="2"
        order-sm="1"
      >
        <VCardItem>
          <VCardTitle class="text-md-h5 text-wrap">
            <span class="me-1">Congratulations</span><strong class="me-1"> John!</strong><span>🎉</span>
          </VCardTitle>
        </VCardItem>

        <VCardText>
          <p>
            You have done 68% 🤩 more sales today.
            <br>
            Check your new raising badge in your profile.
          </p>
          <VBtn>View Profile</VBtn>
        </VCardText>
      </VCol>

      <VCol
        cols="12"
        sm="4"
        order="1"
        order-sm="2"
        class="text-center"
      >
        <img
          :src="johnImage"
          class="john-illustration"
          :height="$vuetify.display.xs ? '165' : '186'"
          :class="$vuetify.display.xs ? 'position-relative' : 'position-absolute'"
          alt="john-illustration"
        >
      </VCol>
    </VRow>
  </VCard>
</template>

<style lang="scss" scoped>
.john-illustration {
  inset-block-end: -0.0625rem;
  inset-inline-end: 0;
}
</style>
