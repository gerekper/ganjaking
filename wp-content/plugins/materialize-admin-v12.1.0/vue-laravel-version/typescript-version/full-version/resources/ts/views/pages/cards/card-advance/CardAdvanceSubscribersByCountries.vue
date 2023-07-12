<script setup lang="ts">
import flagAus from '@images/icons/flags/flag-australia.png'
import flagBrazil from '@images/icons/flags/flag-brazil.png'
import flagChina from '@images/icons/flags/flag-china.png'
import flagFr from '@images/icons/flags/flag-france.png'
import flagIndia from '@images/icons/flags/flag-india.png'
import flagUsa from '@images/icons/flags/flag-usa.png'

const subscribers = [
  {
    country: 'USA',
    subscribers: '22,450',
    trendNumber: 22.5,
    flag: flagUsa,
  },
  {
    country: 'India',
    subscribers: '18,568',
    trendNumber: 18.5,
    flag: flagIndia,
  },
  {
    country: 'Brazil',
    subscribers: '8,457',
    trendNumber: -8.3,
    flag: flagBrazil,
  },
  {
    country: 'Australia',
    subscribers: '2,850',
    trendNumber: 15.2,
    flag: flagAus,
  },
  {
    country: 'France',
    subscribers: '1,930',
    trendNumber: -12.6,
    flag: flagFr,
  },
  {
    country: 'China',
    subscribers: '852',
    trendNumber: -2.4,
    flag: flagChina,
  },
]

const moreList = [
  { title: 'Last 28 Days', value: 'Last 28 Days' },
  { title: 'Last Month', value: 'Last Month' },
  { title: 'Last Year', value: 'Last Year' },
]
</script>

<template>
  <VCard title="Subscribers By Countries">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VTable class="subscriber-table text-no-wrap">
      <thead>
        <tr>
          <th scope="col">
            <span class="text-medium-emphasis font-weight-medium text-sm">Countries</span>
          </th>
          <th scope="col">
            <span class="text-medium-emphasis font-weight-medium text-sm">Subscribers</span>
          </th>
          <th
            scope="col"
            class="text-end"
          >
            <span class="text-medium-emphasis font-weight-medium text-sm">Change</span>
          </th>
        </tr>
      </thead>

      <tbody class="font-weight-medium text-sm text-high-emphasis">
        <tr
          v-for="subscriber in subscribers"
          :key="subscriber.country"
        >
          <td>
            <div class="d-flex align-center">
              <VAvatar
                :image="subscriber.flag"
                :size="30"
                class="me-3"
              />
              <span class="text-high-emphasis">{{ subscriber.country }}</span>
            </div>
          </td>

          <td>{{ subscriber.subscribers }}</td>

          <td>
            <div
              class="d-flex justify-end align-center"
              :class="subscriber.trendNumber > 0 ? 'text-success' : 'text-error'"
            >
              <span>{{ subscriber.trendNumber > 0 ? `+${subscriber.trendNumber}` : subscriber.trendNumber }}%</span>

              <VIcon
                :icon="subscriber.trendNumber > 0 ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                size="24"
              />
            </div>
          </td>
        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>

<style lang="scss" scoped>
.subscriber-table {
  tbody {
    td {
      block-size: 3.625rem !important;
      border-block-end: none !important;
    }
  }
}
</style>
