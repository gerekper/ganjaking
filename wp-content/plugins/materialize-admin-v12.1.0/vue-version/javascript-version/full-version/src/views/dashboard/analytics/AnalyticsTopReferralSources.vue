<script setup>
import facebookLogo from '@images/logos/facebook-round.png'
import googleLogo from '@images/logos/google.png'
import instagramLogo from '@images/logos/instagram.png'
import redditLogo from '@images/logos/reddit.png'

const currentTab = ref('google')

const categories = [
  {
    title: 'Google',
    img: { src: googleLogo },
  },
  {
    title: 'Facebook',
    img: { src: facebookLogo },
  },
  {
    title: 'Instagram',
    img: { src: instagramLogo },
  },
  {
    title: 'Reddit',
    img: { src: redditLogo },
  },
]

const productData = {
  google: [
    {
      title: 'Email Marketing Campaign',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: 24,
      totalRevenue: '42,857',
    },
    {
      title: 'Google Workspace',
      status: {
        text: 'Completed',
        color: 'warning',
      },
      conversion: -12,
      totalRevenue: '850',
    },
    {
      title: 'Affiliation Program',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: 24,
      totalRevenue: '5,576',
    },
    {
      title: 'Google AdSense',
      status: {
        text: 'In Draft',
        color: 'info',
      },
      conversion: 0,
      totalRevenue: '0',
    },
  ],
  facebook: [
    {
      title: 'Create Audiences in Ads Manager',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: -8,
      totalRevenue: '322',
    },
    {
      title: 'Facebook page advertising',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: 19,
      totalRevenue: '5,634',
    },
    {
      title: 'Messenger advertising',
      status: {
        text: 'Expired',
        color: 'error',
      },
      conversion: -23,
      totalRevenue: '751',
    },
    {
      title: 'Video campaign',
      status: {
        text: 'Completed',
        color: 'warning',
      },
      conversion: 21,
      totalRevenue: '3,585',
    },
  ],
  instagram: [
    {
      title: 'Create shopping advertising',
      status: {
        text: 'In Draft',
        color: 'info',
      },
      conversion: -15,
      totalRevenue: '599',
    },
    {
      title: 'IGTV advertising',
      status: {
        text: 'Completed',
        color: 'warning',
      },
      conversion: 37,
      totalRevenue: '1,467',
    },
    {
      title: 'Collection advertising',
      status: {
        text: 'In Draft',
        color: 'info',
      },
      conversion: 0,
      totalRevenue: '0',
    },
    {
      title: 'Stories advertising',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: 29,
      totalRevenue: '4,546',
    },
  ],
  reddit: [
    {
      title: 'Interests advertising',
      status: {
        text: 'Expired',
        color: 'error',
      },
      conversion: 2,
      totalRevenue: '404',
    },
    {
      title: 'Community advertising',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: 25,
      totalRevenue: '399',
    },
    {
      title: 'Device advertising',
      status: {
        text: 'Completed',
        color: 'warning',
      },
      conversion: 21,
      totalRevenue: '177',
    },
    {
      title: 'Campaigning',
      status: {
        text: 'Active',
        color: 'primary',
      },
      conversion: -5,
      totalRevenue: '1,139',
    },
  ],
}

const selectedProduct = computed(() => {
  return productData[currentTab.value]
})

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
</script>

<template>
  <VCard
    title="Top Referral Sources"
    subtitle="82% activity growth"
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
          :value="category.title.toLowerCase()"
        >
          <div
            :class="isSelected ? 'selected-category' : 'not-selected-category'"
            class="d-flex flex-column justify-center align-center cursor-pointer rounded px-5 py-2 me-4"
            style="block-size: 6rem; inline-size: 6rem;"
            @click="toggle"
          >
            <VImg
              v-bind="category.img"
              alt="slide-img"
              width="34"
              height="34"
            />
            <h6 class="text-sm">
              {{ category.title }}
            </h6>
          </div>
        </VSlideGroupItem>

        <VSlideGroupItem>
          <div
            class="d-flex flex-column justify-center align-center rounded me-4 not-selected-category"
            style="block-size: 6rem; inline-size: 6rem;"
          >
            <VAvatar
              rounded
              size="30"
              color="default"
              variant="tonal"
            >
              <VIcon icon="mdi-plus" />
            </VAvatar>
          </div>
        </VSlideGroupItem>
      </VSlideGroup>
    </VCardText>

    <VTable class="text-no-wrap">
      <thead>
        <tr>
          <th scope="col">
            PARAMETER
          </th>
          <th scope="col">
            STATUS
          </th>
          <th scope="col">
            CONVERSION
          </th>
          <th scope="col">
            TOTAL REVENUE
          </th>
        </tr>
      </thead>

      <tbody class="text-high-emphasis">
        <tr
          v-for="currentProduct in selectedProduct"
          :key="currentProduct.title"
        >
          <td>
            <span class="font-weight-medium text-sm">{{ currentProduct.title }}</span>
          </td>

          <td class="font-weight-medium">
            <VChip
              size="x-small"
              :color="currentProduct.status.color"
            >
              {{ currentProduct.status.text }}
            </VChip>
          </td>

          <td>
            <span
              class="font-weight-medium text-sm"
              :class="Math.sign(currentProduct.conversion) > 0 ? 'text-success' : 'text-error'"
            >
              {{ Math.sign(currentProduct.conversion) > 0 ? `+${currentProduct.conversion}` : currentProduct.conversion }}%
            </span>
          </td>

          <td
            style="inline-size: 1rem;"
            class="font-weight-medium text-end text-sm"
          >
            ${{ currentProduct.totalRevenue }}
          </td>
        </tr>
      </tbody>
    </VTable>
  </VCard>
</template>

<style lang="scss" scoped>
.v-table--density-default > .v-table__wrapper > table > tbody > tr > td {
  block-size: 46px;
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
