<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const currentTheme = vuetifyTheme.current.value.colors

const series = [{ data: [12, 12, 18, 18, 13, 13, 5, 5, 17, 17, 25, 25] }]

const chartOptions = {
  chart: {
    parentHeightOffset: 0,
    toolbar: { show: false },
    dropShadow: {
      top: 14,
      blur: 4,
      left: 0,
      enabled: true,
      opacity: 0.12,
      color: currentTheme.primary,
    },
  },
  tooltip: { enabled: false },
  grid: {
    xaxis: {
      lines: { show: false },
    },
    yaxis: {
      lines: { show: false },
    },
    padding: {
      top: -12,
      left: -2,
      right: 8,
      bottom: -10,
    },
  },
  stroke: {
    width: 5,
    lineCap: 'round',
  },
  markers: { size: 0 },
  colors: [currentTheme.primary],
  xaxis: {
    labels: { show: false },
    axisTicks: { show: false },
    axisBorder: { show: false },
  },
  yaxis: {
    min: 0,
    labels: { show: false },
  },
}
</script>

<template>
  <VCard>
    <VCardText class="pb-0">
      <h6 class="text-h6 mb-2">
        Sales this Month
      </h6>
      <p class="mb-0">
        Total Sales This Month
      </p>
      <h6 class="text-h6">
        $28,450
      </h6>

      <VueApexCharts
        type="line"
        :height="115"
        :options="chartOptions"
        :series="series"
      />
    </VCardText>
  </VCard>
</template>
