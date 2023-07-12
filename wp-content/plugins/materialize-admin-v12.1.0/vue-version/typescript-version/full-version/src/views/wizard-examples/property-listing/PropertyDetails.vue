<script setup lang="ts">
import type { PropertyDetails } from './types'
import type { CustomInputContent } from '@core/types'

const props = defineProps<{
  formData: PropertyDetails
}>()

const emit = defineEmits<{
  (e: 'update:formData', value: PropertyDetails): void
}>()

const radioContent: CustomInputContent[] = [
  {
    title: 'Sell the property',
    desc: 'Post your property for sale. Unlimited free listing.',
    icon: 'mdi-currency-usd',
    value: 'sell',
  },
  {
    title: 'Rent the property',
    desc: 'Post your property for rent. Unlimited free listing.',
    icon: 'mdi-bank',
    value: 'rent',
  },
]

const formData = ref<PropertyDetails>(props.formData)

watch(formData, () => {
  emit('update:formData', formData.value)
})
</script>

<template>
  <VForm>
    <VRow>
      <VCol cols="12">
        <!-- 👉 Property Deal Type  -->
        <CustomRadiosWithIcon
          v-model:selected-radio="formData.propertyDealType"
          :radio-content="radioContent"
          :grid-column="{ cols: '12', sm: '6' }"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Property Type -->
        <VSelect
          v-model="formData.propertyType"
          label="Property type"
          placeholder="Select Property Type"
          :items="['Residential', 'Commercial']"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Zip Code -->
        <VTextField
          v-model="formData.zipCode"
          label="Zip Code"
          type="number"
          placeholder="123456"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Country -->
        <VSelect
          v-model="formData.country"
          label="Country"
          placeholder="Select Country"
          :items="['India', 'UK', 'USA', 'AUS', 'Germany']"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 State -->
        <VTextField
          v-model="formData.state"
          label="State"
          placeholder="California"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 City -->
        <VTextField
          v-model="formData.city"
          label="City"
          placeholder="Los Angeles"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <!-- 👉 Landmark -->
        <VTextField
          v-model="formData.landmark"
          label="Landmark"
          placeholder="Near to bus stop"
        />
      </VCol>

      <VCol>
        <!-- 👉 Address -->
        <VTextarea
          v-model="formData.address"
          label="Address"
          placeholder="112, 1st Cross, 1st Stage, 1st Phase, BTM Layout, Bangalore - 560068"
          rows="2"
        />
      </VCol>
    </VRow>
  </VForm>
</template>
