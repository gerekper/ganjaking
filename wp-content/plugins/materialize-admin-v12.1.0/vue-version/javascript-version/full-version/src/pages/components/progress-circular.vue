<script setup>
import * as demoCode from '@/views/demos/components/progress-circular/demoCodeProgressCircular'
</script>

<template>
  <VRow class="match-height">
    <!-- 👉 Progress circular color -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="color"
        :code="demoCode.color"
      >
        <p>Alternate colors can be applied to <code>v-progress-circular</code> using the <code>color</code> prop.</p>

        <DemoProgressCircularColor />
      </AppCardCode>
    </VCol>

    <!-- 👉 Progress circular Indeterminate -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Indeterminate"
        :code="demoCode.indeterminate"
      >
        <p>Using the <code>indeterminate</code> prop, a <code>v-progress-circular</code> continues to animate indefinitely.</p>

        <DemoProgressCircularIndeterminate />
      </AppCardCode>
    </VCol>

    <!-- 👉 Progress circular Rotate -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Rotate"
        :code="demoCode.rotate"
      >
        <p>The <code>rotate</code> prop gives you the ability to customize the <code>v-progress-circular</code>'s origin.</p>

        <DemoProgressCircularRotate />
      </AppCardCode>
    </VCol>

    <!-- 👉 Progress circular Size and Width -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Size"
        :code="demoCode.size"
      >
        <p>The <code>size</code> and <code>width</code> props allow you to easily alter the size and width of the <code>v-progress-circular</code> component.</p>

        <DemoProgressCircularSize />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
