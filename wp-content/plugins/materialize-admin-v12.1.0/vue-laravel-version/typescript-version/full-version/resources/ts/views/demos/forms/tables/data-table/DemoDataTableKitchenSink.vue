<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import type { SalesDetails } from '@/@fake-db/types'
import axios from '@axios'

const search = ref('')
const productList = ref<SalesDetails[]>([])

// headers
const headers = [
  { title: 'PRODUCT', key: 'product.name' },
  { title: 'DATE', key: 'date' },
  { title: 'CATEGORY', key: 'product.category' },
  { title: 'BUYERS', key: 'buyer.name' },
  { title: 'PAYMENT', key: 'payment', sortable: false },
  { title: 'STATUS', key: 'status', sortable: false },
  { title: 'DELETE', key: 'delete', sortable: false },
]

// 👉 methods
const deleteItem = (itemId: number) => {
  const index = productList.value.findIndex(item => item.product.id === itemId)

  productList.value.splice(index, 1)
}

const categoryIcons = [
  { name: 'Mouse', icon: 'mdi-mouse', color: 'warning' },
  { name: 'Glass', icon: 'mdi-glasses', color: 'primary' },
  { name: 'Smart Watch', icon: 'mdi-watch-variant', color: 'success' },
  { name: 'Bag', icon: 'mdi-bag-personal-outline', color: 'info' },
  { name: 'Storage Device', icon: 'mdi-tape-drive', color: 'warning' },
  { name: 'Bluetooth', icon: 'mdi-bluetooth', color: 'error' },
  { name: 'Gaming', icon: 'mdi-controller-classic-outline', color: 'warning' },
  { name: 'Home', icon: 'mdi-home-outline', color: 'error' },
  { name: 'VR', icon: 'mdi-virtual-reality', color: 'primary' },
  { name: 'Shoes', icon: 'mdi-shoe-sneaker', color: 'success' },
  { name: 'Electronics', icon: 'mdi-flash', color: 'info' },
  { name: 'Projector', icon: 'mdi-projector', color: 'warning' },
  { name: 'IPod', icon: 'mdi-ipod', color: 'error' },
  { name: 'Keyboard', icon: 'mdi-keyboard-variant', color: 'primary' },
  { name: 'Smart Phone', icon: 'mdi-cellphone', color: 'success' },
  { name: 'Smart TV', icon: 'mdi-television', color: 'info' },
  { name: 'Google Home', icon: 'mdi-google-home', color: 'warning' },
  { name: 'Mac', icon: 'mdi-apple', color: 'error' },
  { name: 'Headphone', icon: 'mdi-headphones', color: 'primary' },
  { name: 'IMac', icon: 'mdi-desktop-mac', color: 'success' },
  { name: 'IPhone', icon: 'mdi-apple', color: 'warning' },
]

const resolveStatusColor = (status: string) => {
  if (status === 'Confirmed')
    return 'primary'
  if (status === 'Completed')
    return 'success'
  if (status === 'Cancelled')
    return 'error'
}

const categoryIconFilter = (categoryName: string): {
  icon: string
  color: string }[] => {
  const index = categoryIcons.findIndex(category => category.name === categoryName)

  if (index !== -1)
    return [{ icon: categoryIcons[index].icon, color: categoryIcons[index].color }]

  return [{ icon: 'mdi-help-circle-outline', color: 'primary' }]
}

onMounted(() => {
  axios.get('pages/datatables').then(res => {
    productList.value = res.data
  })
})
</script>

<template>
  <div>
    <VCardText>
      <VRow>
        <VCol
          cols="12"
          offset-md="8"
          md="4"
        >
          <VTextField
            v-model="search"
            density="compact"
            label="Search"
            append-inner-icon="mdi-magnify"
            single-line
            hide-details
            dense
            outlined
          />
        </VCol>
      </VRow>
    </VCardText>

    <!-- 👉 Data Table  -->
    <VDataTable
      :headers="headers"
      :items="productList"
      :search="search"
      :items-per-page="5"
    >
      <!-- product -->
      <template #item.product.name="{ item }">
        <div class="d-flex align-center">
          <div>
            <VImg
              :src="item.raw.product.image"
              height="40"
              width="40"
            />
          </div>
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.raw.product.name }}</span>
            <span class="text-xs">{{ item.raw.product.brand }}</span>
          </div>
        </div>
      </template>

      <!-- category -->
      <template #item.product.category="{ item }">
        <div class="d-flex align-center">
          <VAvatar
            v-for="(category, index) in categoryIconFilter(item.raw.product.category)"
            :key="index"
            size="26"
            :color="category.color"
            variant="tonal"
          >
            <VIcon
              size="20"
              :color="category.color"
              class="rounded-0"
            >
              {{ category.icon }}
            </VIcon>
          </VAvatar>
          <span class="ms-1 text-no-wrap">{{ item.raw.product.category }}</span>
        </div>
      </template>

      <!-- buyer -->
      <template #item.buyer.name="{ item }">
        <div class="d-flex align-center">
          <VAvatar
            size="1.875rem"
            :color="!item.raw.avatar ? 'primary' : undefined"
            :variant="!item.raw.avatar ? 'tonal' : undefined"
          >
            <VImg
              v-if="item.raw.buyer.avatar"
              :src="item.raw.buyer.avatar"
            />
            <span v-else>{{ item.raw.buyer.name.slice(0, 2).toUpperCase() }}</span>
          </VAvatar>
          <span class="text-no-wrap font-weight-medium text-high-emphasis ms-2">{{ item.raw.buyer.name }}</span>
        </div>
      </template>

      <!-- Payment -->
      <template #item.payment="{ item }">
        <div class="d-flex flex-column">
          <div class="d-flex align-center">
            <span class="text-high-emphasis font-weight-medium">${{ item.raw.payment.paid_amount }}</span>
            <span v-if="item.raw.payment.paid_amount !== item.raw.payment.total">/{{ item.raw.payment.total }}</span>
          </div>
          <span class="text-xs text-no-wrap">{{ item.raw.payment.received_payment_status }}</span>
        </div>
      </template>

      <!-- Status -->
      <template #item.status="{ item }">
        <VChip
          :color="resolveStatusColor(item.raw.payment.status)"
          :class="`text-${resolveStatusColor(item.raw.payment.status)}`"
          size="small"
          class="font-weight-medium"
        >
          {{ item.raw.payment.status }}
        </VChip>
      </template>

      <!-- Delete -->
      <template #item.delete="{ item }">
        <IconBtn @click="deleteItem(item.raw.product.id)">
          <VIcon icon="mdi-delete-outline" />
        </IconBtn>
      </template>
    </VDataTable>
  </div>
</template>
