<script setup>
import * as demoCode from '@/views/demos/forms/tables/data-table/demoCodeDataTable'
</script>

<template>
  <VRow>
    <VCol cols="12">
      <!-- 👉 Basic -->
      <AppCardCode
        title="Basic"
        :code="demoCode.basic"
      >
        <DemoDataTableBasic />
      </AppCardCode>
    </VCol>

    <VCol>
      <!-- 👉 Dense  -->
      <AppCardCode
        title="dense"
        :code="demoCode.dense"
      >
        <DemoDataTableDense />
      </AppCardCode>
    </VCol>

    <!-- 👉 Table Cell Slot  -->
    <VCol cols="12">
      <AppCardCode
        title="cell Slot"
        :code="demoCode.cellSlot"
      >
        <DemoDataTableCellSlot />
      </AppCardCode>
    </VCol>

    <!-- 👉 Table Row selection  -->
    <VCol cols="12">
      <AppCardCode
        title="Row Selection"
        :code="demoCode.rowSelection"
      >
        <DemoDataTableRowSelection />
      </AppCardCode>
    </VCol>

    <!-- 👉 Fixed Header  -->
    <VCol cols="12">
      <AppCardCode
        title="Fixed Header"
        :code="demoCode.fixedHeader"
      >
        <DemoDataTableFixedHeader />
      </AppCardCode>
    </VCol>

    <!-- 👉 Expandable rows -->
    <VCol cols="12">
      <AppCardCode
        title="Expandable Rows"
        :code="demoCode.expandableRows"
      >
        <DemoDataTableExpandableRows />
      </AppCardCode>
    </VCol>
    <!-- 👉 Grouping Rows -->
    <VCol cols="12">
      <AppCardCode
        title="Grouping Rows"
        :code="demoCode.groupingRows"
      >
        <DemoDataTableGroupingRows />
      </AppCardCode>
    </VCol>

    <!-- 👉 Row Editing via Dialog -->
    <VCol cols="12">
      <AppCardCode
        title="Row Editing via Dialog"
        :code="demoCode.rowEditingViaDialog"
      >
        <DemoDataTableRowEditingViaDialog />
      </AppCardCode>
    </VCol>

    <!-- 👉 External Pagination  -->
    <VCol cols="12">
      <AppCardCode
        title="External Pagination"
        :code="demoCode.externalPagination"
      >
        <DemoDataTableExternalPagination />
      </AppCardCode>
    </VCol>

    <!-- 👉 Kitchen Sink  -->
    <VCol cols="12">
      <AppCardCode
        title="Kitchen Sink"
        :code="demoCode.kitchenSink"
      >
        <DemoDataTableKitchenSink />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
