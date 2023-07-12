<script setup>
const props = defineProps({
  confirmationQuestion: {
    type: String,
    required: true,
  },
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  confirmTitle: {
    type: String,
    required: true,
  },
  confirmMsg: {
    type: String,
    required: true,
  },
  cancelTitle: {
    type: String,
    required: true,
  },
  cancelMsg: {
    type: String,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'confirm',
])

const unsubscribed = ref(false)
const cancelled = ref(false)

const updateModelValue = val => {
  emit('update:isDialogVisible', val)
}

const onConfirmation = () => {
  emit('confirm', true)
  updateModelValue(false)
  unsubscribed.value = true
}

const onCancel = () => {
  emit('confirm', false)
  emit('update:isDialogVisible', false)
  cancelled.value = true
}
</script>

<template>
  <!-- 👉 Confirm Dialog -->
  <VDialog
    max-width="500"
    :model-value="props.isDialogVisible"
    @update:model-value="updateModelValue"
  >
    <VCard class="text-center px-10 py-6">
      <VCardText>
        <VBtn
          icon
          variant="outlined"
          color="warning"
          class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;"
        >
          <span class="text-5xl">!</span>
        </VBtn>

        <h6 class="text-lg font-weight-medium">
          {{ props.confirmationQuestion }}
        </h6>
      </VCardText>

      <VCardText class="d-flex align-center justify-center gap-2">
        <VBtn
          variant="elevated"
          @click="onConfirmation"
        >
          Confirm
        </VBtn>

        <VBtn
          color="secondary"
          variant="tonal"
          @click="onCancel"
        >
          Cancel
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

  <!-- Unsubscribed -->
  <VDialog
    v-model="unsubscribed"
    max-width="500"
  >
    <VCard>
      <VCardText class="text-center px-10 py-6">
        <VBtn
          icon
          variant="outlined"
          color="success"
          class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;"
        >
          <span class="text-3xl">
            <VIcon icon="mdi-check" />
          </span>
        </VBtn>

        <h1 class="text-h4 mb-4">
          {{ props.confirmTitle }}
        </h1>

        <p>{{ props.confirmMsg }}</p>

        <VBtn
          color="success"
          @click="unsubscribed = false"
        >
          Ok
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

  <!-- Cancelled -->
  <VDialog
    v-model="cancelled"
    max-width="500"
  >
    <VCard>
      <VCardText class="text-center px-10 py-6">
        <VBtn
          icon
          variant="outlined"
          color="error"
          class="my-4"
          style=" block-size: 88px;inline-size: 88px; pointer-events: none;"
        >
          <span class="text-5xl font-weight-light">X</span>
        </VBtn>

        <h1 class="text-h4 mb-4">
          {{ props.cancelTitle }}
        </h1>

        <p>{{ props.cancelMsg }}</p>

        <VBtn
          color="success"
          @click="cancelled = false"
        >
          Ok
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>
</template>
