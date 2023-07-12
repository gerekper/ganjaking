export const basic = {
  ts: `<template>
  <VAlert color="primary">
    Good Morning! Start your day with some alerts.
  </VAlert>
</template>
`,
  js: `<template>
  <VAlert color="primary">
    Good Morning! Start your day with some alerts.
  </VAlert>
</template>
`,
}

export const border = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      color="primary"
      border="top"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>

    <VAlert
      border="end"
      color="secondary"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>

    <VAlert
      border="bottom"
      color="success"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>

    <VAlert
      border="start"
      color="info"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      color="primary"
      border="top"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>

    <VAlert
      border="end"
      color="secondary"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>

    <VAlert
      border="bottom"
      color="success"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>

    <VAlert
      border="start"
      color="info"
      variant="tonal"
    >
      Good Morning! Start your day with some alerts.
    </VAlert>
  </div>
</template>
`,
}

export const closable = {
  ts: `<script lang="ts" setup>
const isAlertVisible = ref(true)
</script>

<template>
  <VAlert
    v-model="isAlertVisible"
    closable
    close-label="Close Alert"
    color="primary"
  >
    Pudding wafer I love chocolate bar wafer chupa chups wafer. Cake gummies pudding gummies cake.
  </VAlert>

  <!-- Button -->
  <div class="text-center">
    <VBtn
      v-if="!isAlertVisible"
      @click="isAlertVisible = true"
    >
      Reset
    </VBtn>
  </div>
</template>
`,
  js: `<script setup>
const isAlertVisible = ref(true)
</script>

<template>
  <VAlert
    v-model="isAlertVisible"
    closable
    close-label="Close Alert"
    color="primary"
  >
    Pudding wafer I love chocolate bar wafer chupa chups wafer. Cake gummies pudding gummies cake.
  </VAlert>

  <!-- Button -->
  <div class="text-center">
    <VBtn
      v-if="!isAlertVisible"
      @click="isAlertVisible = true"
    >
      Reset
    </VBtn>
  </div>
</template>
`,
}

export const coloredBorder = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      border="start"
      border-color="primary"
    >
      Cake sweet roll sesame snaps cheesecake halvah apple pie gingerbread cake.
    </VAlert>
    <VAlert
      border="start"
      border-color="secondary"
    >
      Cookie brownie tootsie roll pudding biscuit chupa chups. Dragée gingerbread carrot.
    </VAlert>
    <VAlert
      border="start"
      border-color="success"
    >
      Gingerbread jelly beans macaroon croissant soufflé. Muffin halvah cake brownie cake.
    </VAlert>
    <VAlert
      border="start"
      border-color="info"
    >
      Muffin I love wafer pudding caramels jelly beans fruitcake I love cotton candy.
    </VAlert>

    <VAlert
      border="start"
      border-color="warning"
    >
      Cake sweet roll sesame snaps cheesecake halvah apple pie gingerbread cake.
    </VAlert>

    <VAlert
      border="start"
      border-color="error"
    >
      Lemon drops tootsie roll liquorice marzipan lollipop I love tiramisu tootsie roll.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      border="start"
      border-color="primary"
    >
      Cake sweet roll sesame snaps cheesecake halvah apple pie gingerbread cake.
    </VAlert>
    <VAlert
      border="start"
      border-color="secondary"
    >
      Cookie brownie tootsie roll pudding biscuit chupa chups. Dragée gingerbread carrot.
    </VAlert>
    <VAlert
      border="start"
      border-color="success"
    >
      Gingerbread jelly beans macaroon croissant soufflé. Muffin halvah cake brownie cake.
    </VAlert>
    <VAlert
      border="start"
      border-color="info"
    >
      Muffin I love wafer pudding caramels jelly beans fruitcake I love cotton candy.
    </VAlert>

    <VAlert
      border="start"
      border-color="warning"
    >
      Cake sweet roll sesame snaps cheesecake halvah apple pie gingerbread cake.
    </VAlert>

    <VAlert
      border="start"
      border-color="error"
    >
      Lemon drops tootsie roll liquorice marzipan lollipop I love tiramisu tootsie roll.
    </VAlert>
  </div>
</template>
`,
}

