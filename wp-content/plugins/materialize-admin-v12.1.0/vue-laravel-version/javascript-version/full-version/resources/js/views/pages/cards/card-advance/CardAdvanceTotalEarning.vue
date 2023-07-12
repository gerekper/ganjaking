<script setup>
const earnings = [
  {
    progress: 75,
    storeName: 'Amazon',
    amount: '$24,453',
  },
  {
    progress: 59,
    storeName: 'Flipkart',
    amount: '$12,763',
  },
  {
    progress: 20,
    storeName: 'eBay',
    amount: '$4,978',
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

const resolveProgressColor = storeName => {
  if (storeName === 'Amazon')
    return 'primary'
  else if (storeName === 'Flipkart')
    return 'success'
  else if (storeName === 'eBay')
    return 'error'
}
</script>

<template>
  <VCard title="Total Earnings">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <div class="d-flex align-center">
        <h4 class="text-h4 me-1">
          $42,880
        </h4>
        <VIcon
          icon="mdi-menu-up"
          color="success"
          size="30"
        />
        <span class="text-success font-weight-medium">22%</span>
      </div>

      <p class="text-xs mb-7">
        Compared to $84,325 last year
      </p>

      <div
        v-for="earning in earnings"
        :key="earning.storeName"
        class="mb-7"
      >
        <div class="d-flex justify-space-between mb-1 font-weight-medium text-high-emphasis">
          <span class="text-base">{{ earning.storeName }}</span>
          <span>{{ earning.amount }}</span>
        </div>

        <VProgressLinear
          rounded
          :model-value="earning.progress"
          :color="resolveProgressColor(earning.storeName)"
          height="6"
        />
      </div>
    </VCardText>
  </VCard>
</template>
