<script setup>
const props = defineProps({
  formData: {
    type: null,
    required: true,
  },
})

const emit = defineEmits(['update:formData'])

const formData = ref(props.formData)

const offeredItems = [
  'Apple iPhone 12 Pro Max (256GB)',
  'Apple iPhone 12 Pro (512GB)',
  'Apple iPhone 11 Pro Max (256GB)',
  'Apple iPhone 11 (64GB)',
  'Apple iPhone 12 Mini (256GB)',
  'OnePlus Nord CE 56 (128GB)',
]

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
        <VTextField
          v-model="formData.title"
          label="Deal Title"
          placeholder="Black Friday Sale, 25% off"
        />
      </VCol>

      <VCol
        cols="12"
        sm
      >
        <VTextField
          v-model="formData.code"
          label="Deal Code"
          placeholder="BLACKFRIDAY25"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VTextarea
          v-model="formData.description"
          label="Deal Description"
          placeholder="To sale or distribute something as a business deal."
          rows="4"
          auto-grow
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <VRow>
          <VCol cols="12">
            <VSelect
              v-model="formData.offeredUItems"
              multiple
              chips
              label="Offered Items"
              placeholder="Select Offered Items"
              :items="offeredItems"
            />
          </VCol>

          <VCol cols="12">
            <VSelect
              v-model="formData.cartCondition"
              label="Cart Condition"
              placeholder="Select Cart Condition"
              :items="['Cart must contain all selected Downloads', 'Cart needs one or more of the selected Downloads']"
            />
          </VCol>
        </VRow>
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <AppDateTimePicker
          v-model="formData.dealDuration"
          label="Deal Duration"
          placeholder="Select Date"
          :config="{ mode: 'range' }"
        />
      </VCol>

      <VCol
        cols="12"
        sm="6"
      >
        <h6 class="text-sm font-weight-medium">
          Notify Users
        </h6>

        <div class="d-flex align-center flex-wrap gap-x-3">
          <VCheckbox
            v-model="formData.notification.email"
            label="Email"
            value="email"
          />
          <VCheckbox
            v-model="formData.notification.sms"
            label="SMS"
            value="sms"
          />
          <VCheckbox
            v-model="formData.notification.pushNotification"
            label="Push Notification"
            value="push-notification"
          />
        </div>
      </VCol>
    </VRow>
  </VForm>
</template>
