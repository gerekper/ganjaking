<script setup>
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import authV1RegisterMaskDark from '@images/pages/auth-v1-register-mask-dark.png'
import authV1RegisterMaskLight from '@images/pages/auth-v1-register-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const authV1ThemeTwoStepMask = useGenerateImageVariant(authV1RegisterMaskLight, authV1RegisterMaskDark)
</script>

<template>
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-2 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <template #prepend>
          <div class="me-n2">
            <VNodeRenderer :nodes="themeConfig.app.logo" />
          </div>
        </template>

        <VCardTitle class="font-weight-bold text-2xl text-capitalize">
          {{ themeConfig.app.title }}
        </VCardTitle>
      </VCardItem>

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
    <VImg
      :src="authV1ThemeTwoStepMask"
      class="d-none d-md-block auth-footer-mask"
    />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
