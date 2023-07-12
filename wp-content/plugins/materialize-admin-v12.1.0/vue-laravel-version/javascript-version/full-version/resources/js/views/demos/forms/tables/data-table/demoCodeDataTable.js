export const basic = { ts: `<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  { title: 'ID', sortable: false, key: 'id' },
  { title: 'NAME', key: 'full_name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'DATE', key: 'start_date' },
  { title: 'EXPERIENCE', key: 'experience' },
  { title: 'AGE', key: 'age' },
]
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    :items-per-page="5"
  />
</template>
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  {
    title: 'ID',
    sortable: false,
    key: 'id',
  },
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'EXPERIENCE',
    key: 'experience',
  },
  {
    title: 'AGE',
    key: 'age',
  },
]
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    :items-per-page="5"
  />
</template>
` }

export const cellSlot = { ts: `<script setup lang="ts">
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
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
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
` }

export const dense = { ts: `<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  { title: 'ID', sortable: false, key: 'id' },
  { title: 'NAME', key: 'full_name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'DATE', key: 'start_date' },
  { title: 'EXPERIENCE', key: 'experience' },
  { title: 'AGE', key: 'age' },
]
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    density="compact"
    :items-per-page="5"
  />
</template>
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  {
    title: 'ID',
    sortable: false,
    key: 'id',
  },
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'EXPERIENCE',
    key: 'experience',
  },
  {
    title: 'AGE',
    key: 'age',
  },
]
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    density="compact"
    :items-per-page="5"
  />
</template>
` }

export const expandableRows = { ts: `<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

// Headers
const headers = [
  { title: '', key: 'data-table-expand' },
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
    expand-on-click
  >
    <!-- Expanded Row Data -->
    <template #expanded-row="slotProps">
      <tr class="v-data-table__tr">
        <td :colspan="headers.length">
          <p class="my-1">
            City: {{ slotProps.item.raw.city }}
          </p>
          <p class="my-1">
            Experience: {{ slotProps.item.raw.experience }}
          </p>
          <p>Post: {{ slotProps.item.raw.post }}</p>
        </td>
      </tr>
    </template>

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
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  {
    title: '',
    key: 'data-table-expand',
  },
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
}
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    :items-per-page="5"
    expand-on-click
  >
    <!-- Expanded Row Data -->
    <template #expanded-row="slotProps">
      <tr class="v-data-table__tr">
        <td :colspan="headers.length">
          <p class="my-1">
            City: {{ slotProps.item.raw.city }}
          </p>
          <p class="my-1">
            Experience: {{ slotProps.item.raw.experience }}
          </p>
          <p>Post: {{ slotProps.item.raw.post }}</p>
        </td>
      </tr>
    </template>

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
` }

export const externalPagination = { ts: `<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import type { Data } from '@/@fake-db/types'
import data from '@/views/demos/forms/tables/data-table/datatable'

const userList = ref<Data[]>([])
const options = ref({ page: 1, itemsPerPage: 5, sortBy: [''], sortDesc: [false] })

// headers
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

onMounted(() => {
  userList.value = JSON.parse(JSON.stringify(data))
})
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="userList"
    :items-per-page="options.itemsPerPage"
    :page="options.page"
    @update:options="options = $event"
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

    <!-- status -->
    <template #item.status="{ item }">
      <VChip
        :color="resolveStatusVariant(item.raw.status).color"
        class="font-weight-medium"
        size="small"
      >
        {{ resolveStatusVariant(item.raw.status).text }}
      </VChip>
    </template>

    <template #bottom>
      <VCardText class="pt-2">
        <div class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2">
          <VTextField
            v-model="options.itemsPerPage"
            label="Rows per page:"
            type="number"
            min="-1"
            max="15"
            hide-details
            variant="underlined"
            style="min-width: 5rem;max-width: 8rem;"
          />

          <VPagination
            v-model="options.page"
            :total-visible="$vuetify.display.smAndDown ? 3 : 5"
            :length="Math.ceil(userList.length / options.itemsPerPage)"
          />
        </div>
      </VCardText>
    </template>
  </VDataTable>
</template>
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const userList = ref([])

const options = ref({
  page: 1,
  itemsPerPage: 5,
  sortBy: [''],
  sortDesc: [false],
})

// headers
const headers = [
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
}

onMounted(() => {
  userList.value = JSON.parse(JSON.stringify(data))
})
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="userList"
    :items-per-page="options.itemsPerPage"
    :page="options.page"
    @update:options="options = $event"
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

    <!-- status -->
    <template #item.status="{ item }">
      <VChip
        :color="resolveStatusVariant(item.raw.status).color"
        class="font-weight-medium"
        size="small"
      >
        {{ resolveStatusVariant(item.raw.status).text }}
      </VChip>
    </template>

    <template #bottom>
      <VCardText class="pt-2">
        <div class="d-flex flex-wrap justify-center justify-sm-space-between gap-y-2 mt-2">
          <VTextField
            v-model="options.itemsPerPage"
            label="Rows per page:"
            type="number"
            min="-1"
            max="15"
            hide-details
            variant="underlined"
            style="min-width: 5rem;max-width: 8rem;"
          />

          <VPagination
            v-model="options.page"
            :total-visible="$vuetify.display.smAndDown ? 3 : 5"
            :length="Math.ceil(userList.length / options.itemsPerPage)"
          />
        </div>
      </VCardText>
    </template>
  </VDataTable>
</template>
` }

