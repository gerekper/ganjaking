<script lang="ts" setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { getRadialBarChartConfig } from '@core/libs/apex-chart/apexCharConfig'

const vuetifyTheme = useTheme()

const statisticsChartConfig = computed(() => getRadialBarChartConfig(vuetifyTheme.current.value))

const series = [80, 50, 35]
</script>

<template>
  <VueApexCharts
    type="radialBar"
    height="400"
    :options="statisticsChartConfig"
    :series="series"
  />
</template>
