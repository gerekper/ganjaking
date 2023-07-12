<script setup lang="ts">
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'

import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'
import miscObj from '@images/pages/misc-under-maintenance-object.png'
import miscUnderMaintenance from '@images/pages/misc-under-maintenance.png'

const miscThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark)
</script>

<template>
  <div class="misc-wrapper">
    <div class="text-center mb-4">
      <!-- 👉 Title and subtitle -->
      <h5 class="text-h5 mb-3">
        Under Maintenance! 🚧
      </h5>
      <p>Sorry for the inconvenience but we're performing some maintenance at the moment</p>
    </div>

    <!-- 👉 Image -->
    <div class="misc-avatar w-100 text-center">
      <VImg
        :src="miscUnderMaintenance"
        :height="$vuetify.display.xs ? 400 : 500"
        alt="Coming Soon"
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
