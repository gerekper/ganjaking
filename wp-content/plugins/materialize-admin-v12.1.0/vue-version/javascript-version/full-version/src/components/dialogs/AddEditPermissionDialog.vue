<script setup>
const props = defineProps({
  isDialogVisible: {
    type: Boolean,
    required: true,
  },
  permissionName: {
    type: String,
    required: false,
    default: '',
  },
})

const emit = defineEmits([
  'update:isDialogVisible',
  'update:permissionName',
])

const permissionName = ref('')

const onReset = () => {
  emit('update:isDialogVisible', false)
  permissionName.value = ''
}

const onSubmit = () => {
  emit('update:isDialogVisible', false)
  emit('update:permissionName', permissionName.value)
}

watch(props, () => {
  permissionName.value = props.permissionName
})
</script>

<template>
  <VDialog
    :width="$vuetify.display.smAndDown ? 'auto' : 600"
    :model-value="props.isDialogVisible"
    @update:model-value="onReset"
  >
    <VCard class="pa-sm-8 pa-5">
      <!-- 👉 dialog close btn -->
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="onReset"
      />

      <!-- 👉 Title -->
      <VCardItem class="text-center">
        <VCardTitle class="text-h5">
          {{ props.permissionName ? 'Edit' : 'Add' }} Permission
        </VCardTitle>
        <VCardSubtitle>
          {{ props.permissionName ? 'Edit' : 'Add' }}  permission as per your requirements.
        </VCardSubtitle>
      </VCardItem>

      <VCardText class="mt-1">
        <!-- 👉 Form -->
        <VForm>
          <VAlert
            type="warning"
            title="Warning!"
            variant="tonal"
            class="mb-6"
          >
            By editing the permission name, you might break the system permissions functionality. Please ensure you're absolutely certain before proceeding.
          </VAlert>

          <!-- 👉 Role name -->
          <div class="d-flex align-center gap-3 mb-3">
            <VTextField
              v-model="permissionName"
              density="compact"
              label="Permission Name"
              placeholder="Enter Permission Name"
            />

            <VBtn @click="onSubmit">
              Update
            </VBtn>
          </div>

          <VCheckbox label="Set as core permission" />
        </VForm>
      </VCardText>
    </VCard>
  </VDialog>
</template>

<style lang="scss">
.permission-table {
  td {
    border-block-end: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    padding-block: 0.5rem;
    padding-inline: 0;
  }
}
</style>
