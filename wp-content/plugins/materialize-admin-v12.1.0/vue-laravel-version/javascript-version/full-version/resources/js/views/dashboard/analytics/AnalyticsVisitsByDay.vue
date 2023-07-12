<script setup>
import VueApexCharts from 'vue3-apexcharts'
import { useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const moreList = [
  {
    title: 'Refresh',
    value: 'refresh',
  },
  {
    title: 'Update',
    value: 'update',
  },
  {
    title: 'Share',
    value: 'share',
  },
]

const vuetifyTheme = useTheme()

const chartConfig = computed(() => {
  const themeColors = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables
  const disabledText = `rgba(${ hexToRgb(String(themeColors['on-background'])) },${ variableTheme['disabled-opacity'] })`
  
  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    legend: { show: false },
    dataLabels: { enabled: false },
    tooltip: { enabled: false },
    colors: [
      `rgba(${ hexToRgb(String(themeColors.primary)) },0.1)`,
      `rgba(${ hexToRgb(String(themeColors.primary)) },1)`,
      `rgba(${ hexToRgb(String(themeColors.primary)) },0.1)`,
      `rgba(${ hexToRgb(String(themeColors.primary)) },1)`,
      `rgba(${ hexToRgb(String(themeColors.primary)) },1)`,
      `rgba(${ hexToRgb(String(themeColors.primary)) },0.1)`,
      `rgba(${ hexToRgb(String(themeColors.primary)) },0.1)`,
    ],
    grid: {
      show: false,
      padding: {
        top: -15,
        left: -7,
        right: -4,
      },
    },
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    plotOptions: {
      bar: {
        borderRadius: 8,
        distributed: true,
        columnWidth: '55%',
        endingShape: 'rounded',
        startingShape: 'rounded',
      },
    },
    xaxis: {
      axisTicks: { show: false },
      axisBorder: { show: false },
      categories: [
        'S',
        'M',
        'T',
        'W',
        'T',
        'F',
        'S',
      ],
      labels: { style: { colors: disabledText } },
    },
    yaxis: { show: false },
  }
})

const series = [{
  data: [
    38,
    55,
    48,
    65,
    80,
    38,
    48,
  ],
}]
</script>

<template>
  <VCard
    title="Visits by Day"
    subtitle="Total 248.5k Visits"
  >
    <template #append>
      <div class="mt-n8 me-n3">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VueApexCharts
        type="bar"
        height="222"
        :options="chartConfig"
        :series="series"
      />

      <div class="d-flex align-center justify-space-between mt-5">
        <div>
          <h6 class="text-base font-weight-medium">
            Most Visited Day
          </h6>
          <span>Total 62.4k Visits on Thursday</span>
        </div>

        <IconBtn
          color="primary"
          variant="tonal"
          class="rounded"
        >
          <VIcon
            icon="mdi-chevron-right"
            class="flip-in-rtl"
          />
        </IconBtn>
      </div>
    </VCardText>
  </VCard>
</template>
