<script setup>
const countrySales = [
  {
    sales: '18,879',
    title: 'Australia',
    trendNumber: -15,
  },
  {
    sales: '10,357',
    title: 'Canada',
    trendNumber: 85,
  },
  {
    sales: '4,860',
    title: 'India',
    trendNumber: 48,
  },
  {
    sales: '899',
    title: 'US',
    trendNumber: -16,
  },
  {
    sales: '43',
    title: 'Japan',
    trendNumber: 35,
  },
  {
    sales: '18',
    title: 'Brazil',
    trendNumber: 12,
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
  <VCard title="Most Sales in Countries">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <div class="d-flex align-center mb-1">
        <h3 class="text-h3 font-weight-medium me-3">
          22,842
        </h3>

        <VChip
          text="+42%"
          size="x-small"
          color="success"
        />
      </div>

      <p class="text-xs mb-5">
        Sales Last 90 Days
      </p>

      <VDivider />

      <VTable>
        <tbody class="text-high-emphasis text-no-wrap">
          <tr
            v-for="sale in countrySales"
            :key="sale.title"
          >
            <td class="ps-0">
              {{ sale.title }}
            </td>

            <td class="text-end font-weight-medium">
              {{ sale.sales }}
            </td>

            <td class="d-flex align-center justify-end text-end font-weight-medium pe-0">
              {{ Math.abs(sale.trendNumber) }}%

              <VIcon
                :icon="sale.trendNumber > 0 ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                :color="sale.trendNumber > 0 ? 'success' : 'error'"
                size="24"
              />
            </td>
          </tr>
        </tbody>
      </VTable>
    </VCardText>
  </VCard>
</template>
