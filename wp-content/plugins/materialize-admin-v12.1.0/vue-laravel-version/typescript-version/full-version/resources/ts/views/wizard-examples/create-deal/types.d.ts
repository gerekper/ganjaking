export interface DealDetails {
  title: string
  code: string
  description: string
  offeredUItems: string[]
  cartCondition: string | null
  dealDuration: string
  notification: {
    email: boolean
    sms: boolean
    pushNotification: boolean
  }
}


export interface DealType {
  Offer: string
  discount: number | null
  region: string | null
}

export interface DealUsage {
  userType: string | null
  maxUsers: number | null
  cartAmount: number | null
  promotionFree: number | null
  paymentMethod: string | null
  dealStatus: string | null
  isSingleUserCustomer: boolean
}

export interface DealReviewComplete {
  isDealDetailsConfirmed: boolean
}

export interface CreateDealData {
  dealDetails: DealDetails
  dealType: DealType
  dealUsage:DealUsage
  dealReviewComplete:DealReviewComplete
}