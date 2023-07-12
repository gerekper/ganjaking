<script setup lang="ts">
import * as demoCode from '@/views/demos/forms/form-elements/custom-input/demoCodeCustomInput'
</script>

<template>
  <VRow>
    <!-- 👉 Custom Radios -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Custom Radios"
        :code="demoCode.customRadios"
      >
        <DemoCustomInputCustomRadios />
      </AppCardCode>
    </VCol>

    <!-- 👉 Custom Checkboxes -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Custom Checkboxes"
        :code="demoCode.customCheckboxes"
      >
        <DemoCustomInputCustomCheckboxes />
      </AppCardCode>
    </VCol>

    <!-- 👉 Custom Radios With Icon -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Custom Radios With Icon"
        :code="demoCode.customRadiosWithIcon"
      >
        <DemoCustomInputCustomRadiosWithIcon />
      </AppCardCode>
    </VCol>

    <!-- 👉 Custom Checkboxes with icon -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Custom Checkboxes With Icon"
        :code="demoCode.customCheckboxesWithIcon"
      >
        <DemoCustomInputCustomCheckboxesWithIcon />
      </AppCardCode>
    </VCol>

    <!-- 👉 Custom Radios with image -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Custom Radios With Image"
        :code="demoCode.customRadiosWithImage"
      >
        <DemoCustomInputCustomRadiosWithImage />
      </AppCardCode>
    </VCol>

    <!-- 👉 Custom Checkboxes with Image -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Custom Checkboxes With Image"
        :code="demoCode.customCheckboxesWithImage"
      >
        <DemoCustomInputCustomCheckboxesWithImage />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
