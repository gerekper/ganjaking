<script setup>
const props = defineProps({
  icon: {
    type: String,
    required: false,
    default: 'mdi-close',
  },
  iconSize: {
    type: String,
    required: false,
    default: '22',
  },
})
</script>

<template>
  <IconBtn class="v-dialog-close-btn">
    <VIcon
      :icon="props.icon"
      :size="props.iconSize"
    />
  </IconBtn>
</template>
