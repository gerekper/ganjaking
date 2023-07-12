<script setup lang="ts">
import illustration1 from '@images/misc/pricing-cta-illustration.png'

const features = [
  {
    feature: '14-days free trial',
    basic: true,
    standard: true,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'No user limit',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Product Support',
    basic: false,
    standard: true,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Email Support',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: true,
      enterprise: false,
    },
  },
  {
    feature: 'Integrations',
    basic: false,
    standard: true,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Removal of Front branding',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: true,
      enterprise: false,
    },
  },
  {
    feature: 'Active maintenance & support',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
  {
    feature: 'Data storage for 365 days',
    basic: false,
    standard: false,
    enterprise: true,
    addOnAvailable: {
      starter: false,
      pro: false,
      enterprise: false,
    },
  },
]

const faqs = [
  {
    question: 'General settings',
    answer: 'Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.Donec placerat, lectus sed mattis semper, neque lectus feugiat lectus, varius pulvinar diam eros in elit. Pellentesque convallis laoreet laoreet.',
  },
  {
    question: 'Users',
    answer: 'We accept Visa®, MasterCard®, American Express®, and PayPal®. So you can be confident that your credit card information will be kept safe and secure.',
  },
  {
    question: 'Personal data',
    answer: '2Checkout accepts all types of credit and debit cards.',
  },
  {
    question: 'Advanced settings',
    answer: 'Yes. You may request a refund within 30 days of your purchase without any additional explanations.',
  },
]
</script>

