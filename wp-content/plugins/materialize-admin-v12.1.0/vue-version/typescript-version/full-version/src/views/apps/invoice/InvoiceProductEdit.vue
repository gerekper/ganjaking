<!-- eslint-disable vue/no-mutating-props -->
<script setup lang="ts">
interface Emit {
  (e: 'removeProduct', value: number): void
  (e: 'totalAmount', value: number): void
}

interface Props {
  id: number
  data: {
    title: string
    cost: number
    hours: number
    description: string
  }
}

const props = withDefaults(defineProps<Props>(), {
  data: () => ({
    title: 'App Design',
    cost: 24,
    hours: 1,
    description: 'Designed UI kit & app pages.',
  }),
})

const emit = defineEmits<Emit>()

const itemsOptions: Props['data'][] = [
  {
    title: 'App Design',
    cost: 24,
    hours: 1,
    description: 'Designed UI kit & app pages.',
  },
  {
    title: 'App Customization',
    cost: 26,
    hours: 1,
    description: 'Customization & Bug Fixes.',
  },
  {
    title: 'ABC Template',
    cost: 28,
    hours: 1,
    description: 'Vuetify admin template.',
  },
  {
    title: 'App Development',
    cost: 32,
    hours: 1,
    description: 'Native App Development.',
  },
]

const selectedItem = ref('App Customization')
const localProductData = ref(structuredClone(toRaw(props.data)))

watch(selectedItem, () => {
  const item = itemsOptions.filter(obj => {
    return obj.title === selectedItem.value
  })

  localProductData.value = item[0]
})

const removeProduct = () => {
  emit('removeProduct', props.id)
}

const totalPrice = computed(() => Number(localProductData.value.cost) * Number(localProductData.value.hours))

watch(totalPrice, () => {
  emit('totalAmount', totalPrice.value)
}, { immediate: true })
</script>

<template>
  <!-- eslint-disable vue/no-mutating-props -->
  <div class="add-products-header mb-2 d-none d-md-flex ps-5 pe-16">
    <VRow>
      <VCol
        cols="12"
        md="6"
      >
        <h6 class="text-sm font-weight-medium">
          Item
        </h6>
      </VCol>
      <VCol
        cols="12"
        md="2"
      >
        <h6 class="text-sm font-weight-medium">
          Cost
        </h6>
      </VCol>
      <VCol
        cols="12"
        md="2"
      >
        <h6 class="text-sm font-weight-medium">
          Hours
        </h6>
      </VCol>
      <VCol
        cols="12"
        md="2"
      >
        <h6 class="text-sm font-weight-medium">
          Price
        </h6>
      </VCol>
    </VRow>
  </div>

  <VCard
    flat
    border
    class="d-flex flex-sm-row flex-column-reverse"
  >
    <!-- 👉 Left Form -->
    <div class="pa-5 flex-grow-1">
      <VRow>
        <VCol
          cols="12"
          md="6"
        >
          <VSelect
            v-model="selectedItem"
            :items="itemsOptions"
            item-title="title"
            item-value="title"
            label="Select Item"
            placeholder="Select Item"
            density="compact"
            class="mb-5"
          />

          <VTextarea
            v-model="localProductData.description"
            rows="3"
            density="compact"
            label="Description"
            placeholder="Item description"
          />
        </VCol>
        <VCol
          cols="12"
          md="2"
          sm="4"
        >
          <VTextField
            v-model="localProductData.cost"
            type="number"
            label="Cost"
            placeholder="100"
            density="compact"
          />

          <div class="text-body-2 mt-5">
            <p class="mb-1">
              Discount
            </p>
            <span>0%</span>
            <span class="mx-2">
              0%
              <VTooltip activator="parent">Tax 1</VTooltip>
            </span>
            <span>
              0%
              <VTooltip activator="parent">Tax 2</VTooltip>
            </span>
          </div>
        </VCol>
        <VCol
          cols="12"
          md="2"
          sm="4"
        >
          <VTextField
            v-model="localProductData.hours"
            type="number"
            label="Hours"
            placeholder="5"
            density="compact"
          />
        </VCol>
        <VCol
          cols="12"
          md="2"
          sm="4"
        >
          <p class="my-2">
            <span class="d-inline d-md-none">Price: </span>
            <span class="text-body-1">${{ totalPrice }}</span>
          </p>
        </VCol>
      </VRow>
    </div>

    <!-- 👉 Item Actions -->
    <div
      class="d-flex flex-column item-actions pa-1"
      :class="$vuetify.display.smAndUp ? 'border-s' : 'border-b' "
    >
      <IconBtn @click="removeProduct">
        <VIcon
          :size="20"
          icon="mdi-close"
        />
      </IconBtn>
    </div>
  </VCard>
</template>
