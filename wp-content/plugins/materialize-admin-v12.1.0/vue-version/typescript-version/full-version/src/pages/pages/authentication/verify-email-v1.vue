<script setup lang="ts">
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import authV1LoginMaskDark from '@images/pages/auth-v1-login-mask-dark.png'
import authV1LoginMaskLight from '@images/pages/auth-v1-login-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const authV1ThemeVerifyEmailMask = useGenerateImageVariant(authV1LoginMaskLight, authV1LoginMaskDark)
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
          Verify your email ✉️
        </h5>
        <p>
          Account activation link sent to your email address: hello@example.com Please follow the link inside to continue.
        </p>

        <VBtn
          block
          to="/"
          class="mb-6"
        >
          Skip for now
        </VBtn>

        <div class="d-flex align-center justify-center">
          <span class="me-1">Didn't get the mail? </span><a href="#">Resend</a>
        </div>
      </VCardText>
    </VCard>
    <VImg
      :src="authV1ThemeVerifyEmailMask"
      class="d-none d-md-block auth-footer-mask"
    />
  </div>
</template>

<style lang="scss">
@use "@core/scss/template/pages/page-auth.scss";
</style>

<route lang="yaml">
meta:
  layout: blank
</route>
