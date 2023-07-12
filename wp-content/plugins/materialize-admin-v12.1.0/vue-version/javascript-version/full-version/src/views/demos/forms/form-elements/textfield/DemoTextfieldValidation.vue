<script setup>
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const email = ref('')
</script>

<template>
  <VForm>
    <VTextField
      v-model="email"
      :rules="[requiredValidator, emailValidator]"
      placeholder="johnedoe@email.com"
      label="E-mail"
    />
  </VForm>
</template>
