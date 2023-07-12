<script lang="ts" setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const message = ref('Hey!')
const loading = ref(false)

const clickMe = () => {
  loading.value = true
  message.value = 'Wait for it...'

  setTimeout(() => {
    loading.value = false
    message.value = 'You\'ve clicked me!'
  }, 2000)
}
</script>

<template>
  <VTextField
    v-model="message"
    clearable
    clear-icon="mdi-close-circle-outline"
    label="Message"
    placeholder="Hey!!"
    type="text"
  >
    <!-- Prepend -->
    <template #prepend>
      <VTooltip location="bottom">
        <template #activator="{ props }">
          <VIcon
            v-bind="props"
            icon="mdi-help-circle-outline"
          />
        </template>
        I'm a tooltip
      </VTooltip>
    </template>

    <!-- AppendInner -->
    <template #append-inner>
      <VFadeTransition leave-absolute>
        <VProgressCircular
          v-if="loading"
          size="24"
          color="info"
          indeterminate
        />

        <VNodeRenderer
          v-else
          :nodes="themeConfig.app.logo"
        />
      </VFadeTransition>
    </template>

    <!-- Append -->
    <template #append>
      <VBtn
        :size="$vuetify.display.smAndDown ? 'small' : 'large'"
        :class="$vuetify.display.smAndDown ? 'mt-n2' : 'mt-n3'"
        :icon="$vuetify.display.smAndDown"
        @click="clickMe"
      >
        <VIcon icon="mdi-target" />
        <span
          v-if="$vuetify.display.mdAndUp"
          class="ms-3"
        >Click me</span>
      </VBtn>
    </template>
  </VTextField>
</template>
