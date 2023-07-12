<script lang="ts" setup>
const totalTabs = ref(3)
const currentTab = ref(0)

watch(totalTabs, newValue => {
  currentTab.value = newValue - 1
})
</script>

<template>
  <VTabs v-model="currentTab">
    <VTab
      v-for="n in totalTabs"
      :key="n"
      :value="n"
    >
      Tab {{ n }}
    </VTab>
  </VTabs>

  <!-- buttons -->
  <div class="text-center mt-9">
    <VBtn
      :disabled="!totalTabs"
      variant="text"
      @click="totalTabs--"
    >
      Remove Tab
    </VBtn>

    <VBtn
      variant="text"
      @click="totalTabs++"
    >
      Add Tab
    </VBtn>
  </div>
</template>
