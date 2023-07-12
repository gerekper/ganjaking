<script setup lang="ts">
import VueApexCharts from 'vue3-apexcharts'
import { useRtl, useTheme } from 'vuetify'
import { hexToRgb } from '@layouts/utils'

const moreList = [
  { title: 'Refresh', value: 'refresh' },
  { title: 'Update', value: 'update' },
  { title: 'Share', value: 'share' },
]

const projects = [
  {
    icon: 'mdi-cellphone',
    color: 'primary',
    title: 'IOS Application',
    task: '840/2.5k',
  },
  {
    icon: 'mdi-creation',
    color: 'success',
    title: 'Web Application',
    task: '99/1.42k',
  },
  {
    icon: 'mdi-credit-card-outline',
    color: 'secondary',
    title: 'Web Application',
    task: '99/1.42k',
  },
  {
    icon: 'mdi-pencil-ruler-outline',
    color: 'info',
    title: 'UI Kit Design',
    task: '120/350',
  },
]

const labels = ['Development Apps', 'UI Design', 'IOS Application', 'Web App Wireframing', 'Prototyping']

const vuetifyTheme = useTheme()

const chartConfig = computed(() => {
  const rtl = useRtl()

  const themeColors = vuetifyTheme.current.value.colors
  const variableTheme = vuetifyTheme.current.value.variables

  const borderColor = `rgba(${hexToRgb(String(variableTheme['border-color']))},${variableTheme['border-opacity']})`
  const disabledText = `rgba(${hexToRgb(String(themeColors['on-background']))},${variableTheme['disabled-opacity']})`
  const primaryText = `rgba(${hexToRgb(String(themeColors['on-background']))},${variableTheme['high-emphasis-opacity']})`

  return {
    chart: {
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    tooltip: { enabled: false },
    plotOptions: {
      bar: {
        barHeight: '60%',
        horizontal: true,
        borderRadius: 15,
        distributed: true,
        endingShape: 'rounded',
        startingShape: 'rounded',
      },
    },
    stroke: {
      width: 2,
      colors: ['rgba(var(--v-theme-surface), 1)'],
    },
    colors: [
      `rgba(${hexToRgb(String(themeColors.primary))}, 1)`,
      `rgba(${hexToRgb(String(themeColors.success))}, 1)`,
      `rgba(${hexToRgb(String(themeColors.secondary))}, 1)`,
      `rgba(${hexToRgb(String(themeColors.info))}, 1)`,
      `rgba(${hexToRgb(String(themeColors.warning))}, 1)`,
    ],
    dataLabels: {
      enabled: true,
      style: { fontWeight: 400 },
      formatter: (val: unknown, opts: { dataPointIndex: number }) => labels[opts.dataPointIndex],
    },
    states: {
      hover: {
        filter: { type: 'none' },
      },
      active: {
        filter: { type: 'none' },
      },
    },
    legend: { show: false },
    grid: {
      strokeDashArray: 6,
      borderColor,
      xaxis: {
        lines: { show: true },
      },
      yaxis: {
        lines: { show: false },
      },
      padding: {
        top: -22,
        left: 20,
        right: 18,
        bottom: 4,
      },
    },
    xaxis: {
      type: 'datetime',
      axisTicks: { show: false },
      axisBorder: { show: false },
      labels: {
        style: { colors: disabledText },
        datetimeFormatter: {
          year: 'MMM',
          month: 'MMM',
        },
      },
    },
    yaxis: {
      labels: {
        show: true,
        align: rtl.isRtl.value ? 'right' : 'left',
        style: {
          fontSize: '0.875rem',
          colors: primaryText,
        },
      },
    },
    responsive: [
      {
        breakpoint: 425,
        options: {
          yaxis: {
            labels: {
              show: false,
            },
          },
          grid: {
            padding: {
              top: -22,
              left: 0,
              right: -18,
              bottom: 4,
            },
          },
        },
      },
    ],
  }
})

const series = [
  {
    data: [
      {
        x: 'Catherine',
        y: [
          new Date(`${new Date().getFullYear()}-01-01`).getTime(),
          new Date(`${new Date().getFullYear()}-05-02`).getTime(),
        ],
      },
      {
        x: 'Janelle',
        y: [
          new Date(`${new Date().getFullYear()}-02-18`).getTime(),
          new Date(`${new Date().getFullYear()}-05-30`).getTime(),
        ],
      },
      {
        x: 'Wellington',
        y: [
          new Date(`${new Date().getFullYear()}-02-07`).getTime(),
          new Date(`${new Date().getFullYear()}-05-31`).getTime(),
        ],
      },
      {
        x: 'Blake',
        y: [
          new Date(`${new Date().getFullYear()}-01-14`).getTime(),
          new Date(`${new Date().getFullYear()}-06-30`).getTime(),
        ],
      },
      {
        x: 'Quinn',
        y: [
          new Date(`${new Date().getFullYear()}-04-01`).getTime(),
          new Date(`${new Date().getFullYear()}-07-31`).getTime(),
        ],
      },
    ],
  },
]
</script>

<template>
  <VCard>
    <VRow no-gutters>
      <VCol
        cols="12"
        sm="8"
        :class="$vuetify.display.xs ? 'border-b' : 'border-e'"
      >
        <VCardItem>
          <VCardTitle>Project Timeline</VCardTitle>
          <VCardSubtitle>Total 840 Task Completed</VCardSubtitle>
        </VCardItem>

        <VueApexCharts
          type="rangeBar"
          height="272"
          :options="chartConfig"
          :series="series"
        />
      </VCol>

      <VCol
        cols="12"
        sm="4"
      >
        <VCardItem>
          <VCardTitle>Project List</VCardTitle>
          <VCardSubtitle>{{ projects.length }} Ongoing Projects</VCardSubtitle>

          <template #append>
            <div class="mt-n7 me-n4">
              <MoreBtn :menu-list="moreList" />
            </div>
          </template>
        </VCardItem>

        <VCardText>
          <VList class="card-list">
            <VListItem
              v-for="project in projects"
              :key="project.title"
            >
              <template #prepend>
                <VAvatar
                  rounded
                  variant="tonal"
                  size="45"
                  :color="project.color"
                >
                  <VIcon
                    size="24"
                    :icon="project.icon"
                  />
                </VAvatar>
              </template>

              <VListItemTitle class="text-sm font-weight-medium">
                {{ project.title }}
              </VListItemTitle>
              <VListItemSubtitle class="text-xs">
                task {{ project.task }}
              </VListItemSubtitle>
            </VListItem>
          </VList>
        </VCardText>
      </VCol>
    </VRow>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 1.5rem;
}
</style>