export const colors = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert color="primary">
      I'm an alert with primary background color.
    </VAlert>

    <VAlert color="secondary">
      I'm an alert with secondary background color.
    </VAlert>

    <VAlert color="success">
      I'm an alert with success background color.
    </VAlert>

    <VAlert color="info">
      I'm an alert with info background color.
    </VAlert>

    <VAlert color="warning">
      I'm an alert with warning background color.
    </VAlert>

    <VAlert color="error">
      I'm an alert with error background color.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert color="primary">
      I'm an alert with primary background color.
    </VAlert>

    <VAlert color="secondary">
      I'm an alert with secondary background color.
    </VAlert>

    <VAlert color="success">
      I'm an alert with success background color.
    </VAlert>

    <VAlert color="info">
      I'm an alert with info background color.
    </VAlert>

    <VAlert color="warning">
      I'm an alert with warning background color.
    </VAlert>

    <VAlert color="error">
      I'm an alert with error background color.
    </VAlert>
  </div>
</template>
`,
}

export const density = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      density="compact"
      color="primary"
      variant="tonal"
    >
      I'm a compact alert with a <strong>color</strong> of primary.
    </VAlert>

    <VAlert
      density="comfortable"
      color="secondary"
      variant="tonal"
    >
      I'm a comfortable alert with the <strong>variant</strong> prop and a <strong>color</strong> of secondary.
    </VAlert>

    <VAlert
      density="default"
      color="success"
      variant="tonal"
    >
      I'm a default alert with the <strong>color</strong> of success.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      density="compact"
      color="primary"
      variant="tonal"
    >
      I'm a compact alert with a <strong>color</strong> of primary.
    </VAlert>

    <VAlert
      density="comfortable"
      color="secondary"
      variant="tonal"
    >
      I'm a comfortable alert with the <strong>variant</strong> prop and a <strong>color</strong> of secondary.
    </VAlert>

    <VAlert
      density="default"
      color="success"
      variant="tonal"
    >
      I'm a default alert with the <strong>color</strong> of success.
    </VAlert>
  </div>
</template>
`,
}

export const elevation = {
  ts: `<script lang="ts" setup>
const alertShadow = ref(5)
</script>

<template>
  <VSlider
    v-model="alertShadow"
    color="primary"
    :max="24"
    :min="0"
    :step="1"
    thumb-label
  />

  <VAlert
    color="primary"
    :elevation="alertShadow"
  >
    I'm an alert with box shadow.
  </VAlert>
</template>
`,
  js: `<script setup>
const alertShadow = ref(5)
</script>

<template>
  <VSlider
    v-model="alertShadow"
    color="primary"
    :max="24"
    :min="0"
    :step="1"
    thumb-label
  />

  <VAlert
    color="primary"
    :elevation="alertShadow"
  >
    I'm an alert with box shadow.
  </VAlert>
</template>
`,
}

export const icons = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      color="primary"
      icon="mdi-rocket-launch-outline"
    >
      Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
    </VAlert>

    <VAlert
      color="secondary"
      icon="mdi-whatsapp"
    >
      Phasellus blandit leo ut odio. Morbi mattis ullamcorper velit.
    </VAlert>

    <VAlert
      color="success"
      icon="mdi-nodejs"
    >
      Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      color="primary"
      icon="mdi-rocket-launch-outline"
    >
      Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
    </VAlert>

    <VAlert
      color="secondary"
      icon="mdi-whatsapp"
    >
      Phasellus blandit leo ut odio. Morbi mattis ullamcorper velit.
    </VAlert>

    <VAlert
      color="success"
      icon="mdi-nodejs"
    >
      Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus.
    </VAlert>
  </div>
