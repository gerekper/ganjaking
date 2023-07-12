<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

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

const vuetifyTheme = useTheme()

const chartConfig = computed(() => {
  const themeColors = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const borderColor = `rgba(${ hexToRgb(String(variableTheme['border-color'])) },${ variableTheme['border-opacity'] })`
  const disabledText = `rgba(${ hexToRgb(String(themeColors['on-background'])) },${ variableTheme['disabled-opacity'] })`
  
  return {
    chart: {
      offsetY: -9,
      offsetX: -16,
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { enabled: false },
    plotOptions: {
      bar: {
        borderRadius: 9,
        columnWidth: '50%',
        endingShape: 'rounded',
        startingShape: 'rounded',
        colors: {
          ranges: [{
            to: 50,
            from: 40,
            color: `rgba(${ hexToRgb(String(themeColors.primary)) }, 1)`,
          }],
        },
      },
    },
    markers: {
      size: 3.5,
      strokeWidth: 2,
      fillOpacity: 1,
      strokeOpacity: 1,
      colors: ['rgba(var(--v-theme-surface),1)'],
      strokeColors: `rgba(${ hexToRgb(String(themeColors.primary)) }, 1)`,
    },
    stroke: {
      width: [
        0,
        2,
      ],
      colors: [
        themeColors['grey-100'],
        themeColors.primary,
      ],
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [`rgba(${ hexToRgb(String(themeColors['grey-100'])) }, 1)`],
    grid: {
      strokeDashArray: 7,
      borderColor,
    },
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    xaxis: {
      categories: [
        'Sun',
        'Mon',
        'Tue',
        'Wed',
        'Thu',
        'Fri',
        'Sat',
      ],
      tickPlacement: 'on',
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: {
      min: 0,
      max: 90,
      show: true,
      tickAmount: 3,
      labels: {
        formatter: value => `${ value > 999 ? `${ (value / 1000).toFixed(0) }` : value }k`,
        style: {
          fontSize: '0.75rem',
          colors: disabledText,
        },
      },
    },
  }
})

const series = [
  {
    name: 'Sales',
    type: 'column',
    data: [
      83,
      68,
      56,
      65,
      65,
      50,
      39,
    ],
  },
  {
    type: 'line',
    name: 'Sales',
    data: [
      63,
      38,
      31,
      45,
      46,
      27,
      18,
    ],
  },
]
</script>

<template>
  <VCard title="Weekly Overview">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VueApexCharts
        type="line"
        height="208"
        :options="chartConfig"
        :series="series"
      />

      <div class="d-flex align-center gap-4 mb-3">
        <h5 class="text-h5">
          62%
        </h5>
        <p class="mb-0">
          Your sales performance is 35% 😎 better compared to last month
        </p>
      </div>

      <VBtn block>
        Details
      </VBtn>
    </VCardText>
  </VCard>
</template>
