<script setup>
import avatar2 from '@images/avatars/avatar-2.png'
import pdf from '@images/icons/project-icons/pdf.png'

const moreList = [
  {
    title: 'Refresh',
    value: 'refresh',
  },
  {
    title: 'Update',
    value: 'update',
  },
  {
    title: 'Share',
    value: 'share',
  },
]
</script>

<template>
  <VCard title="Activity Timeline">
    <template #append>
      <div class="mt-n2 me-n3">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText class="mt-4">
      <VTimeline
        side="end"
        align="start"
        line-inset="8"
        truncate-line="both"
        density="compact"
      >
        <VTimelineItem
          dot-color="error"
          size="x-small"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between flex-wrap mb-3">
            <h6 class="text-base font-weight-medium me-3">
              8 Invoices have been paid
            </h6>
            <small class="text-xs text-disabled my-1">Wednesday</small>
          </div>

          <!-- 👉 Content -->
          <p class="mb-2">
            Invoices have been paid to the company.
          </p>
          <div class="d-flex align-center">
            <img
              :src="pdf"
              width="28"
              class="me-2"
              alt="img"
            >

            <span class="font-weight-medium text-sm">
              booking-card.pdf
            </span>
          </div>
        </VTimelineItem>

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

          <p class="mb-0">
            Invoices have been paid to the company.
          </p>

          <!-- 👉 Avatar & Personal Info -->
          <span class="d-flex align-bottom mt-2">
            <VAvatar
              size="24"
              class="me-2"
              :image="avatar2"
            />

            <span class="text-sm font-weight-medium">John Doe (Client)</span>
          </span>
        </VTimelineItem>

        <VTimelineItem
          size="x-small"
          dot-color="success"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between flex-wrap mb-3">
            <h6 class="text-base font-weight-medium me-3">
              Order #37745 from September
            </h6>
            <small class="text-xs text-disabled text-no-wrap my-1">January, 10</small>
          </div>

          <!-- 👉 Content -->
          <p class="mb-0">
            Invoices have been paid to the company.
          </p>
        </VTimelineItem>
      </VTimeline>
    </VCardText>
  </VCard>
</template>
