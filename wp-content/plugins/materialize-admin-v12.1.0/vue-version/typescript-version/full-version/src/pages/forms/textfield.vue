<script setup lang="ts">
import * as demoCode from '@/views/demos/forms/form-elements/textfield/demoCodeTextfield'
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
        <p>Text fields components are used for collecting user provided information.</p>

        <DemoTextfieldBasic />
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
        <p>The <code>density</code> prop decreases the height of the text field based upon 1 of 3 levels of density; <code>default</code>, <code>comfortable</code>, and <code>compact</code>.</p>

        <DemoTextfieldDensity />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Variant -->
      <AppCardCode
        title="Variant"
        :code="demoCode.variant"
      >
        <p>Use <code>solo</code>, <code>filled</code>, <code>outlined</code>, <code>plain</code> and <code>underlined</code> option of <code>variant</code> prop to change the look of the textfield. </p>

        <DemoTextfieldVariant />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 State -->
      <AppCardCode
        title="State"
        :code="demoCode.state"
      >
        <p>Text fields can be disabled or readonly.</p>

        <DemoTextfieldState />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Counter -->
      <AppCardCode
        title="Counter"
        :code="demoCode.counter"
      >
        <p>Use a <code>counter</code> prop to inform a user of the character limit.</p>

        <DemoTextfieldCounter />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Clearable -->
      <AppCardCode
        title="Clearable"
        :code="demoCode.clearable"
      >
        <p>When clearable, you can customize the clear icon with clear-icon.</p>

        <DemoTextfieldClearable />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Custom Colors -->
      <AppCardCode
        title="Custom Colors"
        :code="demoCode.customColors"
      >
        <p>Use <code>color</code> prop to change the input border color.</p>

        <DemoTextfieldCustomColors />
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
        <p>You can add icons to the text field with <code>prepend-icon</code>, <code>append-icon</code> and <code>append-inner-icon</code> and <code>prepend-inner-icon</code> props.</p>

        <DemoTextfieldIcons />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Prefixes and suffixes -->
      <AppCardCode
        title="Prefixes and suffixes"
        :code="demoCode.prefixesAndSuffixes"
      >
        <p>The <code>prefix</code> and <code>suffix</code> properties allows you to prepend and append inline non-modifiable text next to the text field.</p>

        <DemoTextfieldPrefixesAndSuffixes />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Single line  -->
      <AppCardCode
        title="Single line"
        :code="demoCode.singleLine"
      >
        <p><code>single-line</code> text fields do not float their label on focus or with data.</p>

        <DemoTextfieldSingleLine />
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

        <DemoTextfieldValidation />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Icon events -->
      <AppCardCode
        title="Icon events"
        :code="demoCode.iconEvents"
      >
        <p><code>click:prepend</code>, <code>click:append</code>, <code>click:append-inner</code>, and <code>click:clear</code> will be emitted when you click on the respective icon</p>

        <DemoTextfieldIconEvents />
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
        <p>Text field label can be defined in <code>label</code> slot - that will allow to use HTML content.</p>

        <DemoTextfieldLabelSlot />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Password input -->
      <AppCardCode
        title="Password input"
        :code="demoCode.passwordInput"
      >
        <p>Using the HTML input <code>type</code> password can be used with an appended icon and callback to control the visibility.</p>

        <DemoTextfieldPasswordInput />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Icon slots -->
      <AppCardCode
        title="Icon slots"
        :code="demoCode.iconSlots"
      >
        <p>Instead of using <code>prepend</code>/<code>append</code>/<code>append-inner</code> icons you can use slots to extend input's functionality.</p>

        <DemoTextfieldIconSlots />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
