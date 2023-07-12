<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const seriesSales = [
  {
    data: [0, 15, 0, 17, 5, 30],
  },
]

const seriesProfit = [
  {
    data: [5, 25, 0, 30, 15, 30],
  },
]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors

  return {
    sales: {
      chart: {
        parentHeightOffset: 0,
        toolbar: { show: false },
      },
      grid: {
        show: false,
        padding: {
          left: -7,
        },
      },
      tooltip: { enabled: false },
      colors: [currentTheme.success],
      markers: {
        size: 5,
        offsetY: 1,
        offsetX: -2,
        strokeWidth: 2,
        strokeOpacity: 1,
        colors: ['transparent'],
        strokeColors: 'transparent',
        discrete: [
          {
            size: 5,
            seriesIndex: 0,
            strokeColor: currentTheme.success,
            fillColor: currentTheme.surface,
            dataPointIndex: seriesSales[0].data.length - 1,
          },
        ],
      },
      stroke: {
        width: 3,
        curve: 'smooth',
        lineCap: 'round',
      },
      xaxis: {
        labels: { show: false },
        axisTicks: { show: false },
        axisBorder: { show: false },
      },
      yaxis: {
        labels: { show: false },
      },
    },
    profit: {
      chart: {
        parentHeightOffset: 0,
        toolbar: { show: false },
      },
      grid: {
        show: false,
        padding: {
          left: -5,
        },
      },
      tooltip: { enabled: false },
      colors: [currentTheme.error],
      markers: {
        size: 5,
        offsetY: 0,
        offsetX: -2,
        strokeWidth: 2,
        strokeOpacity: 1,
        colors: ['transparent'],
        strokeColors: 'transparent',
        discrete: [
          {
            size: 5,
            seriesIndex: 0,
            strokeColor: currentTheme.error,
            fillColor: currentTheme.surface,
            dataPointIndex: seriesSales[0].data.length - 1,
          },
        ],
      },
      stroke: {
        width: 3,
        curve: 'smooth',
        lineCap: 'round',
      },
      xaxis: {
        labels: { show: false },
        axisTicks: { show: false },
        axisBorder: { show: false },
      },
      yaxis: {
        labels: { show: false },
      },
    },
  }
})
</script>

<template>
  <VCard>
    <VRow no-gutters>
      <VCol cols="12">
        <VCardText class="py-3">
          <VRow no-gutters>
            <VCol cols="6">
              <VueApexCharts
                type="line"
                :options="chartOptions.sales"
                :series="seriesSales"
                :height="90"
              />
            </VCol>
            <VCol
              cols="6"
              class="pt-4 ps-4"
            >
              <div class="d-flex align-center flex-wrap gap-1">
                <h6 class="text-h6">
                  152k
                </h6>

                <div class="text-success text-no-wrap text-sm">
                  <span>+12%</span>
                  <VIcon icon="mdi-chevron-up" />
                </div>
              </div>
              <span class="text-sm">Total Sales</span>
            </VCol>
          </VRow>
        </VCardText>
      </VCol>

      <VDivider />

      <VCol cols="12">
        <VCardText class="py-3">
          <VRow no-gutters>
            <VCol cols="6">
              <VueApexCharts
                type="line"
                :options="chartOptions.profit"
                :series="seriesProfit"
                :height="90"
              />
            </VCol>
            <VCol
              cols="6"
              class="pt-4 ps-4"
            >
              <div class="d-flex align-center flex-wrap gap-2">
                <h6 class="text-h6">
                  89.5k
                </h6>

                <div class="text-error text-no-wrap text-sm">
                  <span>-8%</span>
                  <VIcon icon="mdi-chevron-down" />
                </div>
              </div>
              <span class="text-sm">Total Profit</span>
            </VCol>
          </VRow>
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>
