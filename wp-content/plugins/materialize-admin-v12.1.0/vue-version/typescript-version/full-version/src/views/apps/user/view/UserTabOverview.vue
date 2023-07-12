<script lang="ts" setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import UserInvoiceTable from './UserInvoiceTable.vue'

// Images
import avatar2 from '@images/avatars/avatar-2.png'
import figma from '@images/icons/project-icons/figma.png'
import html5 from '@images/icons/project-icons/html5.png'
import python from '@images/icons/project-icons/python.png'
import react from '@images/icons/project-icons/react.png'
import sketch from '@images/icons/project-icons/sketch.png'
import vue from '@images/icons/project-icons/vue.png'
import xamarin from '@images/icons/project-icons/xamarin.png'

// Project Table Header
const projectTableHeaders = [
  { title: 'PROJECT', key: 'project' },
  { title: 'TOTAL TASK', key: 'totalTask' },
  { title: 'PROGRESS', key: 'progress' },
  { title: 'HOURS', key: 'hours' },
]

const projects = [
  {
    logo: react,
    name: 'BGC eCommerce App',
    project: 'React Project',
    totalTask: '122/240',
    progress: 78,
    hours: '18:42',
  },
  {
    logo: figma,
    name: 'Falcon Logo Design',
    project: 'Figma Project',
    totalTask: '09/56',
    progress: 18,
    hours: '20:42',
  },
  {
    logo: vue,
    name: 'Dashboard Design',
    project: 'Vuejs Project',
    totalTask: '290/320',
    progress: 62,
    hours: '120:87',
  },
  {
    logo: xamarin,
    name: 'Foodista mobile app',
    project: 'Xamarin Project',
    totalTask: '290/320',
    progress: 8,
    hours: '120:87',
  },
  {
    logo: python,
    name: 'Dojo Email App',
    project: 'Python Project',
    totalTask: '120/186',
    progress: 49,
    hours: '230:10',
  },
  {
    logo: sketch,
    name: 'Blockchain Website',
    project: 'Sketch Project',
    totalTask: '99/109',
    progress: 92,
    hours: '342:41',
  },
  {
    logo: html5,
    name: 'Hoffman Website',
    project: 'HTML Project',
    totalTask: '98/110',
    progress: 88,
    hours: '12:45',
  },
]

const resolveUserProgressVariant = (progress: number) => {
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
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VCard title="Project List">
        <VDivider />
        <!-- 👉 User Project List Table -->

        <!-- SECTION Datatable -->
        <VDataTable
          :headers="projectTableHeaders"
          :items="projects"
          class="text-no-wrap rounded-0"
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
                <p class="font-weight-medium text-high-emphasis text-sm mb-0">
                  {{ item.raw.name }}
                </p>
                <p class="text-xs text-medium-emphasis mb-0">
                  {{ item.raw.project }}
                </p>
              </div>
            </div>
          </template>

          <!-- total tasks  -->
          <template #item.totalTask="{ item }">
            <span class="text-high-emphasis text-sm">{{ item.raw.totalTask }}</span>
          </template>

          <!-- Progress -->
          <template #item.progress="{ item }">
            <span class="text-sm text-high-emphasis">{{ item.raw.progress }}%</span>
            <VProgressLinear
              :height="6"
              :model-value="item.raw.progress"
              rounded
              :color="resolveUserProgressVariant(item.raw.progress)"
            />
          </template>

          <!-- hours  -->
          <template #item.hours="{ item }">
            <span class="text-sm">{{ item.raw.hours }}</span>
          </template>

          <!-- remove footer -->
          <!-- TODO refactor this after vuetify community gives answer -->
          <template #bottom />
        </VDataTable>
        <!-- !SECTION -->
      </VCard>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Activity timeline -->
      <VCard title="User Activity Timeline">
        <VCardText>
          <VTimeline
            density="compact"
            align="start"
            truncate-line="both"
            class="v-timeline-density-compact"
          >
            <VTimelineItem
              dot-color="error"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-3">
                <span class="app-timeline-title">
                  12 Invoices have been paid
                </span>
                <span class="app-timeline-meta">12 min ago</span>
              </div>

              <p class="app-timeline-text mb-2">
                Invoices have been paid to the company
              </p>
              <div class="d-flex align-center mt-2">
                <VIcon
                  color="error"
                  icon="mdi-file-pdf-box"
                  size="24"
                  class="me-2"
                />
                <h6 class="font-weight-medium text-sm">
                  Invoices.pdf
                </h6>
              </div>
            </VTimelineItem>

            <VTimelineItem
              dot-color="primary"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-3">
                <span class="app-timeline-title">
                  Meeting with john
                </span>
                <span class="app-timeline-meta">45 min ago</span>
              </div>

              <p class="app-timeline-text mb-1">
                React Project meeting with john @10:15am
              </p>

              <div class="d-flex align-center mt-3">
                <VAvatar
                  size="34"
                  class="me-2"
                  :image="avatar2"
                />
                <div>
                  <h6 class="text-sm font-weight-medium mb-0">
                    John Doe (Client)
                  </h6>
                  <span class="text-xs">CEO of Kelly Group</span>
                </div>
              </div>
            </VTimelineItem>

            <VTimelineItem
              dot-color="info"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-3">
                <span class="app-timeline-title">
                  Create a new react project for client
                </span>
                <span class="app-timeline-meta">2 day ago</span>
              </div>

              <p class="app-timeline-text mb-0">
                Add files to new design folder
              </p>
            </VTimelineItem>

            <VTimelineItem
              dot-color="success"
              size="x-small"
            >
              <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-3">
                <span class="app-timeline-title">
                  12 Create invoices for client
                </span>
                <span class="app-timeline-meta">5 day ago</span>
              </div>
              <p class="app-timeline-text mb-0">
                Weekly review of freshly prepared design for our new app.
              </p>
            </VTimelineItem>
          </VTimeline>
        </VCardText>
      </VCard>
    </VCol>

    <VCol cols="12">
      <UserInvoiceTable />
    </VCol>
  </VRow>
</template>
