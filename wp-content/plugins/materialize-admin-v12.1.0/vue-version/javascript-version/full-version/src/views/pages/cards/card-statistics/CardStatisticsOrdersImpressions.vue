<template>
  <VCard>
    <VCardText class="d-flex align-center gap-4 my-1">
      <VProgressCircular
        :size="65"
        :width="6"
        :model-value="70"
        color="primary"
      >
        <VIcon icon="mdi-tablet-cellphone" />
      </VProgressCircular>

      <div>
        <div class="d-flex align-center gap-2">
          <h6 class="text-h6">
            84k
          </h6>
          <div class="text-error">
            <span>-24%</span>
            <VIcon icon="mdi-chevron-down" />
          </div>
        </div>
        <span>Total Impressions</span>
      </div>
    </VCardText>

    <VDivider />

    <VCardText class="d-flex align-center gap-4 my-1">
      <VProgressCircular
        :size="65"
        :width="6"
        :model-value="40"
        color="warning"
      >
        <VIcon icon="mdi-shopping-outline" />
      </VProgressCircular>

      <div>
        <div class="d-flex align-center gap-2">
          <h6 class="text-h6">
            22k
          </h6>
          <div class="text-success">
            <span>+15%</span>
            <VIcon icon="mdi-chevron-up" />
          </div>
        </div>
        <span>Total Orders</span>
      </div>
    </VCardText>
  </VCard>
</template>
