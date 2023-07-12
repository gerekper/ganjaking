<script setup lang="ts">
import type { CartItem, CheckoutData } from './types'

interface Props {
  currentStep?: number
  checkoutData: CheckoutData
}

interface Emit {
  (e: 'update:currentStep', value: number): void
  (e: 'update:checkout-data', value: CheckoutData): void
}

const props = defineProps<Props>()

const emit = defineEmits<Emit>()

const checkoutCartDataLocal = ref(props.checkoutData)

// remove item from cart
const removeItem = (item: CartItem) => {
  checkoutCartDataLocal.value.cartItems = checkoutCartDataLocal.value.cartItems.filter(i => i.id !== item.id)

  console.log(checkoutCartDataLocal.value.cartItems)
}

//  cart total
const totalCost = computed(() => {
  return checkoutCartDataLocal.value.orderAmount = checkoutCartDataLocal.value.cartItems.reduce((acc, item) => {
    return acc + item.price * item.quantity
  }, 0)
})

const updateCartData = () => {
  emit('update:checkout-data', checkoutCartDataLocal.value)
}

const nextStep = () => {
  updateCartData()
  emit('update:currentStep', props.currentStep ? props.currentStep + 1 : 1)
}

watch(() => props.currentStep, updateCartData)
</script>

<template>
  <VRow v-if="checkoutCartDataLocal">
    <VCol
      cols="12"
      md="8"
    >
      <!-- 👉 Offers alert -->
      <VAlert
        color="success"
        variant="tonal"
        icon="mdi-check-circle-outline"
      >
        <VAlertTitle class="text-body-1 text-success mb-1">
          Available Offers
        </VAlertTitle>

        <p class="mb-1">
          - 10% Instant Discount on Bank of America Corp Bank Debit and Credit cards
        </p>
        <p class="mb-0">
          - 25% Cashback Voucher of up to $60 on first ever PayPal transaction. TCA
        </p>
      </VAlert>

      <h6 class="text-h6 my-4">
        My Shopping Bag ({{ checkoutCartDataLocal.cartItems.length }} Items)
      </h6>

      <!-- 👉 Cart items -->
      <div class="border rounded">
        <template
          v-for="(item, index) in checkoutCartDataLocal.cartItems"
          :key="item.name"
        >
          <div
            class="d-flex align-center gap-3 pa-6 position-relative flex-column flex-sm-row"
            :class="index ? 'border-t' : ''"
          >
            <IconBtn
              size="x-small"
              class="checkout-item-remove-btn"
              @click="removeItem(item)"
            >
              <VIcon
                size="18"
                icon="mdi-close"
              />
            </IconBtn>

            <div>
              <VImg
                width="140"
                :src="item.image"
              />
            </div>

            <div
              class="d-flex w-100"
              :class="$vuetify.display.width <= 700 ? 'flex-column' : 'flex-row'"
            >
              <div>
                <h6 class="text-base font-weight-regular mb-4">
                  {{ item.name }}
                </h6>
                <div class="d-flex align-center text-no-wrap gap-2 text-base">
                  <span class="text-disabled">Sold by:</span>
                  <span class="text-primary">{{ item.seller }}</span>
                  <VChip
                    :color="item.inStock ? 'success' : 'error'"
                    density="compact"
                  >
                    <span class="text-xs">
                      {{ item.inStock ? 'In Stock' : 'Out of Stock' }}
                    </span>
                  </VChip>
                </div>

                <div class="mt-1">
                  <VRating
                    density="compact"
                    :model-value="item.rating"
                    readonly
                  />
                </div>

                <VTextField
                  v-model="item.quantity"
                  type="number"
                  style="width: 7.5rem;"
                  density="compact"
                />
              </div>

              <VSpacer />

              <div
                class="d-flex flex-column justify-space-between mt-8"
                :class="$vuetify.display.width <= 700 ? 'text-start' : 'text-end'"
              >
                <p class="text-base">
                  <span class="text-primary">${{ item.price }}</span>
                  <span>/</span>
                  <span class="text-decoration-line-through">${{ item.discountPrice }}</span>
                </p>

                <div>
                  <VBtn
                    variant="outlined"
                    density="comfortable"
                  >
                    move to wishlist
                  </VBtn>
                </div>
              </div>
            </div>
          </div>
        </template>
      </div>

      <!-- 👉 Add more from wishlist -->
      <div class="d-flex align-center justify-space-between border rounded py-2 px-5 text-base mt-4">
        <a href="#">Add more products from wishlist</a>
        <VIcon
          icon="mdi-chevron-right"
          class="flip-in-rtl"
        />
      </div>
    </VCol>

    <VCol
      cols="12"
      md="4"
    >
      <VCard
        flat
        variant="outlined"
      >
        <!-- 👉 payment offer -->
        <VCardText class="pa-6">
          <h6 class="text-base font-weight-medium mb-4">
            Offer
          </h6>

          <div class="d-flex align-center gap-4">
            <VTextField
              v-model="checkoutCartDataLocal.promoCode"
              density="compact"
              placeholder="Enter Promo Code"
            />

            <VBtn
              variant="outlined"
              @click="updateCartData"
            >
              Apply
            </VBtn>
          </div>

          <!-- 👉 Gift wrap banner -->
          <div class="bg-var-theme-background rounded pa-5 mt-4">
            <h6 class="text-base font-weight-medium mb-2">
              Buying gift for a loved one?
            </h6>
            <p class="mb-2">
              Gift wrap and personalized message on card, Only for $2.
            </p>

            <a
              href="#"
              class="font-weight-medium"
            >Add a gift wrap</a>
          </div>
        </VCardText>

        <VDivider />

        <!-- 👉 Price details -->
        <VCardText class="pa-6">
          <h6 class="text-base font-weight-medium mb-4">
            Price Details
          </h6>

          <div class="text-high-emphasis">
            <div class="d-flex justify-space-between mb-2">
              <span>Bag Total</span>
              <span>${{ totalCost }}.00</span>
            </div>

            <div class="d-flex justify-space-between mb-2">
              <span>Coupon Discount</span>
              <a
                href="#"
                class="font-weight-medium"
              >Apply Coupon</a>
            </div>

            <div class="d-flex justify-space-between mb-2">
              <span>Order Total</span>
              <span>${{ totalCost }}.00</span>
            </div>

            <div class="d-flex justify-space-between">
              <span>Delivery Charges</span>

              <div>
                <span class="text-decoration-line-through text-disabled me-2">$5.00</span>
                <VChip
                  density="comfortable"
                  color="success"
                >
                  Free
                </VChip>
              </div>
            </div>
          </div>
        </VCardText>

        <VDivider />

        <VCardText class="d-flex justify-space-between py-4 px-6">
          <h6 class="text-base font-weight-medium">
            Total
          </h6>
          <h6 class="text-base font-weight-medium">
            ${{ totalCost }}.00
          </h6>
        </VCardText>
      </VCard>

      <VBtn
        block
        class="mt-4"
        @click="nextStep"
      >
        Place Order
      </VBtn>
    </VCol>
  </VRow>
</template>

<style lang="scss">
.checkout-item-remove-btn {
  position: absolute;
  inset-block-start: 10px;
  inset-inline-end: 10px;
}
</style>
