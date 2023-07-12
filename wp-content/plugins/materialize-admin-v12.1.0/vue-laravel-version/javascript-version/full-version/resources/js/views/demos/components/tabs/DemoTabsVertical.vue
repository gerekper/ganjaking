<script setup>
const currentTab = ref(0)
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      sm="4"
    >
      <VTabs
        v-model="currentTab"
        direction="vertical"
      >
        <VTab>
          <VIcon
            start
            icon="mdi-account-outline"
          />
          Option 1
        </VTab>

        <VTab>
          <VIcon
            start
            icon="mdi-lock-outline"
          />
          Option 2
        </VTab>

        <VTab>
          <VIcon
            start
            icon="mdi-access-point"
          />
          Option 3
        </VTab>
      </VTabs>
    </VCol>

    <VCol
      cols="12"
      sm="8"
    >
      <VWindow v-model="currentTab">
        <VWindowItem>
          <p>
            Sed aliquam ultrices mauris. Donec posuere vulputate arcu. Morbi ac felis. Etiam feugiat lorem non metus. Sed a libero.
          </p>

          <p class="mb-0">
            Phasellus dolor. Fusce neque. Fusce fermentum odio nec arcu. Pellentesque libero tortor, tincidunt et, tincidunt eget, semper nec, quam. Phasellus blandit leo ut odio.
          </p>
        </VWindowItem>

        <VWindowItem>
          <p>
            Morbi nec metus. Suspendisse faucibus, nunc et pellentesque egestas, lacus ante convallis tellus, vitae iaculis lacus elit id tortor. Sed mollis, eros et ultrices tempus, mauris ipsum aliquam libero, non adipiscing dolor urna a orci. Curabitur ligula sapien, tincidunt non, euismod vitae, posuere imperdiet, leo. Nunc sed turpis.
          </p>

          <p class="mb-0">
            Donec venenatis vulputate lorem. Aenean viverra rhoncus pede. In dui magna, posuere eget, vestibulum et, tempor auctor, justo. Fusce commodo aliquam arcu. Suspendisse enim turpis, dictum sed, iaculis a, condimentum nec, nisi.
          </p>
        </VWindowItem>

        <VWindowItem>
          <p>
            Fusce a quam. Phasellus nec sem in justo pellentesque facilisis. Nam eget dui. Proin viverra, ligula sit amet ultrices semper, ligula arcu tristique sapien, a accumsan nisi mauris ac eros. In dui magna, posuere eget, vestibulum et, tempor auctor, justo.
          </p>

          <p class="mb-0">
            Cras sagittis. Phasellus nec sem in justo pellentesque facilisis. Proin sapien ipsum, porta a, auctor quis, euismod ut, mi. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nam at tortor in tellus interdum sagittis.
          </p>
        </VWindowItem>
      </VWindow>
    </VCol>
  </VRow>
</template>