export const fixedHeader = { ts: `<script setup lang="ts">
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
    :items-per-page="10"
    height="300"
    fixed-header
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

    <!-- status -->
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
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
}
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    :items-per-page="10"
    height="300"
    fixed-header
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

    <!-- status -->
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
` }

export const groupingRows = { ts: `<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'

import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar4 from '@images/avatars/avatar-4.png'
import avatar7 from '@images/avatars/avatar-7.png'
import avatar8 from '@images/avatars/avatar-8.png'

const userList = [
  {
    responsive_id: '',
    id: 1,
    avatar: avatar8,
    full_name: 'Korrie O\'Crevy',
    post: 'Nuclear Power Engineer',
    email: 'kocrevy0@thetimes.co.uk',
    city: 'Krasnosilka',
    start_date: '09/23/2016',
    salary: 23896.35,
    age: '61',
    experience: '1 Year',
    status: 'Professional',
  },
  {
    responsive_id: '',
    id: 2,
    avatar: avatar1,
    full_name: 'Bailie Coulman',
    post: 'VP Quality Control',
    email: 'bcoulman1@yolasite.com',
    city: 'Hinigaran',
    start_date: '05/20/2018',
    salary: 13633.69,
    age: '63',
    experience: '3 Years',
    status: 'Professional',
  },
  {
    responsive_id: '',
    id: 3,
    avatar: avatar7,
    full_name: 'Stella Ganderton',
    post: 'Operator',
    email: 'sganderton2@tuttocitta.it',
    city: 'Golcowa',
    start_date: '03/24/2018',
    salary: 13076.28,
    age: '66',
    experience: '6 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 4,
    avatar: avatar8,
    full_name: 'Dorolice Crossman',
    post: 'Cost Accountant',
    email: 'dcrossman3@google.co.jp',
    city: 'Paquera',
    start_date: '12/03/2017',
    salary: 12336.17,
    age: '22',
    experience: '2 Years',
    status: 'Professional',
  },
  {
    responsive_id: '',
    id: 6,
    avatar: '',
    full_name: 'Genevra Honeywood',
    post: 'Geologist',
    email: 'ghoneywood5@narod.ru',
    city: 'Maofan',
    start_date: '06/01/2017',
    salary: 17803.8,
    age: '61',
    experience: '1 Year',
    status: 'Current',
  },
  {
    responsive_id: '',
    id: 7,
    avatar: '',
    full_name: 'Eileen Diehn',
    post: 'Environmental Specialist',
    email: 'ediehn6@163.com',
    city: 'Lampuyang',
    start_date: '10/15/2017',
    salary: 18991.67,
    age: '59',
    experience: '9 Years',
    status: 'Rejected',
  },
  {
    responsive_id: '',
    id: 8,
    avatar: avatar7,
    full_name: 'Richardo Aldren',
    post: 'Senior Sales Associate',
    email: 'raldren7@mtv.com',
    city: 'Skoghall',
    start_date: '11/05/2016',
    salary: 19230.13,
    age: '55',
    experience: '5 Years',
    status: 'Rejected',
  },
  {
    responsive_id: '',
    id: 9,
    avatar: avatar2,
    full_name: 'Allyson Moakler',
    post: 'Safety Technician',
    email: 'amoakler8@shareasale.com',
    city: 'Mogilany',
    start_date: '12/29/2018',
    salary: 11677.32,
    age: '39',
    experience: '9 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 11,
    avatar: '',
    full_name: 'De Falloon',
    post: 'Sales Representative',
    email: 'dfalloona@ifeng.com',
    city: 'Colima',
    start_date: '06/12/2018',
    salary: 19252.12,
    age: '30',
    experience: '0 Year',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 12,
    avatar: '',
    full_name: 'Cyrus Gornal',
    post: 'Senior Sales Associate',
    email: 'cgornalb@fda.gov',
    city: 'Boro Utara',
    start_date: '12/09/2017',
    salary: 16745.47,
    age: '22',
    experience: '2 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 13,
    avatar: '',
    full_name: 'Tallou Balf',
    post: 'Staff Accountant',
    email: 'tbalfc@sina.com.cn',
    city: 'Siliana',
    start_date: '01/21/2016',
    salary: 15488.53,
    age: '36',
    experience: '6 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 15,
    avatar: '',
    full_name: 'Wilmar Bourton',
    post: 'Administrative Assistant',
    email: 'wbourtone@sakura.ne.jp',
    city: 'Bích Động',
    start_date: '04/25/2018',
    salary: 13304.45,
    age: '19',
    experience: '9 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 16,
    avatar: avatar4,
    full_name: 'Robinson Brazenor',
    post: 'General Manager',
    email: 'rbrazenorf@symantec.com',
    city: 'Gendiwu',
    start_date: '12/23/2017',
    salary: 11953.08,
    age: '66',
    experience: '6 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 17,
    avatar: '',
    full_name: 'Nadia Bettenson',
    post: 'Environmental Tech',
    email: 'nbettensong@joomla.org',
    city: 'Chabařovice',
    start_date: '07/11/2018',
    salary: 20484.44,
    age: '64',
    experience: '4 Years',
    status: 'Current',
  },
  {
    responsive_id: '',
    id: 18,
    avatar: '',
    full_name: 'Titus Hayne',
    post: 'Web Designer',
    email: 'thayneh@kickstarter.com',
    city: 'Yangon',
    start_date: '05/25/2019',
    salary: 16871.48,
    age: '59',
    experience: '9 Years',
    status: 'Current',
  },
  {
    responsive_id: '',
    id: 19,
    avatar: avatar4,
    full_name: 'Roxie Huck',
    post: 'Administrative Assistant',
    email: 'rhucki@ed.gov',
    city: 'Polýkastro',
    start_date: '04/04/2019',
    salary: 19653.56,
    age: '41',
    experience: '1 Year',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 23,
    avatar: avatar7,
    full_name: 'Rosmunda Steed',
    post: 'Assistant Media Planner',
    email: 'rsteedm@xing.com',
    city: 'Manzanares',
    start_date: '12/23/2017',
    salary: 13778.34,
    age: '21',
    experience: '1 Year',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 26,
    avatar: avatar2,
    full_name: 'Morgen Benes',
    post: 'Senior Sales Associate',
    email: 'mbenesp@ted.com',
    city: 'Cà Mau',
    start_date: '04/10/2016',
    salary: 16969.63,
    age: '42',
    experience: '2 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 28,
    avatar: '',
    full_name: 'Kliment McGinney',
    post: 'Chief Design Engineer',
    email: 'kmcginneyr@paginegialle.it',
    city: 'Xiaocheng',
    start_date: '07/09/2018',
    salary: 24027.81,
    age: '28',
    experience: '8 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 31,
    avatar: '',
    full_name: 'Teressa Bleakman',
    post: 'Senior Editor',
    email: 'tbleakmanu@phpbb.com',
    city: 'Žebrák',
    start_date: '09/03/2016',
    salary: 24875.41,
    age: '37',
    experience: '7 Years',
    status: 'Applied',
  },
]

const headers = [
  { title: 'NAME', key: 'full_name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'DATE', key: 'start_date' },
  { title: 'SALARY', key: 'salary' },
  { title: 'AGE', key: 'age' },
  { title: 'STATUS', key: 'status' },
]

const groupBy = [{ key: 'status' }]

const resolveStatusVariant = (status: string) => {
  if (status === 'Current')
    return { color: 'primary' }
  else if (status === 'Professional')
    return { color: 'success' }
  else if (status === 'Rejected')
    return { color: 'error' }
  else if (status === 'Resigned')
    return { color: 'warning' }
  else
    return { color: 'info' }
}
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="userList"
    :items-per-page="10"
    :group-by="groupBy"
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
        size="small"
        class="font-weight-medium"
      >
        {{ item.raw.status }}
      </VChip>
    </template>
  </VDataTable>
</template>
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar4 from '@images/avatars/avatar-4.png'
import avatar7 from '@images/avatars/avatar-7.png'
import avatar8 from '@images/avatars/avatar-8.png'

const userList = [
  {
    responsive_id: '',
    id: 1,
    avatar: avatar8,
    full_name: 'Korrie O\'Crevy',
    post: 'Nuclear Power Engineer',
    email: 'kocrevy0@thetimes.co.uk',
    city: 'Krasnosilka',
    start_date: '09/23/2016',
    salary: 23896.35,
    age: '61',
    experience: '1 Year',
    status: 'Professional',
  },
  {
    responsive_id: '',
    id: 2,
    avatar: avatar1,
    full_name: 'Bailie Coulman',
    post: 'VP Quality Control',
    email: 'bcoulman1@yolasite.com',
    city: 'Hinigaran',
    start_date: '05/20/2018',
    salary: 13633.69,
    age: '63',
    experience: '3 Years',
    status: 'Professional',
  },
  {
    responsive_id: '',
    id: 3,
    avatar: avatar7,
    full_name: 'Stella Ganderton',
    post: 'Operator',
    email: 'sganderton2@tuttocitta.it',
    city: 'Golcowa',
    start_date: '03/24/2018',
    salary: 13076.28,
    age: '66',
    experience: '6 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 4,
    avatar: avatar8,
    full_name: 'Dorolice Crossman',
    post: 'Cost Accountant',
    email: 'dcrossman3@google.co.jp',
    city: 'Paquera',
    start_date: '12/03/2017',
    salary: 12336.17,
    age: '22',
    experience: '2 Years',
    status: 'Professional',
  },
  {
    responsive_id: '',
    id: 6,
    avatar: '',
    full_name: 'Genevra Honeywood',
    post: 'Geologist',
    email: 'ghoneywood5@narod.ru',
    city: 'Maofan',
    start_date: '06/01/2017',
    salary: 17803.8,
    age: '61',
    experience: '1 Year',
    status: 'Current',
  },
  {
    responsive_id: '',
    id: 7,
    avatar: '',
    full_name: 'Eileen Diehn',
    post: 'Environmental Specialist',
    email: 'ediehn6@163.com',
    city: 'Lampuyang',
    start_date: '10/15/2017',
    salary: 18991.67,
    age: '59',
    experience: '9 Years',
    status: 'Rejected',
  },
  {
    responsive_id: '',
    id: 8,
    avatar: avatar7,
    full_name: 'Richardo Aldren',
    post: 'Senior Sales Associate',
    email: 'raldren7@mtv.com',
    city: 'Skoghall',
    start_date: '11/05/2016',
    salary: 19230.13,
    age: '55',
    experience: '5 Years',
    status: 'Rejected',
  },
  {
    responsive_id: '',
    id: 9,
    avatar: avatar2,
    full_name: 'Allyson Moakler',
    post: 'Safety Technician',
    email: 'amoakler8@shareasale.com',
    city: 'Mogilany',
    start_date: '12/29/2018',
    salary: 11677.32,
    age: '39',
    experience: '9 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 11,
    avatar: '',
    full_name: 'De Falloon',
    post: 'Sales Representative',
    email: 'dfalloona@ifeng.com',
    city: 'Colima',
    start_date: '06/12/2018',
    salary: 19252.12,
    age: '30',
    experience: '0 Year',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 12,
    avatar: '',
    full_name: 'Cyrus Gornal',
    post: 'Senior Sales Associate',
    email: 'cgornalb@fda.gov',
    city: 'Boro Utara',
    start_date: '12/09/2017',
    salary: 16745.47,
    age: '22',
    experience: '2 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 13,
    avatar: '',
    full_name: 'Tallou Balf',
    post: 'Staff Accountant',
    email: 'tbalfc@sina.com.cn',
    city: 'Siliana',
    start_date: '01/21/2016',
    salary: 15488.53,
    age: '36',
    experience: '6 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 15,
    avatar: '',
    full_name: 'Wilmar Bourton',
    post: 'Administrative Assistant',
    email: 'wbourtone@sakura.ne.jp',
    city: 'Bích Động',
    start_date: '04/25/2018',
    salary: 13304.45,
    age: '19',
    experience: '9 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 16,
    avatar: avatar4,
    full_name: 'Robinson Brazenor',
    post: 'General Manager',
    email: 'rbrazenorf@symantec.com',
    city: 'Gendiwu',
    start_date: '12/23/2017',
    salary: 11953.08,
    age: '66',
    experience: '6 Years',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 17,
    avatar: '',
    full_name: 'Nadia Bettenson',
    post: 'Environmental Tech',
    email: 'nbettensong@joomla.org',
    city: 'Chabařovice',
    start_date: '07/11/2018',
    salary: 20484.44,
    age: '64',
    experience: '4 Years',
    status: 'Current',
  },
  {
    responsive_id: '',
    id: 18,
    avatar: '',
    full_name: 'Titus Hayne',
    post: 'Web Designer',
    email: 'thayneh@kickstarter.com',
    city: 'Yangon',
    start_date: '05/25/2019',
    salary: 16871.48,
    age: '59',
    experience: '9 Years',
    status: 'Current',
  },
  {
    responsive_id: '',
    id: 19,
    avatar: avatar4,
    full_name: 'Roxie Huck',
    post: 'Administrative Assistant',
    email: 'rhucki@ed.gov',
    city: 'Polýkastro',
    start_date: '04/04/2019',
    salary: 19653.56,
    age: '41',
    experience: '1 Year',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 23,
    avatar: avatar7,
    full_name: 'Rosmunda Steed',
    post: 'Assistant Media Planner',
    email: 'rsteedm@xing.com',
    city: 'Manzanares',
    start_date: '12/23/2017',
    salary: 13778.34,
    age: '21',
    experience: '1 Year',
    status: 'Applied',
  },
  {
    responsive_id: '',
    id: 26,
    avatar: avatar2,
    full_name: 'Morgen Benes',
    post: 'Senior Sales Associate',
    email: 'mbenesp@ted.com',
    city: 'Cà Mau',
    start_date: '04/10/2016',
    salary: 16969.63,
    age: '42',
    experience: '2 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 28,
    avatar: '',
    full_name: 'Kliment McGinney',
    post: 'Chief Design Engineer',
    email: 'kmcginneyr@paginegialle.it',
    city: 'Xiaocheng',
    start_date: '07/09/2018',
    salary: 24027.81,
    age: '28',
    experience: '8 Years',
    status: 'Resigned',
  },
  {
    responsive_id: '',
    id: 31,
    avatar: '',
    full_name: 'Teressa Bleakman',
    post: 'Senior Editor',
    email: 'tbleakmanu@phpbb.com',
    city: 'Žebrák',
    start_date: '09/03/2016',
    salary: 24875.41,
    age: '37',
    experience: '7 Years',
    status: 'Applied',
  },
]

const headers = [
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
]

const groupBy = [{ key: 'status' }]

const resolveStatusVariant = status => {
  if (status === 'Current')
    return { color: 'primary' }
  else if (status === 'Professional')
    return { color: 'success' }
  else if (status === 'Rejected')
    return { color: 'error' }
  else if (status === 'Resigned')
    return { color: 'warning' }
  else
    return { color: 'info' }
}
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="userList"
    :items-per-page="10"
    :group-by="groupBy"
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
        size="small"
        class="font-weight-medium"
      >
        {{ item.raw.status }}
      </VChip>
    </template>
  </VDataTable>
</template>
` }

