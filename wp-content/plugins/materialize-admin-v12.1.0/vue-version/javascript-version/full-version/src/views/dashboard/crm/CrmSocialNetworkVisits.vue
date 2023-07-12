<script setup>
import dribble from '@images/icons/brands/dribbble.png'
import instagram from '@images/icons/brands/instagram.png'
import facebookRound from '@images/logos/facebook-round.png'
import twitterSocial from '@images/logos/social-twitter.png'

const socialVisits = [
  {
    socialSite: 'Facebook',
    site: 'Social Media',
    totalVisit: '12,348',
    progress: 12,
    logo: facebookRound,
  },
  {
    socialSite: 'Dribbble',
    site: 'Community',
    totalVisit: '8,450',
    progress: 32,
    logo: dribble,
  },
  {
    socialSite: 'Twitter',
    site: 'Social Media',
    totalVisit: '350',
    progress: -18,
    logo: twitterSocial,
  },
  {
    socialSite: 'Instagram',
    site: 'Social Media',
    totalVisit: '25,566',
    progress: 45,
    logo: instagram,
  },
]

const moreList = [
  {
    title: 'Last 28 Days',
    value: 'Last 28 Days',
  },
  {
    title: 'Last Month',
    value: 'Last Month',
  },
  {
    title: 'Last Year',
    value: 'Last Year',
  },
]
</script>

<template>
  <VCard title="Social Network Visits">
    <template #append>
      <div class="me-n4 mt-n2">
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
          v-for="item in socialVisits"
          :key="item.socialSite"
        >
          <template #prepend>
            <VAvatar
              :image="item.logo"
              :size="34"
              class="me-3"
            />
          </template>

          <VListItemTitle class="font-weight-medium text-sm">
            {{ item.socialSite }}
          </VListItemTitle>

          <VListItemSubtitle class="text-xs">
            {{ item.site }}
          </VListItemSubtitle>

          <template #append>
            <div class="d-flex align-center gap-4 font-weight-medium">
              <span>{{ item.totalVisit }}</span>
              <VChip
                :color="Math.sign(item.progress) > 0 ? 'success' : 'error'"
                size="x-small"
              >
                {{ Math.sign(item.progress) > 0 ? `+${item.progress}` : item.progress }}%
              </VChip>
            </div>
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 30px;
}
</style>
