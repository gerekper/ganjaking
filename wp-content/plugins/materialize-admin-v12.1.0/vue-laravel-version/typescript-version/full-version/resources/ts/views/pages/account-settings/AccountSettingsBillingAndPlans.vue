<script lang="ts" setup>
import BillingHistoryTable from './BillingHistoryTable.vue'

// Images
import mastercard from '@images/icons/payments/mastercard.png'
import visa from '@images/icons/payments/visa.png'

interface CardDetails {
  name: string
  number: string
  expiry: string
  isPopular: boolean
  type: string
  cvv: string
  image: string
}
const selectedPaymentMethod = ref('credit-debit-atm-card')

const isPricingPlanDialogVisible = ref(false)
const isConfirmDialogVisible = ref(false)
const isCardEditDialogVisible = ref(false)
const isCardDetailSaveBilling = ref(false)

const creditCards: CardDetails[] = [
  {
    name: 'Tom McBride',
    number: '5577 0000 5577 9865',
    expiry: '12/24',
    isPopular: true,
    type: 'mastercard',
    cvv: '587',
    image: mastercard,
  },
  {
    name: 'Mildred Wagner',
    number: '4532 3616 2070 5678',
    expiry: '02/24',
    isPopular: false,
    type: 'visa',
    cvv: '681',
    image: visa,
  },
]

const countryList = ['United States', 'Canada', 'United Kingdom', 'Australia', 'New Zealand', 'India', 'Russia', 'China', 'Japan']

const currentCardDetails = ref()

const openEditCardDialog = (cardDetails: CardDetails) => {
  currentCardDetails.value = cardDetails

  isCardEditDialogVisible.value = true
}

const cardNumber = ref(135632156548789)
const cardName = ref('john Doe')
const cardExpiryDate = ref('05/24')
const cardCvv = ref(420)

const resetPaymentForm = () => {
  cardNumber.value = 135632156548789
  cardName.value = 'john Doe'
  cardExpiryDate.value = '05/24'
  cardCvv.value = 420

  selectedPaymentMethod.value = 'credit-debit-atm-card'
}
</script>

