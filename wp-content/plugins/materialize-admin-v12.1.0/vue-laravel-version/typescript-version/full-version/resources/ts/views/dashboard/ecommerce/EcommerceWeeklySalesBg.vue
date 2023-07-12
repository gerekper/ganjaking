<script setup lang="ts">
import { VIcon } from 'vuetify/components/VIcon'
import appleIphone from '@images/cards/apple-iphone-x-lg.png'
import appleWatch from '@images/cards/apple-watch-green-lg.png'
import ps4Joystick from '@images/cards/ps4-joystick-lg.png'

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
        number: '12',
        text: 'Tablets',
      },
      {
        number: '50',
        text: 'Accessories',
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
  <VCard color="primary">
    <VCarousel
      cycle
      :continuous="false"
      :show-arrows="false"
      hide-delimiter-background
      :delimiter-icon="() => h(VIcon, { icon: 'fa-circle', size: '6' })"
      height="auto"
      class="carousel-delimiter-top-end weekly-sale-carousel"
    >
      <VCarouselItem
        v-for="item in websiteAnalytics"
        :key="item.name"
      >
        <VCardText>
          <VRow>
            <VCol cols="12">
              <h6 class="text-h6 text-white mb-1">
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
              sm="8"
              lg="7"
              order="2"
              order-sm="1"
            >
              <VRow no-gutters>
                <VCol
                  cols="12"
                  class="pb-0"
                >
                  <p class="text-base font-weight-medium mb-0">
                    {{ item.name }}
                  </p>
                </VCol>

                <VCol
                  v-for="d in item.data"
                  :key="d.number"
                  cols="6"
                  class="text-no-wrap text-truncate pt-4"
                >
                  <VChip
                    label
                    variant="flat"
                    size="default"
                    color="#5A5FE0"
                    class="font-weight-medium text-white rounded me-2"
                  >
                    {{ d.number }}
                  </VChip>
                  <span class="me-2">{{ d.text }}</span>
                </VCol>
              </VRow>
            </VCol>

            <VCol
              cols="12"
              sm="4"
              lg="5"
              order="1"
              order-sm="2"
              class="position-relative text-center"
            >
              <img
                :src="item.slideImg"
                class="card-weekly-sales-img"
                style="filter: drop-shadow(0 4px 40px rgba(0, 0, 0, 40%));"
              >
            </VCol>
          </VRow>
        </VCardText>
      </VCarouselItem>
    </VCarousel>
  </VCard>
</template>

<style lang="scss">
.card-weekly-sales-img {
  block-size: 200px;
}

@media screen and (min-width: 600px) {
  .card-weekly-sales-img {
    position: absolute;
    margin: auto;
    inset-block-end: 40px;
    inset-block-start: -1rem;
    inset-inline-end: 1rem;
  }
}

.weekly-sale-carousel {
  .v-carousel__controls {
    .v-btn:not(.v-btn--active) {
      opacity: 0.4;
    }
  }
}
</style>
