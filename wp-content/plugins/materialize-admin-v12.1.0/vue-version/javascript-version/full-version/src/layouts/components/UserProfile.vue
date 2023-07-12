<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { initialAbility } from '@/plugins/casl/ability'
import { useAppAbility } from '@/plugins/casl/useAppAbility'

const router = useRouter()
const ability = useAppAbility()
const userData = JSON.parse(localStorage.getItem('userData') || 'null')

const logout = () => {

  // Remove "userData" from localStorage
  localStorage.removeItem('userData')

  // Remove "accessToken" from localStorage
  localStorage.removeItem('accessToken')
  router.push('/login').then(() => {

    // Remove "userAbilities" from localStorage
    localStorage.removeItem('userAbilities')

    // Reset ability to initial ability
    ability.update(initialAbility)
  })
}

const userProfileList = [
  { type: 'divider' },
  {
    type: 'navItem',
    icon: 'mdi-account-outline',
    title: 'Profile',
    to: {
      name: 'apps-user-view-id',
      params: { id: 21 },
    },
  },
  {
    type: 'navItem',
    icon: 'mdi-cog-outline',
    title: 'settings',
    to: {
      name: 'pages-account-settings-tab',
      params: { tab: 'account' },
    },
  },
  {
    type: 'navItem',
    icon: 'mdi-credit-card-outline',
    title: 'Billing',
    to: {
      name: 'pages-account-settings-tab',
      params: { tab: 'billing-plans' },
    },
    badgeProps: {
      color: 'light-error',
      content: '3',
      textColor: 'error',
    },
  },
  { type: 'divider' },
  {
    type: 'navItem',
    icon: 'mdi-lifebuoy',
    title: 'Help',
    to: { name: 'pages-help-center' },
  },
  {
    type: 'navItem',
    icon: 'mdi-currency-usd',
    title: 'Pricing',
    to: { name: 'pages-pricing' },
  },
  {
    type: 'navItem',
    icon: 'mdi-help-circle-outline',
    title: 'FAQ',
    to: { name: 'pages-faq' },
  },
  { type: 'divider' },
  {
    type: 'navItem',
    icon: 'mdi-logout',
    title: 'Logout',
    onClick: logout,
  },
]
</script>

<template>
  <VBadge
    dot
    bordered
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
  >
    <VAvatar
      class="cursor-pointer"
      :color="!(userData && userData.avatar) ? 'primary' : undefined"
      :variant="!(userData && userData.avatar) ? 'tonal' : undefined"
    >
      <VImg
        v-if="userData && userData.avatar"
        :src="userData.avatar"
      />
      <VIcon
        v-else
        icon="mdi-account-outline"
      />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="230"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    :color="!(userData && userData.avatar) ? 'primary' : undefined"
                    :variant="!(userData && userData.avatar) ? 'tonal' : undefined"
                  >
                    <VImg
                      v-if="userData && userData.avatar"
                      :src="userData.avatar"
                    />
                    <VIcon
                      v-else
                      icon="mdi-account-outline"
                    />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-medium">
              {{ userData.fullName || userData.username }}
            </VListItemTitle>
            <VListItemSubtitle>{{ userData.role }}</VListItemSubtitle>
          </VListItem>

          <PerfectScrollbar :options="{ wheelPropagation: false }">
            <template
              v-for="item in userProfileList"
              :key="item.title"
            >
              <VListItem
                v-if="item.type === 'navItem'"
                :to="item.to"
                @click="item.onClick && item.onClick()"
              >
                <template #prepend>
                  <VIcon
                    class="me-2"
                    :icon="item.icon"
                    size="22"
                  />
                </template>

                <VListItemTitle>{{ item.title }}</VListItemTitle>

                <template
                  v-if="item.badgeProps"
                  #append
                >
                  <VBadge v-bind="item.badgeProps" />
                </template>
              </VListItem>

              <VDivider
                v-else
                class="my-2"
              />
            </template>
          </PerfectScrollbar>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
