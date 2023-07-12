<script setup>
import * as demoCode from '@/views/demos/forms/form-elements/switch/demoCodeSwitch'
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
        <p>A <code>v-switch</code> in its simplest form provides a toggle between 2 values.</p>

        <DemoSwitchBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Inset -->
      <AppCardCode
        title="Inset"
        :code="demoCode.inset"
      >
        <p>You can make switch render in inset mode.</p>

        <DemoSwitchInset />
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
        <p>Switches can be colored by using any of the builtin colors and contextual names using the <code>color</code> prop.</p>

        <DemoSwitchColors />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Model as array -->
      <AppCardCode
        title="Model as array"
        :code="demoCode.modelAsArray"
      >
        <p>Multiple <code>v-switch</code>'s can share the same <code>v-model</code> by using an array.</p>

        <DemoSwitchModelAsArray />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Label slot -->
      <AppCardCode
        title="Label slot"
        :code="demoCode.labelSlot"
      >
        <p>Switch labels can be defined in <code>label</code> slot - that will allow to use HTML content.</p>

        <DemoSwitchLabelSlot />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 True and False Value -->
      <AppCardCode
        title="True and False Value"
        :code="demoCode.trueAndFalseValue"
      >
        <p>
          Use <code>false-value</code> and <code>true-value</code> prop to sets value for truthy and falsy state
        </p>

        <DemoSwitchTrueAndFalseValue />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 States -->
      <AppCardCode
        title="States"
        :code="demoCode.states"
      >
        <p><code>v-switch</code> can have different states such as <code>default</code>, <code>disabled</code>, and <code>loading</code>.</p>

        <DemoSwitchStates />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
