<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const vuetifyTheme = useTheme()

const series = [
  35,
  30,
  23,
]

const chartOptions = computed(() => {
  const currentTheme = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const textSecondary = `rgba(${ hexToRgb(currentTheme['on-surface']) },${ variableTheme['medium-emphasis-opacity'] })`
  
  return {
    legend: { show: false },
    stroke: {
      width: 5,
      colors: ['rgba(var(--v-theme-surface), 1)'],
    },
    colors: [
      currentTheme.primary,
      currentTheme.success,
      currentTheme.secondary,
    ],
    labels: [
      `${ new Date().getFullYear() }`,
      `${ new Date().getFullYear() - 1 }`,
      `${ new Date().getFullYear() - 2 }`,
    ],
    tooltip: { y: { formatter: val => `${ val }%` } },
    dataLabels: { enabled: false },
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    plotOptions: {
      pie: {
        donut: {
          size: '70%',
          labels: {
            show: true,
            name: { show: false },
            total: {
              label: '',
              show: true,
              fontWeight: 600,
              fontSize: '1rem',
              color: textSecondary,
              formatter: val => typeof val === 'string' ? `${ val }%` : '12%',
            },
            value: {
              offsetY: 6,
              fontWeight: 600,
              fontSize: '1rem',
              formatter: val => `${ val }%`,
              color: textSecondary,
            },
          },
        },
      },
    },
  }
})
</script>

<template>
  <VCard>
    <VCardText>
      <div class="d-flex align-center gap-2">
        <h6 class="text-h6">
          $27.9k
        </h6>
        <span class="text-success font-weight-medium">+16%</span>
      </div>
      <span class="text-sm text-center">
        Total Growth
      </span>

      <VueApexCharts
        type="donut"
        :options="chartOptions"
        :series="series"
        :height="135"
      />
    </VCardText>
  </VCard>
</template>
