<script setup>
const panel = ref(0)
</script>

<template>
  <VExpansionPanels v-model="panel">
    <VExpansionPanel>
      <VExpansionPanelTitle disable-icon-rotate>
        Server Down
        <template #actions>
          <VIcon
            icon="mdi-alert-circle-outline"
            color="error"
          />
        </template>
      </VExpansionPanelTitle>
      <VExpansionPanelText>
        Gummies biscuit dessert macaroon liquorice carrot cake oat cake jelly beans cake. Candy wafer tiramisu sugar plum sweet. Ice cream topping gummies biscuit soufflé marzipan topping brownie marshmallow. Chocolate cake cookie pudding gummies cotton candy ice cream. Pie liquorice marzipan cake carrot cake macaroon jelly toffee. Lollipop donut gummi bears caramels icing marzipan.
      </VExpansionPanelText>
    </VExpansionPanel>

    <VExpansionPanel>
      <VExpansionPanelTitle disable-icon-rotate>
        Sales report generated
        <template #actions>
          <VIcon
            icon="mdi-check"
            color="success"
          />
        </template>
      </VExpansionPanelTitle>
      <VExpansionPanelText>
        Bear claw ice cream icing gummies gingerbread cotton candy tootsie roll cupcake macaroon. Halvah brownie soufflé. Pie dragée macaroon. Tart tootsie roll chocolate bar biscuit jujubes lemon drops. Pudding cotton candy tart jelly-o bear claw lollipop. Jelly-o apple pie candy bonbon chupa chups cupcake cotton candy. Sweet roll cotton candy toffee caramels. Jelly-o chocolate cake toffee pastry halvah. Muffin tiramisu ice cream danish jelly-o brownie powde
      </VExpansionPanelText>
    </VExpansionPanel>

    <VExpansionPanel>
      <VExpansionPanelTitle disable-icon-rotate>
        High Memory usage
        <template #actions>
          <VIcon
            icon="mdi-alert-outline"
            color="warning"
          />
        </template>
      </VExpansionPanelTitle>
      <VExpansionPanelText>
        Jelly beans wafer lemon drops macaroon muffin gummies muffin. Ice cream oat cake chocolate bar sesame snaps. Halvah macaroon caramels gummies. Marshmallow jelly beans danish. Cake chocolate cake tiramisu chocolate bar sugar plum biscuit jelly danish. Pudding gummi bears sesame snaps cake soufflé ice cream chocolate bar. Cotton candy ice cream danish chocolate cake topping ice cream. Brownie muffin gingerbread.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>
