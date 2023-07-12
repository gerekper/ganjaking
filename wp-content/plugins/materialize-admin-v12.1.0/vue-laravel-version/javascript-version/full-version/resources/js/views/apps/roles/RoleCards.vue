<script setup>
import avatar1 from '@images/avatars/avatar-1.png'
import avatar10 from '@images/avatars/avatar-10.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar4 from '@images/avatars/avatar-4.png'
import avatar5 from '@images/avatars/avatar-5.png'
import avatar6 from '@images/avatars/avatar-6.png'
import avatar7 from '@images/avatars/avatar-7.png'
import avatar8 from '@images/avatars/avatar-8.png'
import avatar9 from '@images/avatars/avatar-9.png'
import boyWithTab from '@images/illustrations/account-settings-security-illustration.png'

const roles = ref([
  {
    role: 'Administrator',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
    ],
    details: {
      name: 'Administrator',
      permissions: [
        {
          name: 'User Management',
          read: true,
          write: true,
          create: true,
        },
        {
          name: 'Disputes Management',
          read: true,
          write: true,
          create: true,
        },
        {
          name: 'API Control',
          read: true,
          write: true,
          create: true,
        },
      ],
    },
  },
  {
    role: 'Manager',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
      avatar5,
      avatar6,
      avatar7,
    ],
    details: {
      name: 'Manager',
      permissions: [
        {
          name: 'Reporting',
          read: true,
          write: true,
          create: false,
        },
        {
          name: 'Payroll',
          read: true,
          write: true,
          create: true,
        },
        {
          name: 'User Management',
          read: true,
          write: true,
          create: true,
        },
      ],
    },
  },
  {
    role: 'Users',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
      avatar5,
    ],
    details: {
      name: 'Users',
      permissions: [
        {
          name: 'User Management',
          read: true,
          write: false,
          create: false,
        },
        {
          name: 'Content Management',
          read: true,
          write: false,
          create: false,
        },
        {
          name: 'Disputes Management',
          read: true,
          write: false,
          create: false,
        },
        {
          name: 'Database Management',
          read: true,
          write: false,
          create: false,
        },
      ],
    },
  },
  {
    role: 'Support',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
      avatar5,
      avatar6,
    ],
    details: {
      name: 'Support',
      permissions: [
        {
          name: 'Repository Management',
          read: true,
          write: true,
          create: false,
        },
        {
          name: 'Content Management',
          read: true,
          write: true,
          create: false,
        },
        {
          name: 'Database Management',
          read: true,
          write: true,
          create: false,
        },
      ],
    },
  },
  {
    role: 'Restricted User',
    users: [
      avatar1,
      avatar2,
      avatar3,
      avatar4,
      avatar5,
      avatar6,
      avatar7,
      avatar8,
      avatar9,
      avatar10,
    ],
    details: {
      name: 'Restricted User',
      permissions: [
        {
          name: 'User Management',
          read: true,
          write: false,
          create: false,
        },
        {
          name: 'Content Management',
          read: true,
          write: false,
          create: false,
        },
        {
          name: 'Disputes Management',
          read: true,
          write: false,
          create: false,
        },
        {
          name: 'Database Management',
          read: true,
          write: false,
          create: false,
        },
      ],
    },
  },
])

const isRoleDialogVisible = ref(false)
const roleDetail = ref()
const isAddRoleDialogVisible = ref(false)

const editPermission = value => {
  isRoleDialogVisible.value = true
  roleDetail.value = value
}
</script>

<template>
  <VRow>
    <!-- 👉 Roles -->
    <VCol
      v-for="item in roles"
      :key="item.role"
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard>
        <VCardText class="d-flex align-center">
          <span>Total {{ item.users.length }} users</span>

          <VSpacer />

          <div class="v-avatar-group">
            <template
              v-for="(user, index) in item.users"
              :key="user"
            >
              <VAvatar
                v-if="item.users.length > 4 && item.users.length !== 4 && index < 3"
                size="40"
                :image="user"
              />

              <VAvatar
                v-if="item.users.length === 4"
                size="40"
                :image="user"
              />
            </template>
            <VAvatar
              v-if="item.users.length > 4"
              color="#eee"
            >
              <span
                class="text-lg"
                :class="$vuetify.theme.current.dark ? 'text-background' : 'text-high-emphasis'"
              >
                +{{ item.users.length - 3 }}
              </span>
            </VAvatar>
          </div>
        </VCardText>

        <VCardText>
          <p class="font-weight-medium text-xl mb-0">
            {{ item.role }}
          </p>
          <div class="d-flex align-center">
            <a
              href="javascript:void(0)"
              @click="editPermission(item.details)"
            >
              Edit Role
            </a>

            <VSpacer />
            <VBtn
              color="default"
              variant="text"
              icon
              size="small"
            >
              <VIcon
                size="24"
                icon="mdi-content-copy"
              />
            </VBtn>
          </div>
        </VCardText>
      </VCard>
    </VCol>

    <!-- 👉 Add New Role -->
    <VCol
      cols="12"
      sm="6"
      lg="4"
    >
      <VCard
        class="h-100"
        :ripple="false"
        @click="isAddRoleDialogVisible = true"
      >
        <VRow
          no-gutters
          class="h-100"
        >
          <VCol
            cols="5"
            class="d-flex flex-column justify-end align-center mt-5"
          >
            <img
              width="70"
              height="140"
              :src="boyWithTab"
            >
          </VCol>

          <VCol cols="7">
            <VCardText class="d-flex flex-column align-end justify-start gap-2 h-100">
              <VBtn>Add Role</VBtn>
              <span class="text-end">Add role, if it doesn't exist.</span>
            </VCardText>
          </VCol>
        </VRow>
      </VCard>
      <AddEditRoleDialog v-model:is-dialog-visible="isAddRoleDialogVisible" />
    </VCol>
  </VRow>

  <AddEditRoleDialog
    v-model:is-dialog-visible="isRoleDialogVisible"
    v-model:role-permissions="roleDetail"
  />
</template>
