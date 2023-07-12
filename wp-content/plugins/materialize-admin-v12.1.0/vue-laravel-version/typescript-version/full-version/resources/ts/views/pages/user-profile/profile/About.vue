<script lang="ts" setup>
import type { ProfileTab } from '@/@fake-db/types'

interface Props {
  data: ProfileTab
}
const props = defineProps<Props>()
</script>

<template>
  <VCard class="mb-6">
    <VCardText>
      <p class="text-xs mb-5">
        ABOUT
      </p>

      <VList class="card-list text-medium-emphasis">
        <VListItem
          v-for="item in props.data.about"
          :key="item.property"
        >
          <template #prepend>
            <VIcon
              :icon="item.icon"
              size="24"
              class="me-2"
            />
          </template>
          <VListItemTitle>
            <span class="font-weight-medium me-2">{{ item.property }}:</span>
            <span>{{ item.value }}</span>
          </VListItemTitle>
        </VListItem>
      </VList>

      <p class="text-xs mt-6 mb-5">
        CONTACTS
      </p>

      <VList class="card-list text-medium-emphasis">
        <VListItem
          v-for="item in props.data.contacts"
          :key="item.property"
        >
          <template #prepend>
            <VIcon
              :icon="item.icon"
              size="24"
              class="me-2"
            />
          </template>
          <VListItemTitle>
            <span class="font-weight-medium me-1">{{ item.property }}:</span>
            <span>{{ item.value }}</span>
          </VListItemTitle>
        </VListItem>
      </VList>

      <p class="text-xs mt-6 mb-5">
        TEAMS
      </p>

      <VList class="card-list text-medium-emphasis">
        <VListItem
          v-for="item in props.data.teams"
          :key="item.property"
        >
          <template #prepend>
            <VIcon
              :icon="item.icon"
              size="24"
              :color="item.color"
              class="me-2"
            />
          </template>
          <VListItemTitle>
            <span class="font-weight-medium me-1">{{ item.property }}:</span>
            <span>{{ item.value }}</span>
          </VListItemTitle>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>

  <VCard>
    <VCardText>
      <p class="text-xs">
        OVERVIEW
      </p>

      <VList class="card-list text-medium-emphasis">
        <VListItem
          v-for="item in props.data.overview"
          :key="item.property"
        >
          <template #prepend>
            <VIcon
              :icon="item.icon"
              size="24"
              class="me-2"
            />
          </template>
          <VListItemTitle>
            <span class="font-weight-medium me-1">{{ item.property }}:</span>
            <span>{{ item.value }}</span>
          </VListItemTitle>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 16px;
}
</style>
