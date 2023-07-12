<script setup>
import { useThemeConfig } from '@core/composable/useThemeConfig'

const props = defineProps({
  themes: {
    type: Array,
    required: true,
  },
})

const { theme } = useThemeConfig()

const {
  state: currentThemeName,
  next: getNextThemeName,
  index: currentThemeIndex,
} = useCycleList(props.themes.map(t => t.name), { initialValue: theme.value })

const changeTheme = () => {
  theme.value = getNextThemeName()
}

// Update icon if theme is changed from other sources
watch(theme, val => {
  currentThemeName.value = val
})
</script>

<template>
  <IconBtn @click="changeTheme">
    <VIcon :icon="props.themes[currentThemeIndex].icon" />
    <VTooltip
      activator="parent"
      open-delay="1000"
      scroll-strategy="close"
    >
      <span class="text-capitalize">{{ currentThemeName }}</span>
    </VTooltip>
  </IconBtn>
</template>
