<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'

const moreList = [
  { title: 'Last 28 Days', value: 'Last 28 Days' },
  { title: 'Last Month', value: 'Last Month' },
  { title: 'Last Year', value: 'Last Year' },
]

const vuetifyTheme = useTheme()

const chartConfig = computed(() => {
  const themeColors = vuetifyTheme.current.value.colors

  return {
    chart: {
      offsetY: -8,
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { enabled: false },
    dataLabels: { enabled: false },
    stroke: {
      width: 5,
      curve: 'smooth',
    },
    grid: {
      show: false,
      padding: {
        left: 10,
        top: -24,
        right: 12,
      },
    },
    fill: {
      type: 'gradient',
      gradient: {
        opacityTo: 0.7,
        opacityFrom: 0.5,
        shadeIntensity: 1,
        stops: [0, 90, 100],
        colorStops: [
          [
            {
              offset: 0,
              opacity: 0.6,
              color: themeColors.success,
            },
            {
              offset: 100,
              opacity: 0.1,
              color: 'rgba(var(--v-theme-surface), 1)',
            },
          ],
        ],
      },
    },
    theme: {
      monochrome: {
        enabled: true,
        shadeTo: 'light',
        shadeIntensity: 1,
        color: themeColors.success,
      },
    },
    xaxis: {
      type: 'numeric',
      labels: { show: false },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: { show: false },
    markers: {
      size: 1,
      offsetY: 1,
      offsetX: -5,
      strokeWidth: 4,
      strokeOpacity: 1,
      colors: ['transparent'],
      strokeColors: 'transparent',
      discrete: [
        {
          size: 7,
          seriesIndex: 0,
          dataPointIndex: 7,
          strokeColor: themeColors.success,
          fillColor: 'rgba(var(--v-theme-surface), 1)',
        },
      ],
    },
  }
})

const series = [{ name: 'Traffic Rate', data: [0, 85, 25, 125, 90, 250, 200, 350] }]
</script>

<template>
  <VCard title="Monthly Budget">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VueApexCharts
        type="area"
        height="262"
        :options="chartConfig"
        :series="series"
      />

      <p class="mb-0">
        Last month you had $2.42 expense transactions, 12 savings entries and 4 bills.
      </p>
    </VCardText>
  </VCard>
</template>
