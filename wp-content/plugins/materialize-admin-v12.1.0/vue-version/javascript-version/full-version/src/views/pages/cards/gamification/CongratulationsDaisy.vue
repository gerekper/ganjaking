<script setup>
import illustrationDaisyDark from '@images/cards/illustration-daisy-dark.png'
import illustrationDaisyLight from '@images/cards/illustration-daisy-light.png'
import { useGenerateImageVariant } from '@core/composable/useGenerateImageVariant'

const daisyImage = useGenerateImageVariant(illustrationDaisyLight, illustrationDaisyDark)
</script>

<template>
  <VCard class="py-3 px-2">
    <VRow no-gutters>
      <VCol
        cols="12"
        sm="8"
      >
        <VCardItem>
          <VCardTitle class="text-md-h5 text-wrap">
            <span class="me-1">Congratulations</span><strong class="me-1"> Daisy!</strong><span>🎉</span>
          </VCardTitle>
        </VCardItem>

        <VCardText>
          <p>You already completed 68% 😍 tasks. Good job! <br> Check your new raising badge in your profile.</p>
          <VBtn>View Profile</VBtn>
        </VCardText>
      </VCol>

      <VCol
        cols="12"
        sm="4"
        class="text-center"
      >
        <img
          :src="daisyImage"
          class="john-illustration"
          :height="$vuetify.display.xs ? '165' : '200'"
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
