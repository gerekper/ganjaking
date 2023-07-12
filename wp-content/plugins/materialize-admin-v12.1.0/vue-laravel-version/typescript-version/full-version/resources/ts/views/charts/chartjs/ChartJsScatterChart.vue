<script setup lang="ts">
import { useTheme } from 'vuetify'
import type { ChartJsCustomColors } from '@/views/charts/chartjs/types'
import { getScatterChartConfig } from '@core/libs/chartjs/chartjsConfig'
import ScatterChart from '@core/libs/chartjs/components/ScatterChart'

interface Props {
  colors: ChartJsCustomColors
}

const props = defineProps<Props>()

const vuetifyTheme = useTheme()

const chartConfig = computed(() => getScatterChartConfig(vuetifyTheme.current.value))

const data = {
  datasets: [
    {
      pointRadius: 5,
      label: 'iPhone',
      pointBorderWidth: 2,
      backgroundColor: props.colors.primary,
      pointHoverBorderWidth: 2,
      borderColor: 'transparent',
      data: [
        { x: 72, y: 225 },
        { x: 81, y: 270 },
        { x: 90, y: 230 },
        { x: 103, y: 305 },
        { x: 103, y: 245 },
        { x: 108, y: 275 },
        { x: 110, y: 290 },
        { x: 111, y: 315 },
        { x: 109, y: 350 },
        { x: 116, y: 340 },
        { x: 113, y: 260 },
        { x: 117, y: 275 },
        { x: 117, y: 295 },
        { x: 126, y: 280 },
        { x: 127, y: 340 },
        { x: 133, y: 330 },
      ],
    },
    {
      pointRadius: 5,
      pointBorderWidth: 2,
      label: 'Samsung Note',
      backgroundColor: props.colors.scatterChartWarning,
      pointHoverBorderWidth: 2,
      borderColor: 'transparent',
      data: [
        { x: 13, y: 95 },
        { x: 22, y: 105 },
        { x: 17, y: 115 },
        { x: 19, y: 130 },
        { x: 21, y: 125 },
        { x: 35, y: 125 },
        { x: 13, y: 155 },
        { x: 21, y: 165 },
        { x: 25, y: 155 },
        { x: 18, y: 190 },
        { x: 26, y: 180 },
        { x: 43, y: 180 },
        { x: 53, y: 202 },
        { x: 61, y: 165 },
        { x: 67, y: 225 },
      ],
    },
    {
      pointRadius: 5,
      label: 'OnePlus',
      pointBorderWidth: 2,
      backgroundColor: props.colors.scatterChartGreen,
      pointHoverBorderWidth: 2,
      borderColor: 'transparent',
      data: [
        { x: 70, y: 195 },
        { x: 72, y: 270 },
        { x: 98, y: 255 },
        { x: 100, y: 215 },
        { x: 87, y: 240 },
        { x: 94, y: 280 },
        { x: 99, y: 300 },
        { x: 102, y: 290 },
        { x: 110, y: 275 },
        { x: 111, y: 250 },
        { x: 94, y: 280 },
        { x: 92, y: 340 },
        { x: 100, y: 335 },
        { x: 108, y: 330 },
      ],
    },
  ],
}
</script>

<template>
  <ScatterChart
    :height="380"
    :chart-data="data"
    :chart-options="chartConfig"
  />
</template>
