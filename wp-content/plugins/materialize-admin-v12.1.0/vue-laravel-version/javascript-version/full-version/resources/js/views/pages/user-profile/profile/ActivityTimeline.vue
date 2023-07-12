<script setup>
import avatar2 from '@images/avatars/avatar-2.png'
import avatar5 from '@images/avatars/avatar-5.png'
import avatar6 from '@images/avatars/avatar-6.png'
</script>

<template>
  <VCard>
    <VCardItem>
      <template #prepend>
        <VIcon
          icon="mdi-format-list-bulleted"
          class="text-disabled"
        />
      </template>

      <VCardTitle>User Activity Timeline</VCardTitle>
    </VCardItem>

    <VCardText>
      <VTimeline
        density="compact"
        align="start"
        truncate-line="start"
        :line-inset="12"
        class="v-timeline-density-compact"
      >
        <VTimelineItem
          dot-color="error"
          size="x-small"
        >
          <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-1">
            <span class="app-timeline-title mb-3">
              12 Invoices have been paid
            </span>
            <span class="app-timeline-meta">12 min ago</span>
          </div>
          <p class="app-timeline-text mb-2">
            Invoices have been paid to the company.
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
          <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-1">
            <span class="app-timeline-title mb-3">
              Meeting with John
            </span>
            <span class="app-timeline-meta">45 min ago</span>
          </div>

          <p class="app-timeline-text mb-2">
            project meeting with john @10:15am
          </p>

          <div class="d-flex align-center mt-3">
            <VAvatar
              size="38"
              class="me-2"
              :image="avatar2"
            />
            <div class="d-flex flex-column">
              <span class="text-sm text-high-emphasis font-weight-medium mb-0">
                John Doe (Client)
              </span>
              <p class="app-timeline-text mb-0">
                Project meeting with john @10:15am
              </p>
            </div>
          </div>
        </VTimelineItem>

        <VTimelineItem
          dot-color="warning"
          size="x-small"
        >
          <div class="d-flex justify-space-between align-center flex-wrap mb-1 gap-2">
            <span class="app-timeline-title mb-3">
              Create a new react project for client
            </span>
            <span class="app-timeline-meta">2 day ago</span>
          </div>
          <p class="app-timeline-text mb-0">
            5 team members in one project.
          </p>
          <div class="v-avatar-group">
            <VAvatar
              size="40"
              :image="avatar2"
            />
            <VAvatar
              size="40"
              :image="avatar5"
            />
            <VAvatar
              size="40"
              :image="avatar6"
            />
            <VAvatar
              :size="40"
              variant="elevated"
              color="grey-200"
            >
              <span class="text-secondary">
                +3
              </span>
            </VAvatar>
          </div>
        </VTimelineItem>

        <VTimelineItem
          dot-color="info"
          size="x-small"
        >
          <div class="d-flex justify-space-between align-center flex-wrap gap-2 mb-1">
            <span class="app-timeline-title mb-3">
              12 create invoice for client
            </span>
            <span class="app-timeline-meta">5 day ago</span>
          </div>
          <p class="app-timeline-text">
            Weekly review of freshly prepared design for our new app.
          </p>
        </VTimelineItem>
      </VTimeline>
    </VCardText>
  </VCard>
</template>
