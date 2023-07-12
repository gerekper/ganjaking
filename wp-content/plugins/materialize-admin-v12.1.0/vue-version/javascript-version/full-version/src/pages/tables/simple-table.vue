<script setup>
import * as demoCode from '@/views/demos/forms/tables/simple-table/demoCodeSimpleTable'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <AppCardCode
        title="Basic"
        :code="demoCode.basic"
      >
        <DemoSimpleTableBasic />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <AppCardCode
        title="Theme"
        :code="demoCode.theme"
      >
        <p>
          use <code>theme</code> prop to switch table to the dark theme.
        </p>
        <DemoSimpleTableTheme />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <AppCardCode
        title="Density"
        :code="demoCode.density"
      >
        <p>
          You can show a dense version of the table by using the <code>density</code> prop.
        </p>
        <DemoSimpleTableDensity />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <AppCardCode
        title="Height"
        :code="demoCode.height"
      >
        <p>
          You can set the height of the table by using the <code>height</code> prop.
        </p>
        <DemoSimpleTableHeight />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <AppCardCode
        title="Fixed Header"
        :code="demoCode.fixedHeader"
      >
        <p>
          You can fix the header of table by using the <code>fixed-header</code> prop.
        </p>
        <DemoSimpleTableFixedHeader />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
