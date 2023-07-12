<script setup lang="ts">
import CardStatisticsGrowthDonutCharts from '@/views/pages/cards/card-statistics/CardStatisticsGrowthDonutCharts.vue'
import CardStatisticsLiveVisitors from '@/views/pages/cards/card-statistics/CardStatisticsLiveVisitors.vue'
import CardStatisticsMarketingSales from '@/views/pages/cards/card-statistics/CardStatisticsMarketingSales.vue'
import CardStatisticsOrdersImpressions from '@/views/pages/cards/card-statistics/CardStatisticsOrdersImpressions.vue'
import CardStatisticsOverviewRadialBarCharts from '@/views/pages/cards/card-statistics/CardStatisticsOverviewRadialBarCharts.vue'
import CardStatisticsProfitBarWithGapCharts from '@/views/pages/cards/card-statistics/CardStatisticsProfitBarWithGapCharts.vue'
import CardStatisticsSalesMonthCharts from '@/views/pages/cards/card-statistics/CardStatisticsSalesMonthCharts.vue'
import CardStatisticsSalesOverview from '@/views/pages/cards/card-statistics/CardStatisticsSalesOverview.vue'
import CardStatisticsSalesProfitCharts from '@/views/pages/cards/card-statistics/CardStatisticsSalesProfitCharts.vue'
import CardStatisticsSmoothLineCharts from '@/views/pages/cards/card-statistics/CardStatisticsSmoothLineCharts.vue'
import CardStatisticsTotalProfitLineCharts from '@/views/pages/cards/card-statistics/CardStatisticsTotalProfitLineCharts.vue'
import CardStatisticsTotalRevenueBarCharts from '@/views/pages/cards/card-statistics/CardStatisticsTotalRevenueBarCharts.vue'
import CardStatisticsTotalVisits from '@/views/pages/cards/card-statistics/CardStatisticsTotalVisits.vue'
import CardStatisticsWeeklySales from '@/views/pages/cards/card-statistics/CardStatisticsWeeklySales.vue'
import CardStatisticsWeeklySalesBg from '@/views/pages/cards/card-statistics/CardStatisticsWeeklySalesBg.vue'
import CardStatisticsHorizontal from '@core/components/cards/CardStatisticsHorizontal.vue'
import CardStatisticsVertical from '@core/components/cards/CardStatisticsVertical.vue'
import illustration1 from '@images/cards/illustration-1.png'
import illustration2 from '@images/cards/illustration-2.png'
import illustration3 from '@images/cards/illustration-3.png'
import illustration4 from '@images/cards/illustration-4.png'

const statisticsHorizontal = [
  {
    title: 'New Customers',
    color: 'primary',
    icon: 'mdi-account-outline',
    stats: 8458,
    change: -8.1,
  },
  {
    title: 'Total Revenue',
    color: 'success',
    icon: 'mdi-currency-usd',
    stats: 28600,
    change: 25.8,
  },
  {
    title: 'New Transactions',
    color: 'info',
    icon: 'mdi-trending-up',
    stats: 13600,
    change: -12.1,
  },
  {
    title: 'Total Profit',
    color: 'warning',
    icon: 'mdi-poll',
    stats: 2856,
    change: 54.6,
  },
]

const statisticsVertical = [
  {
    title: 'Total Orders',
    color: 'primary',
    icon: 'mdi-cart-plus',
    stats: '155k',
    change: 22,
    subtitle: 'Last 4 Month',
  },
  {
    title: 'Total Profit',
    color: 'warning',
    icon: 'mdi-wallet-giftcard',
    stats: '$89.34k',
    change: -18,
    subtitle: 'Last One Year',
  },
  {
    title: 'Total Impression',
    color: 'info',
    icon: 'mdi-link',
    stats: '142.8k',
    change: 62,
    subtitle: 'Last One Year',
  },
  {
    title: 'Total Sales',
    color: 'success',
    icon: 'mdi-currency-usd',
    stats: '$13.4k',
    change: 38,
    subtitle: 'Last Six Months',
  },
  {
    title: 'Total Expenses',
    color: 'error',
    icon: 'mdi-briefcase-outline',
    stats: '$8.16k',
    change: -16,
    subtitle: 'Last One Month',
  },
  {
    title: 'Transactions',
    color: 'secondary',
    icon: 'mdi-trending-up',
    stats: '$2.55k',
    change: 46,
    subtitle: 'Last One Year',
  },
]

