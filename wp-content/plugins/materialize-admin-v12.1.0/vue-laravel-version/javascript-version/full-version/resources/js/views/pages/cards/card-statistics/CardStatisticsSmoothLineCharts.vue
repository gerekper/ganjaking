<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()
const currentTheme = vuetifyTheme.current.value.colors

const series = [{
  data: [
    0,
    30,
    10,
    70,
    40,
    110,
    95,
  ],
}]

const chartOptions = {
  chart: {
    parentHeightOffset: 0,
    toolbar: { show: false },
  },
  grid: {
    show: false,
    padding: {
      left: -5,
      top: -15,
      right: 5,
      bottom: -10,
    },
  },
  colors: [currentTheme.warning],
  tooltip: { enabled: false },
  stroke: {
    width: 4,
    curve: 'smooth',
    lineCap: 'round',
  },
  xaxis: {
    labels: { show: false },
    axisTicks: { show: false },
    axisBorder: { show: false },
  },
  yaxis: { labels: { show: false } },
}
</script>

<template>
  <VCard>
    <VCardText>
      <div class="d-flex align-center gap-2">
        <h6 class="text-h6">
          $22.6k
        </h6>
        <span class="text-error font-weight-medium">+38%</span>
      </div>
      <span class="text-sm text-center">
        Total Sales
      </span>

      <VueApexCharts
        type="line"
        :height="108"
        :options="chartOptions"
        :series="series"
      />
    </VCardText>
  </VCard>
</template>
