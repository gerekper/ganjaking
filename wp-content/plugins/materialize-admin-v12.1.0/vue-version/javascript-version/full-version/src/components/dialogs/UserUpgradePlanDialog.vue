<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits(['update:isDialogVisible'])

const selectedPlan = ref('standard')

const plansList = [
  {
    desc: 'Standard - $99/month',
    title: 'Standard',
    value: 'standard',
  },
  {
    desc: 'Basic - $0/month',
    title: 'Basic',
    value: 'basic',
  },
  {
    desc: 'Enterprise - $499/month',
    title: 'Enterprise',
    value: 'enterprice',
  },
  {
    desc: 'Company - $999/month',
    title: 'Company',
    value: 'company',
  },
]

const isConfirmDialogVisible = ref(false)
</script>

<template>
  <!-- 👉 upgrade plan -->
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 650"
    :model-value="props.isDialogVisible"
    @update:model-value="val => $emit('update:isDialogVisible', val)"
  >
    <VCard class="py-8">
      <!-- 👉 dialog close btn -->
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="$emit('update:isDialogVisible', false)"
      />

      <VCardItem class="text-center">
        <VCardTitle class="text-h5 mb-5">
          Upgrade Plan
        </VCardTitle>

        <VCardSubtitle>
          Choose the best plan for user.
        </VCardSubtitle>
      </VCardItem>

      <VCardText class="d-flex align-center flex-column flex-sm-nowrap px-15">
        <CustomRadios
          :radio-content="plansList"
          :selected-radio="selectedPlan"
          :grid-column="{ cols: '12', sm: '6' }"
        />
        <VBtn class="mt-5">
          Upgrade
        </VBtn>
      </VCardText>

      <VDivider class="my-3" />

      <VCardText class="px-15">
        <p class="font-weight-medium mb-2">
          User current plan is standard plan
        </p>
        <div class="d-flex justify-space-between flex-wrap">
          <div class="d-flex align-center me-3">
            <sup class="text-primary">$</sup>
            <h3 class="text-h3 text-primary">
              99
            </h3>
            <sub class="text-body-1 mt-3">/ month</sub>
          </div>
          <VBtn
            color="error"
            variant="outlined"
            class="mt-3"
            @click="isConfirmDialogVisible = true"
          >
            Cancel Subscription
          </VBtn>
        </div>
      </VCardText>

      <!-- 👉 Confirm Dialog -->
      <ConfirmDialog
        v-model:isDialogVisible="isConfirmDialogVisible"
        cancel-title="Cancelled"
        confirm-title="Unsubscribed!"
        confirm-msg="Your subscription cancelled successfully."
        confirmation-question="Are you sure to cancel your subscription?"
        cancel-msg="Unsubscription Cancelled!!"
      />
    </VCard>
  </VDialog>
</template>
