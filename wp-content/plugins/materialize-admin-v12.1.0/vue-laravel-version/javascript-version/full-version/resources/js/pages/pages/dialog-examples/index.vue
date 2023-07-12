<script setup>
const isCardAddDialogVisible = ref(false)
const isPricingPlanDialogVisible = ref(false)
const isEditAddressDialogVisible = ref(false)
const isTwoFactorDialogOpen = ref(false)
const isUserInfoEditDialogVisible = ref(false)
const isShareProjectDialogVisible = ref(false)
const isReferAndEarnDialogVisible = ref(false)
const isCreateAppVisible = ref(false)

const userData = {
  id: 110,
  fullName: 'Selena Kyle',
  company: 'pixinvent',
  role: 'Web developer',
  username: 'charwomen1940',
  country: 'USA',
  contact: '(829) 537-0057',
  email: 'irena.dubrovna@wayne.com',
  currentPlan: '',
  status: 'active',
  avatar: '',
  taskDone: null,
  projectDone: null,
  taxId: 'Tax-8894',
  language: 'English',
}
</script>

<template>
  <VRow class="match-height">
    <!-- 👉 Share Project Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-file-document-outline"
            />
          </template>
          <VCardTitle>Share Project</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Quickly collect the credit card details, built in input mask and form validation support.
          </p>

          <VBtn @click="isShareProjectDialogVisible = !isShareProjectDialogVisible">
            Show
          </VBtn>
        </VCardText>

        <ShareProjectDialog v-model:isDialogVisible="isShareProjectDialogVisible" />
      </VCard>
    </VCol>

    <!-- 👉 Add New Card Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-credit-card-outline"
            />
          </template>
          <VCardTitle>Add New Card</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Quickly collect the credit card details, built in input mask and form validation support.
          </p>

          <VBtn @click="isCardAddDialogVisible = !isCardAddDialogVisible">
            Show
          </VBtn>
        </VCardText>

        <CardAddEditDialog v-model:isDialogVisible="isCardAddDialogVisible" />
      </VCard>
    </VCol>

    <!-- 👉 Pricing Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-currency-usd"
            />
          </template>
          <VCardTitle>Pricing</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Elegant pricing options dialog popup example, easy to use in any page.
          </p>

          <VBtn @click="isPricingPlanDialogVisible = !isPricingPlanDialogVisible">
            Show
          </VBtn>
        </VCardText>

        <!-- 👉 plan and pricing dialog -->
        <PricingPlanDialog v-model:is-dialog-visible="isPricingPlanDialogVisible" />
      </VCard>
    </VCol>

    <!-- 👉 Refer And Earn Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-gift-outline"
            />
          </template>
          <VCardTitle>Refer & Earn</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Use Refer & Earn modal to encourage your exiting customers refer their friends & colleague.
          </p>

          <VBtn @click="isReferAndEarnDialogVisible = !isReferAndEarnDialogVisible">
            Show
          </VBtn>
        </VCardText>

        <!-- 👉 plan and pricing dialog -->
        <ReferAndEarnDialog v-model:is-dialog-visible="isReferAndEarnDialogVisible" />
      </VCard>
    </VCol>

    <!-- 👉 Add New Address Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-home-outline"
            />
          </template>
          <VCardTitle>Add New Address</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Ready to use form to collect user address data with validation and custom input support.
          </p>

          <VBtn @click="isEditAddressDialogVisible = !isEditAddressDialogVisible">
            Show
          </VBtn>
        </VCardText>

        <!-- 👉 Edit Address dialog -->
        <AddEditAddressDialog v-model:isDialogVisible="isEditAddressDialogVisible" />
      </VCard>
    </VCol>

    <!-- 👉 Create app dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-cube-outline"
            />
          </template>
          <VCardTitle>Create App</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Provide application data with this form to create the app dialog popup example, easy to use in any page.
          </p>

          <VBtn @click="isCreateAppVisible = !isCreateAppVisible">
            Show
          </VBtn>
        </VCardText>
      </VCard>
      <CreateAppDialog v-model:is-dialog-visible="isCreateAppVisible" />
    </VCol>

    <!-- 👉 Two Factor Auth Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-lock-outline"
            />
          </template>
          <VCardTitle>Two Factor Auth</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Enhance your application security by enabling two factor authentication.
          </p>

          <VBtn @click="isTwoFactorDialogOpen = !isTwoFactorDialogOpen">
            Show
          </VBtn>
        </VCardText>

        <!-- 👉 Enable One Time Password Dialog -->

        <TwoFactorAuthDialog v-model:isDialogVisible="isTwoFactorDialogOpen" />
      </VCard>
    </VCol>

    <!-- 👉 Edit User Info Dialog -->
    <VCol
      cols="12"
      sm="6"
      md="4"
    >
      <VCard class="text-high-emphasis text-center">
        <VCardItem class="d-flex flex-column gap-2">
          <template #prepend>
            <VIcon
              size="34"
              icon="mdi-lock-outline"
            />
          </template>
          <VCardTitle>Edit User Info</VCardTitle>
        </VCardItem>

        <VCardText>
          <p class="text-base clamp-text">
            Enhance your application security by enabling two factor authentication.
          </p>

          <VBtn @click="isUserInfoEditDialogVisible = !isUserInfoEditDialogVisible">
            Show
          </VBtn>
        </VCardText>

        <!-- 👉 Edit user info dialog -->
        <UserInfoEditDialog
          v-model:isDialogVisible="isUserInfoEditDialogVisible"
          :user-data="userData"
        />
      </VCard>
    </VCol>
  </VRow>
</template>

<style lang="scss">
.pricing-dialog {
  .pricing-title {
    font-size: 1.5rem !important;
  }
}
</style>
