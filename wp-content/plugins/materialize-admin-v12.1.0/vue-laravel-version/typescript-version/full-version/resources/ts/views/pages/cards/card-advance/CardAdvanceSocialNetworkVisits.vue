<script setup lang="ts">
import dribbleLogo from '@images/icons/brands/dribbble.png'
import instagramLogo from '@images/icons/brands/instagram.png'
import twitterLogo from '@images/icons/brands/twitter.png'
import facebookRound from '@images/logos/facebook-round.png'

const visits = [
  {
    visits: '12,348',
    progress: 12,
    siteName: 'Facebook',
    category: 'Social Media',
    logo: facebookRound,
  },
  {
    visits: '8,450',
    progress: 32,
    siteName: 'Dribbble',
    category: 'Community',
    logo: dribbleLogo,
  },
  {
    visits: '350',
    progress: -18,
    siteName: 'Twitter',
    category: 'Social Media',
    logo: twitterLogo,
  },
  {
    visits: '25,566',
    progress: 45,
    siteName: 'Instagram',
    category: 'Social Media',
    logo: instagramLogo,
  },
]

const moreList = [
  { title: 'Last 28 Days', value: 'Last 28 Days' },
  { title: 'Last Month', value: 'Last Month' },
  { title: 'Last Year', value: 'Last Year' },
]
</script>

<template>
  <VCard title="Social Network Visits">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <div class="d-flex align-center">
        <h5 class="text-h5 me-1">
          28,468
        </h5>

        <VIcon
          icon="mdi-menu-up"
          color="success"
          size="30"
        />

        <span class="text-success font-weight-medium">62%</span>
      </div>
      <p class="text-xs mb-5">
        last 1 year visits
      </p>

      <VList class="card-list">
        <VListItem
          v-for="item in visits"
          :key="item.siteName"
        >
          <template #prepend>
            <VAvatar
              rounded
              :size="34"
              class="me-3"
            >
              <VImg :src="item.logo" />
            </VAvatar>
          </template>

          <VListItemTitle class="font-weight-medium text-sm">
            {{ item.siteName }}
          </VListItemTitle>

          <VListItemSubtitle class="text-xs">
            {{ item.category }}
          </VListItemSubtitle>

          <template #append>
            <div class="d-flex align-center gap-4 font-weight-medium">
              <span>{{ item.visits }}</span>
              <VChip
                :color="item.progress > 0 ? 'success' : 'error'"
                size="x-small"
              >
                {{ item.progress > 0 ? `+${item.progress}%` : `${item.progress}%` }}
              </VChip>
            </div>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>
