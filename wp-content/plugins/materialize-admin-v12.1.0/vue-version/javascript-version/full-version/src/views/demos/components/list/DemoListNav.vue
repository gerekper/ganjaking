<script setup>
const items = [
  {
    title: 'My Files',
    value: 1,
    prependIcon: 'mdi-folder-outline',
  },
  {
    title: 'Shared with me',
    value: 2,
    prependIcon: 'mdi-account-multiple-outline',
  },
  {
    title: 'Starred',
    value: 3,
    prependIcon: 'mdi-star-outline',
  },
  {
    title: 'Recent',
    value: 4,
    prependIcon: 'mdi-history',
  },
  {
    title: 'Offline',
    value: 5,
    prependIcon: 'mdi-check-circle-outline',
  },
  {
    title: 'Uploads',
    value: 6,
    prependIcon: 'mdi-upload-outline',
  },
  {
    title: 'Backups',
    value: 7,
    prependIcon: 'mdi-cloud-upload-outline',
  },
]
</script>

<template>
  <VList
    nav
    :lines="false"
  >
    <VListItem
      v-for="item in items"
      :key="item.value"
      :value="item.value"
    >
      <template #prepend>
        <VIcon :icon="item.prependIcon" />
      </template>

      <VListItemTitle>
        {{ item.title }}
      </VListItemTitle>
    </VListItem>
  </VList>
</template>
