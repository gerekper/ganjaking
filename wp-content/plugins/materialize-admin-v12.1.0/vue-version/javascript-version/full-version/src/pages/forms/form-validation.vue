<script setup>
import * as demoCode from '@/views/demos/forms/form-validation/demoCodeFormValidation'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Simple Form Validation -->
      <AppCardCode
        title="Simple Form Validation"
        :code="demoCode.simpleFormValidation"
      >
        <p>Use <code>Rules</code> prop to validate the input.</p>

        <DemoFormValidationSimpleFormValidation />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Validating Multiple Rules -->
      <AppCardCode
        title="Validating Multiple Rules"
        :code="demoCode.validatingMultipleRules"
      >
        <DemoFormValidationValidatingMultipleRules />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Validation Types -->
      <AppCardCode
        title="Validation Types"
        :code="demoCode.validationTypes"
      >
        <DemoFormValidationValidationTypes />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
