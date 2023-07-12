<script setup lang="ts">
import { useRoute } from 'vue-router'
import type { TeamsTab } from '@/@fake-db/types'
import axios from '@axios'

const router = useRoute()
const teamData = ref<TeamsTab[]>([])

const fetchTeamData = () => {
  if (router.params.tab === 'teams') {
    axios.get('/pages/profile', {
      params: {
        tab: router.params.tab,
      },
    }).then(response => {
      teamData.value = response.data
    })
  }
}

watch(router, fetchTeamData, { immediate: true })

const moreList = [
  { title: 'Rename Project', value: 'Rename Project' },
  { title: 'View Details', value: 'View Details' },
  { title: 'Add to favorites', value: 'Add to favorites' },
  { type: 'divider', class: 'my-2' },
  { title: 'Leave Project', value: 'Leave Project', class: 'text-error' },
]
</script>

<template>
  <VRow
    v-if="teamData"
    class="match-height"
  >
    <VCol
      v-for="team in teamData"
      :key="team.title"
      cols="12"
      md="6"
      lg="4"
    >
      <VCard :title="team.title">
        <template #prepend>
          <VAvatar
            size="38"
            :image="team?.avatar"
          />
        </template>

        <template #append>
          <div class="me-n3">
            <IconBtn>
              <VIcon
                icon="mdi-star-outline"
                class="text-disabled"
              />
            </IconBtn>

            <MoreBtn
              item-props
              :menu-list="moreList"
              class="text-disabled"
            />
          </div>
        </template>

        <VCardText>
          <span class="text-base">{{ team.description }}</span>
        </VCardText>

        <VCardText class="d-flex align-center">
          <div class="v-avatar-group">
            <VAvatar
              v-for="data in team.avatarGroup"
              :key="data.name"
              size="36"
            >
              <VImg :src="data.avatar" />

              <VTooltip
                activator="parent"
                location="top"
              >
                {{ data.name }}
              </VTooltip>
            </VAvatar>
          </div>

          <VSpacer />

          <div class="d-flex align-center gap-2">
            <VChip
              v-for="data in team.chips"
              :key="data.title"
              :color="data.color"
              size="small"
              class="font-weight-medium"
            >
              {{ data.title }}
            </VChip>
          </div>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>
</template>
