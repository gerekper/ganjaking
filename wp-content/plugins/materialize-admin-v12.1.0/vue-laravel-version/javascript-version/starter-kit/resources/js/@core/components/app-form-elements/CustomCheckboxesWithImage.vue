<script setup>
const props = defineProps({
  selectedCheckbox: {
    type: Array,
    required: true,
  },
  checkboxContent: {
    type: Array,
    required: true,
  },
  gridColumn: {
    type: null,
    required: false,
  },
})

const emit = defineEmits(['update:selectedCheckbox'])

const selectedOption = ref(structuredClone(toRaw(props.selectedCheckbox)))

watch(selectedOption, () => {
  emit('update:selectedCheckbox', selectedOption.value)
})
</script>

<template>
  <VRow
    v-if="props.checkboxContent && selectedOption"
    v-model="selectedOption"
  >
    <VCol
      v-for="item in props.checkboxContent"
      :key="item.value"
      v-bind="gridColumn"
    >
      <VLabel
        class="custom-input custom-checkbox rounded cursor-pointer w-100"
        :class="selectedOption.includes(item.value) ? 'active' : ''"
      >
        <div>
          <VCheckbox
            v-model="selectedOption"
            :value="item.value"
          />
        </div>
        <img
          :src="item.bgImage"
          alt="bg-img"
          class="custom-checkbox-image"
        >
      </VLabel>
    </VCol>
  </VRow>
</template>

<style lang="scss" scoped>
.custom-checkbox {
  position: relative;
  padding: 0;
  border-width: 2px;
  transition: all 0.5s;

  .custom-checkbox-image {
    block-size: 100%;
    inline-size: 100%;
    min-inline-size: 100%;
  }

  .v-checkbox {
    position: absolute;
    inset-block-start: 0;
    inset-inline-end: 0;
    visibility: hidden;
  }

  &:hover,
  &.active {
    .v-checkbox {
      visibility: visible;
    }
  }
}
</style>
