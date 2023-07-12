<script setup>
import * as demoCode from '@/views/demos/components/snackbar/demoCodeSnackbar'
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
        <p>The <code>v-snackbar</code> component is used to display a quick message to a user. Snackbars support positioning, removal delay, and callbacks.</p>

        <DemoSnackbarBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 With Action -->
      <AppCardCode
        title="With Action"
        :code="demoCode.withAction"
      >
        <p>Use <code>actions</code> slot to add action button. A <code>v-snackbar</code> in its simplest form displays a temporary and closable notification to the user.</p>

        <DemoSnackbarWithAction />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Multi line -->
      <AppCardCode
        title="Multi Line"
        :code="demoCode.multiLine"
      >
        <p>The <code>multi-line</code> property extends the height of the <code>v-snackbar</code> to give you a little more room for content.</p>

        <DemoSnackbarMultiLine />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Timeout -->
      <AppCardCode
        title="Timeout"
        :code="demoCode.timeout"
      >
        <p>The <code>timeout</code> property lets you customize the delay before the <code>v-snackbar</code> is hidden.</p>

        <DemoSnackbarTimeout />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Vertical -->
      <AppCardCode
        title="Vertical"
        :code="demoCode.vertical"
      >
        <p>The <code>vertical</code> property allows you to stack the content of your <code>v-snackbar</code>.</p>

        <DemoSnackbarVertical />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Position -->
      <AppCardCode
        title="Position"
        :code="demoCode.position"
      >
        <p>Use <code>location</code> prop to change the position of snackbar.</p>

        <DemoSnackbarPosition />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Variants -->
      <AppCardCode
        title="Variants"
        :code="demoCode.variants"
      >
        <p>Apply different styles to the snackbar using props such as <code>shaped</code>, <code>rounded</code>, <code>color</code>, <code>text</code>, <code>outlined</code>, <code>tile</code> and more.</p>

        <DemoSnackbarVariants />
      </AppCardCode>
    </VCol>
    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Transition -->
      <AppCardCode
        title="Transition"
        :code="demoCode.transition"
      >
        <p>Use transition prop to sets the component transition.</p>

        <DemoSnackbarTransition />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
