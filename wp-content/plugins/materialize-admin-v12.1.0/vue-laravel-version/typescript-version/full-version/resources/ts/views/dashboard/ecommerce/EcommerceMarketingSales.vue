<script setup lang="ts">
import { VIcon } from 'vuetify/components'
import accountLogo from '@images/cards/accounting-logo.png'
import marketingExpense from '@images/cards/marketing-expense-logo.png'
import salesOverview from '@images/cards/sales-overview-logo.png'

const websiteAnalytics = [
  {
    name: 'Marketing Expense',
    slideImg: marketingExpense,
    data: [
      {
        number: '5k',
        text: 'Operating',
      },
      {
        number: '6k',
        text: 'COGF',
      },
      {
        number: '2k',
        text: 'Financial',
      },
      {
        number: '1k',
        text: 'Expenses',
      },
    ],
  },
  {
    name: 'Accounting',
    slideImg: accountLogo,
    data: [
      {
        number: '18',
        text: 'Billing',
      },
      {
        number: '28',
        text: 'Sales',
      },
      {
        number: '30',
        text: 'Leads',
      },
      {
        number: '80',
        text: 'Impression',
      },
    ],
  },
  {
    name: 'Sales Overview',
    slideImg: salesOverview,
    data: [
      {
        number: '68',
        text: 'Open',
      },
      {
        number: '52',
        text: 'Converted',
      },
      {
        number: '04',
        text: 'Lost',
      },
      {
        number: '12',
        text: 'Quotations',
      },
    ],
  },
]
</script>

<template>
  <VCard>
    <VCarousel
      cycle
      :continuous="false"
      :show-arrows="false"
      hide-delimiter-background
      :delimiter-icon="() => h(VIcon, { icon: 'fa-circle', size: '6' })"
      height="auto"
      class="carousel-delimiter-top-end dots-active-primary"
    >
      <VCarouselItem
        v-for="item in websiteAnalytics"
        :key="item.name"
      >
        <VCardText>
          <VRow>
            <VCol cols="12">
              <h6 class="text-h6 mb-1">
                Marketing & Sales
              </h6>
              <p class="d-flex align-center gap-2 text-xs mb-0">
                <span>Total 245.8k Sales</span>
                <span class="text-success">
                  +25%
                  <VIcon icon="mdi-chevron-up" />
                </span>
              </p>
            </VCol>

            <VCol
              cols="12"
              class="d-flex align-center gap-4"
            >
              <img
                width="84"
                :src="item.slideImg"
                class="rounded"
              >

              <VRow no-gutters>
                <VCol
                  cols="12"
                  class="pb-0"
                >
                  <h6 class="text-base font-weight-medium mb-0">
                    {{ item.name }}
                  </h6>
                </VCol>

                <VCol
                  v-for="d in item.data"
                  :key="d.number"
                  cols="6"
                  class="text-no-wrap text-truncate text-xs pt-2"
                >
                  <VChip
                    label
                    size="small"
                    class="me-2"
                  >
                    {{ d.number }}
                  </VChip>
                  <span class="me-2">{{ d.text }}</span>
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12">
              <VBtn
                variant="tonal"
                size="small"
                class="me-3"
              >
                Details
              </VBtn>
              <VBtn size="small">
                Report
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
      </VCarouselItem>
    </VCarousel>
  </VCard>
</template>
