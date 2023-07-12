<script setup>
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  divider: {
    type: Boolean,
    required: false,
    default: true,
  },
})
</script>

<template>
  <VDivider v-if="props.divider" />

  <div class="customizer-section">
    <p class="text-caption">
      {{ props.title }}
    </p>

    <slot />
  </div>
</template>
