<script setup>
const props = defineProps({
  selectedRadio: {
    type: String,
    required: true,
  },
  radioContent: {
    type: Array,
    required: true,
  },
  gridColumn: {
    type: null,
    required: false,
  },
})

const emit = defineEmits(['update:selectedRadio'])

const selectedOption = ref(structuredClone(toRaw(props.selectedRadio)))

watch(selectedOption, () => {
  emit('update:selectedRadio', selectedOption.value)
})
</script>

<template>
  <VRadioGroup
    v-if="props.radioContent"
    v-model="selectedOption"
  >
    <VRow>
      <VCol
        v-for="item in props.radioContent"
        :key="item.bgImage"
        v-bind="gridColumn"
      >
        <VLabel
          class="custom-input custom-radio rounded cursor-pointer w-100"
          :class="selectedOption === item.value ? 'active' : ''"
        >
          <img
            :src="item.bgImage"
            alt="bg-img"
            class="custom-radio-image"
          >
          <VRadio :value="item.value" />
        </VLabel>
      </VCol>
    </VRow>
  </VRadioGroup>
</template>

<style lang="scss" scoped>
.custom-radio {
  padding: 0;
  border-width: 2px;

  .custom-radio-image {
    block-size: 100%;
    inline-size: 100%;
    min-inline-size: 100%;
  }

  .v-radio {
    visibility: hidden;
  }
}
</style>
