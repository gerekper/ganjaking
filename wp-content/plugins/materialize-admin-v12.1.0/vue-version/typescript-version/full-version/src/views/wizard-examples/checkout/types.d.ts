export interface CartItem {
  id:number
  name: string
  seller: string
  inStock: boolean
  rating: number
  price: number
  discountPrice: number
  image: string
  quantity: number
  estimatedDelivery:  string
}
export interface Addresses {
  title: string
  desc: string
  subtitle: string
  value: string
}
  
export interface CheckoutData { 
  cartItems: CartItem[]
  promoCode: string
  orderAmount: number
  deliveryAddress: string
  deliverySpeed: string
  deliveryCharges: number
  addresses: Addresses[]
}
