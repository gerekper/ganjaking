<script setup>
import * as demoCode from '@/views/demos/components/button/demoCodeButton'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Colors -->
      <AppCardCode
        title="Colors"
        :code="demoCode.colors"
      >
        <p>The <code>color</code> prop is used to change the background color of the alert.</p>
        <DemoButtonColors />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Outlined -->
      <AppCardCode
        title="Outlined"
        :code="demoCode.outlined"
      >
        <p>The <code>outlined</code> variant option is used to create outlined buttons.</p>
        <DemoButtonOutlined />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Flat -->
      <AppCardCode
        title="Flat"
        :code="demoCode.flat"
      >
        <p>The <code>flat</code> buttons still maintain their background color, but have no box shadow.</p>
        <DemoButtonFlat />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Rounded -->
      <AppCardCode
        title="Rounded"
        :code="demoCode.rounded"
      >
        <p>Use the <code>rounded</code> prop to control the border radius of buttons.</p>
        <DemoButtonRounded />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Text -->
      <AppCardCode
        title="Text"
        :code="demoCode.text"
      >
        <p>Use <code>text</code> variant option to create text button. Text buttons have no box shadow and no background.</p>
        <DemoButtonText />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Plain -->
      <AppCardCode
        title="Plain"
        :code="demoCode.plain"
      >
        <p>Use <code>plain</code> variant option to a create a plain button. Plain buttons have a lower baseline opacity that reacts to hover and focus.</p>
        <DemoButtonPlain />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Tonal -->
      <AppCardCode
        title="Tonal"
        :code="demoCode.tonal"
      >
        <p>Use <code>tonal</code> variant option to a create a light background button.</p>
        <DemoButtonTonal />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Icon -->
      <AppCardCode
        title="Icon"
        :code="demoCode.icon"
      >
        <p>Icons can be used inside of buttons to add emphasis to the action.</p>
        <DemoButtonIcon />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Icon Only -->
      <AppCardCode
        title="Icon Only"
        :code="demoCode.iconOnly"
      >
        <p>Use <code>icon</code> prop to create button with icon only. This property makes the button rounded and applies the text prop styles.</p>
        <DemoButtonIconOnly />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Sizing -->
      <AppCardCode
        title="Sizing"
        :code="demoCode.sizing"
      >
        <p>Buttons can be given different sizing options to fit a multitude of scenarios.</p>
        <DemoButtonSizing />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Block  -->
      <AppCardCode
        title="Block"
        :code="demoCode.block"
      >
        <p>The <code>block</code> prop allows buttons to extend the full available width.</p>
        <DemoButtonBlock />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Loaders -->
      <AppCardCode
        title="Loaders"
        :code="demoCode.loaders"
      >
        <p>Using the <code>loading</code> prop, you can notify a user that there is processing taking place. The default behavior is to use a <code>v-progress-circular</code> component but this can be customized.</p>

        <DemoButtonLoaders />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Router -->
      <AppCardCode
        title="Router"
        :code="demoCode.router"
      >
        <p>Use <code>to</code> prop to create button with router support.</p>
        <VAlert
          color="warning"
          variant="tonal"
          class="mb-4"
        >
          Note: On click of the link button, You will get redirected to another page.
        </VAlert>
        <DemoButtonRouter />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Link -->
      <AppCardCode
        title="Link"
        :code="demoCode.link"
      >
        <p>Designates that the component is a link. This is automatic when using the <code>href</code> or <code>to</code> prop.</p>
        <VAlert
          color="warning"
          variant="tonal"
          class="mb-4"
        >
          Note: On click of the link button, You will get redirected to another page.
        </VAlert>
        <DemoButtonLink />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Button Group -->
      <AppCardCode
        title="Group"
        :code="demoCode.group"
      >
        <p>
          Wrap buttons with the <code>v-btn-toggle</code> component to create a group button. You can add a visual divider  between buttons with the <code>divided</code> prop.
        </p>
        <DemoButtonGroup />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
