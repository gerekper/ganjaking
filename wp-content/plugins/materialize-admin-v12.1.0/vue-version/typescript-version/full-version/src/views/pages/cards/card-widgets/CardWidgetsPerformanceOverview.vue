<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

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
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    stroke: {
      curve: 'stepline',
    },
    tooltip: { enabled: false },
    colors: [`rgba(${hexToRgb(String(themeColors.warning))}, 1)`],
    grid: {
      yaxis: {
        lines: { show: false },
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

const series = [{ data: [7, 65, 40, 7, 40, 80, 45, 65, 65] }]
</script>

<template>
  <VCard title="Performance Overview">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VueApexCharts
        type="line"
        height="202"
        :options="chartConfig"
        :series="series"
      />
      <div class="d-flex align-center justify-center gap-2">
        <VBadge
          dot
          color="warning"
        />
        <span class="text-disabled mt-2">Average cost per interaction is $5.65</span>
      </div>
    </VCardText>
  </VCard>
</template>
