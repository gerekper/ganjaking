<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['update:isDialogVisible'])

const dialogVisibleUpdate = val => {
  emit('update:isDialogVisible', val)
}
</script>

<template>
  <VDialog
    :model-value="props.isDialogVisible"
    class="v-dialog-xl"
    @update:model-value="dialogVisibleUpdate"
  >
    <VCard class="pricing-dialog pa-5 pa-sm-8">
      <!-- 👉 dialog close btn -->
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="emit('update:isDialogVisible', false)"
      />

      <VCardText>
        <AppPricing
          title="Subscription Plan"
          md="4"
        />
      </VCardText>
      <VCardText class="text-center">
        <p class="text-sm">
          Still Not Convinced? Start with a 14-day FREE trial!
        </p>
        <VBtn>Start Your Trial</VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.pricing-dialog {
  .pricing-title {
    font-size: 1.5rem;
  }
}
</style>
