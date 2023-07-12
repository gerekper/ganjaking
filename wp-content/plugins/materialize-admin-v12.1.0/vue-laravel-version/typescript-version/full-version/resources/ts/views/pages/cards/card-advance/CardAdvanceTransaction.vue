<script setup lang="ts">
import creditCard from '@images/cards/credit-card.png'
import masterCard from '@images/cards/mastercard.png'
import paypal from '@images/cards/paypal_primary.png'
import stripe from '@images/cards/stripe.png'
import wallet from '@images/cards/wallet.png'

const transactions = [
  {
    amount: -850,
    paymentMethod: 'Credit Card',
    description: 'Digital Ocean',
    icon: creditCard,
  },
  {
    paymentMethod: 'Paypal',
    amount: +34456,
    description: 'Received Money',
    icon: paypal,
  },
  {
    amount: -199,
    paymentMethod: 'Mastercard',
    description: 'Netflix',
    icon: masterCard,
  },
  {
    paymentMethod: 'Wallet',
    amount: -156,
    description: 'Mac\'D',
    icon: wallet,
  },
  {
    paymentMethod: 'Paypal',
    amount: +12872,
    description: 'Refund',
    icon: paypal,
  },
  {
    paymentMethod: 'Stripe',
    amount: -299,
    description: 'Buy Apple Watch',
    icon: stripe,
  },
]

const moreList = [
  { title: 'Last 28 Days', value: 'Last 28 Days', class: 'text-error' },
  { title: 'Last Month', value: 'Last Month' },
  { title: 'Last Year', value: 'Last Year' },
]

const resolveAvatarColor = (paymentMethod: string) => {
  if (paymentMethod === 'Stripe')
    return 'warning'

  if (paymentMethod === 'Paypal')
    return 'primary'

  if (paymentMethod === 'Wallet')
    return 'error'

  if (paymentMethod === 'Mastercard')
    return 'info'

  if (paymentMethod === 'Credit Card')
    return 'success'
}
</script>

<template>
  <VCard title="Transactions">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VList class="card-list">
        <VListItem
          v-for="item in transactions"
          :key="item.paymentMethod"
        >
          <template #prepend>
            <VAvatar
              rounded
              variant="tonal"
              :color="resolveAvatarColor(item.paymentMethod)"
              class="me-3"
            >
              <VImg
                :src="item.icon"
                height="18"
              />
            </VAvatar>
          </template>

          <VListItemTitle class="font-weight-medium text-sm">
            {{ item.paymentMethod }}
          </VListItemTitle>

          <VListItemSubtitle class="text-xs">
            {{ item.description }}
          </VListItemSubtitle>

          <template #append>
            <VListItemAction class="font-weight-medium">
              <span class="text-sm me-1">{{ item.amount > 0 ? `+$${Math.abs(item.amount)}` : `-$${Math.abs(item.amount)}` }}</span>
              <VIcon
                :size="24"
                :color="item.amount > 0 ? 'success' : 'error'"
                :icon="item.amount > 0 ? 'mdi-chevron-up' : 'mdi-chevron-down'"
              />
            </VListItemAction>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
  .card-list {
    --v-card-list-gap: 1.75rem;
  }
</style>
