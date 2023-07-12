<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import activityTimeline from '@images/cards/activity-timeline.png'
import pdf from '@images/icons/project-icons/pdf.png'
</script>

<template>
  <VCard>
    <VImg
      :src="activityTimeline"
      height="168"
      cover
    />

    <VCardItem>
      <VCardTitle>Activity Timeline</VCardTitle>
    </VCardItem>

    <VCardText>
      <VTimeline
        side="end"
        align="start"
        line-inset="16"
        truncate-line="both"
        density="compact"
      >
        <!-- SECTION Timeline Item: invoices -->
        <VTimelineItem
          dot-color="error"
          size="x-small"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between flex-wrap mb-3">
            <h6 class="text-base font-weight-medium">
              8 Invoices have been paid
            </h6>
            <small class="text-xs text-disabled my-1">Wednesday</small>
          </div>

          <!-- 👉 Content -->
          <p class="text-sm mb-2">
            Invoices have been paid to the company.
          </p>

          <div class="d-flex align-center">
            <img
              :src="pdf"
              width="24"
              class="me-2"
              alt="img"
            >

            <span class="font-weight-medium text-sm">
              booking-card.pdf
            </span>
          </div>
        </VTimelineItem>
        <!-- !SECTION -->

        <!-- SECTION Timeline Item: new Project -->
        <VTimelineItem
          size="x-small"
          dot-color="primary"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between flex-wrap mb-3">
            <h6 class="text-base font-weight-medium me-3">
              Create a new project for client 😎
            </h6>
            <small class="text-xs text-disabled text-no-wrap my-1">April, 18</small>
          </div>

          <!-- 👉 Content -->
          <p class="text-sm mb-2">
            Invoices have been paid to the company.
          </p>
          <div class="d-flex align-center">
            <VAvatar
              :image="avatar1"
              size="24"
              class="me-3"
            />
            <h6 class="text-sm font-weight-medium text-medium-emphasis">
              John Doe (Client)
            </h6>
          </div>
        </VTimelineItem>
        <!-- !SECTION -->

        <!-- SECTION Order details -->
        <VTimelineItem
          size="x-small"
          dot-color="success"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between flex-wrap mb-3">
            <h6 class="text-base font-weight-medium me-3">
              Order #37745 from September
            </h6>
            <small class="text-xs text-disabled text-no-wrap my-1">September, 20</small>
          </div>

          <!-- 👉 Content -->
          <p class="text-sm mb-2">
            Invoices have been paid to the company.
          </p>
        </VTimelineItem>
      <!-- !SECTION -->
      </VTimeline>
    </VCardText>
  </VCard>
</template>
