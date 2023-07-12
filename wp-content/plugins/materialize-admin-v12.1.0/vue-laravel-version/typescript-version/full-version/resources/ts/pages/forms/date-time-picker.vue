<script setup lang="ts">
import * as demoCode from '@/views/demos/forms/form-elements/date-time-picker/demoCodeDateTimePicker'
</script>

<template>
  <VRow>
    <!-- 👉 Basic -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Basic"
        :code="demoCode.basic"
      >
        <DemoDateTimePickerBasic />
      </AppCardCode>
    </VCol>

    <!-- 👉 Time Picker -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Time Picker"
        :code="demoCode.timePicker"
      >
        <DemoDateTimePickerTimePicker />
      </AppCardCode>
    </VCol>

    <!-- 👉 Date & TIme -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Date and Time"
        :code="demoCode.dateAndTime"
      >
        <DemoDateTimePickerDateAndTime />
      </AppCardCode>
    </VCol>

    <!-- 👉 Multiple Dates -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Multiple Dates"
        :code="demoCode.multipleDates"
      >
        <DemoDateTimePickerMultipleDates />
      </AppCardCode>
    </VCol>

    <!-- 👉 Range -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Range"
        :code="demoCode.range"
      >
        <DemoDateTimePickerRange />
      </AppCardCode>
    </VCol>

    <!-- 👉 Human Friendly -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Human Friendly"
        :code="demoCode.humanFriendly"
      >
        <DemoDateTimePickerHumanFriendly />
      </AppCardCode>
    </VCol>

    <!-- 👉 Disabled Range -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Disabled Range"
        :code="demoCode.disabledRange"
      >
        <DemoDateTimePickerDisabledRange />
      </AppCardCode>
    </VCol>

    <!-- 👉 Inline -->
    <VCol
      cols="12"
      md="6"
    >
      <AppCardCode
        title="Inline"
        :code="demoCode.inline"
      >
        <DemoDateTimePickerInline />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
