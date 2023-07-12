<script setup>
import logoCreditCard2 from '@images/cards/logo-credit-card-2.png'
import logoMastercardSmall from '@images/cards/logo-mastercard-small.png'

const savedCards = [
  {
    logo: logoMastercardSmall,
    type: 'Credit card',
    number: '2566 xxxx xxxx 8908',
  },
  {
    logo: logoCreditCard2,
    type: 'Credit card',
    number: '8990 xxxx xxxx 6852',
  },
]

const moreList = [
  {
    title: 'Last 28 Days',
    value: 'Last 28 Days',
  },
  {
    title: 'Last Month',
    value: 'Last Month',
  },
  {
    title: 'Last Year',
    value: 'Last Year',
  },
]
</script>

<template>
  <div>
    <VCard title="Upgrade Your Plan">
      <template #append>
        <div class="me-n3 mt-n2">
          <MoreBtn :menu-list="moreList" />
        </div>
      </template>

      <VCardText>
        <p class="text-xs">
          Please make the payment to start enjoying all the features of our premium plan as soon as possible.
        </p>

        <!-- SECTION upgrade plan banner -->
        <div class="plan-upgrade-banner d-flex bg-light-primary rounded align-center pa-3">
          <VAvatar
            rounded
            color="primary"
            variant="text"
            class="plan-details me-3"
          >
            <VIcon icon="mdi-star-outline" />
          </VAvatar>

          <div class="d-flex flex-column align-start">
            <h3 class="text-base font-weight-medium">
              Platinum
            </h3>
            <span class="text-primary text-xs">Upgrade Plan</span>
          </div>

          <VSpacer />

          <div class="d-flex align-center">
            <sup>
              <h6 class="text-xs font-weight-regular">$</h6>
            </sup>
            <span class="text-h5">2199</span>
            <sub>
              <h6 class="text-sm font-weight-regular">/Year</h6>
            </sub>
          </div>
        </div>
        <!-- !SECTION -->

        <VList class="card-list">
          <div class="font-weight-medium text-high-emphasis my-3">
            Payment Details
          </div>

          <VListItem
            v-for="card in savedCards"
            :key="card.number"
            class="mb-3"
          >
            <!-- 👉 avatar -->
            <template #prepend>
              <VAvatar
                rounded
                class="me-3"
              >
                <VImg :src="card.logo" />
              </VAvatar>
            </template>

            <!-- 👉 Title and Subtitle -->
            <VListItemTitle class="text-sm font-weight-medium ">
              {{ card.type }}
            </VListItemTitle>

            <VListItemSubtitle class="text-xs">
              {{ card.number }}
            </VListItemSubtitle>

            <!-- 👉 Action -->
            <template #append>
              <VTextField
                label="CVV"
                type="number"
                density="compact"
                placeholder="123"
                style="inline-size: 5rem;"
              />
            </template>
          </VListItem>

          <!-- 👉 Add Payment  -->
          <VListItem class="mb-3">
            <a
              href="javascript:void(0)"
              class="text-xs"
            >Add Payment Method</a>
          </VListItem>
        </VList>

        <!-- 👉 Email form  -->
        <VForm @submit.prevent>
          <VTextField
            label="Email Address"
            variant="outlined"
            placeholder="john.doe@email.com"
            density="compact"
            class="mb-3"
          />

          <VBtn
            block
            type="submit"
          >
            Proceed to payment
            <VIcon
              icon="mdi-arrow-right"
              size="24"
              class="ms-2 flip-in-rtl"
            />
          </VBtn>
        </VForm>
      </VCardText>
    </VCard>
  </div>
</template>

<style lang="scss" scoped>
.plan-upgrade-banner {
  .plan-details {
    border: 2px solid rgb(var(--v-theme-primary));
  }
}

.card-list {
  --v-card-list-gap: 0;
}
</style>
