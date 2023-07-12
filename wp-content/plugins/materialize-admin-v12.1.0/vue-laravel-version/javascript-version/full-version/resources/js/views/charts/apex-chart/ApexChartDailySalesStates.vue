<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { getHeatMapChartConfig } from '@core/libs/apex-chart/apexCharConfig'

const vuetifyTheme = useTheme()
const chartConfig = computed(() => getHeatMapChartConfig(vuetifyTheme.current.value))

const generateDataHeat = (count, yrange) => {
  let i = 0
  const series = []
  while (i < count) {
    const x = `w${ (i + 1).toString() }`
    const y = Math.floor(Math.random() * (yrange.max - yrange.min + 1)) + yrange.min

    series.push({
      x,
      y,
    })
    i += 1
  }
  
  return series
}

const series = [
  {
    name: 'SUN',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
  {
    name: 'MON',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
  {
    name: 'TUE',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
  {
    name: 'WED',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
  {
    name: 'THU',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
  {
    name: 'FRI',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
  {
    name: 'SAT',
    data: generateDataHeat(24, {
      min: 0,
      max: 60,
    }),
  },
]
</script>

<template>
  <VueApexCharts
    type="heatmap"
    height="350"
    :options="chartConfig"
    :series="series"
  />
</template>
