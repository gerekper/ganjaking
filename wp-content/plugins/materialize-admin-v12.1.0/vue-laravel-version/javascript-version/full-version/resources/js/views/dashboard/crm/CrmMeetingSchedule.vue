<script setup>
import avatar4 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar1 from '@images/avatars/avatar-4.png'
import avatar2 from '@images/avatars/avatar-5.png'
import avatar6 from '@images/avatars/avatar-7.png'
import avatar5 from '@images/avatars/avatar-8.png'

const meetingSchedules = [
  {
    type: 'Business',
    title: 'Call with Woods',
    avatar: avatar1,
    dateTime: '21 Jul | 08:20-10:30',
  },
  {
    type: 'Dinner',
    title: 'Conference call',
    avatar: avatar2,
    dateTime: '28 Jul | 05:00-6:45',
  },
  {
    type: 'Meetup',
    title: 'Meeting with Mark',
    avatar: avatar3,
    dateTime: '03 Aug | 07:00-8:30',
  },
  {
    type: 'Dinner',
    title: 'Meeting in Oakland',
    avatar: avatar4,
    dateTime: '14 Feb | 04:15-05:30',
  },
  {
    type: 'Meditation',
    title: 'Call with Hilda',
    avatar: avatar5,
    dateTime: '24 Jul | 11:30-12:00',
  },
  {
    type: 'Business',
    title: 'Meeting with Carl',
    avatar: avatar6,
    dateTime: '05 Oct | 10:00-12:45',
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

const resolveChipColor = meetingType => {
  if (meetingType === 'Business')
    return 'primary'
  if (meetingType === 'Dinner')
    return 'warning'
  if (meetingType === 'Meetup')
    return 'secondary'
  if (meetingType === 'Dinner')
    return 'error'
  if (meetingType === 'Meditation')
    return 'success'
}
</script>

<template>
  <VCard title="Meeting Schedule">
    <template #append>
      <div class="me-n3 mt-n2">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText>
      <VList
        lines="two"
        class="meeting-schedule-list card-list"
      >
        <VListItem
          v-for="meeting in meetingSchedules"
          :key="meeting.title"
        >
          <!-- 👉 Avatar  -->
          <template #prepend>
            <VAvatar
              rounded
              :size="38"
              :image="meeting.avatar"
              class="me-3"
            />
          </template>

          <!-- 👉 Title and Subtitle -->
          <VListItemTitle class="text-sm font-weight-medium mb-1">
            {{ meeting.title }}
          </VListItemTitle>

          <VListItemSubtitle class="text-xs text-no-wrap d-flex align-center">
            <VIcon
              start
              :size="16"
              icon="mdi-calendar-blank"
            />
            <span>{{ meeting.dateTime }}</span>
          </VListItemSubtitle>

          <!-- 👉 Business Types -->
          <template #append>
            <VChip
              size="x-small"
              :color="resolveChipColor(meeting.type)"
              :text="meeting.type"
            />
          </template>
        </VListItem>
      </VList>
    </VCardText>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  --v-card-list-gap: 1.5625rem;
}
</style>

<style lang="scss">
.meeting-schedule-list {
  .v-list-item__append {
    block-size: 100%;
  }
}
</style>
