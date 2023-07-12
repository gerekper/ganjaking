<script lang="ts" setup>
defineEmits<{
  (e: 'close'): void
}>()

const to = ref('')
const subject = ref('')
const message = ref('')
const isMenuOpen = ref(false)

const resetValues = () => {
  to.value = subject.value = message.value = ''
}
</script>

<template>
  <VCard
    class="email-compose-dialog"
    elevation="24"
  >
    <VCardItem class="py-3 px-5">
      <div class="d-flex align-center">
        <span class="font-weight-medium">Compose Mail</span>
        <VSpacer />
        <VIcon
          size="20"
          icon="mdi-minus"
          class="me-4"
          @click="$emit('close')"
        />
        <VIcon
          size="20"
          icon="mdi-close"
          @click="$emit('close'); resetValues()"
        />
      </div>
    </VCardItem>

    <div class="pe-5">
      <VTextField
        v-model="to"
        density="compact"
      >
        <template #prepend-inner>
          <div class="text-sm text-disabled">
            To:
          </div>
        </template>
        <template #append>
          <span class="text-sm cursor-pointer">Cc | Bcc</span>
        </template>
      </VTextField>
    </div>

    <VDivider />

    <VTextField
      v-model="subject"
      density="compact"
    >
      <template #prepend-inner>
        <div class="text-sm text-disabled">
          Subject:
        </div>
      </template>
    </VTextField>

    <VDivider />

    <VTextarea
      v-model="message"
      placeholder="Message"
    />

    <VDivider />

    <div class="d-flex align-center px-5 py-4">
      <VBtnGroup
        color="primary"
        divided
        density="compact"
      >
        <VBtn>Send</VBtn>
        <VBtn
          icon
          @click="() => isMenuOpen = !isMenuOpen"
        >
          <VIcon
            :icon="isMenuOpen ? 'mdi-menu-up' : 'mdi-menu-down' "
            size="24"
          />
          <VMenu activator="parent">
            <VList :items="['Schedule Mail', 'Save Draft']" />
          </VMenu>
        </VBtn>
      </VBtnGroup>
      <VIcon
        icon="mdi-attachment"
        class="ms-4 cursor-pointer"
      />

      <VSpacer />

      <VIcon
        icon="mdi-dots-vertical"
        size="20"
        class="cursor-pointer"
      />
      <VIcon
        icon="mdi-delete-outline"
        size="20"
        class="ms-4 cursor-pointer"
        @click="$emit('close'); resetValues()"
      />
    </div>
  </VCard>
</template>

<style lang="scss">
.email-compose-dialog {
  z-index: 910 !important;

  .v-field--prepended {
    padding-inline-start: 20px;
  }

  .v-card-item {
    background-color: rgba(var(--v-theme-on-surface), var(--v-hover-opacity));
  }

  .v-textarea .v-field {
    --v-field-padding-start: 20px;
  }

  .v-field__outline {
    display: none;
  }
}
</style>
