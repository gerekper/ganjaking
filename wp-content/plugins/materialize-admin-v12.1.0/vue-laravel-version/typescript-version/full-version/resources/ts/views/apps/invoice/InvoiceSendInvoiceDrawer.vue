<script setup lang="ts">
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'

interface SubmitData {
  emailFrom: string
  emailTo: string
  invoiceSubject: string
  paymentMessage: string
}
interface Emit {
  (e: 'update:isDrawerOpen', value: boolean): void
  (e: 'submit', value: SubmitData): void
}

interface Props {
  isDrawerOpen: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<Emit>()

const emailFrom = ref('shelbyComapny@email.com')
const emailTo = ref('qConsolidated@email.com')
const invoiceSubject = ref('Invoice of purchased Admin Templates')

const paymentMessage = ref(`Dear Queen Consolidated,

Thank you for your business, always a pleasure to work with you!

We have generated a new invoice in the amount of $95.59

We would appreciate payment of this invoice by 05/11/2019`)

const onSubmit = () => {
  emit('update:isDrawerOpen', false)
  emit('submit', {
    emailFrom: emailFrom.value,
    emailTo: emailTo.value,
    invoiceSubject: invoiceSubject.value,
    paymentMessage: paymentMessage.value,
  })
}

const handleDrawerModelValueUpdate = (val: boolean) => {
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
      title="Send Invoice"
      @cancel="$emit('update:isDrawerOpen', false)"
    />

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <VForm @submit.prevent="onSubmit">
            <VRow>
              <VCol cols="12">
                <VTextField
                  v-model="emailFrom"
                  label="From"
                  placeholder="sender@email.com"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="emailTo"
                  label="To"
                  placeholder="receiver@email.com"
                />
              </VCol>

              <VCol cols="12">
                <VTextField
                  v-model="invoiceSubject"
                  label="Subject"
                  placeholder="Invoice of purchased Admin Templates"
                />
              </VCol>

              <VCol cols="12">
                <VTextarea
                  v-model="paymentMessage"
                  rows="10"
                  label="Message"
                  placeholder="Thank you for your business, always a pleasure to work with you!"
                />
              </VCol>

              <VCol cols="12">
                <div class="mb-6">
                  <VChip
                    label
                    color="primary"
                    size="small"
                  >
                    <VIcon
                      start
                      icon="mdi-link"
                    />
                    Invoice Attached
                  </VChip>
                </div>
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Send
                </VBtn>

                <VBtn
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
