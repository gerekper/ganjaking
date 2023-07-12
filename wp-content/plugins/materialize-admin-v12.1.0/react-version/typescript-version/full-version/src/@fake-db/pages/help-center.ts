// ** Mock Adapter
import mock from 'src/@fake-db/mock'

// ** ThemeConfig Import
import themeConfig from 'src/configs/themeConfig'

// ** Types
import {
  HelpCenterCategoriesType,
  HelpCenterArticlesOverviewType,
  HelpCenterSubcategoryArticlesType
} from 'src/@fake-db/types'

type Data = {
  categories: HelpCenterCategoriesType[]
  keepLearning: HelpCenterArticlesOverviewType[]
  popularArticles: HelpCenterArticlesOverviewType[]
}

const data: Data = {
  popularArticles: [
    {
      slug: 'getting-started',
      title: 'Getting Started',
      img: '/images/pages/rocket.png',
      subtitle: "Whether you're new or you're a power user, this article will help you get started with the App."
    },
    {
      slug: 'first-steps',
      title: 'First Steps',
      img: '/images/pages/gift.png',
      subtitle: 'Are you a new customer wondering how to get started?'
    },
    {
      slug: 'external-content',
      title: 'Add External Content',
      img: '/images/pages/external-content.png',
      subtitle: 'This article will show you how to expand the functionality of the App.'
    }
  ],
  categories: [
    {
      avatarColor: 'success',
      slug: 'getting-started',
      title: 'Getting Started',
      icon: 'mdi:rocket-launch-outline',
      subCategories: [
        {
          slug: 'account',
          title: 'Account',
          icon: 'mdi:cube-outline',
          articles: [
            {
              slug: 'changing-your-username',
              title: 'Changing your username?',
              content:
                "<p>You can change your username to another username that is not currently in use. If the username you want is not available, consider other names or unique variations. Using a number, hyphen, or an alternative spelling might help you find a similar username that's still available.</p> <p>After changing your username, your old username becomes available for anyone else to claim. Most references to your repositories under the old username automatically change to the new username. However, some links to your profile won't automatically redirect.</p><p>You can change your username to another username that is not currently in use. If the username you want is not available, consider other names or unique variations. Using a number, hyphen, or an alternative spelling might help you find a similar username that's still available.</p> <p>After changing your username, your old username becomes available for anyone else to claim. Most references to your repositories under the old username automatically change to the new username. However, some links to your profile won't automatically redirect.</p>"
            },
            {
              slug: 'changing-your-primary-email-address',
              title: 'Changing your primary email address?',
              content:
                '<p>You can change the email address associated with your personal account at any time from account settings.</p> <p><strong>Note:</strong> You cannot change your primary email address to an email that is already set to be your backup email address.</p><p>You can change the email address associated with your personal account at any time from account settings.</p> <p><strong>Note:</strong> You cannot change your primary email address to an email that is already set to be your backup email address.</p>'
            },
            {
              slug: 'changing-your-profile-picture',
              title: 'Changing your profile picture?',
              content:
                '<p>You can change your profile from account settings any time.</p> <p><strong>Note:</strong> Your profile picture should be a PNG, JPG, or GIF file, and it must be less than 1 MB in size and smaller than 3000 by 3000 pixels. For the best quality rendering, we recommend keeping the image at about 500 by 500 pixels.<p>You can change your profile from account settings any time.</p> <p><strong>Note:</strong> Your profile picture should be a PNG, JPG, or GIF file, and it must be less than 1 MB in size and smaller than 3000 by 3000 pixels. For the best quality rendering, we recommend keeping the image at about 500 by 500 pixels.'
            },
            {
              slug: 'setting-your-profile-to-private',
              title: 'Setting your profile to private?',
              content:
                '<p>A private profile displays only limited information, and hides some activity.</p> <p>To hide parts of your profile page, you can make your profile private. This also hides your activity in various social features on the website. A private profile hides information from all users, and there is currently no option to allow specified users to see your activity.</p> <p>You can change your profile to private in account settings.</p> <p>A private profile displays only limited information, and hides some activity.</p> <p>To hide parts of your profile page, you can make your profile private. This also hides your activity in various social features on the website. A private profile hides information from all users, and there is currently no option to allow specified users to see your activity.</p> <p>You can change your profile to private in account settings.</p> '
            },
            {
              slug: 'deleting-your-personal-account',
              title: 'Deleting your personal account?',
              content:
                '<p>Deleting your personal account removes data associated with your account.</p> <p>When you delete your account we stop billing you. The email address associated with the account becomes available for use with a different account on website. After 90 days, the account name also becomes available to anyone else to use on a new account.</p><p>Deleting your personal account removes data associated with your account.</p> <p>When you delete your account we stop billing you. The email address associated with the account becomes available for use with a different account on website. After 90 days, the account name also becomes available to anyone else to use on a new account.</p>'
            }
          ]
        },
        {
          slug: 'authentication',
          title: 'Authentication',
          icon: 'mdi:lock-outline',
          articles: [
            {
              slug: 'how-to-create-a-strong-password',
              title: 'How to create a strong password?',
              content:
                '<p>A strong password is a unique word or phrase a hacker cannot easily guess or crack.</p> <p>To keep your account secure, we recommend you to have a password with at least Eight characters, a number, a lowercase letter & an uppercase character.</p><p>A strong password is a unique word or phrase a hacker cannot easily guess or crack.</p> <p>To keep your account secure, we recommend you to have a password with at least Eight characters, a number, a lowercase letter & an uppercase character.</p>'
            },
            {
              slug: 'what-is-2FA',
              title: 'What is Two-Factor Authentication?',
              content:
                "<p>Two-factor authentication (2FA) is an extra layer of security used when logging into websites or apps. With 2FA, you have to log in with your username and password and provide another form of authentication that only you know or have access to.</p> <p>For our app, the second form of authentication is a code that's generated by an application on your mobile device or sent as a text message (SMS). After you enable 2FA, App generates an authentication code any time someone attempts to sign into your account. The only way someone can sign into your account is if they know both your password and have access to the authentication code on your phone.</p><p>Two-factor authentication (2FA) is an extra layer of security used when logging into websites or apps. With 2FA, you have to log in with your username and password and provide another form of authentication that only you know or have access to.</p> <p>For our app, the second form of authentication is a code that's generated by an application on your mobile device or sent as a text message (SMS). After you enable 2FA, App generates an authentication code any time someone attempts to sign into your account. The only way someone can sign into your account is if they know both your password and have access to the authentication code on your phone.</p>"
            },
            {
              slug: 'how-to-recover-account-if-you-lose-your-2fa-credentials',
              title: 'How to recover account if you lose your 2fa credentials?',
              content:
                '<p>If you lose access to your two-factor authentication credentials, you can use your recovery codes, or another recovery option, to regain access to your account.</p> <p><strong>Warning:</strong> For security reasons, Our Support may not be able to restore access to accounts with two-factor authentication enabled if you lose your two-factor authentication credentials or lose access to your account recovery methods.</p><p>If you lose access to your two-factor authentication credentials, you can use your recovery codes, or another recovery option, to regain access to your account.</p> <p><strong>Warning:</strong> For security reasons, Our Support may not be able to restore access to accounts with two-factor authentication enabled if you lose your two-factor authentication credentials or lose access to your account recovery methods.</p>'
            },
            {
              slug: 'how-to-review-security-logs',
              title: 'How to review security logs?',
              content:
                "<p>You can review the security log for your personal account to better understand actions you've performed and actions others have performed that involve you.</p> <p>You can refer your security log from the settings.</p><p>You can review the security log for your personal account to better understand actions you've performed and actions others have performed that involve you.</p> <p>You can refer your security log from the settings.</p>"
            }
          ]
        },
        {
          slug: 'billing',
          title: 'Billing',
          icon: 'mdi:currency-usd',
          articles: [
            {
              slug: 'how-to-update-payment-method',
              title: 'How to update payment method?',
              content:
                "<p>You can add a payment method to your account or update your account's existing payment method at any time.</p> <p>You can pay with a credit card or with a PayPal account. When you update your payment method for your account's subscription, your new payment method is automatically added to your other subscriptions for paid products.</p><p>You can add a payment method to your account or update your account's existing payment method at any time.</p> <p>You can pay with a credit card or with a PayPal account. When you update your payment method for your account's subscription, your new payment method is automatically added to your other subscriptions for paid products.</p>"
            },
            {
              slug: 'how-to-check-billing-date',
              title: 'How to check billing date?',
              content:
                "<p>You can view your account's subscription, your other paid features and products, and your next billing date in your account's billing settings.</p><p>You can view your account's subscription, your other paid features and products, and your next billing date in your account's billing settings.</p>"
            },
            {
              slug: 'how-to-change-billing-cycle',
              title: 'How to change billing cycle?',
              content:
                "<p>You can change your billing cycle from the account settings billing section.</p> <p>When you change your billing cycle's duration, your GitHub subscription, along with any other paid features and products, will be moved to your new billing cycle on your next billing date.</p><p>You can change your billing cycle from the account settings billing section.</p> <p>When you change your billing cycle's duration, your GitHub subscription, along with any other paid features and products, will be moved to your new billing cycle on your next billing date.</p>"
            },
            {
              slug: 'where-can-i-view-and-download-payment-receipt',
              title: 'Where can i view and download payment receipt?',
              content:
                "<p>You can view your payment from the account settings billing section.</p> <p>You'll also a have a option to download or share your payment receipt from the billing section.</p><p>You can view your payment from the account settings billing section.</p> <p>You'll also a have a option to download or share your payment receipt from the billing section.</p>"
            },
            {
              slug: 'how-to-set-billing-email',
              title: 'How to set billing email?',
              content:
                "<p>Your personal account's primary email is where we send receipts and other billing-related communication.</p> <p>Your primary email address is the first email listed in your account email settings. We also use your primary email address as our billing email address.</p> <p>If you'd like to change your billing email you can do it from account settings.</p><p>Your personal account's primary email is where we send receipts and other billing-related communication.</p> <p>Your primary email address is the first email listed in your account email settings. We also use your primary email address as our billing email address.</p> <p>If you'd like to change your billing email you can do it from account settings.</p>"
            }
          ]
        }
      ]
    },
    {
      slug: 'orders',
      title: 'Orders',
      avatarColor: 'info',
      icon: 'mdi:archive-outline',
      subCategories: [
        {
          slug: 'processing-orders',
          title: 'Processing orders',
          icon: 'mdi:archive-outline',
          articles: [
            {
              slug: 'what-happens-when-you-receive-an-online-order',
              title: 'What happens when you receive an online order?',
              content:
                "<p>When you receive an online order, you'll receive a new order notification by email.</p> <p>You'll be able to see that order on the orders page.</p><p>When you receive an online order, you'll receive a new order notification by email.</p> <p>You'll be able to see that order on the orders page.</p>"
            },
            {
              slug: 'what-happens-when-you-process-an-order',
              title: 'What happens when you process an order?',
              content:
                '<p>When you process an order, The Orders page will show the order with a payment status of Paid or Partially paid.</p> <p>If the customer provided their email address, then they receive a receipt by email.</p><p>When you process an order, The Orders page will show the order with a payment status of Paid or Partially paid.</p> <p>If the customer provided their email address, then they receive a receipt by email.</p>'
            },
            {
              slug: 'how-to-cancel-an-order',
              title: 'How to cancel an order?',
              content:
                "<p>Canceling an order indicates that you are halting order processing. For example, if a customer requests a cancellation or you suspect the order is fraudulent, then you can cancel the order to help prevent staff or fulfillment services from continuing work on the order. You can also cancel an order if an item was ordered and isn't available.</p> <p>You can cancel an order by clicking the cancel button on orders page.</p><p>Canceling an order indicates that you are halting order processing. For example, if a customer requests a cancellation or you suspect the order is fraudulent, then you can cancel the order to help prevent staff or fulfillment services from continuing work on the order. You can also cancel an order if an item was ordered and isn't available.</p> <p>You can cancel an order by clicking the cancel button on orders page.</p>"
            },
            {
              slug: 'whats-the-status-of-my-order',
              title: 'What’s the Status of My Order?',
              content:
                '<p>You can check the shipping status of your order on website or the app. If the seller added a tracking number, you can use that to get detailed information about the package’s movement through the shipping carrier.</p><p>You’ll see the shipping status on the orders page. You’ll also see an estimated delivery date which should give you an idea of when you can expect the order to arrive, and a tracking number if it’s available for your order.</p><p>You can check the shipping status of your order on website or the app. If the seller added a tracking number, you can use that to get detailed information about the package’s movement through the shipping carrier.</p><p>You’ll see the shipping status on the orders page. You’ll also see an estimated delivery date which should give you an idea of when you can expect the order to arrive, and a tracking number if it’s available for your order.</p>'
            },
            {
              slug: 'how-to-return-or-exchange-an-item',
              title: 'How to Return or Exchange an Item?',
              content:
                "<p>If you need to return or exchange an item, the seller you purchased your order from is the best person to help you. Each seller manages their own orders, and makes decisions about cancellations, refunds, and returns.</p><p>Sellers aren’t required to accept returns, exchanges, or provide a refund unless stated in their shop policies. Go to the shop's homepage and scroll to the bottom to see the shop's policies.</p><p>If you need to return or exchange an item, the seller you purchased your order from is the best person to help you. Each seller manages their own orders, and makes decisions about cancellations, refunds, and returns.</p><p>Sellers aren’t required to accept returns, exchanges, or provide a refund unless stated in their shop policies. Go to the shop's homepage and scroll to the bottom to see the shop's policies.</p>"
            }
          ]
        },
        {
          slug: 'payments',
          title: 'Payments',
          icon: 'mdi:currency-usd',
          articles: [
            {
              slug: 'how-do-i-get-paid',
              title: 'How do i get paid?',
              content:
                '<p>When you set up a payment provider to accept credit card payments, each payment must be processed, so there is usually a delay between when the customer pays for their order and when you receive the payment. After the payment is processed, the purchase amount will be transferred to your merchant account.</p><p>When you set up a payment provider to accept credit card payments, each payment must be processed, so there is usually a delay between when the customer pays for their order and when you receive the payment. After the payment is processed, the purchase amount will be transferred to your merchant account.</p>'
            },
            {
              slug: 'how-often-do-i-get-paid',
              title: 'How often do I get paid?',
              content:
                '<p>If you use our payment system, then you can check your pay period to see when you receive payouts from credit card orders. Other payment providers have their own rules on when you receive payouts for credit card orders. Check with your provider to find out how often you will be paid.</p> <p>After the payout is sent, it might not be received by your bank right away. It can take a few days after the payout is sent for it to be deposited into your bank account. Check with your bank if you find your payouts are being delayed.</p><p>If you use our payment system, then you can check your pay period to see when you receive payouts from credit card orders. Other payment providers have their own rules on when you receive payouts for credit card orders. Check with your provider to find out how often you will be paid.</p> <p>After the payout is sent, it might not be received by your bank right away. It can take a few days after the payout is sent for it to be deposited into your bank account. Check with your bank if you find your payouts are being delayed.</p>'
            },
            {
              slug: 'how-much-do-i-get-paid',
              title: 'How much do I get paid?',
              content:
                "<p>You can be charged several third-party transaction fees for online transactions. For credit card transactions, the issuer, the acquirer, and the credit card company all charge a small fee for using their services.</p><p>You aren't charged third-party transaction fees for orders processed through our payment system. You pay credit card processing fees, depending on your subscription plan. If you're using a third-party payment provider with us, then you're charged a third-party transaction fee.</p><p>You can be charged several third-party transaction fees for online transactions. For credit card transactions, the issuer, the acquirer, and the credit card company all charge a small fee for using their services.</p><p>You aren't charged third-party transaction fees for orders processed through our payment system. You pay credit card processing fees, depending on your subscription plan. If you're using a third-party payment provider with us, then you're charged a third-party transaction fee.</p>"
            },
            {
              slug: 'cant-complete-payment-on-paypal',
              title: "Can't Complete Payment on PayPal?",
              content:
                "<p>PayPal uses various security measures to protect their users. Because of this, PayPal may occasionally prohibit a buyer from submitting payment to a seller through PayPal.</p><p>If you're ultimately unable to submit payment, try working with the seller to determine an alternative payment method. Learn how to contact a seller.</p><p>PayPal uses various security measures to protect their users. Because of this, PayPal may occasionally prohibit a buyer from submitting payment to a seller through PayPal.</p><p>If you're ultimately unable to submit payment, try working with the seller to determine an alternative payment method. Learn how to contact a seller.</p>"
            },
            {
              slug: 'why-is-my-order-is-still-processing',
              title: 'Why is my order is still processing?',
              content:
                '<p>If you received an email saying that your order is still processing, it means that your purchase is being screened by our third-party partner. All Payments orders are screened to ensure that the orders are legitimate and to protect from possible fraud.</p><p>Most orders are processed in under 72 hours. You’ll receive a confirmation email when the review is complete.</p><p>If you received an email saying that your order is still processing, it means that your purchase is being screened by our third-party partner. All Payments orders are screened to ensure that the orders are legitimate and to protect from possible fraud.</p><p>Most orders are processed in under 72 hours. You’ll receive a confirmation email when the review is complete.</p>'
            }
          ]
        },
        {
          icon: 'mdi:reload',
          slug: 'returns-refunds-replacements',
          title: 'Returns, Refunds and Replacements',
          articles: [
            {
              slug: 'what-can-i-return',
              title: 'What can I return?',
              content:
                '<p>You may request returns for most items you buy from the sellers listed on the website. However, you can only return items explicitly identified as "returnable" on the product detail page and/or our policy and within the ‘return window’ period.</p> <p> Please refer to the website Returns policy. to know which categories are "non-returnable" and the specific return windows for categories eligible for return.</p><ul><li>Physically damaged</li><li>Has missing parts or accessories</li><li>Defective</li><li>Different from its description on the product detail page on the website</li></ul><p>You may request returns for most items you buy from the sellers listed on the website. However, you can only return items explicitly identified as "returnable" on the product detail page and/or our policy and within the ‘return window’ period.</p> <p> Please refer to the website Returns policy. to know which categories are "non-returnable" and the specific return windows for categories eligible for return.</p><ul><li>Physically damaged</li><li>Has missing parts or accessories</li><li>Defective</li><li>Different from its description on the product detail page on the website</li></ul>'
            },
            {
              slug: 'when-will-i-get-my-refund',
              title: 'When will I get my refund?',
              content:
                '<p>Following are the refund processing timelines after the item is received by Amazon or the Seller notifies us of the receipt of the return:</p><ul><li><strong>Wallet:</strong> 2 hours</li><li><strong>Credit/Debit Card:</strong> 2-4 Business Days</li><li><strong>Bank Account:</strong> 2-4 Business Days</li></ul><p>Following are the refund processing timelines after the item is received by Amazon or the Seller notifies us of the receipt of the return:</p><ul><li><strong>Wallet:</strong> 2 hours</li><li><strong>Credit/Debit Card:</strong> 2-4 Business Days</li><li><strong>Bank Account:</strong> 2-4 Business Days</li></ul>'
            },
            {
              slug: 'can-my-order-be-replaced',
              title: 'Can my order be replaced?',
              content:
                '<p>If the item you ordered arrived in a physically damaged/ defective condition or is different from their description on the product detail page, or has missing parts or accessories, it will be eligible for a free replacement as long as the exact item is available with the same seller.</p><p>If the item you ordered arrived in a physically damaged/ defective condition or is different from their description on the product detail page, or has missing parts or accessories, it will be eligible for a free replacement as long as the exact item is available with the same seller.</p>'
            }
          ]
        }
      ]
    },
    {
      slug: 'safety-security',
      avatarColor: 'primary',
      title: 'Safety and security',
      icon: 'mdi:account-multiple-outline',
      subCategories: [
        {
          slug: 'hacked-accounts',
          title: 'Security and hacked accounts',
          icon: 'mdi:security',
          articles: [
            {
              slug: 'has-my-account-been-compromised',
              title: 'Has my account been compromised?',
              content:
                "<p>Have you:</p><ul><li>Noticed unexpected posts by your account</li><li>Seen unintended Direct Messages sent from your account</li><li>Observed other account behaviors you didn't make or approve (like following, unfollowing, or blocking)</li></ul>. <p>If you've answered yes to any of the above, please change your password and Revoke connections to third-party applications</p><p>Have you:</p><ul><li>Noticed unexpected posts by your account</li><li>Seen unintended Direct Messages sent from your account</li><li>Observed other account behaviors you didn't make or approve (like following, unfollowing, or blocking)</li></ul>. <p>If you've answered yes to any of the above, please change your password and Revoke connections to third-party applications</p>"
            },
            {
              slug: 'how-to-keep-my-account-safe',
              title: 'How to keep my account safe?',
              content:
                '<p>To help keep your account secure, we recommend the following best practices:</p><ul><li>Use a strong password that you don’t reuse on other websites.</li><li>Use two-factor authentication.</li><li>Require email and phone number to request a reset password link or code.</li><li>Be cautious of suspicious links and always make sure you’re on our website before you enter your login information.</li><li>Never give your username and password out to third parties, especially those promising to get you followers, make you money, or verify you.</li></ul><p>To help keep your account secure, we recommend the following best practices:</p><ul><li>Use a strong password that you don’t reuse on other websites.</li><li>Use two-factor authentication.</li><li>Require email and phone number to request a reset password link or code.</li><li>Be cautious of suspicious links and always make sure you’re on our website before you enter your login information.</li><li>Never give your username and password out to third parties, especially those promising to get you followers, make you money, or verify you.</li></ul>'
            },
            {
              slug: 'help-with-my-hacked-account',
              title: 'Help with my hacked account',
              content:
                "<p>If you think you've been hacked and you're unable to log in with your username and password, please take the following two steps:</p><ol><li><p>Request a password reset</p> <p>Reset your password by requesting an email from the password reset form. Try entering both your username and email address, and be sure to check for the reset email at the address associated with your account.</p></li><li><p>Contact Support if you still require assistance</p><p>If you still can't log in, contact us by submitting a Support Request. Be sure to use the email address you associated with the hacked account; we'll then send additional information and instructions to that email address. When submitting your support request please Include both your username and the date you last had access to your account.</p></li></ol><p>If you think you've been hacked and you're unable to log in with your username and password, please take the following two steps:</p><ol><li><p>Request a password reset</p> <p>Reset your password by requesting an email from the password reset form. Try entering both your username and email address, and be sure to check for the reset email at the address associated with your account.</p></li><li><p>Contact Support if you still require assistance</p><p>If you still can't log in, contact us by submitting a Support Request. Be sure to use the email address you associated with the hacked account; we'll then send additional information and instructions to that email address. When submitting your support request please Include both your username and the date you last had access to your account.</p></li></ol>"
            }
          ]
        },
        {
          slug: 'privacy',
          title: 'Privacy',
          icon: 'mdi:lock-outline',
          articles: [
            {
              slug: 'what-is-visible-on-my-profile',
              title: 'What is visible on my profile?',
              content:
                '<p>Most of the profile information you provide us is always public, like your biography, location, website, and picture. For certain profile information fields we provide you with visibility settings to select who can see this information in your profile.</p><p>If you provide us with profile information and you don’t see a visibility setting, that information is public.</p><p>Most of the profile information you provide us is always public, like your biography, location, website, and picture. For certain profile information fields we provide you with visibility settings to select who can see this information in your profile.</p><p>If you provide us with profile information and you don’t see a visibility setting, that information is public.</p>'
            },
            {
              slug: 'should-i-turn-on-precise-location',
              title: 'Should I turn on precise location?',
              content:
                '<p>Enabling precise location through our official app allows us to collect, store, and use your precise location, such as GPS information. This allows us to provide, develop, and improve a variety of our services, including but not limited to:</p><ul><li>Delivery of content, including posts and advertising, that is better tailored to your location.</li><li>Delivery of location-specific trends.</li><li>Showing your followers the location you are posting from as part of your post, if you decide to geo-tag your post.</li></ul><p>Enabling precise location through our official app allows us to collect, store, and use your precise location, such as GPS information. This allows us to provide, develop, and improve a variety of our services, including but not limited to:</p><ul><li>Delivery of content, including posts and advertising, that is better tailored to your location.</li><li>Delivery of location-specific trends.</li><li>Showing your followers the location you are posting from as part of your post, if you decide to geo-tag your post.</li></ul>'
            },
            {
              slug: 'what-location-information-is-displayed',
              title: 'What location information is displayed?',
              content:
                "<ul><li>All geolocation information begins as a location (latitude and longitude), sent from your browser or device. We won't show any location information unless you've opted in to the feature, and have allowed your device or browser to transmit your coordinates to us.</li><li>If you have chosen to attach location information to your Posts, your selected location label is displayed underneath the text of the Post.</li><li>When you use the in-app camera on iOS and Android to attach a photo or video to your post and toggle on the option to tag your precise location, that post will include both the location label of your choice and your device's precise location (latitude and longitude), which can be found via API. Your precise location may be more specific than the location label you select. This is helpful when sharing on-the-ground moments.</li></ul><ul><li>All geolocation information begins as a location (latitude and longitude), sent from your browser or device. We won't show any location information unless you've opted in to the feature, and have allowed your device or browser to transmit your coordinates to us.</li><li>If you have chosen to attach location information to your Posts, your selected location label is displayed underneath the text of the Post.</li><li>When you use the in-app camera on iOS and Android to attach a photo or video to your post and toggle on the option to tag your precise location, that post will include both the location label of your choice and your device's precise location (latitude and longitude), which can be found via API. Your precise location may be more specific than the location label you select. This is helpful when sharing on-the-ground moments.</li></ul>"
            }
          ]
        },
        {
          slug: 'spam-fake-accounts',
          title: 'Spam and fake accounts',
          icon: 'mdi:email-outline',
          articles: [
            {
              slug: 'how-to-detect-fake-email',
              title: 'How to detect fake email?',
              content: `<p>We will only send you emails from @${themeConfig.templateName}.com or @t.${themeConfig.templateName}.com. However, some people may receive fake or suspicious emails that look like they were sent by US. These emails might include malicious attachments or links to spam or phishing websites. Please know that we will never send emails with attachments or request your password by email.</p><p>We will only send you emails from @${themeConfig.templateName}.com or @t.${themeConfig.templateName}.com. However, some people may receive fake or suspicious emails that look like they were sent by US. These emails might include malicious attachments or links to spam or phishing websites. Please know that we will never send emails with attachments or request your password by email.</p>`
            },
            {
              slug: 'how-do-I-report-an-impersonation-violation',
              title: 'How do I report an impersonation violation?',
              content:
                '<p>If you believe an account is posing as you or your brand, you or your authorized representative can file a report in our support Center.</p><p>If you believe an account is misusing the identity of somebody else, you can flag it as a bystander by reporting directly from the account’s profile.</p><p>If you believe an account is posing as you or your brand, you or your authorized representative can file a report in our support Center.</p><p>If you believe an account is misusing the identity of somebody else, you can flag it as a bystander by reporting directly from the account’s profile.</p>'
            },
            {
              slug: 'someone-is-using-my-email-address-what-can-i-do',
              title: 'Someone is using my email address, what can I do?',
              content:
                "<p>Are you trying to create a new account, but you're told your email address or phone number is already in use? This support article outlines how your email address may already be in use and how to resolve the issue.</p><p>Are you trying to create a new account, but you're told your email address or phone number is already in use? This support article outlines how your email address may already be in use and how to resolve the issue.</p>"
            }
          ]
        }
      ]
    },
    {
      slug: 'rules-policies',
      title: 'Rules and policies',
      avatarColor: 'error',
      icon: 'mdi:clipboard-text-outline',
      subCategories: [
        {
          slug: 'general',
          title: 'General',
          icon: 'mdi:web',
          articles: [
            {
              slug: 'our-rules',
              title: 'Our Rules',
              content:
                '<p>Our purpose is to serve the public conversation. Violence, harassment and other similar types of behavior discourage people from expressing themselves, and ultimately diminish the value of global public conversation. Our rules are to ensure all people can participate in the public conversation freely and safely.</p><p>Our purpose is to serve the public conversation. Violence, harassment and other similar types of behavior discourage people from expressing themselves, and ultimately diminish the value of global public conversation. Our rules are to ensure all people can participate in the public conversation freely and safely.</p>'
            },
            {
              slug: 'what-is-username-squatting-policy',
              title: 'What is username squatting policy?',
              content:
                "<p>Username squatting is prohibited by the Rules.</p><p>Please note that if an account has had no updates, no profile image, and there is no intent to mislead, it typically means there's no name-squatting or impersonation. Note that we will not release squatted usernames except in cases of trademark infringement. If your report involves trademark infringement, please consult those policies for instructions for reporting these accounts.</p><p>Attempts to sell, buy, or solicit other forms of payment in exchange for usernames are also violations and may result in permanent account suspension.</p><p>Username squatting is prohibited by the Rules.</p><p>Please note that if an account has had no updates, no profile image, and there is no intent to mislead, it typically means there's no name-squatting or impersonation. Note that we will not release squatted usernames except in cases of trademark infringement. If your report involves trademark infringement, please consult those policies for instructions for reporting these accounts.</p><p>Attempts to sell, buy, or solicit other forms of payment in exchange for usernames are also violations and may result in permanent account suspension.</p>"
            },
            {
              slug: 'third-party-advertising-in-video-content',
              title: 'Third-party advertising in video content',
              content:
                '<p>You may not submit, post, or display any video content on or through our services that includes third-party advertising, such as pre-roll video ads or sponsorship graphics, without our prior consent.</p><p><strong>Note:</strong> we may need to change these rules from time to time in order to support our goal of promoting a healthy public conversation</p><p>You may not submit, post, or display any video content on or through our services that includes third-party advertising, such as pre-roll video ads or sponsorship graphics, without our prior consent.</p><p><strong>Note:</strong> we may need to change these rules from time to time in order to support our goal of promoting a healthy public conversation</p>'
            }
          ]
        },
        {
          slug: 'intellectual-property',
          title: 'Intellectual property',
          icon: 'mdi:registered-trademark',
          articles: [
            {
              slug: 'what-is-your-trademark-policy',
              title: 'What is your trademark policy?',
              content:
                '<p><strong>You may not violate others’ intellectual property rights, including copyright and trademark.</strong></p><p>A trademark is a word, logo, phrase, or device that distinguishes a trademark holder’s good or service in the marketplace. Trademark law may prevent others from using a trademark in an unauthorized or confusing manner.</p><p><strong>You may not violate others’ intellectual property rights, including copyright and trademark.</strong></p><p>A trademark is a word, logo, phrase, or device that distinguishes a trademark holder’s good or service in the marketplace. Trademark law may prevent others from using a trademark in an unauthorized or confusing manner.</p>'
            },
            {
              slug: 'what-are-counterfeit-goods',
              title: 'What are counterfeit goods?',
              content:
                '<p>Counterfeit goods are goods, including digital goods, that are promoted, sold, or otherwise distributed using a trademark or brand that is identical to, or substantially indistinguishable from, the registered trademark or brand of another, without authorization from the trademark or brand owner. Counterfeit goods attempt to deceive consumers into believing the counterfeit is a genuine product of the brand owner, or to represent themselves as faux, replicas or imitations of the genuine product.</p><p>Counterfeit goods are goods, including digital goods, that are promoted, sold, or otherwise distributed using a trademark or brand that is identical to, or substantially indistinguishable from, the registered trademark or brand of another, without authorization from the trademark or brand owner. Counterfeit goods attempt to deceive consumers into believing the counterfeit is a genuine product of the brand owner, or to represent themselves as faux, replicas or imitations of the genuine product.</p>'
            },
            {
              slug: 'what-types-of-copyright-complaints-do-you-respond-to',
              title: 'What types of copyright complaints do you respond to?',
              content:
                '<p>We respond to copyright complaints submitted under the Digital Millennium Copyright Act (“DMCA”). Section 512 of the DMCA outlines the statutory requirements necessary for formally reporting copyright infringement, as well as providing instructions on how an affected party can appeal a removal by submitting a compliant counter-notice.</p><p>If you are concerned about the use of your brand or entity’s name, please review our trademark policy. If you are concerned about a parody, newsfeed, commentary, or fan account, please see the relevant policy here. These are generally not copyright issues.</p><p>We respond to copyright complaints submitted under the Digital Millennium Copyright Act (“DMCA”). Section 512 of the DMCA outlines the statutory requirements necessary for formally reporting copyright infringement, as well as providing instructions on how an affected party can appeal a removal by submitting a compliant counter-notice.</p><p>If you are concerned about the use of your brand or entity’s name, please review our trademark policy. If you are concerned about a parody, newsfeed, commentary, or fan account, please see the relevant policy here. These are generally not copyright issues.</p>'
            }
          ]
        },
        {
          slug: 'guidelines-for-law-enforcement',
          title: 'Guidelines for law enforcement',
          icon: 'mdi:clipboard-text-outline',
          articles: [
            {
              slug: 'does-we-have-access-to-user-generated-photos-or-videos',
              title: 'Does we have access to user-generated photos or videos?',
              content: `<p>We provide photo hosting for some image uploads (i.e., pic.${themeConfig.templateName}.com images) as well as account profile photos, and header photos. However, We are not the sole photo provider for images that may appear on the platform. More information about posting photos on platform.</p><p>We provide photo hosting for some image uploads (i.e., pic.${themeConfig.templateName}.com images) as well as account profile photos, and header photos. However, We are not the sole photo provider for images that may appear on the platform. More information about posting photos on platform.</p>`
            },
            {
              slug: 'data-controller',
              title: 'Data Controller',
              content:
                '<p>For people who live in the United States or any other country outside of the European Union or the European Economic Area, the data controller responsible for personal data, Inc. based in San Francisco, California. For people who live in the European Union or the European Economic Area, the data controller is our International Unlimited Company based in Dublin, Ireland.</p><p>For people who live in the United States or any other country outside of the European Union or the European Economic Area, the data controller responsible for personal data, Inc. based in San Francisco, California. For people who live in the European Union or the European Economic Area, the data controller is our International Unlimited Company based in Dublin, Ireland.</p>'
            },
            {
              slug: 'requests-for-Twitter-account-information',
              title: 'Requests for Twitter account information',
              content:
                '<p>Requests for user account information from law enforcement should be directed to us, Inc. in San Francisco, California or Twitter International Unlimited Company in Dublin, Ireland. We respond to valid legal process issued in compliance with applicable law.</p><p>Requests for user account information from law enforcement should be directed to us, Inc. in San Francisco, California or Twitter International Unlimited Company in Dublin, Ireland. We respond to valid legal process issued in compliance with applicable law.</p>'
            }
          ]
        }
      ]
    },
    {
      slug: 'chats',
      title: 'Chats',
      avatarColor: 'warning',
      icon: 'mdi:message-outline',
      subCategories: [
        {
          slug: 'general',
          title: 'General',
          icon: 'mdi:web',
          articles: [
            {
              slug: 'what-is-forwarding-limits',
              title: 'What is forwarding limit?',
              content:
                '<p>You can forward a message with up to five chats at one time. If a message has already been forwarded, you can forward it to up to five chats, including a maximum of one group.</p><p>However, when a message is forwarded through a chain of five or more chats, meaning it’s at least five forwards away from its original sender, a double arrow icon  and "Forwarded many times" label will be displayed. These messages can only be forwarded to one chat at a time, as a way to help keep conversations on platform intimate and personal. This also helps slow down the spread of rumors, viral messages, and fake news.</p><p>You can forward a message with up to five chats at one time. If a message has already been forwarded, you can forward it to up to five chats, including a maximum of one group.</p><p>However, when a message is forwarded through a chain of five or more chats, meaning it’s at least five forwards away from its original sender, a double arrow icon  and "Forwarded many times" label will be displayed. These messages can only be forwarded to one chat at a time, as a way to help keep conversations on platform intimate and personal. This also helps slow down the spread of rumors, viral messages, and fake news.</p>'
            },
            {
              slug: 'what-is-last-seen-and-online',
              title: 'What is last seen & online?',
              content:
                "<p>Last seen and online tell you the last time your contacts used the app, or if they're online.</p><p>If a contact is online, they have th app open in the foreground on their device and are connected to the Internet. However, it doesn't necessarily mean the contact has read your message.</p><p>Last seen and online tell you the last time your contacts used the app, or if they're online.</p><p>If a contact is online, they have th app open in the foreground on their device and are connected to the Internet. However, it doesn't necessarily mean the contact has read your message.</p>"
            },
            {
              slug: 'how-to-reply-to-a-message',
              title: 'How to reply to a message?',
              content:
                '<p>You can use the reply feature when responding to a specific message in an individual or group chat.</p><p>Tap and hold the message, then tap Reply. Enter your response and tap Send. Alternatively, swipe right on the message to reply.</p><p>You can use the reply feature when responding to a specific message in an individual or group chat.</p><p>Tap and hold the message, then tap Reply. Enter your response and tap Send. Alternatively, swipe right on the message to reply.</p>'
            }
          ]
        },
        {
          slug: 'features',
          title: 'Features',
          icon: 'mdi:star-circle-outline',
          articles: [
            {
              slug: 'how-to-send-disappearing-messages',
              title: 'How to send disappearing messages?',
              content:
                '<p>Disappearing messages is an optional feature you can turn on for more privacy.</p><p>When you enable disappearing messages, you can set messages to disappear 24 hours, 7 days, or 90 days after the time they are sent. The most recent selection only controls new messages in the chat. You can choose to turn disappearing messages on for all of your chats, or select specific chats. This setting won’t affect messages you previously sent or received in the chat. In an individual chat, either user can turn disappearing messages on or off. In a group chat, any group participants can turn disappearing messages on or off. However, a group admin can change group settings to allow only admins to turn disappearing messages on or off.</p><p>Disappearing messages is an optional feature you can turn on for more privacy.</p><p>When you enable disappearing messages, you can set messages to disappear 24 hours, 7 days, or 90 days after the time they are sent. The most recent selection only controls new messages in the chat. You can choose to turn disappearing messages on for all of your chats, or select specific chats. This setting won’t affect messages you previously sent or received in the chat. In an individual chat, either user can turn disappearing messages on or off. In a group chat, any group participants can turn disappearing messages on or off. However, a group admin can change group settings to allow only admins to turn disappearing messages on or off.</p>'
            },
            {
              slug: 'can-i-send-view-once-messages',
              title: 'Can I send view once messages?',
              content:
                '<p>For added privacy, you can now send photos and videos that disappear from your chat after the recipient has opened them once. To use view once, please update the app to the latest version available for your device.</p><p>For added privacy, you can now send photos and videos that disappear from your chat after the recipient has opened them once. To use view once, please update the app to the latest version available for your device.</p>'
            },
            {
              slug: 'how-to-pin-a-chat',
              title: 'How to pin a chat?',
              content:
                '<p>The pin chat feature allows you to pin up to three specific chats to the top of your chats list so you can quickly find them.</p><p>On <strong>iPhone</strong>: Swipe right on the chat you want to pin, then tap Pin.</p><p>On <strong>Android</strong>: Tap and hold the chat you want to pin, then tap Pin chat</p><p>The pin chat feature allows you to pin up to three specific chats to the top of your chats list so you can quickly find them.</p><p>On <strong>iPhone</strong>: Swipe right on the chat you want to pin, then tap Pin.</p><p>On <strong>Android</strong>: Tap and hold the chat you want to pin, then tap Pin chat</p>'
            }
          ]
        },
        {
          slug: 'encryption',
          title: 'Encryption',
          icon: 'mdi:lock-outline',
          articles: [
            {
              slug: 'what-is-end-to-end-encrypted-backup',
              title: 'What is end-to-end encrypted backup?',
              content:
                "<p>End-to-end encryption ensures only you and the person you're communicating with can read or listen to what is sent, and nobody in between, not even us. With end-to-end encrypted backup, you can also add that same layer of protection to your backup on iCloud or Google Drive.</p><p>End-to-end encryption ensures only you and the person you're communicating with can read or listen to what is sent, and nobody in between, not even us. With end-to-end encrypted backup, you can also add that same layer of protection to your backup on iCloud or Google Drive.</p>"
            },
            {
              slug: 'can-i-change-password-for-end-to-end-encrypted-backup',
              title: 'Can I change password for end-to-end encrypted backup?',
              content:
                '<p>When you create an end-to-end encrypted backup, your messages and media are stored in the cloud and secured by a password or a 64-digit encryption key. Your password can be changed at any time as long as you have access to your previous password or key.</p><p><strong>Note:</strong> You won’t be able to restore your backup if you lose your chats and forget your password or key. We can’t reset your password or restore your backup for you.</p><p>When you create an end-to-end encrypted backup, your messages and media are stored in the cloud and secured by a password or a 64-digit encryption key. Your password can be changed at any time as long as you have access to your previous password or key.</p><p><strong>Note:</strong> You won’t be able to restore your backup if you lose your chats and forget your password or key. We can’t reset your password or restore your backup for you.</p>'
            },
            {
              slug: 'can-i-turnoff-end-to-end-encrypted-backup',
              title: 'Can I turnoff end-to-end encrypted backup?',
              content:
                '<p>You can choose to turn off end-to-end encrypted backup by using your password or key, or by authenticating with your biometrics or device PIN. If you turn off end-to-end encrypted backup, your messages and media will no longer back up to the cloud unless you set them up to do so.</p><p>You can choose to turn off end-to-end encrypted backup by using your password or key, or by authenticating with your biometrics or device PIN. If you turn off end-to-end encrypted backup, your messages and media will no longer back up to the cloud unless you set them up to do so.</p>'
            }
          ]
        }
      ]
    },
    {
      slug: 'connections',
      title: 'Connections',
      avatarColor: 'secondary',
      icon: 'mdi:link-variant',
      subCategories: [
        {
          slug: 'conversations',
          title: 'Conversations',
          icon: 'mdi:message-outline',
          articles: [
            {
              slug: 'how-to-send-messages-to-connections',
              title: 'How to send messages to connections?',
              content:
                "<p>You can send a message to your connections directly from the messaging page or connections page.</p><p>The sent message will be visible in the recipient's message list and possibly in their email, depending on their app notification settings.</p><p>You can send a message to your connections directly from the messaging page or connections page.</p><p>The sent message will be visible in the recipient's message list and possibly in their email, depending on their app notification settings.</p>"
            },
            {
              slug: 'how-to-edit-or-delete-a-sent-message-within-a-conversation',
              title: 'How to edit or delete a sent message within a conversation?',
              content:
                '<p>You can edit or delete a text only message you send on app.</p><p><strong>Note:</strong>You can only edit or delete a message within 60 minutes of sending the message.</p><p>You can edit or delete a text only message you send on app.</p><p><strong>Note:</strong>You can only edit or delete a message within 60 minutes of sending the message.</p>'
            },
            {
              slug: 'how-to-delete-a-message',
              title: 'How to delete a message?',
              content:
                "<p>A conversation thread starts when a message is sent to one or more people via app messaging. You can delete conversation threads individually or in bulk.</p><p><strong>Important:</strong>You can’t restore or access deleted messages. <strong>The conversation thread will only be deleted from your inbox and not from the recipient's.</strong></p><p>A conversation thread starts when a message is sent to one or more people via app messaging. You can delete conversation threads individually or in bulk.</p><p><strong>Important:</strong>You can’t restore or access deleted messages. <strong>The conversation thread will only be deleted from your inbox and not from the recipient's.</strong></p>"
            }
          ]
        },
        {
          slug: 'jobs',
          title: 'Jobs',
          icon: 'mdi:briefcase-outline',
          articles: [
            {
              slug: 'find-relevant-jobs-through-social-hiring-and-meeting-the-team',
              title: 'Find relevant jobs through social hiring and meeting the team?',
              content:
                '<p>We have introduced two features that will help both job seekers and hirers fully engage with the power of their platform.</p> <ul><li>The #social hiring feature will notify members when a first- or second-degree connection is hiring for a relevant job. When a network connection posts a relevant job on app or adds a #hiring frame to their profile picture, app will notify the job seeker. From there, job seekers will be able to view open jobs that people in their network are hiring for.</li><li>When a member clicks on the job’s details page, they will see the “Meet the Hiring Team” feature. Members will be able to connect and message the entire team listed in this section, including the job poster.</li></ul><p>These features will allow members to find jobs through their connections and stand out to the hiring team. As a result, the hiring team will also be able to reach more potential candidates through their network.</p><p>We have introduced two features that will help both job seekers and hirers fully engage with the power of their platform.</p> <ul><li>The #social hiring feature will notify members when a first- or second-degree connection is hiring for a relevant job. When a network connection posts a relevant job on app or adds a #hiring frame to their profile picture, app will notify the job seeker. From there, job seekers will be able to view open jobs that people in their network are hiring for.</li><li>When a member clicks on the job’s details page, they will see the “Meet the Hiring Team” feature. Members will be able to connect and message the entire team listed in this section, including the job poster.</li></ul><p>These features will allow members to find jobs through their connections and stand out to the hiring team. As a result, the hiring team will also be able to reach more potential candidates through their network.</p>'
            },
            {
              slug: 'how-does-the-app-determine-when-a-job-is-relevant',
              title: 'How does the app determine when a job is relevant?',
              content:
                '<p>We will notify job seekers when someone in their network is hiring for a job that matches their current job title or industry listed in your profile or open to work preferences.</p><p>We will notify job seekers when someone in their network is hiring for a job that matches their current job title or industry listed in your profile or open to work preferences.</p>'
            },
            {
              slug: 'how-can-job-seekers-receive-these-notifications',
              title: 'How can job seekers receive these notifications?',
              content:
                '<p>Members will automatically receive notifications without having to opt in. To turn off the notification, click the three dots next to the notification and select Turn off.</p><p>Members will automatically receive notifications without having to opt in. To turn off the notification, click the three dots next to the notification and select Turn off.</p>'
            }
          ]
        },
        {
          slug: 'people',
          title: 'People',
          icon: 'mdi:account-group-outline',
          articles: [
            {
              slug: 'how-to-import-and-invite-your-email-contacts',
              title: 'How to import and invite your email contacts?',
              content:
                "<p>You can build your network by importing a list of your contacts you already know on the app. This will run a one-time upload of your address book contacts, as well as their detailed contact information. We periodically import and store details about your address book contacts to suggest relevant contacts for you to connect with, to show you relevant updates, and for other uses explained in our Privacy Policy. We'll never email anyone without your permission.</p><p>You can build your network by importing a list of your contacts you already know on the app. This will run a one-time upload of your address book contacts, as well as their detailed contact information. We periodically import and store details about your address book contacts to suggest relevant contacts for you to connect with, to show you relevant updates, and for other uses explained in our Privacy Policy. We'll never email anyone without your permission.</p>"
            },
            {
              slug: 'various-ways-to-connect-with-people',
              title: 'Various ways to connect with people?',
              content:
                '<p>Building your network is a great way to stay in touch with alumni, colleagues, and recruiters, as well as connect with new, professional opportunities. A primary email address is mandatory to send invitations. Members become 1st-degree connections when they accept your invitation.</p><p>First-degree connections are given access to any information you’ve displayed on your profile. To ensure an optimal site experience, the members can have a maximum of 30,000 1st-degree connections.</p><p>Building your network is a great way to stay in touch with alumni, colleagues, and recruiters, as well as connect with new, professional opportunities. A primary email address is mandatory to send invitations. Members become 1st-degree connections when they accept your invitation.</p><p>First-degree connections are given access to any information you’ve displayed on your profile. To ensure an optimal site experience, the members can have a maximum of 30,000 1st-degree connections.</p>'
            },
            {
              slug: 'how-to-follow-or-unfollow-people',
              title: 'How to follow or unfollow people?',
              content:
                "<p>When you follow someone, new content posted or shared by the person will be displayed in your feed. If you no longer wish to see the content of someone in your feed, you can always unfollow this person.</p><p>You can find people to follow from your feed, the Notifications tab, My Network page, or from the Search bar at the top of the page.</p><p>Unfollowing a person will hide all updates from them on your feed. If you're connected to a person and choose to unfollow them, you'll remain connected, but won't see their updates. They won't be notified that you've unfollowed them. The members will receive a notification if you begin following them again.</p><p>When you follow someone, new content posted or shared by the person will be displayed in your feed. If you no longer wish to see the content of someone in your feed, you can always unfollow this person.</p><p>You can find people to follow from your feed, the Notifications tab, My Network page, or from the Search bar at the top of the page.</p><p>Unfollowing a person will hide all updates from them on your feed. If you're connected to a person and choose to unfollow them, you'll remain connected, but won't see their updates. They won't be notified that you've unfollowed them. The members will receive a notification if you begin following them again.</p>"
            }
          ]
        }
      ]
    }
  ],
  keepLearning: [
    {
      slug: 'blogging',
      title: 'Blogging',
      img: '/images/pages/laptop.png',
      subtitle: 'Expert tips & tools to improve your website or online store using blog.'
    },
    {
      slug: 'inspiration-center',
      title: 'Inspiration Center',
      img: '/images/pages/bulb.png',
      subtitle: 'inspiration from experts to help you start and grow your big ideas.'
    },
    {
      slug: 'community',
      title: 'Community',
      img: '/images/pages/discord.png',
      subtitle: 'A group of people living in the same place or having a particular.'
    }
  ]
}

mock.onGet('/pages/help-center/landing').reply(() => {
  const allArticles: HelpCenterSubcategoryArticlesType[] = []

  data.categories.map(category =>
    category.subCategories.map(subCategory => subCategory.articles.map(article => allArticles.push(article)))
  )

  return [
    200,
    { allArticles, categories: data.categories, popularArticles: data.popularArticles, keepLearning: data.keepLearning }
  ]
})

mock.onGet('/pages/help-center/subcategory').reply(config => {
  const { category, subcategory } = config.params
  const filteredData = data.categories.filter(item => item.slug === category)

  return [
    200,
    {
      data: filteredData[0],
      categories: data.categories,
      activeTab: subcategory || filteredData[0].subCategories[0].slug
    }
  ]
})

mock.onGet('/pages/help-center/article').reply(config => {
  const { article, category, subcategory } = config.params

  const activeCategory = data.categories.filter(item => item.slug === category)[0]
  const activeSubcategory =
    activeCategory.subCategories.filter(item => item.slug === subcategory)[0] || activeCategory.subCategories[0]
  const activeArticle = activeSubcategory.articles.filter(item => item.slug === article)[0]

  return [200, { activeArticle, activeSubcategory, categories: data.categories, articles: activeSubcategory.articles }]
})
