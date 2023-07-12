<script setup lang="ts">
import * as demoCode from '@/views/demos/forms/form-elements/rating/demoCodeRating'
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
        <p>The <code>v-rating</code> component provides a simple interface for gathering user feedback.</p>

        <DemoRatingBasic />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Density -->
      <AppCardCode
        title="Density"
        :code="demoCode.density"
      >
        <p>Control the space occupied by <code>v-rating</code> items using the <code>density</code> prop.</p>

        <DemoRatingDensity />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Colors -->
      <AppCardCode
        title="Colors"
        :code="demoCode.colors"
      >
        <p>The <code>v-rating</code> component can be colored as you want, you can set both selected and not selected colors.</p>

        <DemoRatingColors />
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
        <p>Utilize the same sizing classes available in <code>v-icon</code> or provide your own with the <code>size</code> prop.</p>

        <DemoRatingSize />
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
        <p>Change the number of items by modifying the the <code>length</code> prop.</p>

        <DemoRatingLength />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Clearable -->
      <AppCardCode
        title="Clearable"
        :code="demoCode.clearable"
      >
        <p>Use <code>clearable</code> prop to allows for the component to be cleared. Triggers when the icon containing the current value is clicked.</p>

        <DemoRatingClearable />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Readonly -->
      <AppCardCode
        title="Readonly"
        :code="demoCode.readonly"
      >
        <p>For ratings that are not meant to be changed you can use <code>readonly</code> prop.</p>

        <DemoRatingReadonly />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Hover -->
      <AppCardCode
        title="Hover"
        :code="demoCode.hover"
      >
        <p>Provides visual feedback when hovering over icons</p>

        <DemoRatingHover />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Incremented -->
      <AppCardCode
        title="Incremented"
        :code="demoCode.incremented"
      >
        <p>The <code>half-increments</code> prop increases the granularity of the ratings, allow for .5 values as well.</p>

        <DemoRatingIncremented />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Item slot -->
      <AppCardCode
        title="Item slot"
        :code="demoCode.itemSlot"
      >
        <p>Slots enable advanced customization possibilities and provide you with more freedom in how you display the rating.</p>

        <DemoRatingItemSlot />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
