<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import figma from '@images/icons/project-icons/figma.png'
import html5 from '@images/icons/project-icons/html5.png'
import python from '@images/icons/project-icons/python.png'
import react from '@images/icons/project-icons/react.png'
import sketch from '@images/icons/project-icons/sketch.png'
import vue from '@images/icons/project-icons/vue.png'
import xamarin from '@images/icons/project-icons/xamarin.png'

const projectTableHeaders = [
  {
    title: 'PROJECT',
    key: 'project',
  },
  {
    title: 'LEADER',
    key: 'leader',
  },
  {
    title: 'PROGRESS',
    key: 'progress',
  },
  {
    title: 'Action',
    key: 'Action',
    sortable: false,
  },
]

const projects = [
  {
    logo: react,
    name: 'BGC eCommerce App',
    project: 'React Project',
    leader: 'Eileen',
    progress: 78,
    hours: '18:42',
  },
  {
    logo: figma,
    name: 'Falcon Logo Design',
    project: 'Figma Project',
    leader: 'Owen',
    progress: 25,
    hours: '20:42',
  },
  {
    logo: vue,
    name: 'Dashboard Design',
    project: 'Vuejs Project',
    leader: 'Keith',
    progress: 62,
    hours: '120:87',
  },
  {
    logo: xamarin,
    name: 'Foodista mobile app',
    project: 'Xamarin Project',
    leader: 'Merline',
    progress: 8,
    hours: '120:87',
  },
  {
    logo: python,
    name: 'Dojo Email App',
    project: 'Python Project',
    leader: 'Harmonia',
    progress: 51,
    hours: '230:10',
  },
  {
    logo: sketch,
    name: 'Blockchain Website',
    project: 'Sketch Project',
    leader: 'Allyson',
    progress: 92,
    hours: '342:41',
  },
  {
    logo: html5,
    name: 'Hoffman Website',
    project: 'HTML Project',
    leader: 'Georgie',
    progress: 80,
    hours: '12:45',
  },
]

const resolveUserProgressVariant = progress => {
  if (progress <= 25)
    return 'error'
  if (progress > 25 && progress <= 50)
    return 'warning'
  if (progress > 50 && progress <= 75)
    return 'primary'
  if (progress > 75 && progress <= 100)
    return 'success'
  
  return 'secondary'
}

const moreList = [
  {
    title: 'Download',
    value: 'Download',
  },
  {
    title: 'Delete',
    value: 'Delete',
  },
  {
    title: 'View',
    value: 'View',
  },
]
</script>

<template>
  <VCard title="Project List">
    <!-- 👉 User Project List Table -->

    <!-- SECTION Datatable -->
    <VDataTable
      :headers="projectTableHeaders"
      :items="projects"
      hide-default-footer
      class="text-no-wrap rounded-0 text-sm text-high-emphasis"
    >
      <!-- projects -->
      <template #item.project="{ item }">
        <div class="d-flex">
          <VAvatar
            :size="34"
            class="me-3"
            :image="item.raw.logo"
          />
          <div>
            <p class="text-sm text-high-emphasis font-weight-medium mb-0">
              {{ item.raw.name }}
            </p>
            <p class="text-xs text-medium-emphasis mb-0">
              {{ item.raw.project }}
            </p>
          </div>
        </div>
      </template>

      <!-- Progress -->
      <template #item.progress="{ item }">
        <div class="d-flex align-center gap-3">
          <div class="flex-grow-1">
            <VProgressLinear
              :height="6"
              :model-value="item.raw.progress"
              rounded
              :color="resolveUserProgressVariant(item.raw.progress)"
            />
          </div>
          <span>{{ item.raw.progress }}%</span>
        </div>
      </template>

      <!-- Action -->
      <template #item.Action>
        <MoreBtn :menu-list="moreList" />
      </template>

      <!-- TODO Refactor this after vuetify provides proper solution for removing default footer -->
      <template #bottom />
    </VDataTable>
    <!-- !SECTION -->
  </VCard>
</template>
