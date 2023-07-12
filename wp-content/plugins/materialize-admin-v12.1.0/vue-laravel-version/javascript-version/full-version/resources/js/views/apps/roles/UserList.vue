<script setup>
import { VDataTableServer } from 'vuetify/labs/VDataTable'
import { paginationMeta } from '@/@fake-db/utils'
import AddNewUserDrawer from '@/views/apps/user/list/AddNewUserDrawer.vue'
import { useUserListStore } from '@/views/apps/user/useUserListStore'
import { avatarText } from '@core/utils/formatters'

const userListStore = useUserListStore()
const searchQuery = ref('')
const selectedRole = ref()
const selectedPlan = ref()
const selectedStatus = ref()
const totalPage = ref(1)
const totalUsers = ref(0)
const users = ref([])

const options = ref({
  page: 1,
  itemsPerPage: 10,
  sortBy: [],
  groupBy: [],
  search: undefined,
})

const headers = [
  {
    title: 'User',
    key: 'user',
  },
  {
    title: 'Email',
    key: 'email',
  },
  {
    title: 'Role',
    key: 'role',
  },
  {
    title: 'Plan',
    key: 'plan',
  },
  {
    title: 'Status',
    key: 'status',
  },
  {
    title: 'Action',
    key: 'actions',
    sortable: false,
  },
]

// 👉 Fetching users
const fetchUsers = () => {
  userListStore.fetchUsers({
    q: searchQuery.value,
    status: selectedStatus.value,
    plan: selectedPlan.value,
    role: selectedRole.value,
    options: options.value,
  }).then(response => {
    users.value = response.data.users
    totalPage.value = response.data.totalPage
    totalUsers.value = response.data.totalUsers
  }).catch(error => {
    console.error(error)
  })
}

watchEffect(fetchUsers)

// 👉 search filters
const roles = [
  {
    title: 'Admin',
    value: 'admin',
  },
  {
    title: 'Author',
    value: 'author',
  },
  {
    title: 'Editor',
    value: 'editor',
  },
  {
    title: 'Maintainer',
    value: 'maintainer',
  },
  {
    title: 'Subscriber',
    value: 'subscriber',
  },
]

const resolveUserRoleVariant = role => {
  const roleLowerCase = role.toLowerCase()
  if (roleLowerCase === 'subscriber')
    return {
      color: 'primary',
      icon: 'mdi-account-outline',
    }
  if (roleLowerCase === 'author')
    return {
      color: 'warning',
      icon: 'mdi-cog-outline',
    }
  if (roleLowerCase === 'maintainer')
    return {
      color: 'success',
      icon: 'mdi-chart-donut',
    }
  if (roleLowerCase === 'editor')
    return {
      color: 'info',
      icon: 'mdi-pencil-outline',
    }
  if (roleLowerCase === 'admin')
    return {
      color: 'error',
      icon: 'mdi-laptop',
    }
  
  return {
    color: 'primary',
    icon: 'mdi-account-outline',
  }
}

const resolveUserStatusVariant = stat => {
  const statLowerCase = stat.toLowerCase()
  if (statLowerCase === 'pending')
    return 'warning'
  if (statLowerCase === 'active')
    return 'success'
  if (statLowerCase === 'inactive')
    return 'secondary'
  
  return 'primary'
}

const isAddNewUserDrawerVisible = ref(false)

const addNewUser = userData => {
  userListStore.addUser(userData)

  // refetch User
  fetchUsers()
}
</script>

