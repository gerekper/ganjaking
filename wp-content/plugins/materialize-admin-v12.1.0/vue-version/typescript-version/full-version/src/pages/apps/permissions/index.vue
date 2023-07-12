<script setup lang="ts">
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import type { Permission } from '@/@fake-db/types'
import { paginationMeta } from '@/@fake-db/utils'
import axios from '@axios'
import type { Options } from '@core/types'

// 👉 headers
const headers = [
  { title: 'Name', key: 'name' },
  { title: 'Assigned To', key: 'assignedTo', sortable: false },
  { title: 'Created Date', key: 'createdDate', sortable: false },
  { title: 'Actions', key: 'actions', sortable: false },
]

const permissions = ref<Permission[]>([])
const search = ref('')
const rowPerPage = ref(10)
const totalPermissions = ref(0)

const options = ref<Options>({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

const isPermissionDialogVisible = ref(false)
const isAddPermissionDialogVisible = ref(false)
const permissionName = ref('')

// eslint-disable-next-line @typescript-eslint/no-explicit-any
const colors: any = {
  'support': { color: 'info', text: 'Support' },
  'users': { color: 'success', text: 'Users' },
  'manager': { color: 'warning', text: 'Manager' },
  'administrator': { color: 'primary', text: 'Administrator' },
  'restricted-user': { color: 'error', text: 'Restricted User' },
}

const fetchPermissions = () => {
  axios.get('/apps/permissions/data', {
    params: {
      q: search.value,
      options: options.value,
    },
  }).then(response => {
    permissions.value = response.data.permissions
    totalPermissions.value = response.data.totalPermissions
  }).catch(error => {
    console.log(error)
  })
}

onMounted(fetchPermissions)

watchEffect(fetchPermissions)

const editPermission = (name: string) => {
  isPermissionDialogVisible.value = true
  permissionName.value = name
}
</script>

<template>
  <VRow>
    <VCol cols="12">
      <h5 class="text-h5">
        Permissions List
      </h5>
      <p class="text-sm mb-0">
        Each category (Basic, Professional, and Business) includes the four predefined roles shown below.
      </p>
    </VCol>

    <VCol cols="12">
      <VCard>
        <VCardText class="d-flex align-center justify-sm-space-between justify-start gap-4 flex-wrap">
          <VTextField
            v-model="search"
            placeholder="Search"
            density="compact"
            style="min-width: 12rem;max-width: 15rem;"
          />

          <VBtn
            density="default"
            @click="isAddPermissionDialogVisible = true"
          >
            Add Permission
          </VBtn>
        </VCardText>
        <!-- 👉 Datatable  -->
        <VDataTableServer
          v-model:items-per-page="rowPerPage"
          v-model:page="options.page"
          :items-length="totalPermissions"
          :headers="headers"
          :items="permissions"
          class="text-no-wrap rounded-0"
          @update:options="options = $event"
        >
          <!-- Assigned To -->
          <template #item.assignedTo="{ item }">
            <!-- {{ item.raw.assignedTo }} -->
            <div class="d-flex gap-2">
              <VChip
                v-for="text in item.raw.assignedTo"
                :key="text"
                :color="colors[text].color"
                density="comfortable"
              >
                {{ colors[text].text }}
              </VChip>
            </div>
          </template>

          <template #item.createdDate="{ item }">
            <span class="text-sm text-medium-emphasis">{{ item.raw.createdDate }}</span>
          </template>

          <!-- Actions -->
          <template #item.actions="{ item }">
            <VBtn
              icon
              size="small"
              color="medium-emphasis"
              variant="text"
              @click="editPermission(item.raw.name)"
            >
              <VIcon
                size="24"
                icon="mdi-pencil-outline"
              />
            </VBtn>
            <VBtn
              icon
              size="small"
              variant="text"
              color="medium-emphasis"
            >
              <VIcon
                size="24"
                icon="mdi-delete-outline"
              />
            </VBtn>
          </template>

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
                {{ paginationMeta(options, totalPermissions) }}
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
                  :disabled="options.page >= Math.ceil(totalPermissions / options.itemsPerPage)"
                  @click="options.page >= Math.ceil(totalPermissions / options.itemsPerPage) ? options.page = Math.ceil(totalPermissions / options.itemsPerPage) : options.page++ "
                />
              </div>
            </div>
          </template>
        </VDataTableServer>
      </VCard>

      <AddEditPermissionDialog
        v-model:isDialogVisible="isPermissionDialogVisible"
        v-model:permission-name="permissionName"
      />
      <AddEditPermissionDialog v-model:isDialogVisible="isAddPermissionDialogVisible" />
    </VCol>
  </VRow>
</template>
