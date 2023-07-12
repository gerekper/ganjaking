<script setup lang="ts">
import { useTheme } from 'vuetify'
import { getLineChartConfig } from '@core/libs/chartjs/chartjsConfig'
import LineChart from '@core/libs/chartjs/components/LineChart'

import type { ChartJsCustomColors } from '@/views/charts/chartjs/types'

interface Props {
  colors: ChartJsCustomColors
}

const props = defineProps<Props>()

const vuetifyTheme = useTheme()

const data = {
  labels: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120, 130, 140],
  datasets: [
    {
      fill: false,
      tension: 0.5,
      pointRadius: 1,
      label: 'Europe',
      pointHoverRadius: 5,
      pointStyle: 'circle',
      borderColor: props.colors.primary,
      backgroundColor: props.colors.primary,
      pointHoverBorderWidth: 5,
      pointHoverBorderColor: props.colors.white,
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: props.colors.primary,
      data: [80, 150, 180, 270, 210, 160, 160, 202, 265, 210, 270, 255, 290, 360, 375],
    },
    {
      fill: false,
      tension: 0.5,
      label: 'Asia',
      pointRadius: 1,
      pointHoverRadius: 5,
      pointStyle: 'circle',
      borderColor: props.colors.warningShade,
      backgroundColor: props.colors.warningShade,
      pointHoverBorderWidth: 5,
      pointHoverBorderColor: props.colors.white,
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: props.colors.warningShade,
      data: [80, 125, 105, 130, 215, 195, 140, 160, 230, 300, 220, 170, 210, 200, 280],
    },
    {
      fill: false,
      tension: 0.5,
      pointRadius: 1,
      label: 'Africa',
      pointHoverRadius: 5,
      pointStyle: 'circle',
      borderColor: props.colors.yellow,
      backgroundColor: props.colors.yellow,
      pointHoverBorderWidth: 5,
      pointHoverBorderColor: props.colors.white,
      pointBorderColor: 'transparent',
      pointHoverBackgroundColor: props.colors.yellow,
      data: [80, 99, 82, 90, 115, 115, 74, 75, 130, 155, 125, 90, 140, 130, 180],
    },
  ],
}

const chartConfig = computed(() => getLineChartConfig(vuetifyTheme.current.value))
</script>

<template>
  <LineChart
    :chart-options="chartConfig"
    :height="400"
    :chart-data="data"
  />
</template>
