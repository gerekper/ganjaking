<script setup>
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import miscComingSoonObj from '@images/pages/misc-coming-soon-object.png'
import miscComingSoon from '@images/pages/misc-coming-soon.png'
import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'

const miscThemeMask = useGenerateImageVariant(miscMaskLight, miscMaskDark)
const email = ref('')
</script>

<template>
  <div class="misc-wrapper">
    <div>
      <!-- 👉 Title and subtitle -->
      <div class="text-center mb-10">
        <h5 class="text-h5 mb-3">
          We are launching soon 🚀
        </h5>
        <p class="text-sm">
          Our website is opening soon. Please register to get notified when it's ready!
        </p>
      </div>

      <!-- 👉 Email input -->
      <VForm
        class="d-flex align-center justify-center flex-wrap gap-4"
        @submit.prevent="() => {}"
      >
        <VTextField
          v-model="email"
          autofocus
          placeholder="Enter your email"
          class="misc-email-input"
          density="compact"
        />
        <VBtn type="submit">
          Notify
        </VBtn>
      </VForm>
    </div>

    <!-- 👉 Image -->
    <div class="misc-avatar w-100 mt-10">
      <VImg
        :src="miscComingSoon"
        alt="Coming Soon"
        :height="$vuetify.display.xs ? 400 : 450"
      />
      <VImg
        :src="miscThemeMask"
        class="d-none d-md-block footer-coming-soon"
        cover
      />

      <VImg
        :src="miscComingSoonObj"
        class="d-none d-md-block footer-coming-soon-obj"
        :max-width="174"
        height="158"
      />
    </div>
  </div>
</template>

<style lang="scss">
@use "@core/scss/template/pages/misc.scss";

@media (max-width: 1200px) {
  .footer-coming-soon-obj {
    inset-block-end: 5%;
  }
}
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
