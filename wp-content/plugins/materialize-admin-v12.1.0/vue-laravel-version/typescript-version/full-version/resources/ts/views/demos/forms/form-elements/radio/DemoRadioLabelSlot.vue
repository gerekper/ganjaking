<script lang="ts" setup>
const radios = ref('Duckduckgo')
</script>

<template>
  <VRadioGroup v-model="radios">
    <template #label>
      <div>Your favorite <strong>search engine</strong></div>
    </template>

    <VRadio value="Google">
      <template #label>
        <div>
          Of course it's <span class="text-success">
            Google
          </span>
        </div>
      </template>
    </VRadio>

    <VRadio value="Duckduckgo">
      <template #label>
        <div>
          Definitely <span class="text-primary">
            Duckduckgo
          </span>
        </div>
      </template>
    </VRadio>
  </VRadioGroup>
</template>
