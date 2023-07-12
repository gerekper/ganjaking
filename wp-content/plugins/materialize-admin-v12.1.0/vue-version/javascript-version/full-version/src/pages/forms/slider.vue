<script setup>
import * as demoCode from '@/views/demos/forms/form-elements/slider/demoCodeSlider'
</script>

<template>
  <VRow class="match-height">
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

        <DemoSliderBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Disabled and Readonly -->
      <AppCardCode
        title="Disabled and Readonly"
        :code="demoCode.disabledAndReadonly"
      >
        <p>You cannot interact with <code>disabled</code> and <code>readonly</code> sliders.</p>

        <DemoSliderDisabledAndReadonly />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Colors -->
      <AppCardCode
        title="Colors"
        :code="demoCode.colors"
      >
        <p>You can set the colors of the slider using the props <code>color</code>, <code>track-color</code> and <code>thumb-color</code>.</p>

        <DemoSliderColors />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Icons -->
      <AppCardCode
        title="Icons"
        :code="demoCode.icons"
      >
        <p>You can add icons to the slider with the <code>append-icon</code> and <code>prepend-icon</code> props.</p>

        <DemoSliderIcons />
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
        <p>Using the <code>step</code> prop you can control the precision of the slider, and how much it should move each step.</p>

        <DemoSliderStep />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Validation -->
      <AppCardCode
        title="Validation"
        :code="demoCode.validation"
      >
        <p>Vuetify includes simple validation through the <code>rules</code> prop.</p>

        <DemoSliderValidation />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Min and Max -->
      <AppCardCode
        title="Min and Max"
        :code="demoCode.minAndMax"
      >
        <p>You can set <code>min</code> and <code>max</code> values of sliders.</p>

        <DemoSliderMinAndMax />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Size -->
      <AppCardCode
        title="Size"
        :code="demoCode.size"
      >
        <p>Use <code>thumb-size</code>, <code>tick-size</code>, and <code>track-size</code> prop to increase and decrease the size of thumb, tick and track. </p>

        <DemoSliderSize />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Thumb -->
      <AppCardCode
        title="Thumb"
        :code="demoCode.thumb"
      >
        <p>You can display a thumb label while sliding or always with the <code>thumb-label</code> prop.</p>

        <DemoSliderThumb />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Ticks -->
      <AppCardCode
        title="Ticks"
        :code="demoCode.ticks"
      >
        <p>Tick marks represent predetermined values to which the user can move the slider.</p>

        <DemoSliderTicks />
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
        <p>
          You can use the <code>vertical</code> prop to switch sliders to a vertical orientation.
        </p>

        <DemoSliderVertical />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Append text field -->
      <AppCardCode
        title="Append text field"
        :code="demoCode.appendTextField"
      >
        <p>Sliders can be combined with other components in its <code>append</code> slot, such as <code>v-text-field</code>, to add additional functionality to the component.</p>

        <DemoSliderAppendTextField />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Append and prepend -->
      <AppCardCode
        title="Append and prepend"
        :code="demoCode.appendAndPrepend"
      >
        <p>Use slots such as <code>append</code> and <code>prepend</code> to easily customize the <code>v-slider</code> to fit any situation.</p>

        <DemoSliderAppendAndPrepend />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
