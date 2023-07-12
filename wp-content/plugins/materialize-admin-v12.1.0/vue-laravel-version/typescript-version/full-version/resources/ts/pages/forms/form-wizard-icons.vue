<script setup lang="ts">
import * as demoCode from '@/views/demos/forms/form-wizard/form-wizard-icons/demoCodeFormWizardIcons'

import DemoFormWizardIconsBasic from '@/views/demos/forms/form-wizard/form-wizard-icons/DemoFormWizardIconsBasic.vue'
import DemoFormWizardIconsValidation from '@/views/demos/forms/form-wizard/form-wizard-icons/DemoFormWizardIconsValidation.vue'
import DemoFormWizardIconsVertical from '@/views/demos/forms/form-wizard/form-wizard-icons/DemoFormWizardIconsVertical.vue'
</script>

<template>
  <VRow>
    <!-- 👉 Basic -->
    <VCol cols="12">
      <AppCardCode
        variant="outlined"
        title="Basic"
        :code="demoCode.basic"
      >
        <DemoFormWizardIconsBasic />
      </AppCardCode>
    </VCol>

    <!-- 👉 Validation -->
    <VCol cols="12">
      <AppCardCode
        variant="outlined"
        title="Validation"
        :code="demoCode.validation"
      >
        <DemoFormWizardIconsValidation />
      </AppCardCode>
    </VCol>

    <!-- 👉 Vertical -->
    <VCol cols="12">
      <AppCardCode
        variant="outlined"
        title="Vertical"
        :code="demoCode.vertical"
      >
        <DemoFormWizardIconsVertical />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
