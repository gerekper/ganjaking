<script setup lang="ts">
import mobile2 from '@images/cards/apple-iPhone-13-pro.png'
import desktop1 from '@images/cards/apple-mac-mini.png'
import desktop3 from '@images/cards/dell-inspiron-3000.png'
import desktop2 from '@images/cards/hp-envy-x360.png'
import console3 from '@images/cards/nintendo-switch.png'
import mobile3 from '@images/cards/oneplus-9-pro.png'
import mobile1 from '@images/cards/samsung-s22.png'
import console1 from '@images/cards/sony-play-station-5.png'
import catImg3 from '@images/cards/tabs-console.png'
import catImg2 from '@images/cards/tabs-desktop.png'
import catImg1 from '@images/cards/tabs-mobile.png'
import console2 from '@images/cards/xbox-series-x.png'

interface Product {
  status: string
  revenue: string
  conversion: number
  product: string
  image: string
}

interface ProductData {
  mobile: Product[]
  desktop: Product[]
  console: Product[]
}

const currentTab = ref<keyof ProductData>('mobile')

const resolveChipColor = (status: string) => {
  if (status === 'In Stock')
    return 'success'
  if (status === 'Out of Stock')
    return 'primary'
  if (status === 'Comming Soon')
    return 'warning'
}

const categories = [
  {
    title: 'mobile',
    img: { src: catImg1, width: 30, height: 58 },
  },
  {
    title: 'desktop',
    img: { src: catImg2, width: 52, height: 42 },

  },
  {

    title: 'console',
    img: { src: catImg3, width: 60, height: 42 },
  },
]

const productData: ProductData = {
  mobile: [
    {
      status: 'Out of Stock',
      revenue: '12.5k',
      conversion: 24,
      product: 'Samsung s22',
      image: mobile1,
    },
    {
      status: 'In Stock',
      revenue: '45k',
      conversion: -18,
      product: 'Apple iPhone 13 Pro',
      image: mobile2,
    },
    {
      status: 'Comming Soon',
      revenue: '98.2k',
      conversion: 55,
      product: 'Oneplus 9 Pro',
      image: mobile3,
    },
  ],
  desktop: [
    {
      status: 'In Stock',
      revenue: '94.6k',
      conversion: 16,
      product: 'Apple Mac Mini',
      image: desktop1,
    },
    {
      status: 'Comming Soon',
      revenue: '76.5k',
      conversion: 27,
      product: 'Newest HP Envy x360',
      image: desktop2,
    },
    {
      status: 'Out of Stock',
      revenue: '69.3k',
      conversion: -9,
      product: 'Dell Inspiron 3000',
      image: desktop3,
    },
  ],
  console: [
    {
      status: 'Comming Soon',
      revenue: '18.6k',
      conversion: 34,
      product: 'Sony Play Station 5',
      image: console1,
    },
    {
      status: 'Out of Stock',
      revenue: '29.7k',
      conversion: -21,
      product: 'XBOX Series X',
      image: console2,
    },
    {
      status: 'In Stock',
      revenue: '10.4k',
      conversion: 38,
      product: 'Nintendo Switch',
      image: console3,
    },
  ],
}

const moreList = [
  { title: 'Last 28 Days', value: 'Last 28 Days' },
  { title: 'Last Month', value: 'Last Month' },
  { title: 'Last Year', value: 'Last Year' },
]
</script>

<template>
  <VCard
    title="Top Referral Sources"
    subtitle="82% Activity Growth"
  >
    <template #append>
      <div class="me-n2 mt-n8">
        <MoreBtn :menu-list="moreList" />
      </div>
    </template>

    <VCardText class="pb-2">
      <VSlideGroup
        v-model="currentTab"
        show-arrows
        mandatory
      >
        <VSlideGroupItem
          v-for="category in categories"
          :key="category.title"
          v-slot="{ isSelected, toggle }"
          :value="category.title"
        >
          <div
            :class="isSelected ? 'selected-category' : 'not-selected-category'"
            class="d-flex flex-column justify-center align-center cursor-pointer rounded px-5 py-2 me-4"
            style="block-size: 6rem;inline-size: 7rem;"
            @click="toggle"
          >
            <VImg
              v-bind="category.img"
              alt="slide-img"
            />
          </div>
        </VSlideGroupItem>

        <VSlideGroupItem>
          <div
            class="d-flex flex-column justify-center align-center cursor-pointer rounded me-4 not-selected-category"
            style="block-size: 6rem;inline-size: 7rem;"
          >
            <VAvatar
              rounded
              size="30"
              color="default"
              variant="tonal"
              class="text-disabled"
            >
              <VIcon icon="mdi-plus" />
            </VAvatar>
          </div>
        </VSlideGroupItem>
      </VSlideGroup>
    </VCardText>

    <VTable class="text-no-wrap mb-2 text-sm">
      <thead>
        <tr>
          <th scope="col">
            IMAGE
          </th>
          <th scope="col">
            PRODUCT NAME
          </th>
          <th
            scope="col"
            class="text-end"
          >
            STATUS
          </th>
          <th
            scope="col"
            class="text-end"
          >
            REVENUE
          </th>
          <th
            scope="col"
            class="text-end"
          >
            CONVERSION
          </th>
        </tr>
      </thead>

      <tbody class="text-high-emphasis">
        <tr
          v-for="currentProduct in productData[currentTab]"
          :key="currentProduct.product"
        >
          <td style="inline-size: 6vw;">
            <VAvatar
              rounded
              :image="currentProduct.image"
              size="34"
            />
          </td>

          <td
            class="font-weight-medium"
            style="inline-size: 20vw;"
          >
            {{ currentProduct.product }}
          </td>

          <td class="text-end">
            <VChip
              :text="currentProduct.status"
              :color="resolveChipColor(currentProduct.status)"
              size="x-small"
            />
          </td>

          <td class="text-end font-weight-medium">
            ${{ currentProduct.revenue }}
          </td>

          <td class="font-weight-medium text-end">
            <span :class="currentProduct.conversion > 0 ? 'text-success' : 'text-error'">

              {{ currentProduct.conversion > 0 ? `+${currentProduct.conversion}%` : `${currentProduct.conversion}%` }}
            </span>
          </td>
        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>

<style lang="scss" scoped>
.card-list {
  color: aliceblue;
}

.selected-category {
  border: 2px solid rgb(var(--v-theme-primary));
}

.not-selected-category {
  border: 2px dashed rgba(var(--v-border-color), var(--v-border-opacity));
}

.v-table .v-table__wrapper > table > tbody > tr:not(:last-child) > td {
  border-block-end: none;
}
</style>
