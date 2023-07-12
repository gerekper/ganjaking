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
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Sales Overview</VCardTitle>
      <p class="text-xs mt-1 mb-0">
        Total 42.5k Sales
        <span class="text-success text-sm">
          <span>+18%</span>
          <VIcon icon="mdi-chevron-down" />
        </span>
      </p>

      <template #append>
        <div class="mt-n7 me-n3">
          <MoreBtn />
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
              size="42"
              class="me-3"
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
