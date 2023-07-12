<script setup>
const open = ref([
  'Users',
  'Admin',
])

const admins = [
  [
    'Management',
    'mdi-account-multiple-outline',
  ],
  [
    'Settings',
    'mdi-cog-outline',
  ],
]

const cruds = [
  [
    'Create',
    'mdi-plus-outline',
  ],
  [
    'Read',
    'mdi-file-outline',
  ],
  [
    'Update',
    'mdi-update',
  ],
  [
    'Delete',
    'mdi-delete-outline',
  ],
]
</script>

<template>
  <VList v-model:opened="open">
    <VListItem
      prepend-icon="mdi-home-outline"
      title="Home"
      value="Home"
    />

    <VListGroup value="Users">
      <template #activator="{ props }">
        <VListItem
          v-bind="props"
          prepend-icon="mdi-account-circle-outline"
          title="Users"
        />
      </template>

      <VListGroup value="Admin">
        <template #activator="{ props }">
          <VListItem
            v-bind="props"
            title="Admin"
          />
        </template>

        <VListItem
          v-for="([title, icon], i) in admins"
          :key="i"
          :value="title"
          :title="title"
          :prepend-icon="icon"
        />
      </VListGroup>

      <VListGroup value="Actions">
        <template #activator="{ props }">
          <VListItem
            v-bind="props"
            title="Actions"
          />
        </template>

        <VListItem
          v-for="([title, icon], i) in cruds"
          :key="i"
          :value="title"
          :title="title"
          :prepend-icon="icon"
        />
      </VListGroup>
    </VListGroup>
  </VList>
</template>
