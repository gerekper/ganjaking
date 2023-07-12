<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

const props = defineProps({
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'update:isDrawerOpen',
  'submit',
])

const invoiceBalance = ref()
const paymentAmount = ref()
const paymentDate = ref('')
const paymentMethod = ref()
const paymentNote = ref('')

const onSubmit = () => {
  emit('update:isDrawerOpen', false)
  emit('submit', {
    invoiceBalance: invoiceBalance.value,
    paymentAmount: paymentAmount.value,
    paymentDate: paymentDate.value,
    paymentMethod: paymentMethod.value,
    paymentNote: paymentNote.value,
  })
}

const handleDrawerModelValueUpdate = val => {
  emit('update:isDrawerOpen', val)
}
</script>

<template>
  <VNavigationDrawer
    temporary
    location="end"
    :width="400"
    :model-value="props.isDrawerOpen"
    class="scrollable-content"
    @update:model-value="handleDrawerModelValueUpdate"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      title="Add Payment"
      @cancel="$emit('update:isDrawerOpen', false)"
    />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <VForm @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="invoiceBalance"
                  label="Invoice Balance"
                  type="number"
                  placeholder="$99"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="paymentAmount"
                  label="Payment Amount"
                  type="number"
                  placeholder="$99"
                />
              </VCol>

              <VCol cols="12">
                <AppDateTimePicker
                  v-model="paymentDate"
                  label="Payment Date"
                  placeholder="YYYY-MM-DD"
                />
              </VCol>

              <VCol cols="12">
                <VSelect
                  v-model="paymentMethod"
                  label="Select Payment Method"
                  placeholder="Select Payment Method"
                  :items="['Cash', 'Bank Transfer', 'Debit', 'Credit', 'Paypal']"
                />
              </VCol>

              <VCol cols="12">
                <VTextarea
                  v-model="paymentNote"
                  label="Internal Payment Note"
                  placeholder="Internal Payment Note"
                />
              </VCol>

              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Send
                </VBtn>

                <VBtn
                  type="reset"
                  color="secondary"
                  variant="tonal"
                  @click="$emit('update:isDrawerOpen', false)"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
