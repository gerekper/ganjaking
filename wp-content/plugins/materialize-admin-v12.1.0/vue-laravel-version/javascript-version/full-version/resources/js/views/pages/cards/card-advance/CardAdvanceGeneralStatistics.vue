<script setup>
const trends = [
  {
    title: 'Profit',
    amount: '$54,234',
    trendNumber: 85,
  },
  {
    title: 'Sales',
    amount: '8,657',
    trendNumber: 42,
  },
  {
    title: 'User',
    amount: '16,456',
    trendNumber: -12,
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

const resolveDotColor = title => {
  if (title === 'User')
    return 'info'
  else if (title === 'Sales')
    return 'warning'
  else
    return 'primary'
}
</script>

<template>
  <VCard title="General Statistics">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <div class="d-flex align-center mb-6">
        <VAvatar
          rounded
          variant="tonal"
          color="primary"
          size="50"
          class="me-4"
        >
          <VIcon
            icon="mdi-credit-card"
            size="32"
          />
        </VAvatar>

        <div style="line-height: 1;">
          <h4 class="text-h4">
            $89,522
          </h4>

          <span class="text-xs">Last 6 Month profit</span>
        </div>
      </div>

      <h6 class="font-weight-medium text-base mb-2">
        Current Activity
      </h6>

      <VProgressLinear
        rounded
        :model-value="85"
        color="primary"
        height="6"
        class="mb-4"
      />

      <VTable class="text-no-wrap">
        <tbody class="text-high-emphasis">
          <tr
            v-for="trend in trends"
            :key="trend.trendNumber"
          >
            <td class="ps-0">
              <div class="d-flex align-center gap-2">
                <VIcon
                  icon="mdi-circle"
                  :color="resolveDotColor(trend.title)"
                  :size="16"
                />
                {{ trend.title }}
              </div>
            </td>

            <td class="font-weight-medium text-end">
              {{ trend.amount }}
            </td>

            <td class="d-flex align-center justify-end font-weight-medium text-end pe-0">
              {{ trend.trendNumber > 0 ? `+${trend.trendNumber}%` : `${trend.trendNumber}%` }}
              <VIcon
                :icon="trend.trendNumber > 0 ? 'mdi-chevron-up' : 'mdi-chevron-down'"
                :color="trend.trendNumber > 0 ? 'success' : 'error'"
                size="24"
              />
            </td>
          </tr>
        </tbody>
      </VTable>
    </VCardText>
  </VCard>
</template>
