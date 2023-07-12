<script lang="ts" setup>
const isDialogVisible = ref(false)
const firstName = ref('')
const middleName = ref('')
const lastName = ref('')
const email = ref('')
const password = ref('')
const age = ref()
const interest = ref<string[]>([])
</script>

<template>
  <VDialog
    v-model="isDialogVisible"
    max-width="600"
  >
    <!-- Dialog Activator -->
    <template #activator="{ props }">
      <VBtn v-bind="props">
        Open Dialog
      </VBtn>
    </template>

    <!-- Dialog Content -->
    <VCard title="User Profile">
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="isDialogVisible = false"
      />

      <VCardText>
        <VRow>
          <VCol
            cols="12"
            sm="6"
            md="4"
          >
            <VTextField
              v-model="firstName"
              label="First Name"
              placeholder="John"
            />
          </VCol>
          <VCol
            cols="12"
            sm="6"
            md="4"
          >
            <VTextField
              v-model="middleName"
              label="Middle Name"
              placeholder="peter"
            />
          </VCol>
          <VCol
            cols="12"
            sm="6"
            md="4"
          >
            <VTextField
              v-model="lastName"
              label="Last Name"
              persistent-hint
              placeholder="Doe"
            />
          </VCol>
          <VCol cols="12">
            <VTextField
              v-model="email"
              label="Email"
              placeholder="johndoe@email.com"
            />
          </VCol>
          <VCol cols="12">
            <VTextField
              v-model="password"
              label="Password"
              type="password"
              placeholder="············"
            />
          </VCol>
          <VCol
            cols="12"
            sm="6"
          >
            <VTextField
              v-model="age"
              label="Age"
              type="number"
              placeholder="18"
            />
          </VCol>
          <VCol
            cols="12"
            sm="6"
          >
            <VTextField
              v-model="interest"
              label="Interests"
              placeholder="Sports, Music, Movies"
            />
          </VCol>
        </VRow>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="error"
          @click="isDialogVisible = false"
        >
          Close
        </VBtn>
        <VBtn
          color="success"
          @click="isDialogVisible = false"
        >
          Save
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
