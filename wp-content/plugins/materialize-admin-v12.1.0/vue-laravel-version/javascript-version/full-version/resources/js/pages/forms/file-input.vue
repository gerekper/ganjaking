<script setup>
import * as demoCode from '@/views/demos/forms/form-elements/file-input/demoCodeFileInput'
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
        <p>The <code>v-file-input</code> component is used to selecting files.</p>

        <DemoFileInputBasic />
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
        <p>You can reduces the file input height with <code>density</code> prop.  Available options are: <code>default</code>, <code>comfortable</code>, and <code>compact</code>.</p>

        <DemoFileInputDensity />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Variant -->
      <AppCardCode
        title="Variant"
        :code="demoCode.variant"
      >
        <p>use <code>solo</code>, <code>filled</code>, <code>outlined</code>, <code>plain</code> and <code>underlined</code> option of <code>variant</code> prop to change the look of file input.</p>

        <DemoFileInputVariant />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Accept -->
      <AppCardCode
        title="Accept"
        :code="demoCode.accept"
      >
        <p><code>v-file-input</code> component can accept only specific media formats/file types if you want.</p>

        <DemoFileInputAccept />
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
        <p>Use <code>chip</code> prop to display the selected file as a chip.</p>

        <DemoFileInputChips />
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
        <p>When using the <code>show-size</code> property along with <code>counter</code>, the total number of files and size will be displayed under the input.</p>

        <DemoFileInputCounter />
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
        <p>
          The <code>v-file-input</code> can contain multiple files at the same time when using the <code>multiple</code> prop.
        </p>

        <DemoFileInputMultiple />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Prepend icon -->
      <AppCardCode
        title="Prepend icon"
        :code="demoCode.prependIcon"
      >
        <p>
          The <code>v-file-input</code> has a default <code>prepend-icon</code> that can be set on the component or adjusted globally.
        </p>

        <DemoFileInputPrependIcon />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Show size -->
      <AppCardCode
        title="Show size"
        :code="demoCode.showSize"
      >
        <p>The displayed size of the selected file(s) can be configured with the <code>show-size</code> property.</p>

        <DemoFileInputShowSize />
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
        <p>You can use the <code>rules</code> prop to create your own custom validation parameters.</p>

        <DemoFileInputValidation />
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
        <p>Using the <code>selection</code> slot, you can customize the appearance of your input selections.</p>

        <DemoFileInputSelectionSlot />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Loading -->
      <AppCardCode
        title="Loading"
        :code="demoCode.loading"
      >
        <p>Use <code>loading</code> prop to displays linear progress bar.</p>

        <DemoFileInputLoading />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
