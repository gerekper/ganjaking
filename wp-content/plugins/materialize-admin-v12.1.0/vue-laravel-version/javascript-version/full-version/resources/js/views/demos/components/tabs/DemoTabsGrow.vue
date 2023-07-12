<script setup>
const currentTab = ref('Appetizers')

const items = [
  'Appetizers',
  'Entrees',
  'Deserts',
  'Cocktails',
]

const tabItemText = 'hortbread chocolate bar marshmallow bear claw tiramisu chocolate cookie wafer. Gummies sweet brownie brownie marshmallow chocolate cake pastry. Topping macaroon shortbread liquorice dragée macaroon.'
</script>

<template>
  <VTabs
    v-model="currentTab"
    grow
  >
    <VTab
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ item }}
    </VTab>
  </VTabs>

  <VWindow
    v-model="currentTab"
    class="mt-6"
  >
    <VWindowItem
      v-for="item in items"
      :key="item"
      :value="item"
    >
      {{ tabItemText }}
    </VWindowItem>
  </VWindow>
</template>
