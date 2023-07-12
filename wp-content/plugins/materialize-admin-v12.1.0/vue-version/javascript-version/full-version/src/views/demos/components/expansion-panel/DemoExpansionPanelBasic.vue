<template>
  <VExpansionPanels multiple>
    <VExpansionPanel
      v-for="i in 4"
      :key="i"
    >
      <VExpansionPanelTitle>
        Item {{ i }}
      </VExpansionPanelTitle>
      <VExpansionPanelText>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>
