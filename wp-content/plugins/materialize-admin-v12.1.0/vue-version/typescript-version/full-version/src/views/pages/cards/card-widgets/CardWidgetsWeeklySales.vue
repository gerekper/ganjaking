<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const moreList = [
  { title: 'Refresh', value: 'refresh' },
  { title: 'Update', value: 'update' },
  { title: 'Share', value: 'share' },
]

const vuetifyTheme = useTheme()

const chartConfig = computed(() => {
  const themeColors = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables

  const disabledText = `rgba(${hexToRgb(String(themeColors['on-background']))},${variableTheme['disabled-opacity']})`

  return {
    chart: {
      stacked: true,
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { enabled: false },
    markers: {
      size: 4,
      strokeWidth: 3,
      fillOpacity: 1,
      strokeOpacity: 1,
      colors: 'rgba(var(--v-theme-surface), 1)',
      strokeColors: themeColors.warning,
    },
    stroke: {
      curve: 'smooth',
      width: [0, 0, 3],
      colors: [themeColors.warning],
    },
    colors: [
      `rgba(${hexToRgb(String(themeColors.primary))}, 1)`,
      `rgba(${hexToRgb(String(themeColors.primary))}, 0.12)`,
    ],
    dataLabels: { enabled: false },
    states: {
      hover: {
        filter: { type: 'none' },
      },
      active: {
        filter: { type: 'none' },
      },
    },
    legend: { show: false },
    grid: {
      yaxis: {
        lines: { show: false },
      },
      padding: {
        top: -28,
        left: -6,
        right: -8,
        bottom: -5,
      },
    },
    plotOptions: {
      bar: {
        borderRadius: 8,
        columnWidth: '57%',
        endingShape: 'flat',
        startingShape: 'rounded',
      },
    },
    xaxis: {
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
      labels: {
        style: { colors: disabledText },
      },
    },
    yaxis: {
      max: 100,
      min: -100,
      show: false,
    },
  }
})

const series = [
  {
    type: 'column',
    name: 'Earning',
    data: [90, 52, 67, 45, 75, 55, 48],
  },
  {
    type: 'column',
    name: 'Expense',
    data: [-53, -29, -67, -84, -60, -40, -77],
  },
  {
    type: 'line',
    name: 'Expense',
    data: [73, 20, 50, -20, 58, 15, 31],
  },
]

const salesReport = [
  {
    title: 'Net Income',
    amount: '$438.5k',
    avatarColor: 'primary',
    avatarIcon: 'mdi-trending-up',
  },
  {
    title: 'Expense',
    amount: '$22.4k',
    avatarColor: 'warning',
    avatarIcon: 'mdi-currency-usd',
  },
]
</script>

<template>
  <VCard
    title="Weekly Sales"
    subtitle="Total 85.4k Sales"
  >
    <template #append>
      <div class="mt-n8 me-n3">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VRow class="mb-2">
        <VCol
          v-for="sale in salesReport"
          :key="sale.title"
          cols="6"
        >
          <div class="d-flex align-center gap-4">
            <VAvatar
              rounded
              variant="tonal"
              :color="sale.avatarColor"
            >
              <VIcon :icon="sale.avatarIcon" />
            </VAvatar>
            <div>
              <span class="text-xs">{{ sale.title }}</span>
              <h6 class="text-base font-weight-medium">
                {{ sale.amount }}
              </h6>
            </div>
          </div>
        </VCol>
      </VRow>

      <VueApexCharts
        id="weekly-sales-chart"
        type="line"
        height="225"
        :options="chartConfig"
        :series="series"
      />
    </VCardText>
  </VCard>
</template>

<style lang="scss">
#weekly-sales-chart {
  .apexcharts-series[rel="2"] {
    transform: translateY(-8px);
  }
}
</style>
