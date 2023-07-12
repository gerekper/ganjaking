<script setup lang="ts">
import { useGenerateImageVariant } from '@/@core/composable/useGenerateImageVariant'
import authV2ResetPasswordIllustrationBorderedDark from '@images/pages/auth-v2-reset-password-illustration-bordered-dark.png'
import authV2ResetPasswordIllustrationBorderedLight from '@images/pages/auth-v2-reset-password-illustration-bordered-light.png'
import authV2ResetPasswordIllustrationDark from '@images/pages/auth-v2-reset-password-illustration-dark.png'
import authV2ResetPasswordIllustrationLight from '@images/pages/auth-v2-reset-password-illustration-light.png'
import authV2ResetPasswordMaskDark from '@images/pages/auth-v2-reset-password-mask-dark.png'
import authV2ResetPasswordMaskLight from '@images/pages/auth-v2-reset-password-mask-light.png'
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const authV2ResetPasswordMask = useGenerateImageVariant(authV2ResetPasswordMaskLight, authV2ResetPasswordMaskDark)
const authV2ResetPasswordIllustration = useGenerateImageVariant(authV2ResetPasswordIllustrationLight, authV2ResetPasswordIllustrationDark, authV2ResetPasswordIllustrationBorderedLight, authV2ResetPasswordIllustrationBorderedDark, true)

const form = ref({
  newPassword: '',
  confirmPassword: '',
})

const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
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
    no-gutters
    class="auth-wrapper"
  >
    <VCol
      md="8"
      class="d-none d-md-flex align-center justify-center position-relative"
    >
      <!-- here your illustrator -->
      <div class="d-flex align-center justify-center pa-10">
        <img
          :src="authV2ResetPasswordIllustration"
          class="auth-illustration w-100"
          alt="auth-illustration"
        >
      </div>
      <VImg
        :src="authV2ResetPasswordMask"
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
        <VCardText>
          <h5 class="text-h5 mb-1">
            Reset Password 🔒
          </h5>
          <p class="mb-0">
            Enter your email and we'll send you instructions to reset your password
          </p>
        </VCardText>

        <VCardText>
          <VForm @submit.prevent="() => {}">
            <VRow>
              <!-- password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.newPassword"
                  autofocus
                  label="New Password"
                  placeholder="············"
                  :type="isPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                  @click:append-inner="isPasswordVisible = !isPasswordVisible"
                />
              </VCol>

              <!-- Confirm Password -->
              <VCol cols="12">
                <VTextField
                  v-model="form.confirmPassword"
                  label="Confirm Password"
                  placeholder="············"
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  :append-inner-icon="isConfirmPasswordVisible ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
                />
              </VCol>

              <!-- Set password -->
              <VCol cols="12">
                <VBtn
                  block
                  type="submit"
                >
                  Set New Password
                </VBtn>
              </VCol>

              <!-- back to login -->
              <VCol cols="12">
                <RouterLink
                  class="d-flex align-center justify-center"
                  :to="{ name: 'pages-authentication-login-v2' }"
                >
                  <VIcon
                    start
                    size="30"
                    icon="mdi-chevron-left"
                    class="flip-in-rtl"
                  />
                  <span class="text-base">Back to login</span>
                </RouterLink>
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
