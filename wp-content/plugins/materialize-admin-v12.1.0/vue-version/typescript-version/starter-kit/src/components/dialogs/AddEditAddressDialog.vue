<script setup lang="ts">
interface BillingAddress {
  companyName: string
  billingEmail: string
  taxID: string
  vatNumber: string
  address: string
  contact: string
  country: string | null
  state: string
  zipCode: number | null
  isSaveDefaultAddress: boolean
}
interface Props {
  billingAddress?: BillingAddress
  isDialogVisible: boolean
}
interface Emit {
  (e: 'update:isDialogVisible', value: boolean): void
  (e: 'submit', value: BillingAddress): void
}

const props = withDefaults(defineProps<Props>(), {
  billingAddress: () => ({
    companyName: '',
    billingEmail: '',
    taxID: '',
    vatNumber: '',
    address: '',
    contact: '',
    country: null,
    state: '',
    zipCode: null,
    isSaveDefaultAddress: true,
  }),
})

const emit = defineEmits<Emit>()

const billingAddress = ref<BillingAddress>(structuredClone(toRaw(props.billingAddress)))

const resetForm = () => {
  emit('update:isDialogVisible', false)
  billingAddress.value = structuredClone(toRaw(props.billingAddress))
}

const onFormSubmit = () => {
  emit('update:isDialogVisible', false)
  emit('submit', billingAddress.value)
}

const selectedAddress = ref('Home')

const addressTypes = [
  {
    icon: 'mdi-home-outline',
    title: 'Home',
    time: 'Delivery Time (7am - 9pm)',
  },
  {
    icon: 'mdi-briefcase-outline',
    title: 'Office',
    time: 'Delivery Time (10am - 6pm)',
  },
]
</script>

<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 800"
    :model-value="props.isDialogVisible"
    @update:model-value="val => $emit('update:isDialogVisible', val)"
  >
    <VCard
      v-if="props.billingAddress"
      class="pa-sm-8 pa-5"
    >
      <!-- 👉 dialog close btn -->
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="resetForm"
      />

      <!-- 👉 Title -->
      <VCardItem>
        <VCardTitle class="text-h5 text-center">
          {{ props.billingAddress.address ? 'Edit' : 'Add New' }} Address
        </VCardTitle>
      </VCardItem>

      <VCardText class="pt-3">
        <!-- 👉 Subtitle -->
        <VCardSubtitle class="text-center mb-8">
          Edit Address for future billing
        </VCardSubtitle>

        <VRow>
          <VCol
            v-for="type in addressTypes"
            :key="type.title"
            cols="12"
            sm="6"
          >
            <div
              class="custom-address-input border rounded cursor-pointer pa-4 border-opacity-100"
              :class="selectedAddress === type.title ? 'bg-light-primary text-primary border-primary' : 'bg-var-theme-background border-secondary text-high-emphasis'"

              @click="selectedAddress = type.title"
            >
              <div class="d-flex align-center font-weight-medium gap-2 text-xl mb-1">
                <VIcon
                  size="24"
                  :icon="type.icon"
                />
                <span>{{ type.title }}</span>
              </div>
              <span>{{ type.time }}</span>
            </div>
          </VCol>
        </VRow>

        <!-- 👉 Form -->
        <VForm
          class="mt-4"
          @submit.prevent="onFormSubmit"
        >
          <VRow>
            <!-- 👉 Company Name -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.companyName"
                label="Company Name"
                placeholder="Pixinvent"
              />
            </VCol>

            <!-- 👉 Email -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.billingEmail"
                label="Email"
                placeholder="john@emaill.com"
              />
            </VCol>

            <!-- 👉 Tax ID -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.taxID"
                label="Tax ID"
                placeholder="123 345 32"
              />
            </VCol>

            <!-- 👉 VAT Number -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.vatNumber"
                label="VAT Number"
                placeholder="123 12 1223"
              />
            </VCol>

            <!-- 👉 Billing Address -->
            <VCol cols="12">
              <VTextarea
                v-model="billingAddress.address"
                rows="2"
                label="Billing Address"
                placeholder="1, Pixinvent Street, USA"
              />
            </VCol>

            <!-- 👉 Contact -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.contact"
                label="Contact"
                placeholder="+1 23 456 7890"
              />
            </VCol>

            <!-- 👉 Country -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.country"
                label="Country"
                placeholder="USA"
              />
            </VCol>

            <!-- 👉 State -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.state"
                label="State"
                placeholder="New York"
              />
            </VCol>

            <!-- 👉 Zip Code -->
            <VCol
              cols="12"
              md="6"
            >
              <VTextField
                v-model="billingAddress.zipCode"
                label="Zip Code"
                placeholder="123123"
                type="number"
              />
            </VCol>

            <!-- 👉 default address -->
            <VCol cols="12">
              <VSwitch
                v-model="billingAddress.isSaveDefaultAddress"
                label="Make this default shipping address"
              />
            </VCol>

            <!-- 👉 Submit and Cancel button -->
            <VCol
              cols="12"
              class="text-center"
            >
              <VBtn
                type="submit"
                class="me-3"
              >
                submit
              </VBtn>

              <VBtn
                variant="outlined"
                color="secondary"
                @click="resetForm"
              >
                Cancel
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>
