<script setup lang="ts">
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'

import ErrorHeader from '@/components/ErrorHeader.vue'
import pages500 from '@images/pages/500.png'
import miscObj from '@images/pages/misc-500-object.png'
import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'

const miscThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark)
</script>

<template>
  <div class="misc-wrapper">
    <ErrorHeader
      error-code="500"
      error-title="Internal server error 👨🏻‍💻"
      error-description="Oops, something went wrong!"
    />

    <!-- 👉 Image -->
    <div class="misc-avatar w-100 text-center">
      <VImg
        :src="pages500"
        alt="Coming Soon"
        :height="$vuetify.display.xs ? 400 : 500"
        class="my-sm-4"
      />

      <VBtn
        to="/"
        class="mt-10"
      >
        Back to Home
      </VBtn>

      <VImg
        :src="miscThemeMask"
        class="d-none d-md-block footer-coming-soon"
        cover
      />

      <VImg
        :src="miscObj"
        class="d-none d-md-block footer-coming-soon-obj"
        :max-width="174"
        height="158"
      />
    </div>
  </div>
</template>

<style lang="scss">
@use "@core/scss/template/pages/misc.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
