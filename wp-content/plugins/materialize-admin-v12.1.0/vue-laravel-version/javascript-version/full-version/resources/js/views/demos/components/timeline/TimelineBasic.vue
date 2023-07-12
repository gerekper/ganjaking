<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import pdf from '@images/icons/project-icons/pdf.png'
import pumaShoes from '@images/pages/puma-shoes.jpeg'
</script>

<template>
  <VCard title="Basic Timeline">
    <VCardText>
      <VTimeline
        side="end"
        align="start"
        line-inset="8"
        truncate-line="both"
        density="compact"
      >
        <!-- SECTION Timeline Item: Flight -->
        <VTimelineItem
          dot-color="error"
          size="x-small"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
            <span class="app-timeline-title">
              Get on the flight
            </span>
            <span class="app-timeline-meta">Wednesday</span>
          </div>

          <!-- 👉 Content -->
          <p class="app-timeline-text mb-1">
            <span>Charles de Gaulle Airport, Paris</span>
            <VIcon
              size="20"
              icon="mdi-arrow-right"
              class="mx-2 flip-in-rtl"
            />
            <span>Heathrow Airport, London</span>
          </p>

          <p class="app-timeline-meta">
            6:30 AM
          </p>

          <div class="d-flex align-center">
            <img
              :src="pdf"
              width="22"
              class="me-2"
              alt="img"
            >

            <span class="app-timeline-text">
              booking-card.pdf
            </span>
          </div>
        </VTimelineItem>
        <!-- !SECTION -->

        <!-- SECTION Timeline Item: Interview Schedule -->
        <VTimelineItem
          size="x-small"
          dot-color="primary"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
            <span class="app-timeline-title">
              Interview Schedule
            </span>
            <span class="app-timeline-meta">April, 18</span>
          </div>

          <p class="app-timeline-text mb-0">
            Bonbon gummies caramels brownie topping fruitcake gingerbread jelly-o marzipan.
          </p>

          <!-- 👉 Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Person -->
          <div class="d-flex justify-space-between align-center flex-wrap">
            <!-- 👉 Avatar & Personal Info -->
            <span class="d-flex align-bottom mt-2">
              <VAvatar
                size="32"
                class="me-2"
                :image="avatar2"
              />
              <div>
                <h6 class="text-sm font-weight-medium">Rebecca Godman</h6>
                <span class="text-xs">Javascript Developer</span>
              </div>
            </span>

            <!-- 👉 Person Actions -->
            <div>
              <IconBtn class="me-2">
                <VIcon
                  size="20"
                  icon="mdi-message-outline"
                />
              </IconBtn>
              <IconBtn>
                <VIcon
                  size="20"
                  icon="mdi-phone"
                />
              </IconBtn>
            </div>
          </div>
        </VTimelineItem>
        <!-- !SECTION -->

        <!-- SECTION Timeline Item: Puma Shoes -->
        <VTimelineItem
          size="x-small"
          dot-color="info"
        >
          <div class="d-flex align-start flex-sm-row flex-column mb-3">
            <VImg
              aspect-ratio="1"
              width="100"
              :src="pumaShoes"
              class="rounded me-4"
            />

            <div>
              <!-- Header -->
              <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
                <span class="app-timeline-title">
                  Sold Puma POPX Blue Color
                </span>
                <span class="app-timeline-meta">January, 10</span>
              </div>
              <span class="app-timeline-text">PUMA presents the latest shoes from its collection. Light &amp; comfortable made with highly durable material.</span>
            </div>
          </div>

          <!-- 👉 Timeline Item: Meta Content -->
          <div class="d-flex justify-space-between flex-column flex-sm-row gap-3">
            <div class="text-sm-center">
              <h6 class="text-sm font-weight-medium mb-1">
                Customer
              </h6>
              <span class="text-xs">Micheal Scott</span>
            </div>
            <div class="text-sm-center">
              <h6 class="text-sm font-weight-medium mb-1">
                Price
              </h6>
              <span class="text-xs">$375.00</span>
            </div>
            <div class="text-sm-center">
              <h6 class="text-sm font-weight-medium mb-1">
                Quantity
              </h6>
              <span class="text-xs">1</span>
            </div>
          </div>
        </VTimelineItem>
        <!-- !SECTION -->

        <!-- SECTION Design Review -->
        <VTimelineItem
          size="x-small"
          dot-color="success"
        >
          <!-- 👉 Header -->
          <div class="d-flex justify-space-between align-center gap-2 flex-wrap">
            <span class="app-timeline-title">
              Design Review
            </span>
            <span class="app-timeline-meta">September, 20</span>
          </div>

          <!-- 👉 Content -->
          <p>
            Weekly review of freshly prepared design for our new application.
          </p>
          <div class="d-flex align-center">
            <VAvatar
              size="32"
              :image="avatar1"
              class="me-2"
            />
            <h6 class="text-sm font-weight-medium">
              John Doe (Client)
            </h6>
          </div>
        </VTimelineItem>
        <!-- !SECTION -->
      </VTimeline>
    </VCardText>
  </VCard>
</template>
