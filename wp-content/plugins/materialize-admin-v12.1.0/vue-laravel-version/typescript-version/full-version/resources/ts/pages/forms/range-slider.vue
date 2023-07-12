<script setup lang="ts">
import * as demoCode from '@/views/demos/forms/form-elements/range-slider/demoCodeRangeSlider'
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Basic -->
      <AppCardCode
        title="Basic"
        :code="demoCode.basic"
      >
        <p>The <code>v-slider</code> component is a better visualization of the number input.</p>

        <DemoRangeSliderBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Disabled -->
      <AppCardCode
        title="Disabled"
        :code="demoCode.disabled"
      >
        <p>You cannot interact with <code>disabled</code> sliders.</p>

        <DemoRangeSliderDisabled />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Color -->
      <AppCardCode
        title="Color"
        :code="demoCode.color"
      >
        <p>Use <code>color</code> prop to the sets the slider color. <code>track-color</code> prop to sets the color of slider's unfilled track.</p>

        <DemoRangeSliderColor />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Step -->
      <AppCardCode
        title="Step"
        :code="demoCode.step"
      >
        <p><code>v-range-slider</code> can have steps other than 1. This can be helpful for some applications where you need to adjust values with more or less accuracy.</p>

        <DemoRangeSliderStep />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Thumb label -->
      <AppCardCode
        title="Thumb label"
        :code="demoCode.thumbLabel"
      >
        <p>
          Using the <code>tick-labels</code> prop along with the <code>thumb-label</code> slot, you can create a very customized solution.
        </p>

        <DemoRangeSliderThumbLabel />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Vertical -->
      <AppCardCode
        title="Vertical"
        :code="demoCode.vertical"
      >
        <p>You can use the <code>vertical</code> prop to switch sliders to a vertical orientation. If you need to change the height of the slider, use css.</p>

        <DemoRangeSliderVertical />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
