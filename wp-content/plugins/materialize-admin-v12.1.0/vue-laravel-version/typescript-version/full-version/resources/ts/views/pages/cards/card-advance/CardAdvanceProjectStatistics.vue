<script setup lang="ts">
import illustration3d from '@images/cards/3d-illustration.png'
import square4 from '@images/cards/4-square.png'
import deltaWebApp from '@images/cards/delta-web-app.png'
import ecommerceWebsite from '@images/cards/ecommerce-website.png'
import financeAppDesign from '@images/cards/finance-app-design.png'

const projects = [
  {
    budget: '$6,500',
    title: '3D Illustration',
    subtitle: 'Blender Illustration',
    img: illustration3d,
  },
  {
    budget: '$4,290',
    title: 'Finance App Design',
    subtitle: 'Figma UI Kit',
    img: financeAppDesign,
  },
  {
    budget: '$44,500',
    title: '4 Square',
    subtitle: 'Android Application',
    img: square4,
  },
  {
    budget: '$12,690',
    title: 'Delta Web App',
    subtitle: 'React Dashboard',
    img: deltaWebApp,
  },
  {
    budget: '$10,850',
    title: 'eCommerce Website',
    subtitle: 'Vue + Laravel',
    img: ecommerceWebsite,
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
          <VListItemTitle class="d-flex justify-space-between font-weight-medium pb-1">
            <span class="text-xs">
              NAME
            </span>
            <span class="text-xs">BUDGET</span>
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
                :src="project.img"
                height="22"
              />
            </VAvatar>
          </template>

          <VListItemTitle class="font-weight-medium text-sm">
            {{ project.title }}
          </VListItemTitle>

          <VListItemSubtitle class="text-xs">
            {{ project.subtitle }}
          </VListItemSubtitle>

          <template #append>
            <VListItemAction>
              <VChip
                :text="project.budget"
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
  --v-card-list-gap: 1.375rem;
}
</style>
