<script setup lang="ts">
import pdf from '@images/icons/project-icons/pdf.png'

const moreList = [
  { title: 'Refresh', value: 'refresh' },
  { title: 'Update', value: 'update' },
  { title: 'Share', value: 'share' },
]
</script>

<template>
  <VCard title="Activity Timeline">
    <template #append>
      <div class="mt-n2 me-n3">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText class="mt-2">
      <VTimeline
        side="end"
        align="start"
        line-inset="8"
        truncate-line="start"
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
              Create youtube video for next product 😎
            </h6>
            <small class="text-xs text-disabled my-1">Tomorrow</small>
          </div>

          <!-- 👉 Content -->
          <p class="text-sm mb-2">
            Product introduction and details video
          </p>

          <div class="d-flex align-center">
            <VIcon
              icon="mdi-play-circle"
              color="error"
              class="me-2"
            />

            <span class="text-truncate font-weight-medium text-sm">
              <a
                target="_blank"
                rel="noopener noreferrer"
                href="https://www.youtube.com/channel/UCuryo5s0CW4aP83itLjIdZg"
              >www.youtube.com/channel/UCuryo5s0CW4aP83itLjIdZg</a>
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
              Received payment from USA client 😍
            </h6>
            <small class="text-xs text-disabled text-no-wrap my-1">January, 18</small>
          </div>

          <!-- 👉 Content -->
          <p class="text-sm mb-2">
            Received payment $1,490 for banking ios app
          </p>
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
              Meeting with joseph morgan for next project
            </h6>
            <small class="text-xs text-disabled text-no-wrap my-1">April, 23</small>
          </div>

          <!-- 👉 Content -->
          <p class="text-sm mb-2">
            Meeting Video call on zoom at 9pm
          </p>
          <div class="d-flex align-center">
            <img
              :src="pdf"
              width="24"
              class="me-2"
              alt="img"
            >

            <span class="font-weight-medium text-sm">
              presentation.pdf
            </span>
          </div>
        </VTimelineItem>
      <!-- !SECTION -->
      </VTimeline>
    </VCardText>
  </VCard>
</template>
