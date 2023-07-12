<script setup>
import logo3 from '@images/logos/logo-american-express.png'
import logo2 from '@images/logos/logo-mastercard.png'
import logo1 from '@images/logos/logo-visa.png'

const payments = [
  {
    lastDigits: '4399',
    method: 'Credit Card',
    date: '05/Jan',
    debitAmount: '2,820',
    balance: '10,450',
    logo: logo1,
  },
  {
    lastDigits: '5545',
    method: 'Debit Card',
    date: '12/Feb',
    debitAmount: '345',
    balance: '8,709',
    logo: logo2,
  },
  {
    lastDigits: '9860',
    method: 'ATM Card',
    date: '24/Feb',
    debitAmount: '999',
    balance: '25,900',
    logo: logo3,
  },
  {
    lastDigits: '4300',
    method: 'Credit Card',
    date: '08/Mar',
    debitAmount: '8,453',
    balance: '9,233',
    logo: logo1,
  },
  {
    lastDigits: '5545',
    method: 'Debit Card',
    date: '15/Apr',
    debitAmount: '24',
    balance: '500',
    logo: logo2,
  },
  {
    lastDigits: '4399',
    method: 'Credit Card',
    date: '28/Apr',
    debitAmount: '299',
    balance: '1,380',
    logo: logo1,
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
  <VCard title="Payment History">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VTable class="payment-history-table text-no-wrap">
      <thead>
        <tr>
          <th scope="col">
            <span class="text-sm text-medium-emphasis">Card</span>
          </th>
          <th
            scope="col"
            class="text-start text-sm"
          >
            <span class="text-sm text-medium-emphasis">Date</span>
          </th>
          <th
            scope="col"
            class="text-end text-sm"
          >
            <span class="text-sm text-medium-emphasis">Spend</span>
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="payment in payments"
          :key="payment.lastDigits"
        >
          <td>
            <div class="d-flex align-center">
              <div class="me-3">
                <div class="py-1 px-2 rounded bg-background">
                  <img
                    :src="payment.logo"
                    width="30"
                    alt="payment-card"
                  >
                </div>
              </div>

              <div class="d-flex flex-column">
                <p class="text-sm text-high-emphasis font-weight-medium mb-0">
                  *{{ payment.lastDigits }}
                </p>
                <span class="text-xs text-medium-emphasis">{{ payment.method }}</span>
              </div>
            </div>
          </td>

          <td>
            <span class="text-xs">{{ payment.date }}</span>
          </td>

          <td class="text-end">
            <div class="d-flex flex-column">
              <span class="font-weight-medium text-high-emphasis text-sm">-${{ payment.debitAmount }}</span>
              <span class="text-xs text-medium-emphasis">${{ payment.balance }}</span>
            </div>
          </td>
        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>

<style lang="scss">
.payment-history-table {
  tbody {
    td {
      block-size: 3.375rem !important;
      border-block-end: none !important;
    }
  }
}
</style>
