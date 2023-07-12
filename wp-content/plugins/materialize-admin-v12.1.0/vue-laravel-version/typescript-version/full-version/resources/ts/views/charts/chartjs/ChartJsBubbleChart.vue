<script setup lang="ts">
import { useTheme } from 'vuetify'
import type { ChartJsCustomColors } from '@/views/charts/chartjs/types'
import { getBubbleChartConfig } from '@core/libs/chartjs/chartjsConfig'
import BubbleChart from '@core/libs/chartjs/components/BubbleChart'

interface Props {
  colors: ChartJsCustomColors
}

const props = defineProps<Props>()

const vuetifyTheme = useTheme()

const chartConfig = computed(() => getBubbleChartConfig(vuetifyTheme.current.value))

const data = {
  animation: { duration: 10000 },
  datasets: [
    {
      label: 'Dataset 1',
      borderColor: props.colors.primary,
      backgroundColor: props.colors.primary,
      data: [
        { x: 20, y: 74, r: 10 },
        { x: 10, y: 110, r: 5 },
        { x: 30, y: 165, r: 7 },
        { x: 40, y: 200, r: 20 },
        { x: 90, y: 185, r: 7 },
        { x: 50, y: 240, r: 7 },
        { x: 60, y: 275, r: 10 },
        { x: 70, y: 305, r: 5 },
        { x: 80, y: 325, r: 4 },
        { x: 100, y: 310, r: 5 },
        { x: 110, y: 240, r: 5 },
        { x: 120, y: 270, r: 7 },
        { x: 130, y: 300, r: 6 },
      ],
    },
    {
      label: 'Dataset 2',
      borderColor: props.colors.yellow,
      backgroundColor: props.colors.yellow,
      data: [
        { x: 30, y: 72, r: 5 },
        { x: 40, y: 110, r: 7 },
        { x: 20, y: 135, r: 6 },
        { x: 10, y: 160, r: 12 },
        { x: 50, y: 285, r: 5 },
        { x: 60, y: 235, r: 5 },
        { x: 70, y: 275, r: 7 },
        { x: 80, y: 290, r: 4 },
        { x: 90, y: 250, r: 10 },
        { x: 100, y: 220, r: 7 },
        { x: 120, y: 230, r: 4 },
        { x: 110, y: 320, r: 15 },
        { x: 130, y: 330, r: 7 },
      ],
    },
  ],
}
</script>

<template>
  <BubbleChart
    :height="400"
    :chart-data="data"
    :chart-options="chartConfig"
  />
</template>
