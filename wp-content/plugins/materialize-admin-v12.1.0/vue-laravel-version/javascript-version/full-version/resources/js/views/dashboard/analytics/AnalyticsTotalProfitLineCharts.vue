<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const vuetifyTheme = useTheme()

const series = [{
  data: [
    0,
    20,
    5,
    30,
    15,
    45,
  ],
}]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { enabled: false },
    grid: {
      borderColor: `rgba(${ hexToRgb(String(variableTheme['border-color'])) },${ variableTheme['border-opacity'] })`,
      strokeDashArray: 6,
      xaxis: { lines: { show: true } },
      yaxis: { lines: { show: false } },
      padding: {
        top: -15,
        left: -7,
        right: 7,
        bottom: -15,
      },
    },
    stroke: { width: 3 },
    colors: [currentTheme.info],
    markers: {
      size: 6,
      offsetY: 2,
      offsetX: -1,
      strokeWidth: 3,
      colors: ['transparent'],
      strokeColors: 'transparent',
      discrete: [{
        size: 6,
        seriesIndex: 0,
        strokeColor: currentTheme.info,
        fillColor: currentTheme.surface,
        dataPointIndex: series[0].data.length - 1,
      }],
      hover: { size: 7 },
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: { labels: { show: false } },
  }
})
</script>

<template>
  <VCard>
    <VCardText>
      <div class="d-flex align-center gap-2">
        <h6 class="text-h6">
          $42.5k
        </h6>
        <span class="text-success font-weight-medium">+62%</span>
      </div>
      <span class="text-sm text-center">
        Total Profit
      </span>

      <VueApexCharts
        type="line"
        :options="chartOptions"
        :series="series"
        :height="108"
      />
    </VCardText>
  </VCard>
</template>
