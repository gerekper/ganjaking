<template>
  <div class="demo-space-x">
    <VBtn>
      Accept
      <VIcon
        end
        icon="mdi-checkbox-marked-circle-outline"
      />
    </VBtn>

    <VBtn color="secondary">
      <VIcon
        start
        icon="mdi-minus-circle-outline"
      />Cancel
    </VBtn>

    <VBtn color="success">
      Upload
      <VIcon
        end
        icon="mdi-cloud-upload-outline"
      />
    </VBtn>

    <VBtn color="info">
      <VIcon
        start
        icon="mdi-arrow-left"
      />
      Back
    </VBtn>

    <VBtn color="warning">
      <VIcon icon="mdi-wrench-outline" />
    </VBtn>

    <VBtn color="error">
      <VIcon icon="mdi-cancel" />
    </VBtn>
  </div>
</template>