<template>
  <section>
    <VCard>
      <VCardText class="d-flex flex-wrap gap-4">
        <!-- 👉 Export button -->
        <VBtn
          variant="outlined"
          color="secondary"
          prepend-icon="mdi-tray-arrow-up"
        >
          Export
        </VBtn>

        <VSpacer />

        <div class="app-user-search-filter d-flex align-center">
          <!-- 👉 Search  -->
          <VTextField
            v-model="searchQuery"
            placeholder="Search User"
            density="compact"
            class="me-3"
          />

          <!-- 👉 Add user button -->
          <VSelect
            v-model="selectedRole"
            label="Select Role"
            placeholder="Select Role"
            :items="roles"
            density="compact"
            clearable
            clear-icon="mdi-close"
            style="width: 5rem;"
          />
        </div>
      </VCardText>

      <!-- SECTION datatable -->
      <VDataTableServer
        v-model:items-per-page="options.itemsPerPage"
        v-model:page="options.page"
        :items="users"
        :items-length="totalUsers"
        :headers="headers"
        show-select
        class="text-no-wrap rounded-0"
        @update:options="options = $event"
      >
        <!-- User -->
        <template #item.user="{ item }">
          <div class="d-flex">
            <VAvatar
              size="34"
              :variant="!item.raw.avatar ? 'tonal' : undefined"
              :color="!item.raw.avatar ? resolveUserRoleVariant(item.raw.role).color : undefined"
              class="me-3"
            >
              <VImg
                v-if="item.raw.avatar"
                :src="item.raw.avatar"
              />
              <span
                v-else
                class="text-sm"
              >{{ avatarText(item.raw.fullName) }}</span>
            </VAvatar>
            <div class="d-flex flex-column">
              <h6 class="text-sm">
                <RouterLink
                  :to="{ name: 'apps-user-view-id', params: { id: item.raw.id } }"
                  class="font-weight-medium user-list-name"
                >
                  {{ item.raw.fullName }}
                </RouterLink>
              </h6>
              <span class="text-xs text-medium-emphasis">@{{ item.raw.username }}</span>
            </div>
          </div>
        </template>

        <!-- Email -->
        <template #item.email="{ item }">
          <span class="text-sm">
            {{ item.raw.email }}
          </span>
        </template>
        <!-- Role -->
        <template #item.role="{ item }">
          <div class="d-flex gap-x-2">
            <VIcon
              :icon="resolveUserRoleVariant(item.raw.role).icon"
              :color="resolveUserRoleVariant(item.raw.role).color"
            />
            <span class="text-capitalize">{{ item.raw.role }}</span>
          </div>
        </template>

        <!-- Plan -->
        <template #item.plan="{ item }">
          <span class="text-capitalize text-high-emphasis">{{ item.raw.currentPlan }}</span>
        </template>

        <!-- Status -->
        <template #item.status="{ item }">
          <VChip
            :color="resolveUserStatusVariant(item.raw.status)"
            size="small"
            class="text-capitalize"
          >
            {{ item.raw.status }}
          </VChip>
        </template>

        <!-- Actions -->
        <template #item.actions="{ item }">
          <VBtn
            icon
            variant="text"
            size="small"
            color="medium-emphasis"
            :to="{ name: 'apps-user-view-id', params: { id: item.raw.id } }"
          >
            <VIcon
              size="24"
              icon="mdi-eye-outline"
            />
          </VBtn>
        </template>

        <!-- Pagination -->
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
              {{ paginationMeta(options, totalUsers) }}
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
                :disabled="options.page >= Math.ceil(totalUsers / options.itemsPerPage)"
                @click="options.page >= Math.ceil(totalUsers / options.itemsPerPage) ? options.page = Math.ceil(totalUsers / options.itemsPerPage) : options.page++ "
              />
            </div>
          </div>
        </template>
      </VDataTableServer>
      <!-- SECTION -->
    </VCard>

    <!-- 👉 Add New User -->
    <AddNewUserDrawer
      v-model:isDrawerOpen="isAddNewUserDrawerVisible"
      @user-data="addNewUser"
    />
  </section>
</template>

<style lang="scss">
.app-user-search-filter {
  inline-size: 24.0625rem;
}

.text-capitalize {
  text-transform: capitalize;
}

.user-list-name:not(:hover) {
  color: rgba(var(--v-theme-on-background), var(--v-high-emphasis-opacity));
}
</style>