export const kitchenSink = { ts: `<script setup lang="ts">
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
            <span class="text-high-emphasis font-weight-medium">\${{ item.raw.payment.paid_amount }}</span>
            <span v-if="item.raw.payment.paid_amount !== item.raw.payment.total">/{{ item.raw.payment.total }}</span>
          </div>
          <span class="text-xs text-no-wrap">{{ item.raw.payment.received_payment_status }}</span>
        </div>
      </template>

      <!-- Status -->
      <template #item.status="{ item }">
        <VChip
          :color="resolveStatusColor(item.raw.payment.status)"
          :class="\`text-\${resolveStatusColor(item.raw.payment.status)}\`"
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
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import axios from '@axios'

const search = ref('')
const productList = ref([])

// headers
const headers = [
  {
    title: 'PRODUCT',
    key: 'product.name',
  },
  {
    title: 'DATE',
    key: 'date',
  },
  {
    title: 'CATEGORY',
    key: 'product.category',
  },
  {
    title: 'BUYERS',
    key: 'buyer.name',
  },
  {
    title: 'PAYMENT',
    key: 'payment',
    sortable: false,
  },
  {
    title: 'STATUS',
    key: 'status',
    sortable: false,
  },
  {
    title: 'DELETE',
    key: 'delete',
    sortable: false,
  },
]

const deleteItem = itemId => {
  const index = productList.value.findIndex(item => item.product.id === itemId)

  productList.value.splice(index, 1)
}

const categoryIcons = [
  {
    name: 'Mouse',
    icon: 'mdi-mouse',
    color: 'warning',
  },
  {
    name: 'Glass',
    icon: 'mdi-glasses',
    color: 'primary',
  },
  {
    name: 'Smart Watch',
    icon: 'mdi-watch-variant',
    color: 'success',
  },
  {
    name: 'Bag',
    icon: 'mdi-bag-personal-outline',
    color: 'info',
  },
  {
    name: 'Storage Device',
    icon: 'mdi-tape-drive',
    color: 'warning',
  },
  {
    name: 'Bluetooth',
    icon: 'mdi-bluetooth',
    color: 'error',
  },
  {
    name: 'Gaming',
    icon: 'mdi-controller-classic-outline',
    color: 'warning',
  },
  {
    name: 'Home',
    icon: 'mdi-home-outline',
    color: 'error',
  },
  {
    name: 'VR',
    icon: 'mdi-virtual-reality',
    color: 'primary',
  },
  {
    name: 'Shoes',
    icon: 'mdi-shoe-sneaker',
    color: 'success',
  },
  {
    name: 'Electronics',
    icon: 'mdi-flash',
    color: 'info',
  },
  {
    name: 'Projector',
    icon: 'mdi-projector',
    color: 'warning',
  },
  {
    name: 'IPod',
    icon: 'mdi-ipod',
    color: 'error',
  },
  {
    name: 'Keyboard',
    icon: 'mdi-keyboard-variant',
    color: 'primary',
  },
  {
    name: 'Smart Phone',
    icon: 'mdi-cellphone',
    color: 'success',
  },
  {
    name: 'Smart TV',
    icon: 'mdi-television',
    color: 'info',
  },
  {
    name: 'Google Home',
    icon: 'mdi-google-home',
    color: 'warning',
  },
  {
    name: 'Mac',
    icon: 'mdi-apple',
    color: 'error',
  },
  {
    name: 'Headphone',
    icon: 'mdi-headphones',
    color: 'primary',
  },
  {
    name: 'IMac',
    icon: 'mdi-desktop-mac',
    color: 'success',
  },
  {
    name: 'IPhone',
    icon: 'mdi-apple',
    color: 'warning',
  },
]

const resolveStatusColor = status => {
  if (status === 'Confirmed')
    return 'primary'
  if (status === 'Completed')
    return 'success'
  if (status === 'Cancelled')
    return 'error'
}

const categoryIconFilter = categoryName => {
  const index = categoryIcons.findIndex(category => category.name === categoryName)
  if (index !== -1)
    return [{
      icon: categoryIcons[index].icon,
      color: categoryIcons[index].color,
    }]
  
  return [{
    icon: 'mdi-help-circle-outline',
    color: 'primary',
  }]
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
            <span class="text-high-emphasis font-weight-medium">\${{ item.raw.payment.paid_amount }}</span>
            <span v-if="item.raw.payment.paid_amount !== item.raw.payment.total">/{{ item.raw.payment.total }}</span>
          </div>
          <span class="text-xs text-no-wrap">{{ item.raw.payment.received_payment_status }}</span>
        </div>
      </template>

      <!-- Status -->
      <template #item.status="{ item }">
        <VChip
          :color="resolveStatusColor(item.raw.payment.status)"
          :class="\`text-\${resolveStatusColor(item.raw.payment.status)}\`"
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
` }

