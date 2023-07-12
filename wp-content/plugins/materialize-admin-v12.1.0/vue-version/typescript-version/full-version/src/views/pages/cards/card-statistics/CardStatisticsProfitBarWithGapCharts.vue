<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const currentTheme = computed(() => vuetifyTheme.current.value.colors)

const series = [
  {
    name: 'Earning',
    data: [44, 21, 56, 34, 47],
  },
  {
    name: 'Expense',
    data: [-27, -17, -31, -23, -31],
  },
]

const chartOptions = computed(() => {
  return {
    chart: {
      stacked: true,
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    colors: [currentTheme.value.secondary, currentTheme.value.error],
    tooltip: { enabled: false },
    plotOptions: {
      bar: {
        borderRadius: 4,
        columnWidth: '21%',
        endingShape: 'rounded',
        startingShape: 'rounded',
      },
    },
    grid: {
      padding: {
        top: -21,
        right: 0,
        left: -17,
        bottom: -16,
      },
      yaxis: {
        lines: { show: false },
      },
    },
    states: {
      hover: {
        filter: { type: 'none' },
      },
      active: {
        filter: { type: 'none' },
      },
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: {
      labels: { show: false },
    },
  }
})
</script>

<template>
  <VCard>
    <VCardText>
      <div class="d-flex align-center gap-2">
        <h6 class="text-h6">
          $38.5k
        </h6>
        <span class="text-error font-weight-medium">-18%</span>
      </div>
      <span class="text-sm text-center">
        Total Profit
      </span>

      <VueApexCharts
        type="bar"
        :options="chartOptions"
        :series="series"
        :height="108"
      />
    </VCardText>
  </VCard>
</template>
