<script setup>
const props = defineProps({
  formData: {
    type: null,
    required: true,
  },
})

const emit = defineEmits(['update:formData'])

const formData = ref(props.formData)

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
        <VSelect
          v-model="formData.userType"
          label="User Type"
          placeholder="Select User Type"
          :items="['All', 'Registered', 'Unregistered', 'Prime Member']"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VTextField
          v-model="formData.maxUsers"
          label="Max Users"
          placeholder="500"
          type="number"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VTextField
          v-model="formData.cartAmount"
          label="Minimum Cart Amount"
          placeholder="$99"
          type="number"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VTextField
          v-model="formData.promotionFree"
          label="Promotion Fee"
          placeholder="$10"
          type="number"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VSelect
          v-model="formData.paymentMethod"
          label="Payment Method"
          placeholder="Select Payment Method"
          :items="['Any', 'Credit Card', 'Net Banking', 'Wallet']"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VSelect
          v-model="formData.dealStatus"
          label="Deal Status"
          :items="['Active', 'Inactive', 'Suspended', 'Abandoned']"
          placeholder="Select Deal Status"
        />
      </VCol>

      <VCol cols="12">
        <VSwitch
          v-model="formData.isSingleUserCustomer"
          label="Limit this discount to a single-use per customer?"
        />
      </VCol>
    </VRow>
  </VForm>
</template>
