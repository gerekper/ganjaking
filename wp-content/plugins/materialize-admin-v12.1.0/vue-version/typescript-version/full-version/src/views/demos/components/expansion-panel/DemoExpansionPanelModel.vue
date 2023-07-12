<script lang="ts" setup>
const openedPanels = ref<number[]>([])

const items = ref(5)

const all = () => {
  // [...Array(5).keys()] => [0, 1, 2, 3, 4]
  openedPanels.value = [...Array(items.value).keys()]
}

const none = () => {
  openedPanels.value = []
}
</script>

<template>
  <div>
    <div class="mb-4">
      <VBtn
        class="me-4"
        @click="all"
      >
        all
      </VBtn>

      <VBtn
        color="error"
        @click="none"
      >
        none
      </VBtn>

      <div class="mt-3">
        <span class="font-weight-bold">Selected: </span>{{ openedPanels }}
      </div>
    </div>

    <VExpansionPanels
      v-model="openedPanels"
      multiple
    >
      <VExpansionPanel
        v-for="item in items"
        :key="item"
      >
        <VExpansionPanelTitle>Header {{ item }}</VExpansionPanelTitle>
        <VExpansionPanelText>
          I love I love jujubes halvah cheesecake cookie macaroon sugar plum. Sugar plum I love bear claw marzipan wafer. Wafer sesame snaps danish candy cheesecake carrot cake tootsie roll.
        </VExpansionPanelText>
      </VExpansionPanel>
    </VExpansionPanels>
  </div>
</template>