const statisticsWithImages = [
  {
    title: 'Ratings',
    subtitle: 'Year of 2021',
    stats: '8.14k',
    change: 15.6,
    image: illustration1,
    imgWidth: 95,
    color: 'primary',
  },
  {
    title: 'Sessions',
    subtitle: 'Last Month',
    stats: '12.2k',
    change: -25.5,
    image: illustration2,
    imgWidth: 82,
    color: 'success',
  },
  {
    title: 'Customers',
    subtitle: 'Daily Customers',
    stats: '42.4k',
    change: 9.2,
    image: illustration3,
    imgWidth: 85,
    color: 'warning',
  },
  {
    title: 'Total Orders',
    subtitle: 'Last Week',
    stats: '42.5k',
    change: 10.8,
    image: illustration4,
    imgWidth: 78,
    color: 'secondary',
  },
]
</script>

<template>
  <VRow>
    <!-- 👉 Horizontal Cards -->
    <VCol
      v-for="statistics in statisticsHorizontal"
      :key="statistics.title"
      cols="12"
      sm="6"
      md="3"
    >
      <CardStatisticsHorizontal v-bind="statistics" />
    </VCol>

    <!-- 👉 Images Cards -->
    <VCol
      v-for="statistics in statisticsWithImages"
      :key="statistics.title"
      cols="12"
      lg="3"
      sm="6"
    >
      <CardStatisticsWithImages v-bind="statistics" />
    </VCol>

    <!-- 👉 Vertical Cards -->
    <VCol
      v-for="statistics in statisticsVertical"
      :key="statistics.title"
      cols="12"
      sm="4"
      lg="2"
    >
      <CardStatisticsVertical v-bind="statistics" />
    </VCol>

    <!-- 👉 Total Revenue Bar Chart -->
    <VCol
      cols="12"
      sm="6"
      md="4"
      lg="2"
    >
      <CardStatisticsTotalRevenueBarCharts />
    </VCol>

    <!-- 👉 Total Profit Line Chart -->
    <VCol
      cols="12"
      sm="6"
      md="4"
      lg="2"
    >
      <CardStatisticsTotalProfitLineCharts />
    </VCol>

    <!-- 👉 Total Overview Radial Bar Chart -->
    <VCol
      cols="12"
      sm="6"
      md="4"
      lg="2"
    >
      <CardStatisticsOverviewRadialBarCharts />
    </VCol>

    <!-- 👉 Profit Bar Chart with Gaps -->
    <VCol
      cols="12"
      sm="6"
      md="4"
      lg="2"
    >
      <CardStatisticsProfitBarWithGapCharts />
    </VCol>

    <!-- 👉 Total Smooth line Chart -->
    <VCol
      cols="12"
      sm="6"
      md="4"
      lg="2"
    >
      <CardStatisticsSmoothLineCharts />
    </VCol>

    <!-- 👉 Growth Donut Chart -->
    <VCol
      cols="12"
      sm="6"
      md="4"
      lg="2"
    >
      <CardStatisticsGrowthDonutCharts />
    </VCol>

    <!-- 👉 Sales Profit Chart -->
    <VCol
      cols="12"
      sm="6"
      lg="3"
    >
      <CardStatisticsSalesProfitCharts />
    </VCol>

    <!-- 👉 Total Visits -->
    <VCol
      cols="12"
      sm="6"
      lg="3"
    >
      <CardStatisticsTotalVisits />
    </VCol>

    <!-- 👉 Sales this Month Chart -->
    <VCol
      cols="12"
      sm="6"
      lg="3"
    >
      <CardStatisticsSalesMonthCharts />
    </VCol>

    <!-- 👉 Order and Impressions card -->
    <VCol
      cols="12"
      sm="6"
      lg="3"
    >
      <CardStatisticsOrdersImpressions />
    </VCol>

    <!-- 👉 Weekly Sales card -->
    <VCol
      cols="12"
      md="6"
    >
      <CardStatisticsWeeklySales />
    </VCol>

    <!-- 👉 Marketing And Sales card -->
    <VCol
      cols="12"
      md="6"
    >
      <CardStatisticsMarketingSales />
    </VCol>

    <!-- 👉 Weekly Sales card with background color -->
    <VCol
      cols="12"
      md="6"
    >
      <CardStatisticsWeeklySalesBg />
    </VCol>

    <!-- 👉 Transition Card  -->
    <VCol
      cols="12"
      md="6"
    >
      <CardStatisticsSalesOverview />
    </VCol>

    <!-- 👉 Live Visitors -->
    <VCol
      cols="12"
      md="6"
    >
      <CardStatisticsLiveVisitors />
    </VCol>
  </VRow>
</template>

<style lang="scss">
@use "@core-scss/template/libs/apex-chart.scss";
</style>
