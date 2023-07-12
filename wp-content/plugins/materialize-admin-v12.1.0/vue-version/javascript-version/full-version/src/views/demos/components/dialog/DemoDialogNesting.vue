<script setup>
const isDialogVisible = ref(false)
const isDialogTwoShow = ref(false)
</script>

<template>
  <VBtn @click="isDialogVisible = true">
    Open Dialog
  </VBtn>

  <!-- Dialog -->
  <VDialog
    v-model="isDialogVisible"
    class="v-dialog-sm"
  >
    <VCard title="Dialog">
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="isDialogVisible = false"
      />

      <VCardText>
        Biscuit fruitcake marshmallow jelly beans jujubes halvah cupcake topping. Chocolate cookie jelly-o toffee tart oat cake. Tart sugar plum gingerbread halvah muffin sweet. Cake halvah tart soufflé pudding.
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="error"
          @click="isDialogVisible = false"
        >
          Close
        </VBtn>
        <VBtn @click="isDialogTwoShow = !isDialogTwoShow">
          Open Dialog 2
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Dialog 2 -->
  <VDialog
    v-model="isDialogTwoShow"
    class="v-dialog-sm"
  >
    <VCard title="Dialog 2">
      <DialogCloseBtn
        variant="text"
        size="small"
        @click="isDialogTwoShow = false"
      />

      <VCardText>I'm a nested dialog.</VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn @click="isDialogTwoShow = false">
          Close
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>
