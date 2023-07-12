<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const series = [
  {
    name: 'Earning',
    data: [
      120,
      200,
      150,
      120,
    ],
  },
  {
    name: 'Expense',
    data: [
      72,
      120,
      50,
      65,
    ],
  },
]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    grid: {
      padding: {
        top: -15,
        left: -14,
        right: -4,
        bottom: -15,
      },
      yaxis: { lines: { show: false } },
    },
    legend: { show: false },
    tooltip: { enabled: false },
    dataLabels: { enabled: false },
    colors: [
      currentTheme.primary,
      currentTheme.warning,
    ],
    plotOptions: {
      bar: {
        borderRadius: 5,
        columnWidth: '48%',
        startingShape: 'rounded',
      },
    },
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    xaxis: {
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: [
        'Jan',
        'Feb',
        'Mar',
        'Apr',
      ],
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
        <span class="text-error font-weight-medium">-22%</span>
      </div>
      <span class="text-sm text-center">
        Total Revenue
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
