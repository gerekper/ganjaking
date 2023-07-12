<script lang="ts" setup>
const checkbox = ref(1)
const checkboxString = ref('Show')
</script>

<template>
  <div class="demo-space-x">
    <VCheckbox
      v-model="checkbox"
      :true-value="1"
      :false-value="0"
      :label="`${checkbox.toString()}`"
    />

    <VCheckbox
      v-model="checkboxString"
      true-value="Show"
      false-value="Hide"
      color="success"
      :label="`${checkboxString.toString()}`"
    />
  </div>
</template>
