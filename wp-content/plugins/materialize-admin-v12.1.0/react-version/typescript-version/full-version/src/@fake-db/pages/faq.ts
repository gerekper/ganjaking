// ** Mock Adapter
import mock from 'src/@fake-db/mock'

// ** Types
import { FaqType } from 'src/@fake-db/types'

const data: { faqData: FaqType } = {
  faqData: {
    // payment
    payment: {
      id: 'payment',
      title: 'Payment',
      icon: 'mdi:credit-card-outline',
      subtitle: 'Get help with payment',
      qandA: [
        {
          id: 'order-payment',
          question: 'When is payment taken for my order?',
          answer:
            'Payment is taken during the checkout process when you pay for your order. The order number that appears on the confirmation screen indicates payment has been successfully processed.'
        },
        {
          id: 'order',
          question: 'How do I pay for my order?',
          answer:
            'We accept Visa®, MasterCard®, American Express®, and PayPal®. Our servers encrypt all information submitted to them, so you can be confident that your credit card information will be kept safe and secure.'
        },
        {
          id: 'placing-order',
          question: "What should I do if I'm having trouble placing an order?",
          answer:
            'For any technical difficulties you are experiencing with our website, please contact us at our support portal, or you can call us toll-free at 1-000-000-000, or email us at order@companymail.com'
        },
        {
          id: 'users-license',
          question: 'Which license do I need for an end product that is only accessible to paying users?',
          answer:
            'If you have paying users or you are developing any SaaS products then you need an Extended License. For each products, you need a license. You can get free lifetime updates as well.'
        },
        {
          id: 'subscription-review',
          question: 'Does my subscription automatically renew?',
          answer:
            'No, This is not subscription based item.Pastry pudding cookie toffee bonbon jujubes jujubes powder topping. Jelly beans gummi bears sweet roll bonbon muffin liquorice. Wafer lollipop sesame snaps.'
        }
      ]
    },

    // delivery
    delivery: {
      id: 'delivery',
      title: 'Delivery',
      icon: 'mdi:cart-outline',
      subtitle: 'Get help with delivery',
      qandA: [
        {
          id: 'ship-order',
          question: 'How would you ship my order?',
          answer:
            'For large products, we deliver your product via a third party logistics company offering you the “room of choice” scheduled delivery service. For small products, we offer free parcel delivery.'
        },
        {
          id: 'delivery-cost',
          question: 'What is the delivery cost of my order?',
          answer:
            'The cost of scheduled delivery is $69 or $99 per order, depending on the destination postal code. The parcel delivery is free.'
        },
        {
          id: 'product-damaged',
          question: 'What to do if my product arrives damaged?',
          answer:
            'We will promptly replace any product that is damaged in transit. Just contact our support team, to notify us of the situation within 48 hours of product arrival.'
        }
      ]
    },

    // cancellation and return
    cancellationReturn: {
      icon: 'mdi:rotate-right',
      id: 'cancellation-return',
      title: 'Cancellation & Return',
      subtitle: 'Get help with cancellation & return',
      qandA: [
        {
          id: 'cancel-order',
          question: 'Can I cancel my order?',
          answer:
            'Scheduled delivery orders can be cancelled 72 hours prior to your selected delivery date for full refund. Parcel delivery orders cannot be cancelled, however a free return label can be provided upon request.'
        },
        {
          id: 'product-return',
          question: 'Can I return my product?',
          answer:
            'You can return your product within 15 days of delivery, by contacting our support team, All merchandise returned must be in the original packaging with all original items.'
        },
        {
          id: 'return-status',
          question: 'Where can I view status of return?',
          answer: 'Locate the item from Your Orders. Select Return/Refund status'
        }
      ]
    },

    // my orders
    myOrders: {
      id: 'my-orders',
      title: 'My Orders',
      icon: 'mdi:archive-outline',
      subtitle: 'Order details',
      qandA: [
        {
          id: 'order-success',
          question: 'Has my order been successful?',
          answer:
            'All successful order transactions will receive an order confirmation email once the order has been processed. If you have not received your order confirmation email within 24 hours, check your junk email or spam folder. Alternatively, log in to your account to check your order summary. If you do not have a account, you can contact our Customer Care Team on 1-000-000-000.'
        },
        {
          id: 'promo-code',
          question: 'My Promotion Code is not working, what can I do?',
          answer: 'If you are having issues with a promotion code, please contact us at 1 000 000 000 for assistance.'
        },
        {
          id: 'track-orders',
          question: 'How do I track my Orders?',
          answer:
            'If you have an account just sign into your account from here and select “My Orders”. If you have a a guest account track your order from here using the order number and the email address.'
        }
      ]
    },

    // product and services
    productServices: {
      icon: 'mdi:camera-outline',
      id: 'product-services',
      title: 'Product & Services',
      subtitle: 'Get help with product & services',
      qandA: [
        {
          id: 'shipping-notification',
          question: 'Will I be notified once my order has shipped?',
          answer:
            'Yes, We will send you an email once your order has been shipped. This email will contain tracking and order information.'
        },
        {
          id: 'warranty-notification',
          question: 'Where can I find warranty information?',
          answer:
            'We are committed to quality products. For information on warranty period and warranty services, visit our Warranty section here.'
        },
        {
          id: 'warranty-coverage',
          question: 'How can I purchase additional warranty coverage?',
          answer:
            'For the peace of your mind, we offer extended warranty plans that add additional year(s) of protection to the standard manufacturer’s warranty provided by us. To purchase or find out more about the extended warranty program, visit Extended Warranty section here.'
        }
      ]
    }
  }
}

mock.onGet('/pages/faqs').reply(config => {
  if (config.params) {
    const { q = '' } = config.params
    const queryLowered = q.toLowerCase()

    const filteredData: FaqType = {}
    Object.entries(data.faqData).forEach(entry => {
      const [categoryName, categoryObj] = entry
      const filteredQAndAOfCategory = categoryObj.qandA.filter(qAndAObj => {
        return qAndAObj.question.toLowerCase().includes(queryLowered)
      })
      if (filteredQAndAOfCategory.length) {
        filteredData[categoryName] = {
          ...categoryObj,
          qandA: filteredQAndAOfCategory
        }
      }
    })

    return [200, { faqData: filteredData }]
  } else {
    return [200, data]
  }
})
