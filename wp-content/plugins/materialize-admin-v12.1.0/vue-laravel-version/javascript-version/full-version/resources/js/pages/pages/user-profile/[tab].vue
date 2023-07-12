<script setup>
import { useRoute } from 'vue-router'
import UserProfileHeader from '@/views/pages/user-profile/UserProfileHeader.vue'
import UserConnections from '@/views/pages/user-profile/connections/index.vue'
import UserProfile from '@/views/pages/user-profile/profile/index.vue'
import UserProjects from '@/views/pages/user-profile/projects/index.vue'
import UserTeam from '@/views/pages/user-profile/team/index.vue'

const route = useRoute()
const activeTab = ref(route.params.tab)

// tabs
const tabs = [
  {
    title: 'Profile',
    icon: 'mdi-account-outline',
    tab: 'profile',
  },
  {
    title: 'Team',
    icon: 'mdi-account-group-outline',
    tab: 'teams',
  },
  {
    title: 'Projects',
    icon: 'mdi-view-grid-outline',
    tab: 'projects',
  },
  {
    title: 'Connections',
    icon: 'mdi-link',
    tab: 'connections',
  },
]
</script>

<template>
  <div>
    <UserProfileHeader class="mb-5" />

    <VTabs
      v-model="activeTab"
      class="v-tabs-pill"
    >
      <VTab
        v-for="item in tabs"
        :key="item.icon"
        :value="item.tab"
        :to="{ name: 'pages-user-profile-tab', params: { tab: item.tab } }"
      >
        <VIcon
          size="20"
          start
          :icon="item.icon"
        />
        {{ item.title }}
      </VTab>
    </VTabs>

    <VWindow
      v-model="activeTab"
      class="mt-5 disable-tab-transition"
      :touch="false"
    >
      <!-- Profile -->
      <VWindowItem value="profile">
        <UserProfile />
      </VWindowItem>

      <!-- Teams -->
      <VWindowItem value="teams">
        <UserTeam />
      </VWindowItem>

      <!-- Projects -->
      <VWindowItem value="projects">
        <UserProjects />
      </VWindowItem>

      <!-- Connections -->
      <VWindowItem value="connections">
        <UserConnections />
      </VWindowItem>
    </VWindow>
  </div>
</template>

<route lang="yaml">
meta:
  navActiveLink: pages-user-profile-tab
</route>
