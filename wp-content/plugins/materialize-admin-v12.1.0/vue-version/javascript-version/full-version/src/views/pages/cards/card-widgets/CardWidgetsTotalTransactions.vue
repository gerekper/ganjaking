<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const moreList = [
  {
    title: 'Refresh',
    value: 'refresh',
  },
  {
    title: 'Update',
    value: 'update',
  },
  {
    title: 'Share',
    value: 'share',
  },
]

const reports = [
  {
    title: 'This Week',
    stats: 82.45,
  },
  {
    title: 'Last Week',
    stats: -24.86,
  },
]

const vuetifyTheme = useTheme()

const chartConfig = computed(() => {
  const themeColors = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const borderColor = `rgba(${ hexToRgb(String(variableTheme['border-color'])) },${ variableTheme['border-opacity'] })`
  const disabledText = `rgba(${ hexToRgb(String(themeColors['on-background'])) },${ variableTheme['disabled-opacity'] })`
  
  return {
    chart: {
      stacked: true,
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { y: { formatter: val => `${ Math.abs(val) }` } },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [
      `rgba(${ hexToRgb(String(themeColors.primary)) }, 1)`,
      `rgba(${ hexToRgb(String(themeColors.success)) }, 1)`,
    ],
    grid: {
      borderColor,
      xaxis: { lines: { show: true } },
      yaxis: { lines: { show: false } },
      padding: {
        top: -5,
        bottom: -25,
      },
    },
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    plotOptions: {
      bar: {
        borderRadius: 5,
        barHeight: '30%',
        horizontal: true,
        endingShape: 'flat',
        startingShape: 'rounded',
      },
    },
    xaxis: {
      position: 'top',
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: [
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat',
        'Sun',
      ],
      labels: {
        formatter: val => `${ Math.abs(Number(val)) }`,
        style: { colors: disabledText },
      },
    },
    yaxis: { labels: { show: false } },
  }
})

const series = [
  {
    name: 'Last Week',
    data: [
      83,
      153,
      213,
      279,
      213,
      153,
      83,
    ],
  },
  {
    name: 'This Week',
    data: [
      -84,
      -156,
      -216,
      -282,
      -216,
      -156,
      -84,
    ],
  },
]
</script>

<template>
  <VCard>
    <VRow no-gutters>
      <VCol
        cols="12"
        sm="7"
        :class="$vuetify.display.xs ? 'border-b' : 'border-e'"
      >
        <VCardItem>
          <VCardTitle>Total Transactions</VCardTitle>
        </VCardItem>

        <VCardText>
          <VueApexCharts
            id="total-transactions-chart"
            type="bar"
            height="240"
            :options="chartConfig"
            :series="series"
          />
        </VCardText>
      </VCol>

      <VCol
        cols="12"
        sm="5"
      >
        <VCardItem>
          <VCardTitle>Report</VCardTitle>
          <VCardSubtitle>Last month transactions $234.40k</VCardSubtitle>

          <template #append>
            <div class="mt-n8 me-n4">
              <MoreBtn :menu-list="moreList" />
            </div>
          </template>
        </VCardItem>

        <div>
          <VCardText>
            <VRow no-gutters>
              <VCol
                v-for="(report, index) in reports"
                :key="report.title"
                cols="6"
                class="text-center"
                :class="index === 0 ? 'border-e' : ''"
              >
                <VAvatar
                  rounded
                  variant="tonal"
                  :color="Math.sign(report.stats) === 1 ? 'success' : 'primary' "
                >
                  <VIcon :icon="Math.sign(report.stats) === 1 ? 'mdi-trending-up' : 'mdi-trending-down'" />
                </VAvatar>
                <p class="mt-2 mb-0">
                  {{ report.title }}
                </p>
                <h6 class="text-base font-weight-medium">
                  {{ Math.sign(report.stats) === 1 ? `+${report.stats}` : report.stats }}
                </h6>
              </VCol>
            </VRow>

            <VDivider class="my-6" />

            <div class="d-flex align-center justify-space-around flex-wrap gap-y-2  text-center">
              <div class="px-4">
                <p class="mb-0">
                  Performance
                </p>
                <h6 class="text-base font-weight-medium">
                  +94.15%
                </h6>
              </div>

              <div>
                <VBtn>View Report</VBtn>
              </div>
            </div>
          </VCardText>
        </div>
      </VCol>
    </VRow>
  </VCard>
</template>

<style lang="scss">
#total-transactions-chart {
  .apexcharts-series[rel="2"] {
    transform: translateX(5px);
  }
}
</style>