export const rowEditingViaDialog = { ts: `<script setup lang="ts">
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import type { Data } from '@/@fake-db/types'
import data from '@/views/demos/forms/tables/data-table/datatable'

const editDialog = ref(false)
const deleteDialog = ref(false)

const defaultItem = ref<Data>({
  responsive_id: '',
  id: -1,
  avatar: '',
  full_name: '',
  post: '',
  email: '',
  city: '',
  start_date: '',
  salary: -1,
  age: '',
  experience: '',
  status: -1,
})

const editedItem = ref<Data>(defaultItem.value)
const editedIndex = ref(-1)
const userList = ref<Data[]>([])

// status options
const selectedOptions = [
  { text: 'Current', value: 1 },
  { text: 'Professional', value: 2 },
  { text: 'Rejected', value: 3 },
  { text: 'Resigned', value: 4 },
  { text: 'Applied', value: 5 },
]

// headers
const headers = [
  { title: 'NAME', key: 'full_name' },
  { title: 'EMAIL', key: 'email' },
  { title: 'DATE', key: 'start_date' },
  { title: 'SALARY', key: 'salary' },
  { title: 'AGE', key: 'age' },
  { title: 'STATUS', key: 'status' },
  { title: 'ACTIONS', key: 'actions' },
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

// 👉 methods
const editItem = (item: Data) => {
  editedIndex.value = userList.value.indexOf(item)
  editedItem.value = { ...item }
  editDialog.value = true
}

const deleteItem = (item: Data) => {
  editedIndex.value = userList.value.indexOf(item)
  editedItem.value = { ...item }
  deleteDialog.value = true
}

const close = () => {
  editDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const closeDelete = () => {
  deleteDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const save = () => {
  if (editedIndex.value > -1)
    Object.assign(userList.value[editedIndex.value], editedItem.value)

  else
    userList.value.push(editedItem.value)

  close()
}

const deleteItemConfirm = () => {
  userList.value.splice(editedIndex.value, 1)
  closeDelete()
}

onMounted(() => {
  userList.value = JSON.parse(JSON.stringify(data))
})
</script>

<template>
  <!-- 👉 Datatable  -->
  <VDataTable
    :headers="headers"
    :items="userList"
    :items-per-page="5"
  >
    <!-- full name -->
    <template #item.full_name="{ item }">
      <div class="d-flex align-center">
        <!-- avatar -->
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

    <!-- status -->
    <template #item.status="{ item }">
      <VChip
        :color="resolveStatusVariant(item.raw.status).color"
        density="comfortable"
      >
        {{ resolveStatusVariant(item.raw.status).text }}
      </VChip>
    </template>

    <!-- Actions -->
    <template #item.actions="{ item }">
      <div class="d-flex gap-1">
        <IconBtn @click="editItem(item.raw)">
          <VIcon icon="mdi-pencil-outline" />
        </IconBtn>
        <IconBtn @click="deleteItem(item.raw)">
          <VIcon icon="mdi-delete-outline" />
        </IconBtn>
      </div>
    </template>
  </VDataTable>

  <!-- 👉 Edit Dialog  -->
  <VDialog
    v-model="editDialog"
    max-width="600px"
  >
    <VCard>
      <VCardTitle>
        <span class="headline">Edit Item</span>
      </VCardTitle>

      <VCardText>
        {{ editedItem?.full_name }}
        <VContainer>
          <VRow>
            <!-- full_name -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.full_name"
                label="User name"
              />
            </VCol>

            <!-- email -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.email"
                label="Email"
              />
            </VCol>

            <!-- salary -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.salary"
                label="Salary"
                prefix="$"
                type="number"
              />
            </VCol>

            <!-- age -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.age"
                label="Age"
                type="number"
              />
            </VCol>

            <!-- start date -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.start_date"
                label="Date"
              />
            </VCol>

            <!-- status -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VSelect
                v-model="editedItem.status"
                :items="selectedOptions"
                item-title="text"
                item-value="value"
                label="Standard"
                variant="underlined"
                readonly
              />
            </VCol>
          </VRow>
        </VContainer>
      </VCardText>

      <VCardActions>
        <VSpacer />

        <VBtn
          color="error"
          variant="outlined"
          @click="close"
        >
          Cancel
        </VBtn>

        <VBtn
          color="success"
          variant="elevated"
          @click="save"
        >
          Save
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- 👉 Delete Dialog  -->
  <VDialog
    v-model="deleteDialog"
    max-width="500px"
  >
    <VCard>
      <VCardTitle>
        Are you sure you want to delete this item?
      </VCardTitle>

      <VCardActions>
        <VSpacer />

        <VBtn
          color="error"
          variant="outlined"
          @click="closeDelete"
        >
          Cancel
        </VBtn>

        <VBtn
          color="success"
          variant="elevated"
          @click="deleteItemConfirm"
        >
          OK
        </VBtn>

        <VSpacer />
      </VCardActions>
    </VCard>
  </VDialog>
</template>
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const editDialog = ref(false)
const deleteDialog = ref(false)

const defaultItem = ref({
  responsive_id: '',
  id: -1,
  avatar: '',
  full_name: '',
  post: '',
  email: '',
  city: '',
  start_date: '',
  salary: -1,
  age: '',
  experience: '',
  status: -1,
})

const editedItem = ref(defaultItem.value)
const editedIndex = ref(-1)
const userList = ref([])

// status options
const selectedOptions = [
  {
    text: 'Current',
    value: 1,
  },
  {
    text: 'Professional',
    value: 2,
  },
  {
    text: 'Rejected',
    value: 3,
  },
  {
    text: 'Resigned',
    value: 4,
  },
  {
    text: 'Applied',
    value: 5,
  },
]

// headers
const headers = [
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
  {
    title: 'ACTIONS',
    key: 'actions',
  },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
}

const editItem = item => {
  editedIndex.value = userList.value.indexOf(item)
  editedItem.value = { ...item }
  editDialog.value = true
}

const deleteItem = item => {
  editedIndex.value = userList.value.indexOf(item)
  editedItem.value = { ...item }
  deleteDialog.value = true
}

const close = () => {
  editDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const closeDelete = () => {
  deleteDialog.value = false
  editedIndex.value = -1
  editedItem.value = { ...defaultItem.value }
}

const save = () => {
  if (editedIndex.value > -1)
    Object.assign(userList.value[editedIndex.value], editedItem.value)
  else
    userList.value.push(editedItem.value)
  close()
}

const deleteItemConfirm = () => {
  userList.value.splice(editedIndex.value, 1)
  closeDelete()
}

onMounted(() => {
  userList.value = JSON.parse(JSON.stringify(data))
})
</script>

<template>
  <!-- 👉 Datatable  -->
  <VDataTable
    :headers="headers"
    :items="userList"
    :items-per-page="5"
  >
    <!-- full name -->
    <template #item.full_name="{ item }">
      <div class="d-flex align-center">
        <!-- avatar -->
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

    <!-- status -->
    <template #item.status="{ item }">
      <VChip
        :color="resolveStatusVariant(item.raw.status).color"
        density="comfortable"
      >
        {{ resolveStatusVariant(item.raw.status).text }}
      </VChip>
    </template>

    <!-- Actions -->
    <template #item.actions="{ item }">
      <div class="d-flex gap-1">
        <IconBtn @click="editItem(item.raw)">
          <VIcon icon="mdi-pencil-outline" />
        </IconBtn>
        <IconBtn @click="deleteItem(item.raw)">
          <VIcon icon="mdi-delete-outline" />
        </IconBtn>
      </div>
    </template>
  </VDataTable>

  <!-- 👉 Edit Dialog  -->
  <VDialog
    v-model="editDialog"
    max-width="600px"
  >
    <VCard>
      <VCardTitle>
        <span class="headline">Edit Item</span>
      </VCardTitle>

      <VCardText>
        {{ editedItem?.full_name }}
        <VContainer>
          <VRow>
            <!-- full_name -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.full_name"
                label="User name"
              />
            </VCol>

            <!-- email -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.email"
                label="Email"
              />
            </VCol>

            <!-- salary -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.salary"
                label="Salary"
                prefix="$"
                type="number"
              />
            </VCol>

            <!-- age -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.age"
                label="Age"
                type="number"
              />
            </VCol>

            <!-- start date -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VTextField
                v-model="editedItem.start_date"
                label="Date"
              />
            </VCol>

            <!-- status -->
            <VCol
              cols="12"
              sm="6"
              md="4"
            >
              <VSelect
                v-model="editedItem.status"
                :items="selectedOptions"
                item-title="text"
                item-value="value"
                label="Standard"
                variant="underlined"
                readonly
              />
            </VCol>
          </VRow>
        </VContainer>
      </VCardText>

      <VCardActions>
        <VSpacer />

        <VBtn
          color="error"
          variant="outlined"
          @click="close"
        >
          Cancel
        </VBtn>

        <VBtn
          color="success"
          variant="elevated"
          @click="save"
        >
          Save
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- 👉 Delete Dialog  -->
  <VDialog
    v-model="deleteDialog"
    max-width="500px"
  >
    <VCard>
      <VCardTitle>
        Are you sure you want to delete this item?
      </VCardTitle>

      <VCardActions>
        <VSpacer />

        <VBtn
          color="error"
          variant="outlined"
          @click="closeDelete"
        >
          Cancel
        </VBtn>

        <VBtn
          color="success"
          variant="elevated"
          @click="deleteItemConfirm"
        >
          OK
        </VBtn>

        <VSpacer />
      </VCardActions>
    </VCard>
  </VDialog>
</template>
` }

export const rowSelection = { ts: `<script setup lang="ts">
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
    show-select
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

    <!-- status -->
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
`, js: `<script setup>
import { VDataTable } from 'vuetify/labs/VDataTable'
import { avatarText } from '@/@core/utils/formatters'
import data from '@/views/demos/forms/tables/data-table/datatable'

const headers = [
  {
    title: 'NAME',
    key: 'full_name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'DATE',
    key: 'start_date',
  },
  {
    title: 'SALARY',
    key: 'salary',
  },
  {
    title: 'AGE',
    key: 'age',
  },
  {
    title: 'STATUS',
    key: 'status',
  },
]

const resolveStatusVariant = status => {
  if (status === 1)
    return {
      color: 'primary',
      text: 'Current',
    }
  else if (status === 2)
    return {
      color: 'success',
      text: 'Professional',
    }
  else if (status === 3)
    return {
      color: 'error',
      text: 'Rejected',
    }
  else if (status === 4)
    return {
      color: 'warning',
      text: 'Resigned',
    }
  else
    return {
      color: 'info',
      text: 'Applied',
    }
}
</script>

<template>
  <VDataTable
    :headers="headers"
    :items="data"
    :items-per-page="5"
    show-select
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

    <!-- status -->
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
` }

