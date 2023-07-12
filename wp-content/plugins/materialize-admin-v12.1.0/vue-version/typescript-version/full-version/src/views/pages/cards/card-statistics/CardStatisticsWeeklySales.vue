<script setup lang="ts">
import { VIcon } from 'vuetify/components'
import appleIphone from '@images/cards/apple-iphone-x.png'
import appleWatch from '@images/cards/apple-watch-green.png'
import ps4Joystick from '@images/cards/ps4-joystick.png'

const websiteAnalytics = [
  {
    name: 'Mobiles & Computers',
    slideImg: appleIphone,
    data: [
      {
        number: '24',
        text: 'Mobiles',
      },
      {
        number: '50',
        text: 'Accessories',
      },
      {
        number: '12',
        text: 'Tablets',
      },
      {
        number: '38',
        text: 'Computers',
      },
    ],
  },
  {
    name: 'Appliances & Electronics',
    slideImg: ps4Joystick,
    data: [
      {
        number: '16',
        text: 'Tv\'s',
      },
      {
        number: '40',
        text: 'Speakers',
      },
      {
        number: '19',
        text: 'Cameras',
      },
      {
        number: '18',
        text: 'Consoles',
      },
    ],
  },
  {
    name: 'Fashion',
    slideImg: appleWatch,
    data: [
      {
        number: '16',
        text: 'T-shirts',
      },
      {
        number: '29',
        text: 'Watches',
      },
      {
        number: '43',
        text: 'Shoes',
      },
      {
        number: '17',
        text: 'SubGlasses',
      },
    ],
  },
]
</script>

<template>
  <VCard>
    <VCarousel
      cycle
      :continuous="false"
      :show-arrows="false"
      hide-delimiter-background
      :delimiter-icon="() => h(VIcon, { icon: 'fa-circle', size: '6' })"
      height="auto"
      class="carousel-delimiter-top-end dots-active-primary"
    >
      <VCarouselItem
        v-for="item in websiteAnalytics"
        :key="item.name"
      >
        <VCardText>
          <VRow>
            <VCol cols="12">
              <h6 class="text-h6 mb-1">
                Weekly Sales
              </h6>
              <p class="d-flex align-center gap-2 text-xs mb-0">
                <span>Total $23.5k Earning</span>
                <span class="text-success">
                  +62%
                  <VIcon icon="mdi-chevron-up" />
                </span>
              </p>
            </VCol>

            <VCol
              cols="12"
              class="d-flex align-center gap-4"
            >
              <img
                width="84"
                :src="item.slideImg"
                class="rounded"
              >

              <VRow no-gutters>
                <VCol
                  cols="12"
                  class="pb-0"
                >
                  <h6 class="text-base font-weight-medium mb-0">
                    {{ item.name }}
                  </h6>
                </VCol>

                <VCol
                  v-for="d in item.data"
                  :key="d.number"
                  cols="6"
                  class="text-no-wrap text-truncate text-xs pt-2"
                >
                  <VChip
                    label
                    size="small"
                    class="me-2"
                  >
                    {{ d.number }}
                  </VChip>
                  <span class="me-2">{{ d.text }}</span>
                </VCol>
              </VRow>
            </VCol>

            <VCol cols="12">
              <VBtn
                variant="tonal"
                size="small"
                class="me-3"
              >
                Details
              </VBtn>
              <VBtn size="small">
                Report
              </VBtn>
            </VCol>
          </VRow>
        </VCardText>
      </VCarouselItem>
    </VCarousel>
  </VCard>
</template>