</template>
`,
}

export const outlined = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      variant="outlined"
      color="primary"
    >
      Duis vel nibh at velit scelerisque suscipit. Praesent blandit laoreet nibh. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc.
    </VAlert>

    <VAlert
      variant="outlined"
      color="secondary"
    >
      Praesent venenatis metus at tortor pulvinar varius. Aenean commodo ligula eget dolor. Praesent ac massa at ligula laoreet iaculis.
    </VAlert>

    <VAlert
      variant="outlined"
      color="success"
    >
      Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Suspendisse non nisl sit amet velit hendrerit rutrum.
    </VAlert>

    <VAlert
      variant="outlined"
      color="info"
    >
      Marshmallow jelly beans toffee. Sweet roll lemon drops muffin biscuit. Gummies jujubes halvah dessert cream croissant.
    </VAlert>

    <VAlert
      variant="outlined"
      color="warning"
    >
      Tootsie roll candy canes wafer icing sweet jelly macaroon. Caramels icing fruitcake chocolate cake cake donut.
    </VAlert>

    <VAlert
      variant="outlined"
      color="error"
    >
      Jelly beans dragée jelly. Cotton candy danish chocolate cake. Carrot cake pastry jelly beans gummi bears.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      variant="outlined"
      color="primary"
    >
      Duis vel nibh at velit scelerisque suscipit. Praesent blandit laoreet nibh. Aenean posuere, tortor sed cursus feugiat, nunc augue blandit nunc.
    </VAlert>

    <VAlert
      variant="outlined"
      color="secondary"
    >
      Praesent venenatis metus at tortor pulvinar varius. Aenean commodo ligula eget dolor. Praesent ac massa at ligula laoreet iaculis.
    </VAlert>

    <VAlert
      variant="outlined"
      color="success"
    >
      Duis arcu tortor, suscipit eget, imperdiet nec, imperdiet iaculis, ipsum. Suspendisse non nisl sit amet velit hendrerit rutrum.
    </VAlert>

    <VAlert
      variant="outlined"
      color="info"
    >
      Marshmallow jelly beans toffee. Sweet roll lemon drops muffin biscuit. Gummies jujubes halvah dessert cream croissant.
    </VAlert>

    <VAlert
      variant="outlined"
      color="warning"
    >
      Tootsie roll candy canes wafer icing sweet jelly macaroon. Caramels icing fruitcake chocolate cake cake donut.
    </VAlert>

    <VAlert
      variant="outlined"
      color="error"
    >
      Jelly beans dragée jelly. Cotton candy danish chocolate cake. Carrot cake pastry jelly beans gummi bears.
    </VAlert>
  </div>
</template>
`,
}

export const prominent = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      prominent
      type="info"
    >
      <template #text>
        Macaroon I love tiramisu I love wafer apple pie jelly beans shortbread.
      </template>
    </VAlert>

    <VAlert
      color="success"
      icon="mdi-school-outline"
      prominent
    >
      Cotton candy tart tiramisu lollipop gummi bears oat cake cupcake macaroon.
    </VAlert>

    <VAlert
      icon="mdi-shield-lock-outline"
      prominent
      type="warning"
    >
      Ice cream candy I love wafer bonbon gingerbread candy canes tiramisu.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      prominent
      type="info"
    >
      <template #text>
        Macaroon I love tiramisu I love wafer apple pie jelly beans shortbread.
      </template>
    </VAlert>

    <VAlert
      color="success"
      icon="mdi-school-outline"
      prominent
    >
      Cotton candy tart tiramisu lollipop gummi bears oat cake cupcake macaroon.
    </VAlert>

    <VAlert
      icon="mdi-shield-lock-outline"
      prominent
      type="warning"
    >
      Ice cream candy I love wafer bonbon gingerbread candy canes tiramisu.
    </VAlert>
  </div>
