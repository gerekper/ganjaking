<script setup>
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import { paginationMeta } from '@/@fake-db/utils'
import { useInvoiceStore } from '@/views/apps/invoice/useInvoiceStore'
import { avatarText } from '@core/utils/formatters'

const invoiceListStore = useInvoiceStore()
const searchQuery = ref('')
const dateRange = ref('')
const selectedStatus = ref()
const totalPage = ref(1)
const totalInvoices = ref(0)
const invoices = ref([])
const selectedRows = ref([])

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

const isLoading = ref(false)

// 👉 headers
const headers = [
  {
    title: '#',
    key: 'id',
  },
  {
    title: 'Trending',
    key: 'trending',
    sortable: false,
  },
  {
    title: 'Client',
    key: 'client',
  },
  {
    title: 'Total',
    key: 'total',
  },
  {
    title: 'Date',
    key: 'date',
  },
  {
    title: 'Balance',
    key: 'balance',
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetch Invoices
const fetchInvoices = (query, currentStatus, firstDate, lastDate, option) => {
  isLoading.value = true
  invoiceListStore.fetchInvoices({
    q: query,
    status: currentStatus,
    startDate: firstDate,
    endDate: lastDate,
    options: option,
  }).then(response => {
    invoices.value = response.data.invoices
    totalPage.value = response.data.totalPage
    totalInvoices.value = response.data.totalInvoices
    options.value.page = response.data.page
  }).catch(error => {
    console.log(error)
  })
  isLoading.value = false
}

// 👉 Invoice balance variant resolver
const resolveInvoiceBalanceVariant = (balance, total) => {
  if (balance === total)
    return {
      status: 'Unpaid',
      chip: { color: 'error' },
    }
  if (balance === 0)
    return {
      status: 'Paid',
      chip: { color: 'success' },
    }
  
  return {
    status: balance,
    chip: { variant: 'text' },
  }
}

const resolveInvoiceStatusVariantAndIcon = status => {
  if (status === 'Partial Payment')
    return {
      variant: 'warning',
      icon: 'mdi-chart-timeline-variant',
    }
  if (status === 'Paid')
    return {
      variant: 'success',
      icon: 'mdi-check',
    }
  if (status === 'Downloaded')
    return {
      variant: 'info',
      icon: 'mdi-arrow-down',
    }
  if (status === 'Draft')
    return {
      variant: 'secondary',
      icon: 'mdi-content-save-outline',
    }
  if (status === 'Sent')
    return {
      variant: 'primary',
      icon: 'mdi-email-outline',
    }
  if (status === 'Past Due')
    return {
      variant: 'error',
      icon: 'mdi-alert-circle-outline',
    }
  
  return {
    variant: 'secondary',
    icon: 'mdi-close',
  }
}

const computedMoreList = computed(() => {
  return paramId => [
    {
      title: 'Download',
      value: 'download',
      prependIcon: 'mdi-download-outline',
    },
    {
      title: 'Edit',
      value: 'edit',
      prependIcon: 'mdi-pencil-outline',
      to: {
        name: 'apps-invoice-edit-id',
        params: { id: paramId },
      },
    },
    {
      title: 'Duplicate',
      value: 'duplicate',
      prependIcon: 'mdi-layers-outline',
    },
  ]
})

const deleteInvoice = id => {
  invoiceListStore.deleteInvoice(id).then(() => {
    fetchInvoices(searchQuery.value, selectedStatus.value, dateRange.value?.split('to')[0], dateRange.value?.split('to')[1], options.value)
  }).catch(error => {
    console.log(error)
  })
}

// 👉 watch for data table options like itemsPerPage,page,searchQuery,sortBy etc...
watchEffect(() => {
  const [start, end] = dateRange.value ? dateRange.value.split('to') : ''

  fetchInvoices(searchQuery.value, selectedStatus.value, start, end, options.value)
})
</script>

<template>
  <section v-if="invoices">
    <!-- 👉 Invoice Filters  -->

    <VCard id="invoice-list">
      <VCardText class="d-flex align-center flex-wrap gap-4">
        <!-- 👉 Actions  -->
        <div class="me-3">
          <!-- 👉 Create invoice -->
          <VBtn
            prepend-icon="mdi-plus"
            :to="{ name: 'apps-invoice-add' }"
          >
            Create invoice
          </VBtn>
        </div>

        <VSpacer />

        <div class="d-flex align-center flex-wrap gap-4">
          <!-- 👉 Search  -->
          <div class="invoice-list-search">
            <VTextField
              v-model="searchQuery"
              placeholder="Search Invoice"
              density="compact"
            />
          </div>

          <!-- 👉 Fileter Invoice  -->

          <VSelect
            v-model="selectedStatus"
            density="compact"
            label="Select Status"
            placeholder="Select Status"
            clearable
            clear-icon="mdi-close"
            :items="['Downloaded', 'Draft', 'Sent', 'Paid', 'Partial Payment', 'Past Due']"
            style="inline-size: 12rem;"
          />
        </div>
      </VCardText>

      <!-- SECTION Datatable -->
      <VDataTableServer
        v-model="selectedRows"
        v-model:items-per-page="options.itemsPerPage"
        v-model:page="options.page"
        :loading="isLoading"
        show-select
        :items-length="totalInvoices"
        :headers="headers"
        :items="invoices"
        class="text-no-wrap rounded-0"
        @update:options="options = $event"
      >
        <!-- Trending Header -->
        <template #column.trending>
          <VIcon
            size="22"
            icon="mdi-arrow-up"
          />
        </template>

        <!-- id -->
        <template #item.id="{ item }">
          <RouterLink :to="{ name: 'apps-invoice-preview-id', params: { id: item.value } }">
            <span class="text-sm">
              #{{ item.raw.id }}
            </span>
          </RouterLink>
        </template>

        <!-- trending -->
        <template #item.trending="{ item }">
          <VTooltip>
            <template #activator="{ props }">
              <VAvatar
                :size="34"
                v-bind="props"
                :color="resolveInvoiceStatusVariantAndIcon(item.raw.invoiceStatus).variant"
                variant="tonal"
              >
                <VIcon
                  :size="20"
                  :icon="resolveInvoiceStatusVariantAndIcon(item.raw.invoiceStatus).icon"
                />
              </VAvatar>
            </template>
            <p class="mb-0">
              {{ item.raw.invoiceStatus }}
            </p>
            <p class="mb-0">
              Balance: {{ item.raw.balance }}
            </p>
            <p class="mb-0">
              Due date: {{ item.raw.dueDate }}
            </p>
          </VTooltip>
        </template>

        <!-- client -->
        <template #item.client="{ item }">
          <div class="d-flex align-center">
            <VAvatar
              size="34"
              :color="!item.raw.avatar.length ? resolveInvoiceStatusVariantAndIcon(item.raw.invoiceStatus).variant : undefined"
              :variant="!item.raw.avatar.length ? 'tonal' : undefined"
              class="me-3"
            >
              <VImg
                v-if="item.raw.avatar.length"
                :src="item.raw.avatar"
              />
              <span
                v-else
                class="text-sm"
              >{{ avatarText(item.raw.client.name) }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <h6 class="text-sm font-weight-medium mb-0">
                {{ item.raw.client.name }}
              </h6>
              <span class="text-caption">{{ item.raw.client.companyEmail }}</span>
            </div>
          </div>
        </template>

        <!-- Total -->
        <template #item.total="{ item }">
          <span class="text-sm">
            ${{ item.raw.total }}
          </span>
        </template>

        <!-- Issued Date -->
        <template #item.date="{ item }">
          <span class="text-sm">
            {{ item.raw.issuedDate }}
          </span>
        </template>

        <!-- Balance -->
        <template #item.balance="{ item }">
          <VChip

            v-if="typeof ((resolveInvoiceBalanceVariant(item.raw.balance, item.raw.total)).status) === 'string'"
            :color="resolveInvoiceBalanceVariant(item.raw.balance, item.raw.total).chip.color"
            size="small"
          >
            {{ (resolveInvoiceBalanceVariant(item.raw.balance, item.raw.total)).status }}
          </VChip>
          <span
            v-else
            class="text-sm text-high-emphasis"
          >
            {{ Number((resolveInvoiceBalanceVariant(item.raw.balance, item.raw.total)).status) > 0 ? `$${(resolveInvoiceBalanceVariant(item.raw.balance, item.raw.total)).status}` : `-$${Math.abs(Number((resolveInvoiceBalanceVariant(item.raw.balance, item.raw.total)).status))}` }}
          </span>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <IconBtn @click="deleteInvoice(item.raw.id)">
            <VIcon icon="mdi-delete-outline" />
          </IconBtn>

          <IconBtn :to="{ name: 'apps-invoice-preview-id', params: { id: item.raw.id } }">
            <VIcon icon="mdi-eye-outline" />
          </IconBtn>

          <MoreBtn
            :menu-list="computedMoreList(item.raw.id)"
            item-props
          />
        </template>

        <!-- pagination  -->
        <template #bottom>
          <VDivider />
          <div class="d-flex gap-x-6 flex-wrap justify-end pa-2">
            <div class="d-flex align-center gap-x-2 text-sm">
              Rows Per Page:
              <VSelect
                v-model="options.itemsPerPage"
                variant="plain"
                class="per-page-select text-high-emphasis"
                density="compact"
                :items="[10, 20, 25, 50, 100]"
              />
            </div>
            <div class="d-flex text-sm align-center text-high-emphasis">
              {{ paginationMeta(options, totalInvoices) }}
            </div>
            <div class="d-flex gap-x-2 align-center">
              <VBtn
                class="flip-in-rtl"
                icon="mdi-chevron-left"
                variant="text"
                density="comfortable"
                color="default"
                :disabled="options.page <= 1"
                @click="options.page <= 1 ? options.page = 1 : options.page--"
              />

              <VBtn
                class="flip-in-rtl"
                icon="mdi-chevron-right"
                density="comfortable"
                variant="text"
                color="default"
                :disabled="options.page >= Math.ceil(totalInvoices / options.itemsPerPage)"
                @click="options.page >= Math.ceil(totalInvoices / options.itemsPerPage) ? options.page = Math.ceil(totalInvoices / options.itemsPerPage) : options.page++ "
              />
            </div>
          </div>
        </template>
      </VDataTableServer>
      <!-- !SECTION -->
    </VCard>
  </section>
</template>

<style lang="scss">
#invoice-list {
  .invoice-list-actions {
    inline-size: 8rem;
  }

  .invoice-list-search {
    inline-size: 12rem;
  }
}
</style>
