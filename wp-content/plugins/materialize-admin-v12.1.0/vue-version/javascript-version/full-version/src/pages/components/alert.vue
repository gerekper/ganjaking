<script setup>
import * as demoCode from '@/views/demos/components/alert/demoCodeAlert'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Basic -->
      <AppCardCode
        title="Basic"
        :code="demoCode.basic"
      >
        <DemoAlertBasic />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Colors -->
      <AppCardCode
        title="Colors"
        :code="demoCode.colors"
      >
        <p>The <code>color</code> prop is used to change the background color of the alert.</p>

        <DemoAlertColors />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Icons -->
      <AppCardCode
        title="Icons"
        :code="demoCode.icons"
      >
        <p>The <code>icon</code> prop allows you to add an icon to the beginning of the alert component. If a <code>type</code> is provided, this will override the default type icon. Additionally, setting the <code>icon</code> prop to false will remove the icon altogether.</p>

        <DemoAlertIcons />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Border -->
      <AppCardCode
        title="Border"
        :code="demoCode.border"
      >
        <p>The <code>border</code> prop adds a simple border to one of the 4 sides of the alert. This can be combined with props like <code>color</code>, <code>type</code> and <code>icon</code> to provide unique accents to the alert.</p>

        <DemoAlertBorder />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Colored Border -->
      <AppCardCode
        title="Colored Border"
        :code="demoCode.coloredBorder"
      >
        <p>The <code>colored-border</code> prop removes the alert background in order to accent the <code>border</code> prop. If a type is set, it will use the type's default color. If no <code>color</code> or <code>type</code> is set, the color will default to the inverted color of the applied theme (black for light and white/gray for dark).</p>

        <DemoAlertColoredBorder />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Density -->
      <AppCardCode
        title="Density"
        :code="demoCode.density"
      >
        <p>The <code>density</code> prop decreases the height of the alert based upon 1 of 3 levels of density. <code>default</code>, <code>comfortable</code>, and <code>compact</code>.</p>

        <DemoAlertDensity />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Type -->
      <AppCardCode
        title="Type"
        :code="demoCode.type"
      >
        <p>The <code>type</code> prop provides 4 default v-alert styles: <code>success</code>, <code>info</code>, <code>warning</code>, and <code>error</code>. Each of these styles provide a default icon and color.</p>

        <DemoAlertType />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Closable  -->
      <AppCardCode
        title="Closable"
        :code="demoCode.closable"
      >
        <p>The <code>closable</code> prop adds a close button to the end of the alert component. Clicking this button will set its value to false and effectively hide the alert.</p>

        <DemoAlertClosable />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 V-model support -->
      <AppCardCode
        title="v-model support"
        :code="demoCode.vModelSupport"
      >
        <p>Clicking this button will set its value to <code>false</code> and effectively hide the alert. You can restore the alert by binding <code>v-model</code> and setting it to true.</p>

        <DemoAlertVModelSupport />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Outlined -->
      <AppCardCode
        title="Outlined"
        :code="demoCode.outlined"
      >
        <p>The <code>variant="outlined"</code> prop inverts the style of an alert, inheriting the currently applied <code>color</code>, applying it to the text and border, and making its background transparent.</p>

        <DemoAlertOutlined />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Tonal -->
      <AppCardCode
        title="Tonal"
        :code="demoCode.tonal"
      >
        <p>The <code>variant</code> prop provides an easy way to change the overall style of your alerts. The <code>variant="tonal"</code> prop is a simple alert variant that applies a reduced opacity background of the provided color.</p>

        <DemoAlertTonal />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Elevation -->
      <AppCardCode
        title="Elevation"
        :code="demoCode.elevation"
      >
        <p>Use <code>elevation</code> prop to set a box-shadow to alert.</p>

        <DemoAlertElevation />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Prominent -->
      <AppCardCode
        title="Prominent"
        :code="demoCode.prominent"
      >
        <p>The <code>prominent</code> prop provides a more pronounced alert by increasing the size of the icon.</p>

        <DemoAlertProminent />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
