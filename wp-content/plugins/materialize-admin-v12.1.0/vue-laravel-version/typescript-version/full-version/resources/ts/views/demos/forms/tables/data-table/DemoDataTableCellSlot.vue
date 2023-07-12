<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  { title: 'NAME', key: 'full_name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'DATE', key: 'start_date' },
  { title: 'SALARY', key: 'salary' },
  { title: 'AGE', key: 'age' },
  { title: 'STATUS', key: 'status' },
]

const resolveStatusVariant = (status: number) => {
  if (status === 1)
    return { color: 'primary', text: 'Current' }
  else if (status === 2)
    return { color: 'success', text: 'Professional' }
  else if (status === 3)
    return { color: 'error', text: 'Rejected' }
  else if (status === 4)
    return { color: 'warning', text: 'Resigned' }
  else
    return { color: 'info', text: 'Applied' }
}
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    :items-per-page="5"
  >
    <!-- full name -->
    <template #item.full_name="{ item }">
      <div class="d-flex align-center">
        <VAvatar
          size="32"
          :color="item.raw.avatar ? '' : 'primary'"
          :class="item.raw.avatar ? '' : 'v-avatar-light-bg primary--text'"
          :variant="!item.raw.avatar ? 'tonal' : undefined"
        >
          <VImg
            v-if="item.raw.avatar"
            :src="item.raw.avatar"
          />
          <span
            v-else
            class="text-sm"
          >{{ avatarText(item.raw.full_name) }}</span>
        </VAvatar>
        <div class="d-flex flex-column ms-3">
          <span class="d-block font-weight-medium text-high-emphasis text-truncate">{{ item.raw.full_name }}</span>
          <small>{{ item.raw.post }}</small>
        </div>
      </div>
    </template>

    <template #item.status="{ item }">
      <VChip
        :color="resolveStatusVariant(item.raw.status).color"
        class="font-weight-medium"
        size="small"
      >
        {{ resolveStatusVariant(item.raw.status).text }}
      </VChip>
    </template>
  </VDataTable>
</template>
