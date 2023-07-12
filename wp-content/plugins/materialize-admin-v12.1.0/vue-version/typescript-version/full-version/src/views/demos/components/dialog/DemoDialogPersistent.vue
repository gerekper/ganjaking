<script lang="ts" setup>
const isDialogVisible = ref(false)
</script>

<template>
  <VDialog
    v-model="isDialogVisible"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog Activator -->
    <template #activator="{ props }">
      <VBtn v-bind="props">
        Open Dialog
      </VBtn>
    </template>

    <!-- Dialog Content -->
    <VCard title="Use Google's location service?">
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="isDialogVisible = false"
      />

      <VCardText>
        Let Google help apps determine location. This means sending anonymous location data to Google, even when no apps are running.
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="error"
          @click="isDialogVisible = false"
        >
          Disagree
        </VBtn>
        <VBtn
          color="success"
          @click="isDialogVisible = false"
        >
          Agree
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
