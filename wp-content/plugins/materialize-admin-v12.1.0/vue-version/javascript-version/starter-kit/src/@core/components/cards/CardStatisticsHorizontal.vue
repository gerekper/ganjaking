<script setup>
import { kFormatter } from '@core/utils/formatters'

const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  color: {
    type: String,
    required: false,
    default: 'primary',
  },
  icon: {
    type: String,
    required: true,
  },
  stats: {
    type: Number,
    required: true,
  },
  change: {
    type: Number,
    required: true,
  },
})

const isPositive = controlledComputed(() => props.change, () => Math.sign(props.change) === 1)
</script>

<template>
  <VCard>
    <VCardText class="d-flex align-center">
      <VAvatar
        size="40"
        rounded
        :color="props.color"
        variant="tonal"
        class="me-4"
      >
        <VIcon
          :icon="props.icon"
          size="24"
        />
      </VAvatar>

      <div class="d-flex flex-column">
        <div class="d-flex align-center flex-wrap">
          <span class="text-h6">{{ kFormatter(props.stats) }}</span>
          <div
            v-if="props.change"
            :class="`${isPositive ? 'text-success' : 'text-error'}`"
          >
            <VIcon
              size="24"
              :icon="isPositive ? 'mdi-chevron-up' : 'mdi-chevron-down'"
            />
            <span class="text-caption">{{ Math.abs(props.change) }}%</span>
          </div>
        </div>
        <span class="text-caption">{{ props.title }}</span>
      </div>
    </VCardText>
  </VCard>
</template>
