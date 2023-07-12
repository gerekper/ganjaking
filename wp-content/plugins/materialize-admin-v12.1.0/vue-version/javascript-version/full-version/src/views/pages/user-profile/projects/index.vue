<script setup>
import { useRoute } from 'vue-router'
import axios from '@axios'

const router = useRoute()
const projectData = ref([])

const fetchProjectData = () => {
  if (router.params.tab === 'projects') {
    axios.get('/pages/profile', { params: { tab: router.params.tab } }).then(response => {
      projectData.value = response.data
    })
  }
}

watch(router, fetchProjectData, { immediate: true })

const moreList = [
  {
    title: 'Rename Project',
    value: 'Rename Project',
  },
  {
    title: 'View Details',
    value: 'View Details',
  },
  {
    title: 'Add to favorites',
    value: 'Add to favorites',
  },
  {
    type: 'divider',
    class: 'my-2',
  },
  {
    title: 'Leave Project',
    value: 'Leave Project',
    class: 'text-error',
  },
]
</script>

<template>
  <VRow
    v-if="projectData"
    class="match-height"
  >
    <VCol
      v-for="data in projectData"
      :key="data.title"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard>
        <VCardItem>
          <template #prepend>
            <VAvatar
              :image="data.avatar"
              size="38"
            />
          </template>

          <VCardTitle>{{ data.title }}</VCardTitle>
          <p class="mb-0">
            <span class="font-weight-medium me-1">Client:</span>
            <span>{{ data.client }}</span>
          </p>

          <template #append>
            <div class="mt-n8 me-n3">
              <MoreBtn
                item-props
                :menu-list="moreList"
              />
            </div>
          </template>
        </VCardItem>

        <VCardText>
          <div class="d-flex align-center justify-space-between flex-wrap gap-x-2 gap-y-4">
            <div class="pa-2 bg-var-theme-background rounded">
              <h6 class="text-base font-weight-medium">
                {{ data.budgetSpent }} <span class="text-body-1">/ {{ data.budget }}</span>
              </h6>
              <span>Total Budget</span>
            </div>

            <div>
              <h6 class="text-base font-weight-medium">
                Start Date: <span class="text-body-1">{{ data.startDate }}</span>
              </h6>
              <h6 class="text-base font-weight-medium mb-1">
                Deadline: <span class="text-body-1">{{ data.deadline }}</span>
              </h6>
            </div>
          </div>

          <p class="mt-4 mb-0 clamp-text">
            {{ data.description }}
          </p>
        </VCardText>

        <VDivider />

        <VCardText class="pa-6">
          <div class="d-flex align-center justify-space-between flex-wrap gap-2">
            <h6 class="text-base font-weight-medium">
              All Hours: <span class="text-body-1">{{ data.hours }}</span>
            </h6>

            <VChip
              :color="data.chipColor"
              size="small"
            >
              {{ data.daysLeft }} Days left
            </VChip>
          </div>

          <div class="d-flex align-center justify-space-between flex-wrap text-xs mt-4 mb-2">
            <span>Task: {{ data.tasks }}</span>
            <span>{{ Math.floor((data.completedTask / data.totalTask) * 100) }}% Completed</span>
          </div>
          <VProgressLinear
            rounded
            rounded-bar
            height="8"
            :model-value="data.completedTask"
            :max="data.totalTask"
            color="primary"
          />

          <div class="d-flex align-center justify-space-between text-disabled flex-wrap gap-2 mt-3">
            <div class="d-flex align-center">
              <div class="v-avatar-group me-2">
                <VAvatar
                  v-for="avatar in data.avatarGroup"
                  :key="avatar.name"
                  :image="avatar.avatar"
                  :size="36"
                />
              </div>
              <span class="text-xs">
                {{ data.members }}
              </span>
            </div>

            <span>
              <VIcon
                icon="mdi-message-outline"
                class="me-1"
              />
              <span>{{ data.comments }}</span>
            </span>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
