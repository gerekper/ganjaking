<script setup>
import * as demoCode from '@/views/demos/forms/form-elements/select/demoCodeSelect'
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
        <p>Select fields components are used for collecting user provided information from a list of options.</p>
        <DemoSelectBasic />
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
        <p>You can use <code>density</code> prop to reduce the field height and lower max height of list items.</p>
        <DemoSelectDensity />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Variant -->
      <AppCardCode
        title="Variant"
        :code="demoCode.variant"
      >
        <p>
          Use <code>filled</code>, <code>outlined</code>, <code>solo</code>, <code>underlined</code> and <code>plain</code> options of <code>variant</code> prop to change appearance of select.
        </p>

        <DemoSelectVariant />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Custom text and value -->
      <AppCardCode
        title="Custom text and value"
        :code="demoCode.customTextAndValue"
      >
        <p>You can specify the specific properties within your items array that correspond to the title and value fields. In this example we also use the return-object prop which will return the entire object of the selected item on selection.</p>

        <DemoSelectCustomTextAndValue />
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
        <p>Use a custom <code>prepend</code> or <code>appended</code> icon.</p>

        <DemoSelectIcons />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Chips -->
      <AppCardCode
        title="Chips"
        :code="demoCode.chips"
      >
        <p>Use <code>chips</code> prop to make selected option as chip.</p>

        <DemoSelectChips />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Menu Props -->
      <AppCardCode
        title="Menu Props"
        :code="demoCode.menuProps"
      >
        <p>Custom props can be passed directly to <code>v-menu</code> using <code>menuProps</code> prop.</p>

        <DemoSelectMenuProps />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Multiple -->
      <AppCardCode
        title="Multiple"
        :code="demoCode.multiple"
      >
        <p>Use <code>multiple</code> prop to select multiple option.</p>

        <DemoSelectMultiple />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Selection slot -->
      <AppCardCode
        title="Selection slot"
        :code="demoCode.selectionSlot"
      >
        <p>The <code>selection</code> slot can be used to customize the way selected values are shown in the input.</p>

        <DemoSelectSelectionSlot />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