<template>
  <VCard class="pt-6">
    <VCardText class="pt-12 mb-16 pb-16">
      <!-- 👉 App Pricing components -->
      <VRow>
        <VCol
          cols="12"
          sm="8"
          md="12"
          lg="10"
          class="mx-auto"
        >
          <AppPricing md="4" />
        </VCol>
      </VRow>
    </VCardText>

    <!-- 👉 Free trial Banner -->
    <VRow class="page-pricing-free-trial-banner-bg">
      <VCol
        cols="12"
        md="10"
        class="d-flex align-center flex-md-row flex-column position-relative mx-auto"
      >
        <div class="text-center text-md-start py-10 px-10 px-sm-0">
          <h3 class="text-h5 text-primary mb-2">
            Still not convinced? Start with a 14-day FREE trial!
          </h3>
          <p class="text-sm">
            You will get full access to all the features for 14 days.
          </p>

          <VBtn class="mt-4">
            Start-14-day FREE trial
          </VBtn>
        </div>

        <div class="free-trial-illustrator">
          <VImg
            :src="illustration1"
            :width="219"
          />
        </div>
      </VCol>
    </VRow>

    <!-- 👉 Plans -->
    <VCardText class="text-center mt-16">
      <h4 class="text-h5 mb-6">
        Pick a plan that works best for you
      </h4>
      <p class="mb-0">
        Stay cool, we have a 48-hour money back guarantee!
      </p>
    </VCardText>

    <!-- 👉 Features & Tables -->
    <VCardText class="mb-16 mt-2">
      <VRow>
        <VCol
          cols="12"
          md="10"
          class="mx-auto"
        >
          <VTable class="text-no-wrap border rounded pricing-table">
            <!-- 👉 Table head -->
            <thead style="background-color: rgb(var(--v-theme-grey-200));">
              <tr>
                <th
                  scope="col"
                  class="py-4"
                >
                  <span class="text-sm">TYPE</span>
                </th>

                <th
                  scope="col"
                  class="text-center py-4"
                >
                  <h6 class="text-xs font-weight-medium mb-1">
                    STARTER
                  </h6>
                  <h6 class="font-weight-regular text-xs">
                    Free
                  </h6>
                </th>

                <th
                  scope="col"
                  class="text-center py-4"
                >
                  <h6 class="text-xs font-weight-medium mb-1">
                    PRO

                    <VIcon
                      size="24"
                      icon="mdi-star-circle"
                      color="primary"
                      class="mt-n4"
                    />
                  </h6>

                  <h6 class="font-weight-regular text-xs">
                    $7.5/MONTH
                  </h6>
                </th>

                <th
                  scope="col"
                  class="text-center py-4"
                >
                  <h6 class="text-xs font-weight-medium mb-1">
                    ENTERPRISE
                  </h6>
                  <h6 class="font-weight-regular text-xs">
                    $16/MONTH
                  </h6>
                </th>
              </tr>
            </thead>

            <!-- 👉 Table Body -->
            <tbody>
              <tr
                v-for="feature in features"
                :key="feature.feature"
              >
                <td>{{ feature.feature }}</td>
                <td class="text-center">
                  <VIcon
                    v-if="!feature.addOnAvailable.starter"
                    size="20"
                    :color="feature.basic ? 'primary' : 'disabled'"
                    :icon="feature.basic ? 'mdi-checkbox-marked-circle' : 'mdi-close-circle'"
                  />

                  <VChip
                    v-if="feature.addOnAvailable.starter"
                    color="primary"
                    size="small"
                  >
                    Add-on Availbale
                  </VChip>
                </td>

                <td class="text-center">
                  <VIcon
                    v-if="!feature.addOnAvailable.pro"
                    size="20"
                    :color="feature.standard ? 'primary' : 'disabled'"
                    :icon="feature.standard ? 'mdi-checkbox-marked-circle' : 'mdi-close-circle'"
                  />

                  <VChip
                    v-if="feature.addOnAvailable.pro"
                    color="primary"
                    size="small"
                  >
                    Add-on Available
                  </VChip>
                </td>

                <td class="text-center">
                  <VIcon
                    v-if="!feature.addOnAvailable.enterprise"
                    size="20"
                    :color="feature.enterprise ? 'primary' : 'disabled'"
                    :icon="feature.enterprise ? 'mdi-checkbox-marked-circle' : 'mdi-close-circle'"
                  />

                  <VChip
                    v-if="feature.addOnAvailable.enterprise"
                    color="primary"
                    size="small"
                  >
                    ADD-ON AVAILABLE
                  </VChip>
                </td>
              </tr>
            </tbody>

            <!-- 👉 Table footer -->
            <tfoot>
              <tr>
                <td class="py-4">
                  Data storage for 365 days
                </td>

                <td class="text-center py-4">
                  <VBtn variant="outlined">
                    Choose Plan
                  </VBtn>
                </td>
                <td class="text-center py-4">
                  <VBtn>
                    Choose Plan
                  </VBtn>
                </td>
                <td class="text-center py-4">
                  <VBtn variant="outlined">
                    Choose Plan
                  </VBtn>
                </td>
              </tr>
            </tfoot>
          </VTable>
        </VCol>
      </VRow>
    </VCardText>

    <!-- 👉 FAQ -->
    <div>
      <VCardText class="bg-var-theme-background py-10 py-sm-16">
        <div class="text-center">
          <h5 class="text-h5 mb-2 mt-4">
            FAQ's
          </h5>
          <p class="mb-6">
            Let us help answer the most common questions.
          </p>
        </div>

        <div class="pt-sm-6 pb-sm-4 px-sm-16">
          <VExpansionPanels>
            <VExpansionPanel
              v-for="faq in faqs"
              :key="faq.question"
              :title="faq.question"
              :text="faq.answer"
            />
          </VExpansionPanels>
        </div>
      </VCardText>
    </div>
  </VCard>
</template>

<style lang="scss">
.page-pricing-free-trial-banner-bg {
  /* stylelint-disable-next-line color-function-notation */
  background-color: rgba(var(--v-theme-primary), var(--v-idle-opacity));
}

@media screen and (min-width: 960px) {
  .free-trial-illustrator {
    position: absolute;
    inset-block-end: 0;
    inset-inline-end: 1%;
  }
}

@media screen and (max-width: 959px) {
  .free-trial-illustrator {
    position: relative;
    inset-block-end: -11px;
  }
}

.pricing-table {
  tbody {
    tr:nth-child(even) {
      background-color: rgb(var(--v-theme-grey-200));
    }
  }
}
</style>
