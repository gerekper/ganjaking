<script setup>
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import authV2RegisterMaskDark from '@images/pages/auth-v2-register-mask-dark.png'
import authV2RegisterMaskLight from '@images/pages/auth-v2-register-mask-light.png'
import authV2TwoStepsIllustrationBorderedDark from '@images/pages/auth-v2-two-steps-illustration-bordered-dark.png'
import authV2TwoStepsIllustrationBorderedLight from '@images/pages/auth-v2-two-steps-illustration-bordered-light.png'
import authV2TwoStepsIllustrationDark from '@images/pages/auth-v2-two-steps-illustration-dark.png'
import authV2TwoStepsIllustrationLight from '@images/pages/auth-v2-two-steps-illustration-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const authV2ThemeTwoStepMask = useGenerateImageVariant(authV2RegisterMaskLight, authV2RegisterMaskDark)
const authV2TwoStepsIllustration = useGenerateImageVariant(authV2TwoStepsIllustrationLight, authV2TwoStepsIllustrationDark, authV2TwoStepsIllustrationBorderedLight, authV2TwoStepsIllustrationBorderedDark, true)
</script>

<template>
  <div class="auth-logo d-flex align-center gap-x-2">
    <div>
      <VNodeRenderer :nodes="themeConfig.app.logo" />
    </div>

    <h5 class="text-h5 font-weight-bold leading-normal text-capitalize">
      {{ themeConfig.app.title }}
    </h5>
  </div>
  <VRow
    class="auth-wrapper"
    no-gutters
  >
    <VCol
      md="8"
      class="d-none d-md-flex align-center justify-center position-relative"
    >
      <!-- Here your illustrator -->
      <div class="d-flex align-center justify-center pa-10">
        <img
          :src="authV2TwoStepsIllustration"
          class="auth-illustration w-100"
          alt="auth-illustration"
        >
      </div>
      <VImg
        :src="authV2ThemeTwoStepMask"
        class="d-none d-md-flex auth-footer-mask"
        alt="auth-mask"
      />
    </VCol>

    <VCol
      cols="12"
      md="4"
      class="auth-card-v2 d-flex align-center justify-center"
      style="background-color: rgb(var(--v-theme-surface));"
    >
      <VCard
        flat
        :max-width="500"
        class="mt-12 mt-sm-0 pa-4"
      >
        <VCardText class="pt-2">
          <h5 class="text-h5 mb-1">
            Two Step Verification 💬
          </h5>
          <p class="mb-2">
            We sent a verification code to your mobile. Enter the code from the mobile in the field below.
          </p>
          <h6 class="text-base font-weight-medium">
            ············1234
          </h6>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="() => {}">
            <VRow>
              <!-- email -->
              <VCol cols="12">
                <AppOtpInput />
              </VCol>

              <!-- reset password -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                >
                  Verify my account
                </VBtn>
              </VCol>

              <!-- back to login -->
              <VCol cols="12">
                <div class="text-base d-flex justify-center align-center flex-wrap">
                  <span class="me-1">Didn't get the code?</span>
                  <a href="#">Resend</a>
                </div>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
