<script setup>
import avatar2 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar4 from '@images/avatars/avatar-4.png'
import avatar5 from '@images/avatars/avatar-5.png'
import iphoneImg from '@images/cards/iPhone-bg.png'

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

const avatarGroup = [
  {
    avatar: avatar3,
    tooltip: 'Peter Harper',
  },
  {
    avatar: avatar5,
    tooltip: 'Scott Wells',
  },
  {
    avatar: avatar4,
    tooltip: 'Vivian Padilla',
  },
  {
    avatar: avatar2,
    tooltip: 'Jennie O\'Brien',
  },
]
</script>

<template>
  <VCard>
    <VImg
      :src="iphoneImg"
      height="200"
      cover
    />

    <VCardText>
      <div class="d-flex align-center justify-space-between mb-3">
        <div>
          <VChip
            text="UI Design"
            size="x-small"
            color="success"
            class="me-3"
          />

          <VChip
            text="React"
            size="x-small"
            color="error"
          />
        </div>

        <div class="me-n3">
          <MoreBtn :menu-list="moreList" />
        </div>
      </div>

      <div>
        <h6 class="text-h6 mb-1">
          Finance iOS App
        </h6>

        <p class="font-weight-medium">
          Due Date: 20/Dec/2022
        </p>

        <p class="mb-6">
          Managing your money isn't the easiest thing to do. Now that many of us no longer balance a checkbook, tracking and expenses.
        </p>

        <div class="d-flex justify-space-between font-weight-medium text-sm text-high-emphasis mb-1">
          <span>Progress</span>
          <span>68%</span>
        </div>

        <VProgressLinear
          rounded
          height="8"
          color="success"
          :model-value="68"
          class="mb-6"
        />

        <div class="d-flex justify-space-between align-center">
          <div class="v-avatar-group">
            <VAvatar
              v-for="item in avatarGroup"
              :key="item.avatar"
              :size="40"
            >
              <VImg :src="item.avatar" />

              <VTooltip
                activator="parent"
                location="bottom"
              >
                {{ item.tooltip }}
              </VTooltip>
            </VAvatar>
          </div>

          <div class="d-flex align-center">
            <VIcon
              icon="mdi-paperclip"
              size="22"
              class="me-1"
            />
            <span class="font-weight-medium me-2">24</span>

            <VIcon
              icon="mdi-check-circle-outline"
              class="me-1"
              size="22"
            />
            <span class="font-weight-medium">74/180</span>
          </div>
        </div>
      </div>
    </VCardText>
  </VCard>
</template>
