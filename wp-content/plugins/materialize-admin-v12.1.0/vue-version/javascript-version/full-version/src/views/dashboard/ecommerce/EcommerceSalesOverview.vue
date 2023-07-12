<script setup>
const statistics = [
  {
    title: 'Customers',
    stats: '8,458',
    icon: 'mdi-account-outline',
    color: 'primary',
  },
  {
    title: 'Total Profit',
    stats: '$28.5k',
    icon: 'mdi-poll',
    color: 'warning',
  },
  {
    title: 'Transactions',
    stats: '2,450k',
    icon: 'mdi-trending-up',
    color: 'info',
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
  <VCard>
    <VCardItem class="pb-3">
      <VCardTitle>Sales Overview</VCardTitle>
      <p class="text-xs mb-0">
        Total 42.5k Sales
        <span class="text-success text-sm">
          <span>+18%</span>
          <VIcon icon="mdi-chevron-down" />
        </span>
      </p>

      <template #append>
        <div class="mt-n7 me-n3">
          <MoreBtn :menu-list="moreList" />
        </div>
      </template>
    </VCardItem>

    <VCardText>
      <VRow>
        <VCol
          v-for="item in statistics"
          :key="item.title"
          cols="12"
          sm="4"
        >
          <div class="d-flex align-center">
            <VAvatar
              :color="item.color"
              rounded
              variant="tonal"
              size="40"
              class="me-4"
            >
              <VIcon
                size="24"
                :icon="item.icon"
              />
            </VAvatar>

            <div class="d-flex flex-column">
              <h6 class="text-h6">
                {{ item.stats }}
              </h6>
              <span class="text-caption">
                {{ item.title }}
              </span>
            </div>
          </div>
        </VCol>
      </VRow>
    </VCardText>
  </VCard>
</template>
