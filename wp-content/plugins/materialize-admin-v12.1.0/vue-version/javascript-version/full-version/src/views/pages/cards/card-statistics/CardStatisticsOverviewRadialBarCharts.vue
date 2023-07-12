<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const vuetifyTheme = useTheme()
const series = [64]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  
  return {
    chart: { sparkline: { enabled: true } },
    stroke: { lineCap: 'round' },
    colors: [currentTheme.primary],
    plotOptions: {
      radialBar: {
        hollow: { size: '55%' },
        track: { background: currentTheme.background },
        dataLabels: {
          name: { show: false },
          value: {
            offsetY: 5,
            fontWeight: 600,
            fontSize: '1rem',
            color: `rgba(${ hexToRgb(currentTheme['on-surface']) },${ variableTheme['high-emphasis-opacity'] })`,
          },
        },
      },
    },
    grid: { padding: { bottom: -12 } },
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
  }
})
</script>

<template>
  <VCard>
    <VCardText>
      <div class="d-flex align-center gap-2">
        <h6 class="text-h6">
          $67.1k
        </h6>
        <span class="text-success font-weight-medium">+49%</span>
      </div>
      <span class="text-sm text-center">
        Overview
      </span>

      <VueApexCharts
        type="radialBar"
        :options="chartOptions"
        :series="series"
        :height="119"
      />
    </VCardText>
  </VCard>
</template>
