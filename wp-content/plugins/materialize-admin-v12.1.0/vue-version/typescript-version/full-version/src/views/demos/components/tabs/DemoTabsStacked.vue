<script lang="ts" setup>
const currentTab = ref('tab-1')
const tabItemText = 'Biscuit cheesecake gingerbread oat cake tiramisu. Marzipan tiramisu jelly-o muffin biscuit jelly cake pie. Chocolate cookie candy croissant brownie cupcake powder cheesecake. Biscuit sesame snaps biscuit topping tiramisu croissant.'
</script>

<template>
  <VTabs
    v-model="currentTab"
    grow
    stacked
  >
    <VTab value="tab-1">
      <VIcon
        icon="mdi-phone"
        class="mb-2"
      />
      <span>Recent</span>
    </VTab>

    <VTab value="tab-2">
      <VIcon
        icon="mdi-heart-outline"
        class="mb-2"
      />
      <span>Favorites</span>
    </VTab>

    <VTab value="tab-3">
      <VIcon
        icon="mdi-account-box-outline"
        class="mb-2"
      />
      <span>Nearby</span>
    </VTab>
  </VTabs>

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="i in 3"
      :key="i"
      :value="`tab-${i}`"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
