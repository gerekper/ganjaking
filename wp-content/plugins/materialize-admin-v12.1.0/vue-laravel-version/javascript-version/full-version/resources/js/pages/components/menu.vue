<script setup>
import * as demoCode from '@/views/demos/components/menu/demoCodeMenu'
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
        <p>
          Remember to put the element that activates the menu in the activator slot.
        </p>

        <DemoMenuBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Custom transitions -->
      <AppCardCode
        title="Custom transitions"
        :code="demoCode.customTransitions"
      >
        <p>Vuetify comes with 3 standard transitions, <code>scale</code>, <code>slide-x</code> and <code>slide-y</code>. Use <code>transition</code> prop to add transition to a menu.</p>

        <DemoMenuCustomTransitions />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Location -->
      <AppCardCode
        title="Location"
        :code="demoCode.location"
      >
        <p>Menu can be offset relative to the activator by using the <code>location</code> prop.</p>

        <DemoMenuLocation />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Open on hover -->
      <AppCardCode
        title="Open on hover"
        :code="demoCode.openOnHover"
      >
        <p>Menus can be accessed using hover instead of clicking with the <code>open-on-hover</code> prop.</p>

        <DemoMenuOpenOnHover />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Popover -->
      <AppCardCode
        title="Popover"
        :code="demoCode.popover"
      >
        <p>A menu can be configured to be static when opened, allowing it to function as a popover. This can be useful when there are multiple interactive items within the menu contents.</p>

        <DemoMenuPopover />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Activator and tooltip -->
      <AppCardCode
        title="Activator and tooltip"
        :code="demoCode.activatorAndTooltip"
      >
        <p>With the new <code>v-slot</code> syntax, nested activators such as those seen with a <code>v-menu</code> and <code>v-tooltip</code> attached to the same activator button, need a particular setup in order to function correctly</p>

        <DemoMenuActivatorAndTooltip />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
