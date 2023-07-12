<script lang="ts" setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { getLineChartSimpleConfig } from '@core/libs/apex-chart/apexCharConfig'

const vuetifyTheme = useTheme()

const balanceChartConfig = computed(() => getLineChartSimpleConfig(vuetifyTheme.current.value))

const series = [
  {
    data: [280, 200, 220, 180, 270, 250, 70, 90, 200, 150, 160, 100, 150, 100, 50],
  },
]
</script>

<template>
  <VueApexCharts
    type="line"
    height="400"
    :options="balanceChartConfig"
    :series="series"
  />
</template>
