<template>
  <VExpansionPanels variant="popout">
    <VExpansionPanel
      v-for="item in 4"
      :key="item"
    >
      <VExpansionPanelTitle>Popout {{ item }}</VExpansionPanelTitle>
      <VExpansionPanelText>
        Cupcake ipsum dolor sit amet. Candy canes cheesecake chocolate bar I love I love jujubes gummi bears ice cream. Cheesecake tiramisu toffee cheesecake sugar plum candy canes bonbon candy.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>
