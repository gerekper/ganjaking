export const accordion = {
  ts: `<template>
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
`,
  js: `<template>
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
`,
}

export const basic = {
  ts: `<template>
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
`,
  js: `<template>
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
`,
}

export const customIcon = {
  ts: `<script setup lang="ts">
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
`,
  js: `<script setup>
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
`,
}

export const customizedAccordion = {
  ts: `<script setup lang="ts">
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
        :style="\`background-color: \${isDark === 'light' ? 'var(--v-title-bg-light)' : 'var(--v-title-bg-dark)'}\`"
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
`,
  js: `<script setup>
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
        :style="\`background-color: \${isDark === 'light' ? 'var(--v-title-bg-light)' : 'var(--v-title-bg-dark)'}\`"
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
`,
}

export const inset = {
  ts: `<template>
  <VExpansionPanels variant="inset">
    <VExpansionPanel
      v-for="item in 4"
      :key="item"
    >
      <VExpansionPanelTitle>Inset {{ item }}</VExpansionPanelTitle>
      <VExpansionPanelText>
        Chocolate bar sweet roll chocolate cake pastry I love gummi bears pudding chocolate cake. I love brownie powder apple pie sugar plum I love cake candy canes wafer. Tiramisu I love oat cake oat cake danish icing. Dessert sugar plum sugar plum cookie donut chocolate cake oat cake I love gummi bears.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>
`,
  js: `<template>
  <VExpansionPanels variant="inset">
    <VExpansionPanel
      v-for="item in 4"
      :key="item"
    >
      <VExpansionPanelTitle>Inset {{ item }}</VExpansionPanelTitle>
      <VExpansionPanelText>
        Chocolate bar sweet roll chocolate cake pastry I love gummi bears pudding chocolate cake. I love brownie powder apple pie sugar plum I love cake candy canes wafer. Tiramisu I love oat cake oat cake danish icing. Dessert sugar plum sugar plum cookie donut chocolate cake oat cake I love gummi bears.
      </VExpansionPanelText>
    </VExpansionPanel>
  </VExpansionPanels>
</template>
`,
}

export const model = {
  ts: `<script lang="ts" setup>
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
`,
  js: `<script setup>
const openedPanels = ref([])
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
`,
}

export const popout = {
  ts: `<template>
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
`,
  js: `<template>
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
`,
}
