<script setup lang="ts">
import PersonalDetails from '@/views/wizard-examples/property-listing/PersonalDetails.vue'
import PriceDetails from '@/views/wizard-examples/property-listing/PriceDetails.vue'
import PropertyArea from '@/views/wizard-examples/property-listing/PropertyArea.vue'
import PropertyDetails from '@/views/wizard-examples/property-listing/PropertyDetails.vue'
import PropertyFeatures from '@/views/wizard-examples/property-listing/PropertyFeatures.vue'

import type { PropertyListingData } from '@/views/wizard-examples/property-listing/types'

const propertyListingSteps = [
  {
    title: 'Personal Details',
    subtitle: 'Your Name/Email',
  },
  {
    title: 'Property Details',
    subtitle: 'Property Type',
  },
  {
    title: 'Property Features',
    subtitle: 'Bedrooms/Floor No',
  },
  {
    title: 'Property Area',
    subtitle: 'covered Area',
  },
  {
    title: 'Price Details',
    subtitle: 'Expected Price',
  },
]

const propertyListingData = ref<PropertyListingData>({
  personalDetails: {
    userType: 'builder',
    firstName: '',
    lastName: '',
    username: '',
    password: '',
    email: '',
    contact: null,
  },
  priceDetails: {
    expectedPrice: null,
    pricePerSqft: null,
    maintenanceCharge: null,
    maintenancePeriod: null,
    bookingAmount: null,
    otherAmount: null,
    priceDisplayType: 'Negotiable',
    priceIncludes: ['Car Parking'],
  },
  propertyFeatures: {
    bedroomCount: '',
    floorNo: '',
    bathroomCount: '',
    isCommonArea: true,
    furnishedStatus: null,
    furnishingDetails: ['AC', 'TV', 'Fridge'],
    isCommonArea1: 'true',
    isCommonArea2: 'false',
  },
  propertyArea: {
    totalArea: null,
    carpetArea: null,
    plotArea: null,
    availableFrom: null,
    possessionStatus: 'Under Construciton',
    transactionType: 'New Property',
    isOnMainRoad: 'No',
    isGatedColony: 'No',
  },
  propertyDetails: {
    propertyDealType: 'sell',
    propertyType: null,
    zipCode: null,
    country: null,
    state: '',
    city: '',
    landmark: '',
    address: '',
  },
})

const currentStep = ref(0)

const onSubmit = () => {
  console.log('propertyListingData :>> ', propertyListingData.value)
}
</script>

<template>
  <VCard>
    <VRow no-gutters>
      <VCol
        cols="12"
        md="4"
        :class="$vuetify.display.smAndDown ? 'border-b' : 'border-e'"
      >
        <VCardText>
          <AppStepper
            v-model:current-step="currentStep"
            :items="propertyListingSteps"
            direction="vertical"
          />
        </VCardText>
      </VCol>

      <VCol
        cols="12"
        md="8"
      >
        <VCardText class="pa-6">
          <VWindow
            v-model="currentStep"
            class="disable-tab-transition"
          >
            <VWindowItem>
              <PersonalDetails v-model:form-data="propertyListingData.personalDetails" />
            </VWindowItem>

            <VWindowItem>
              <PropertyDetails v-model:form-data="propertyListingData.propertyDetails" />
            </VWindowItem>

            <VWindowItem>
              <PropertyFeatures v-model:form-data="propertyListingData.propertyFeatures" />
            </VWindowItem>

            <VWindowItem>
              <PropertyArea v-model:form-data="propertyListingData.propertyArea" />
            </VWindowItem>

            <VWindowItem>
              <PriceDetails v-model:form-data="propertyListingData.priceDetails" />
            </VWindowItem>
          </VWindow>

          <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-8">
            <VBtn
              :color="currentStep === 0 ? 'secondary' : 'default'"
              variant="outlined"
              :disabled="currentStep === 0"
              @click="currentStep--"
            >
              <VIcon
                icon="mdi-arrow-left"
                start
                class="flip-in-rtl"
              />
              Previous
            </VBtn>

            <VBtn
              v-if="propertyListingSteps.length - 1 === currentStep"
              color="success"
              append-icon="mdi-check"
              @click="onSubmit"
            >
              submit
            </VBtn>

            <VBtn
              v-else
              @click="currentStep++"
            >
              Next

              <VIcon
                icon="mdi-arrow-right"
                end
                class="flip-in-rtl"
              />
            </VBtn>
          </div>
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>
