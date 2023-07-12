<script setup lang="ts">
import { useTheme } from 'vuetify'
import { getRadarChartConfig } from '@core/libs/chartjs/chartjsConfig'
import RadarChart from '@core/libs/chartjs/components/RadarChart'

const vuetifyTheme = useTheme()

const chartConfig = computed(() => getRadarChartConfig(vuetifyTheme.current.value))

const chartData = {
  labels: ['STA', 'STR', 'AGI', 'VIT', 'CHA', 'INT'],
  datasets: [
    {
      fill: true,
      label: 'Donté Panlin',
      borderColor: 'transparent',
      backgroundColor: 'rgba(255,161,161, 0.9)',
      data: [25, 59, 90, 81, 60, 82],
      pointBorderColor: 'transparent',
      pointBackgroundColor: 'transparent',
    },
    {
      fill: true,
      label: 'Mireska Sunbreeze',
      borderColor: 'transparent',
      backgroundColor: 'rgba(155,136,250, 0.9)',
      data: [40, 100, 40, 90, 40, 90],
      pointBorderColor: 'transparent',
      pointBackgroundColor: 'transparent',
    },
  ],
}
</script>

<template>
  <RadarChart
    :height="400"
    :chart-data="chartData"
    :chart-options="chartConfig"
  />
</template>
