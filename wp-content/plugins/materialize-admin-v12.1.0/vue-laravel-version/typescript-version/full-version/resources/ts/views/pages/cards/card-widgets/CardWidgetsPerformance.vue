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
  const variableTheme = vuetifyTheme.current.value.variables

  const secondaryText = `rgba(${hexToRgb(String(themeColors['on-background']))},${variableTheme['medium-emphasis-opacity']})`
  const borderColor = `rgba(${hexToRgb(String(variableTheme['border-color']))},${variableTheme['border-opacity']})`
  const disabledText = `rgba(${hexToRgb(String(themeColors['on-background']))},${variableTheme['disabled-opacity']})`

  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    legend: {
      markers: { offsetX: -2 },
      itemMargin: { horizontal: 10 },
      labels: { colors: secondaryText },
    },
    plotOptions: {
      radar: {
        size: 110,
        polygons: {
          strokeColors: borderColor,
          connectorColors: borderColor,
        },
      },
    },
    fill: {
      type: 'gradient',
      gradient: {
        shade: 'dark',
        gradientToColors: [themeColors.warning, themeColors.primary],
        shadeIntensity: 1,
        type: 'vertical',
        opacityFrom: 1,
        opacityTo: 0.9,
        stops: [0, 100],
      },
    },
    colors: [themeColors.warning, themeColors.primary],
    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
    markers: { size: 0 },
    xaxis: {
      labels: {
        show: true,
        style: {
          fontSize: '14px',
          colors: [
            disabledText,
            disabledText,
            disabledText,
            disabledText,
            disabledText,
            disabledText,
          ],
        },
      },
    },
    yaxis: { show: false },
    grid: { show: false },
  }
})

const series = [
  {
    name: 'Income',
    data: [70, 90, 80, 95, 75, 90],
  },
  {
    name: 'Net Worth',
    data: [110, 72, 62, 65, 100, 75],
  },
]
</script>

<template>
  <VCard title="Performance">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VueApexCharts
        type="radar"
        height="294"
        :options="chartConfig"
        :series="series"
      />
    </VCardText>
  </VCard>
</template>
