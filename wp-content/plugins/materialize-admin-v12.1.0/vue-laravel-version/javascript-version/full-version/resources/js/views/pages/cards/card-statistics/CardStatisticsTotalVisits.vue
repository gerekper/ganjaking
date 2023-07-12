<template>
  <VCard>
    <VCardText>
      <div class="d-flex align-center justify-space-between">
        <span class="text-sm">Total Visits</span>
        <span class="text-success font-weight-medium">
          <span>+18.2%</span>
          <VIcon icon="mdi-chevron-up" />
        </span>
      </div>
      <h6 class="text-h6">
        $42.5k
      </h6>
    </VCardText>

    <VCardText>
      <VRow no-gutters>
        <VCol cols="5">
          <div class="d-flex align-center mb-3">
            <VAvatar
              rounded="sm"
              color="warning"
              variant="tonal"
              :size="24"
              class="me-2"
            >
              <VIcon
                size="14"
                icon="mdi-cellphone"
              />
            </VAvatar>

            <span>Mobile</span>
          </div>
          <h6 class="text-h6">
            23.5%
          </h6>
          <span class="text-sm text-disabled">2,890</span>
        </VCol>

        <VCol cols="2">
          <div class="d-flex flex-column align-center justify-center h-100">
            <VDivider
              vertical
              class="mx-auto"
            />

            <div class="pa-1">
              <VChip size="20">
                <span class="text-xs pa-1">VS</span>
              </VChip>
            </div>

            <VDivider
              vertical
              class="mx-auto"
            />
          </div>
        </VCol>

        <VCol
          cols="5"
          class="text-end"
        >
          <div class="d-flex align-center justify-end mb-3">
            <span class="me-2">Desktop</span>

            <VAvatar
              rounded="sm"
              color="primary"
              variant="tonal"
              :size="24"
            >
              <VIcon
                size="14"
                icon="mdi-monitor"
              />
            </VAvatar>
          </div>
          <h6 class="text-h6">
            76.5%
          </h6>
          <span class="text-sm text-disabled">22,465</span>
        </VCol>
      </VRow>

      <div class="mt-4">
        <VProgressLinear
          model-value="25"
          color="warning"
          height="8"
          rounded
        />
      </div>
    </VCardText>
  </VCard>
</template>
