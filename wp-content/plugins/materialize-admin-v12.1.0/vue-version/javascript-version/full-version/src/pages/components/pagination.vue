<script setup>
import * as demoCode from '@/views/demos/components/pagination/demoCodePagination'
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
        <p>The <code>v-pagination</code> component is used to separate long sets of data.</p>

        <DemoPaginationBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 outline basic variant -->
      <AppCardCode
        title="Outline"
        :code="demoCode.outline"
      >
        <p>The <code>variant='outline'</code> prop is used to give outline to pagination item.</p>

        <DemoPaginationOutline />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Circle -->
      <AppCardCode
        title="Circle"
        :code="demoCode.circle"
      >
        <p>The <code>rounded</code> prop allows you to render pagination buttons with alternative styles.</p>

        <DemoPaginationCircle />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 outline circle variant -->
      <AppCardCode
        title="Outline Circle"
        :code="demoCode.outlineCircle"
      >
        <p>The <code>variant='outline'</code> and <code>rounded</code> prop is used to give rounded outline to pagination item.</p>

        <DemoPaginationOutlineCircle />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Disabled -->
      <AppCardCode
        title="Disabled"
        :code="demoCode.disabled"
      >
        <p>Pagination items can be manually deactivated using the <code>disabled</code> prop.</p>

        <DemoPaginationDisabled />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Icons -->
      <AppCardCode
        title="Icons"
        :code="demoCode.icons"
      >
        <p>Previous and next page icons can be customized with the <code>prev-icon</code> and <code>next-icon</code> props.</p>

        <DemoPaginationIcons />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Length -->
      <AppCardCode
        title="Length"
        :code="demoCode.length"
      >
        <p>Using the <code>length</code> prop you can set the length of <code>v-pagination</code>, if the number of page buttons exceeds the parent container, it will truncate the list.</p>

        <DemoPaginationLength />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Total visible -->
      <AppCardCode
        title="Total visible"
        :code="demoCode.totalVisible"
      >
        <p>You can also manually set the maximum number of visible page buttons with the <code>total-visible</code> prop.</p>

        <DemoPaginationTotalVisible />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Color -->
      <AppCardCode
        title="Color"
        :code="demoCode.color"
      >
        <p>Use <code>active-color</code> prop for create different color pagination.</p>

        <DemoPaginationColor />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Size -->
      <AppCardCode
        title="Size"
        :code="demoCode.size"
      >
        <p>Use <code>size</code> prop to sets the height and width of the component. Default unit is px. Can also use the following predefined sizes: <strong>x-small</strong>, <strong>small</strong>, <strong>default</strong>, <strong>large</strong>, and <strong>x-large</strong>.</p>

        <DemoPaginationSize />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
