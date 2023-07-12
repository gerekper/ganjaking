<script setup>
import * as demoCode from '@/views/demos/forms/form-elements/radio/demoCodeRadio'
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
        <p>The <code>v-radio</code> component is a simple radio button.</p>

        <DemoRadioBasic />
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
        <p>Radios can be colored by using any of the built-in colors and contextual names using the <code>color</code> prop.</p>

        <DemoRadioColors />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Inline -->
      <AppCardCode
        title="Inline"
        :code="demoCode.inline"
      >
        <p>Use <code>inline</code> prop to displays radio buttons in row.</p>

        <DemoRadioInline />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Density -->
      <AppCardCode
        title="Density"
        :code="demoCode.density"
      >
        <p>Use <code>density</code> prop to adjusts the spacing within the component. Available options are: <code>default</code>, <code>comfortable</code>, and <code>compact</code>.</p>

        <DemoRadioDensity />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Label Slot -->
      <AppCardCode
        title="Label Slot"
        :code="demoCode.labelSlot"
      >
        <p>Radio Group labels can be defined in <code>label</code> slot - that will allow to use HTML content.</p>

        <DemoRadioLabelSlot />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Icon -->
      <AppCardCode
        title="Icon"
        :code="demoCode.icon"
      >
        <p>Use <code>false-icon</code> and <code>true-icon</code> prop to set icon on inactive and active state.</p>

        <DemoRadioIcon />
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
        <p>Use <code>rules</code> prop to validate a radio. Accepts a mixed array of types <code>function</code>, <code>boolean</code> and <code>string</code>. Functions pass an input value as an argument and must return either <code>true</code> / <code>false</code> or a string containing an error message.</p>

        <DemoRadioValidation />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