<template>
  <VRow>
    <!-- 👉 Current Plan -->
    <VCol cols="12">
      <VCard title="Current Plan">
        <VCardText>
          <VRow>
            <VCol
              cols="12"
              md="6"
            >
              <div>
                <div class="mb-6">
                  <h3 class="text-base font-weight-medium mb-1">
                    Your Current Plan is Basic
                  </h3>
                  <p class="text-base">
                    A simple start for everyone
                  </p>
                </div>

                <div class="mb-6">
                  <h3 class="text-base font-weight-medium mb-1">
                    Active until Dec 09, 2021
                  </h3>
                  <p class="text-base">
                    We will send you a notification upon Subscription expiration
                  </p>
                </div>

                <div>
                  <h3 class="text-base font-weight-medium mb-1">
                    <span class="me-3">$199 Per Month</span>
                    <VChip
                      color="primary"
                      size="x-small"
                    >
                      Popular
                    </VChip>
                  </h3>
                  <p class="text-base mb-0">
                    Standard plan for small to medium businesses
                  </p>
                </div>
              </div>
            </VCol>

            <VCol
              cols="12"
              md="6"
            >
              <VAlert
                color="warning"
                variant="tonal"
              >
                <div class="d-flex align-center gap-3 mb-1">
                  <div>
                    <VIcon
                      icon="mdi-alert-outline"
                      size="22"
                    />
                  </div>
                  <VAlertTitle>
                    We need your attention!
                  </VAlertTitle>
                </div>

                <span class="ms-sm-9 ms-xs-0">Your plan requires update</span>
              </VAlert>

              <!-- progress -->
              <div class="d-flex font-weight-medium text-sm text-high-emphasis mt-8 mb-2">
                <span>Days</span>
                <VSpacer />
                <span>24 of 30 Days</span>
              </div>
              <VProgressLinear
                color="primary"
                rounded
                height="8"
                model-value="75"
              />
              <p class="text-sm mt-2">
                6 days remaining until your plan requires update
              </p>
            </VCol>

            <VCol cols="12">
              <div class="d-flex flex-wrap gap-y-4">
                <VBtn
                  class="me-3"
                  @click="isPricingPlanDialogVisible = true"
                >
                  upgrade plan
                </VBtn>

                <VBtn
                  color="secondary"
                  variant="outlined"
                  @click="isConfirmDialogVisible = true"
                >
                  Cancel Subscription
                </VBtn>
              </div>
            </VCol>
          </VRow>

          <!-- 👉 Confirm Dialog -->
          <ConfirmDialog
            v-model:isDialogVisible="isConfirmDialogVisible"
            confirmation-question="Are you sure to cancel your subscription?"
            cancel-msg="Unsubscription Cancelled!!"
            cancel-title="Cancelled"
            confirm-msg="Your subscription cancelled successfully."
            confirm-title="Unsubscribed!"
          />

          <!-- 👉 plan and pricing dialog -->
          <PricingPlanDialog v-model:is-dialog-visible="isPricingPlanDialogVisible" />
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Payment Methods -->
    <VCol cols="12">
      <VCard title="Payment Methods">
        <VCardText>
          <VForm @submit.prevent="() => {}">
            <VRow>
              <VCol
                cols="12"
                md="6"
              >
                <VRow>
                  <!-- 👉 card type switch -->
                  <VCol cols="12">
                    <VRadioGroup
                      v-model="selectedPaymentMethod"
                      inline
                    >
                      <VRadio
                        value="credit-debit-atm-card"
                        label="Credit/Debit/ATM Card"
                        color="primary"
                      />
                      <VRadio
                        value="cod-cheque"
                        label="COD/Cheque"
                        color="primary"
                      />
                    </VRadioGroup>
                  </VCol>

                  <VCol cols="12">
                    <VRow v-show="selectedPaymentMethod === 'credit-debit-atm-card'">
                      <!-- 👉 Card Number -->
                      <VCol cols="12">
                        <VTextField
                          v-model="cardNumber"
                          label="Card Number"
                          placeholder="1234 1234 1234 1234"
                          type="number"
                        />
                      </VCol>

                      <!-- 👉 Name -->
                      <VCol
                        cols="12"
                        md="6"
                      >
                        <VTextField
                          v-model="cardName"
                          label="Name"
                          placeholder="John Doe"
                        />
                      </VCol>

                      <!-- 👉 Expiry date -->
                      <VCol
                        cols="6"
                        md="3"
                      >
                        <VTextField
                          v-model="cardExpiryDate"
                          label="Expiry Date"
                          placeholder="MM/YY"
                        />
                      </VCol>

                      <!-- 👉 Cvv code -->
                      <VCol
                        cols="6"
                        md="3"
                      >
                        <VTextField
                          v-model="cardCvv"
                          type="number"
                          label="CVV Code"
                          placeholder="123"
                        />
                      </VCol>

                      <!-- 👉 Future Billing switch -->
                      <VCol cols="12">
                        <VSwitch
                          v-model="isCardDetailSaveBilling"
                          density="compact"
                          label="Save card for future billing?"
                        />
                      </VCol>
                    </VRow>

                    <p
                      v-show="selectedPaymentMethod === 'cod-cheque'"
                      class="text-base"
                    >
                      Cash on delivery is a mode of payment where you make the payment after the goods/services are received.
                    </p>
                    <p
                      v-show="selectedPaymentMethod === 'cod-cheque'"
                      class="text-base"
                    >
                      You can pay cash or make the payment via debit/credit card directly to the delivery person.
                    </p>
                  </VCol>
                </VRow>
              </VCol>

              <!-- 👉 Saved Cards -->
              <VCol
                cols="12"
                md="6"
              >
                <h6 class="text-base font-weight-medium mb-6">
                  My Cards
                </h6>

                <div class="d-flex flex-column gap-y-4">
                  <VCard
                    v-for="card in creditCards"
                    :key="card.name"
                    class="bg-var-theme-background"
                    flat
                  >
                    <VCardText class="d-flex flex-sm-row flex-column justify-space-between">
                      <div class="text-no-wrap">
                        <VImg
                          :src="card.image"
                          width="46"
                        />
                        <h3 class="my-3 text-base">
                          <span class="me-4 font-weight-medium">{{ card.name }}</span>

                          <VChip
                            v-if="card.isPopular"
                            label
                            color="primary"
                            size="small"
                          >
                            Popular
                          </VChip>
                        </h3>
                        <span class="text-base">**** **** **** {{ card.number.substring(card.number.length - 4) }}</span>
                      </div>

                      <div class="d-flex flex-column text-sm-end">
                        <div class="d-flex flex-wrap gap-4 order-sm-0 order-1">
                          <VBtn
                            variant="outlined"
                            @click="openEditCardDialog(card)"
                          >
                            Edit
                          </VBtn>
                          <VBtn
                            color="secondary"
                            variant="outlined"
                          >
                            Delete
                          </VBtn>
                        </div>
                        <span class="mt-sm-auto mb-sm-0 my-5 order-sm-1 order-0">Card expires at {{ card.expiry }}</span>
                      </div>
                    </VCardText>
                  </VCard>
                </div>

                <!-- 👉 Add Edit Card Dialog -->
                <CardAddEditDialog
                  v-model:isDialogVisible="isCardEditDialogVisible"
                  :card-details="currentCardDetails"
                />
              </VCol>

              <!-- 👉 Payment method action button -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn type="submit">
                  Save changes
                </VBtn>
                <VBtn
                  color="secondary"
                  variant="outlined"
                  @click="resetPaymentForm"
                >
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Billing Address -->
    <VCol cols="12">
      <VCard title="Billing Address">
        <VCardText>
          <VForm @submit.prevent="() => {}">
            <VRow>
              <!-- 👉 Company name -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  label="Company Name"
                  placeholder="Pixinvent"
                />
              </VCol>

              <!-- 👉 Billing Email -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  label="Billing Email"
                  placeholder="pixinvent@email.com"
                />
              </VCol>

              <!-- 👉 Tax ID -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  label="Tax ID"
                  placeholder="123 123 1233"
                />
              </VCol>

              <!-- 👉 Vat Number -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  label="VAT Number"
                  placeholder="121212"
                />
              </VCol>

              <!-- 👉 Mobile -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
                  dirty
                  label="Phone Number"
                  type="number"
                  prefix="US (+1)"
                  placeholder="+1 123 456 7890"
                />
              </VCol>

              <!-- 👉 Country -->
              <VCol
                cols="12"
                md="6"
              >
                <VSelect
                  label="Country"
                  :items="countryList"
                  placeholder="Select Country"
                />
              </VCol>

              <!-- 👉 Billing Address -->
              <VCol cols="12">
                <VTextField
                  label="Billing Address"
                  placeholder="1234 Main St"
                />
              </VCol>

              <!-- 👉 State -->
              <VCol
                cols="12"
                md="6"
              >
                <VTextField
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
                  label="Zip Code"
                  type="number"
                  placeholder="100006"
                />
              </VCol>

              <!-- 👉 Actions Button -->
              <VCol
                cols="12"
                class="d-flex flex-wrap gap-4"
              >
                <VBtn type="submit">
                  Save changes
                </VBtn>
                <VBtn
                  type="reset"
                  color="secondary"
                  variant="outlined"
                >
                  Reset
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Billing History -->
    <VCol cols="12">
      <BillingHistoryTable />
    </VCol>
  </VRow>
</template>

<style lang="scss">
.pricing-dialog {
  .pricing-title {
    font-size: 24px !important;
  }

  .v-card {
    border: 0.0625rem solid rgba(var(--v-border-color), var(--v-border-opacity));
    box-shadow: none;
  }
}
</style>
