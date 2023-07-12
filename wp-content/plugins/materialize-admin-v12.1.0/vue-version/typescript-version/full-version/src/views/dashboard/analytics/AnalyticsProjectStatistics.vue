<script setup lang="ts">
import illustration3d from '@images/cards/3d-illustration.png'
import square4 from '@images/cards/4-square.png'
import deltaWebApp from '@images/cards/delta-web-app.png'
import ecommerceWebsite from '@images/cards/ecommerce-website.png'
import financeAppDesign from '@images/cards/finance-app-design.png'

const projects = [
  {
    chipText: '$6,500',
    title: '3D Illustration',
    subtitle: 'Blender Illustration',
    src: illustration3d,
  },
  {
    chipText: '$4,290',
    subtitle: 'Figma UI Kit',
    title: 'Finance App Design',
    src: financeAppDesign,
  },
  {
    title: '4 Square',
    chipText: '$44,500',
    subtitle: 'Android Application',
    src: square4,
  },
  {
    chipText: '$12,690',
    title: 'Delta Web App',
    subtitle: 'React Dashboard',
    src: deltaWebApp,
  },
  {
    chipText: '$10,850',
    subtitle: 'Vue + Laravel',
    title: 'eCommerce Website',
    src: ecommerceWebsite,
  },
]

const moreList = [
  { title: 'Last 28 Days', value: 'Last 28 Days' },
  { title: 'Last Month', value: 'Last Month' },
  { title: 'Last Year', value: 'Last Year' },
]
</script>

<template>
  <VCard title="Project Statistics">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VList class="card-list">
        <VListItem class="pb-0">
          <VListItemTitle class="text-xs font-weight-medium d-flex justify-space-between align-center mb-1">
            <span class="text-sm font-weight-medium">
              NAME
            </span>
            <span class="text-xs font-weight-medium">BUDGET</span>
          </VListItemTitle>
          <VDivider />
        </VListItem>

        <VListItem
          v-for="project in projects"
          :key="project.title"
        >
          <template #prepend>
            <VAvatar
              rounded
              size="45"
              variant="tonal"
              class="me-3"
            >
              <VImg
                :src="project.src"
                height="22"
              />
            </VAvatar>
          </template>

          <VListItemTitle class="font-weight-medium text-sm mb-1">
            {{ project.title }}
          </VListItemTitle>

          <VListItemSubtitle class="text-xs">
            {{ project.subtitle }}
          </VListItemSubtitle>

          <template #append>
            <VListItemAction>
              <VChip
                :text="project.chipText"
                color="primary"
                size="x-small"
                class="font-weight-medium"
              />
            </VListItemAction>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 1.125rem;
}
</style>
