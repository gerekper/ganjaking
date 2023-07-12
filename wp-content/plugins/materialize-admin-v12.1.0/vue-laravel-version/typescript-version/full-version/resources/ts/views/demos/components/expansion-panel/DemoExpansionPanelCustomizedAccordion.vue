<script setup lang="ts">
import { useTheme } from 'vuetify'

const theme = useTheme()

const isDark = ref(theme.name)
</script>

<template>
  <VExpansionPanels
    variant="accordion"
    class="customized-panels border rounded"
  >
    <VExpansionPanel
      v-for="item in 4"
      :key="item"
      elevation="0"
    >
      <VExpansionPanelTitle
        collapse-icon="mdi-minus"
        expand-icon="mdi-plus"
        :style="`background-color: ${isDark === 'light' ? 'var(--v-title-bg-light)' : 'var(--v-title-bg-dark)'}`"
      >
        Accordion {{ item }}
      </VExpansionPanelTitle>

      <VExpansionPanelText>
        Sweet roll ice cream chocolate bar. Ice cream croissant sugar plum I love cupcake gingerbread liquorice cake. Bonbon tart caramels marshmallow chocolate cake icing icing danish pie.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>

<style lang="scss">
.v-expansion-panels.customized-panels {
  --v-title-bg-light: #fafafa;
  --v-title-bg-dark: #3a3e5b;

  .v-expansion-panel-title {
    border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    margin-block-end: -1px;
  }

  .v-expansion-panel-text__wrapper {
    padding: 20px;
  }
}
</style>
