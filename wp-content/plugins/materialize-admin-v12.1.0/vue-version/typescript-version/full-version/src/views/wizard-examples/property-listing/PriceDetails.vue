<script setup lang="ts">
import type { PriceDetails } from './types'

const props = defineProps<{
  formData: PriceDetails
}>()

const emit = defineEmits<{
  (e: 'update:formData', value: PriceDetails): void
}>()

const formData = ref<PriceDetails>(props.formData)

watch(formData, () => {
  emit('update:formData', formData.value)
})
</script>

<template>
  <VForm>
    <VRow>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Expected Price  -->
        <VTextField
          v-model="formData.expectedPrice"
          label="Expected Price"
          type="number"
          append-inner-icon="mdi-currency-usd"
          placeholder="25,000"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Price Per SQFT  -->
        <VTextField
          v-model="formData.pricePerSqft"
          label="Price Per SQFT"
          append-inner-icon="mdi-currency-usd"
          type="number"
          placeholder="500"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Maintenance Charge  -->
        <VTextField
          v-model="formData.maintenanceCharge"
          label="Maintenance Charge"
          append-inner-icon="mdi-currency-usd"
          type="number"
          placeholder="50"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Maintenance Period  -->
        <VSelect
          v-model="formData.maintenancePeriod"
          label="Maintenance Period"
          placeholder="Select Maintenance Period"
          :items="['Monthly', 'Quarterly', 'Half Yearly', 'Yearly']"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Booking/Token Amount  -->
        <VTextField
          v-model="formData.bookingAmount"
          label="Booking/Token Amount"
          append-inner-icon="mdi-currency-usd"
          type="number"
          placeholder="250"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Other Amount  -->
        <VTextField
          v-model="formData.otherAmount"
          label="Other Amount"
          append-inner-icon="mdi-currency-usd"
          type="number"
          placeholder="500"
        />
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Show Price As  -->
        <VRadioGroup v-model="formData.priceDisplayType">
          <template #label>
            <div class="text-high-emphasis">
              Show Price As
            </div>
          </template>
          <VRadio
            label="Negotiable"
            value="Negotiable"
            class="ms-2"
          />
          <VRadio
            label="Call For Price"
            value="Call For Price"
            class="ms-2"
          />
        </VRadioGroup>
      </VCol>
      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Price Includes  -->
        <div class="text-high-emphasis mb-2 text-base">
          Price Includes
        </div>
        <VCheckbox
          v-model="formData.priceIncludes"
          label="Car Parking"
          value="Car Parking"
        />
        <VCheckbox
          v-model="formData.priceIncludes"
          label="Club Membership"
          value="Club Membership"
        />
      </VCol>
    </VRow>
  </VForm>
</template>