</template>
`,
}

export const tonal = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert
      variant="tonal"
      color="primary"
    >
      Maecenas nec odio et ante tincidunt tempus. Sed mollis, eros et ultrices tempus.
    </VAlert>

    <VAlert
      variant="tonal"
      color="secondary"
    >
      Nullam tincidunt adipiscing enim. In consectetuer turpis ut velit.
    </VAlert>

    <VAlert
      variant="tonal"
      color="success"
    >
      Vestibulum ullamcorper mauris at ligula. Nulla porta dolor.
    </VAlert>

    <VAlert
      variant="tonal"
      color="info"
    >
      Praesent blandit laoreet nibh. Praesent nonummy mi in odio. Phasellus tempus. Mauris turpis nunc.
    </VAlert>

    <VAlert
      variant="tonal"
      color="warning"
    >
      Marzipan topping croissant cake sweet roll ice cream soufflé chocolate. Jelly beans chupa chups tootsie roll biscuit.
    </VAlert>

    <VAlert
      variant="tonal"
      color="error"
    >
      Marzipan topping croissant cake sweet roll ice cream soufflé chocolate. Jelly beans chupa chups tootsie roll biscuit.
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert
      variant="tonal"
      color="primary"
    >
      Maecenas nec odio et ante tincidunt tempus. Sed mollis, eros et ultrices tempus.
    </VAlert>

    <VAlert
      variant="tonal"
      color="secondary"
    >
      Nullam tincidunt adipiscing enim. In consectetuer turpis ut velit.
    </VAlert>

    <VAlert
      variant="tonal"
      color="success"
    >
      Vestibulum ullamcorper mauris at ligula. Nulla porta dolor.
    </VAlert>

    <VAlert
      variant="tonal"
      color="info"
    >
      Praesent blandit laoreet nibh. Praesent nonummy mi in odio. Phasellus tempus. Mauris turpis nunc.
    </VAlert>

    <VAlert
      variant="tonal"
      color="warning"
    >
      Marzipan topping croissant cake sweet roll ice cream soufflé chocolate. Jelly beans chupa chups tootsie roll biscuit.
    </VAlert>

    <VAlert
      variant="tonal"
      color="error"
    >
      Marzipan topping croissant cake sweet roll ice cream soufflé chocolate. Jelly beans chupa chups tootsie roll biscuit.
    </VAlert>
  </div>
</template>
`,
}

export const type = {
  ts: `<template>
  <div class="demo-space-y">
    <VAlert type="info">
      I'm a alert with a <strong>type</strong> of info
    </VAlert>

    <VAlert type="success">
      I'm a alert with a <strong>type</strong> of success
    </VAlert>

    <VAlert type="warning">
      I'm a alert with a <strong>type</strong> of warning
    </VAlert>

    <VAlert type="error">
      I'm a alert with a <strong>type</strong> of error
    </VAlert>
  </div>
</template>
`,
  js: `<template>
  <div class="demo-space-y">
    <VAlert type="info">
      I'm a alert with a <strong>type</strong> of info
    </VAlert>

    <VAlert type="success">
      I'm a alert with a <strong>type</strong> of success
    </VAlert>

    <VAlert type="warning">
      I'm a alert with a <strong>type</strong> of warning
    </VAlert>

    <VAlert type="error">
      I'm a alert with a <strong>type</strong> of error
    </VAlert>
  </div>
</template>
`,
}

export const vModelSupport = {
  ts: `<script lang="ts" setup>
const isAlertVisible = ref(true)
</script>

<template>
  <div class="alert-demo-v-model-wrapper">
    <VAlert
      v-model="isAlertVisible"
      color="warning"
      variant="tonal"
    >
      non adipiscing dolor urna a orci. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur blandit mollis lacus. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo.
    </VAlert>
  </div>

  <!-- button -->
  <VBtn @click="isAlertVisible = !isAlertVisible">
    {{ isAlertVisible ? "Hide Alert" : "Show Alert" }}
  </VBtn>
</template>

<style lang="scss">
.alert-demo-v-model-wrapper {
  margin-block-end: 1rem;
  min-block-size: 80px;
}
</style>
`,
  js: `<script setup>
const isAlertVisible = ref(true)
</script>

<template>
  <div class="alert-demo-v-model-wrapper">
    <VAlert
      v-model="isAlertVisible"
      color="warning"
      variant="tonal"
    >
      non adipiscing dolor urna a orci. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur blandit mollis lacus. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo.
    </VAlert>
  </div>

  <!-- button -->
  <VBtn @click="isAlertVisible = !isAlertVisible">
    {{ isAlertVisible ? "Hide Alert" : "Show Alert" }}
  </VBtn>
</template>

<style lang="scss">
.alert-demo-v-model-wrapper {
  margin-block-end: 1rem;
  min-block-size: 80px;
}
</style>
`,
}
