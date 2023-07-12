<template>
  <VExpansionPanels variant="accordion">
    <VExpansionPanel
      v-for="item in 4"
      :key="item"
    >
      <VExpansionPanelTitle>
        Accordion {{ item }}
      </VExpansionPanelTitle>
      <VExpansionPanelText>
        Sweet roll ice cream chocolate bar. Ice cream croissant sugar plum I love cupcake gingerbread liquorice cake. Bonbon tart caramels marshmallow chocolate cake icing icing danish pie.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>
