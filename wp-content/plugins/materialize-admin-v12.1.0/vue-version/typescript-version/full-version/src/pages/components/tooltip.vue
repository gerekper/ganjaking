<script setup lang="ts">
import * as demoCode from '@/views/demos/components/tooltip/demoCodeTooltip'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Location -->
      <AppCardCode
        title="Location"
        :code="demoCode.location"
      >
        <p>Use the <code>location</code> prop to specify on which side of the element the tooltip should show</p>

        <DemoTooltipLocation />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Events -->
      <AppCardCode
        title="Events"
        :code="demoCode.events"
      >
        <DemoTooltipEvents />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Delay On Hover -->
      <AppCardCode
        title="Delay On Hover"
        :code="demoCode.delayOnHover"
      >
        <p>Delay (in ms) after which tooltip opens (when <code>open-on-hover</code> prop is set to true)</p>

        <DemoTooltipDelayOnHover />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 V-model Support -->
      <AppCardCode
        title="V-Model Support"
        :code="demoCode.vModelSupport"
      >
        <p>Tooltip visibility can be programmatically changed using <code>v-model</code>.</p>

        <DemoTooltipVModelSupport />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Transition -->
      <AppCardCode
        title="Transition"
        :code="demoCode.transition"
      >
        <p>Use <code>transition</code> prop to sets the component transition.</p>

        <DemoTooltipTransition />
      </AppCardCode>
    </VCol>

    <VCol cols="12">
      <!-- 👉 Tooltip on Various Elements -->
      <AppCardCode
        title="Tooltip on Various Elements"
        :code="demoCode.tooltipOnVariousElements"
      >
        <p>Tooltips can wrap any element.</p>

        <DemoTooltipTooltipOnVariousElements />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
