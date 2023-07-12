<script setup>
import upgradeAccount from '@images/cards/illustration-upgrade-account.png'
</script>

<template>
  <VCard
    title="Upgrade Account 😀"
    subtitle="Add 15 team members"
  >
    <VCardText>
      <h5 class="text-2xl font-weight-medium text-primary">
        $199
      </h5>
      <p class="mb-3">
        40% OFF 🤩
      </p>

      <VBtn size="small">
        Upgrade Plan
      </VBtn>

      <VImg
        :src="upgradeAccount"
        class="illustration flip-in-rtl"
        :height="$vuetify.display.xs ? '140' : '165'"
      />
    </VCardText>
  </VCard>
</template>

<style lang="scss">
.v-card .illustration {
  position: absolute;
  inline-size: 150px;
  inset-block-end: 0.5rem;
  inset-inline-end: 0.5rem;
}
</style>
