// ** Mock Adapter
import mock from '@/@fake-db/mock'

const database = [
  {
    id: 1,
    url: { name: 'dashboards-analytics' },
    icon: 'mdi-chart-timeline-variant',
    title: 'Analytics Dashboard',
    category: 'dashboards',
  },
  {
    id: 2,
    url: { name: 'dashboards-crm' },
    icon: 'mdi-cart-outline',
    title: 'CRM Dashboard',
    category: 'dashboards',
  },
  {
    id: 3,
    url: { name: 'apps-email' },
    icon: 'mdi-email-outline',
    title: 'Email',
    category: 'appsPages',
  },
  {
    id: 4,
    url: { name: 'apps-chat' },
    icon: 'mdi-message-outline',
    title: 'Chat',
    category: 'appsPages',
  },
  {
    id: 5,
    url: { name: 'apps-calendar' },
    icon: 'mdi-calendar-blank-outline',
    title: 'Calendar',
    category: 'appsPages',
  },
  {
    id: 6,
    url: { name: 'apps-invoice-list' },
    icon: 'mdi-format-list-numbered',
    title: 'Invoice List',
    category: 'appsPages',
  },
  {
    id: 7,
    url: { name: 'apps-invoice-preview-id', params: { id: '5036' } },
    icon: 'mdi-file-document-outline',
    title: 'Invoice Preview',
    category: 'appsPages',
  },
  {
    id: 9,
    url: { name: 'apps-invoice-edit-id', params: { id: '5036' } },
    icon: 'mdi-file-document-edit-outline',
    title: 'Invoice Edit',
    category: 'appsPages',
  },
  {
    id: 11,
    url: { name: 'apps-invoice-add' },
    icon: 'mdi-file-plus-outline',
    title: 'Invoice Add',
    category: 'appsPages',
  },
  {
    id: 12,
    url: { name: 'apps-user-list' },
    icon: 'mdi-account-group-outline',
    title: 'User List',
    category: 'appsPages',
  },
  {
    id: 13,
    url: { name: 'apps-user-view-id', params: { id: 21 } },
    icon: 'mdi-eye-outline',
    title: 'User View',
    category: 'appsPages',
  },
  {
    id: 15,
    url: { name: 'pages-help-center' },
    icon: 'mdi-help-circle-outline',
    title: 'Help Center',
    category: 'appsPages',
  },
  {
    id: 16,
    url: { name: 'pages-user-profile-tab', params: { tab: 'profile' } },
    icon: 'mdi-account-cog-outline',
    title: 'User Profile - Profile',
    category: 'appsPages',
  },
  {
    id: 17,
    url: { name: 'pages-account-settings-tab', params: { tab: 'account' } },
    icon: 'mdi-account-cog-outline',
    title: 'Account Settings - Account',
    category: 'appsPages',
  },
  {
    id: 18,
    url: { name: 'pages-account-settings-tab', params: { tab: 'security' } },
    icon: 'mdi-lock-open-outline',
    title: 'Account Settings - Security',
    category: 'appsPages',
  },
  {
    id: 19,
    url: { name: 'pages-account-settings-tab', params: { tab: 'billing-plans' } },
    icon: 'mdi-currency-usd',
    title: 'Account Settings - Billing',
    category: 'appsPages',
  },
  {
    id: 20,
    url: { name: 'pages-account-settings-tab', params: { tab: 'notification' } },
    icon: 'mdi-bell-outline',
    title: 'Account Settings - Notifications',
    category: 'appsPages',
  },
  {
    id: 21,
    url: { name: 'pages-account-settings-tab', params: { tab: 'connection' } },
    icon: 'mdi-link-variant',
    title: 'Account Settings - Connections',
    category: 'appsPages',
  },
  {
    id: 22,
    url: { name: 'pages-pricing' },
    icon: 'mdi-currency-usd',
    title: 'Pricing',
    category: 'appsPages',
  },
  {
    id: 23,
    url: { name: 'pages-faq' },
    icon: 'mdi-help-circle-outline',
    title: 'FAQ',
    category: 'appsPages',
  },
  {
    id: 24,
    url: { name: 'pages-misc-coming-soon' },
    icon: 'mdi-clock-outline',
    title: 'Coming Soon',
    category: 'appsPages',
  },
  {
    id: 25,
    url: { name: 'pages-misc-under-maintenance' },
    icon: 'mdi-cog-outline',
    title: 'Under Maintenance',
    category: 'appsPages',
  },
  {
    id: 26,
    url: { name: 'pages-misc-not-found' },
    icon: 'mdi-alert-circle-outline',
    title: 'Page Not Found - 404',
    category: 'appsPages',
  },
  {
    id: 27,
    url: { name: 'pages-misc-not-authorized' },
    icon: 'mdi-account-multiple-remove-outline',
    title: 'Not Authorized - 401',
    category: 'appsPages',
  },
  {
    id: 28,
    url: { name: 'pages-misc-internal-server-error' },
    icon: 'mdi-view-list-outline',
    title: 'Server Error - 500',
    category: 'appsPages',
  },
  {
    id: 29,
    url: { name: 'pages-authentication-login-v1' },
    icon: 'mdi-login',
    title: 'Login V1',
    category: 'appsPages',
  },
  {
    id: 30,
    url: { name: 'pages-authentication-login-v2' },
    icon: 'mdi-login',
    title: 'Login V2',
    category: 'appsPages',
  },
  {
    id: 31,
    url: { name: 'pages-authentication-register-v1' },
    icon: 'mdi-account-plus-outline',
    title: 'Register V1',
    category: 'appsPages',
  },
  {
    id: 32,
    url: { name: 'pages-authentication-register-v2' },
    icon: 'mdi-account-plus-outline',
    title: 'Register V2',
    category: 'appsPages',
  },
  {
    id: 33,
    icon: 'mdi-email-check-outline',
    category: 'appsPages',
    title: 'Verify Email V1',
    url: { name: 'pages-authentication-verify-email-v1' },
  },
  {
    id: 34,
    icon: 'mdi-email-check-outline',
    category: 'appsPages',
    title: 'Verify Email V2',
    url: { name: 'pages-authentication-verify-email-v2' },
  },
  {
    id: 35,
    url: { name: 'pages-authentication-forgot-password-v1' },
    icon: 'mdi-lock-alert-outline',
    title: 'Forgot Password V1',
    category: 'appsPages',
  },
  {
    id: 36,
    url: { name: 'pages-authentication-forgot-password-v2' },
    icon: 'mdi-lock-alert-outline',
    title: 'Forgot Password V2',
    category: 'appsPages',
  },
  {
    id: 37,
    url: { name: 'pages-authentication-reset-password-v1' },
    icon: 'mdi-lock-reset',
    title: 'Reset Password V1',
    category: 'appsPages',
  },
  {
    id: 38,
    url: { name: 'pages-authentication-reset-password-v2' },
    icon: 'mdi-lock-reset',
    title: 'Reset Password V2',
    category: 'appsPages',
  },
  {
    id: 39,
    icon: 'mdi-cellphone-link',
    category: 'appsPages',
    title: 'Two Steps V1',
    url: { name: 'pages-authentication-two-steps-v1' },
  },
  {
    id: 40,
    icon: 'mdi-cellphone-link',
    category: 'appsPages',
    title: 'Two Steps V2',
    url: { name: 'pages-authentication-two-steps-v2' },
  },
  {
    id: 41,
    url: { name: 'pages-typography' },
    icon: 'mdi-format-letter-case',
    title: 'Typography',
    category: 'userInterface',
  },
  {
    id: 42,
    url: { name: 'pages-icons' },
    icon: 'mdi-google-circles-extended',
    title: 'Icons',
    category: 'userInterface',
  },
  {
    id: 43,
    url: { name: 'pages-cards-card-basic' },
    icon: 'mdi-card-outline',
    title: 'Card Basic',
    category: 'userInterface',
  },
  {
    id: 44,
    url: { name: 'pages-cards-card-statistics' },
    icon: 'mdi-chart-box-outline',
    title: 'Card Statistics',
    category: 'userInterface',
  },
  {
    id: 45,
    url: { name: 'pages-cards-card-gamification' },
    icon: 'mdi-card-account-details-outline',
    title: 'Card Gamification',
    category: 'userInterface',
  },
  {
    id: 46,
    url: { name: 'pages-cards-card-actions' },
    icon: 'mdi-card-plus-outline',
    title: 'Card Actions',
    category: 'userInterface',
  },
  {
    id: 47,
    url: { name: 'components-alert' },
    icon: 'mdi-alert-outline',
    title: 'Alerts',
    category: 'userInterface',
  },
  {
    id: 48,
    url: { name: 'components-avatar' },
    icon: 'mdi-account-circle-outline',
    title: 'Avatars',
    category: 'userInterface',
  },
  {
    id: 49,
    url: { name: 'components-badge' },
    icon: 'mdi-bell-badge-outline',
    title: 'Badges',
    category: 'userInterface',
  },
  {
    id: 50,
    url: { name: 'components-button' },
    icon: 'mdi-gesture-tap-button',
    title: 'Buttons',
    category: 'userInterface',
  },
  {
    id: 51,
    url: { name: 'components-chip' },
    icon: 'mdi-new-box',
    title: 'Chips',
    category: 'userInterface',
  },
  {
    id: 52,
    url: { name: 'components-dialog' },
    icon: 'mdi-card-bulleted-outline',
    title: 'Dialogs',
    category: 'userInterface',
  },
  {
    id: 53,
    url: { name: 'components-list' },
    icon: 'mdi-format-list-bulleted',
    title: 'List',
    category: 'userInterface',
  },
  {
    id: 54,
    url: { name: 'components-menu' },
    icon: 'mdi-menu',
    title: 'Menu',
    category: 'userInterface',
  },
  {
    id: 55,
    url: { name: 'components-pagination' },
    icon: 'mdi-page-last',
    title: 'Pagination',
    category: 'userInterface',
  },
  {
    id: 56,
    url: { name: 'components-progress-circular' },
    icon: 'mdi-progress-helper',
    title: 'Progress Circular',
    category: 'userInterface',
  },
  {
    id: 83,
    url: { name: 'components-progress-linear' },
    icon: 'mdi-progress-helper',
    title: 'Progress Linear',
    category: 'userInterface',
  },
  {
    id: 57,
    url: { name: 'components-expansion-panel' },
    icon: 'mdi-format-vertical-align-center',
    title: 'Expansion Panel',
    category: 'userInterface',
  },
  {
    id: 58,
    url: { name: 'components-snackbar' },
    icon: 'mdi-message-processing-outline',
    title: 'Snackbar',
    category: 'userInterface',
  },
  {
    id: 59,
    url: { name: 'components-tabs' },
    icon: 'mdi-tab',
    title: 'Tabs',
    category: 'userInterface',
  },
  {
    id: 60,
    url: { name: 'components-timeline' },
    icon: 'mdi-timeline-outline',
    title: 'Timeline',
    category: 'userInterface',
  },
  {
    id: 61,
    url: { name: 'components-tooltip' },
    icon: 'mdi-tooltip-edit-outline',
    title: 'Tooltip',
    category: 'userInterface',
  },
  {
    id: 62,
    url: { name: 'forms-textfield' },
    icon: 'mdi-lastpass',
    title: 'TextField',
    category: 'formsTables',
  },
  {
    id: 63,
    url: { name: 'forms-select' },
    icon: 'mdi-format-list-checkbox',
    title: 'Select',
    category: 'formsTables',
  },
  {
    id: 64,
    url: { name: 'forms-checkbox' },
    icon: 'mdi-checkbox-outline',
    title: 'Checkbox',
    category: 'formsTables',
  },
  {
    id: 65,
    url: { name: 'forms-radio' },
    icon: 'mdi-radiobox-marked',
    title: 'Radio',
    category: 'formsTables',
  },
  {
    id: 66,
    url: { name: 'forms-combobox' },
    icon: 'mdi-checkbox-outline',
    title: 'Combobox',
    category: 'formsTables',
  },
  {
    id: 67,
    url: { name: 'forms-date-time-picker' },
    icon: 'mdi-calendar-range',
    title: 'Date Time picker',
    category: 'formsTables',
  },
  {
    id: 68,
    url: { name: 'forms-textarea' },
    icon: 'mdi-card-text-outline',
    title: 'Textarea',
    category: 'formsTables',
  },
  {
    id: 70,
    url: { name: 'forms-switch' },
    icon: 'mdi-toggle-switch-outline',
    title: 'Switch',
    category: 'formsTables',
  },
  {
    id: 71,
    url: { name: 'forms-file-input' },
    icon: 'mdi-tray-arrow-up',
    title: 'File Input',
    category: 'formsTables',
  },
  {
    id: 72,
    url: { name: 'forms-rating' },
    icon: 'mdi-star-outline',
    title: 'Form Rating',
    category: 'formsTables',
  },
  {
    id: 73,
    url: { name: 'forms-slider' },
    icon: 'mdi-gesture-swipe-horizontal',
    title: 'Slider',
    category: 'formsTables',
  },
  {
    id: 74,
    url: { name: 'forms-range-slider' },
    icon: 'mdi-lastpass',
    title: 'Range Slider',
    category: 'formsTables',
  },
  {
    id: 75,
    url: { name: 'forms-form-layouts' },
    icon: 'mdi-cube-outline',
    title: 'Form Layouts',
    category: 'formsTables',
  },
  {
    id: 76,
    url: { name: 'forms-form-validation' },
    icon: 'mdi-checkbox-marked-circle-outline',
    title: 'Form Validation',
    category: 'formsTables',
  },
  {
    id: 77,
    url: { name: 'charts-apex-chart' },
    icon: 'mdi-chart-line',
    title: 'Apex Charts',
    category: 'chartsMisc',
  },
  {
    id: 78,
    url: { name: 'charts-chartjs' },
    icon: 'mdi-chart-bell-curve',
    title: 'ChartJS',
    category: 'chartsMisc',
  },
  {
    id: 79,
    url: { name: 'access-control' },
    icon: 'mdi-shield-outline',
    title: 'Access Control (ACL)',
    category: 'chartsMisc',
  },
  {
    id: 80,
    url: { name: 'pages-dialog-examples' },
    icon: 'mdi-card-outline',
    title: 'Dialog Examples',
    category: 'appsPages',
  },
  {
    id: 81,
    url: { name: 'forms-custom-input' },
    icon: 'mdi-format-list-checks',
    title: 'Custom Input',
    category: 'formsTables',
  },
  {
    id: 82,
    url: { name: 'forms-autocomplete' },
    icon: 'mdi-format-align-left',
    title: 'Autocomplete',
    category: 'formsTables',
  },
  {
    id: 83,
    url: { name: 'extensions-tour' },
    icon: 'mdi-cube-outline',
    title: 'Tour',
    category: 'userInterface',
  },
  {
    id: 84,
    url: { name: 'pages-authentication-register-multi-steps' },
    icon: 'mdi-account-plus-outline',
    title: 'Register Multi-Steps',
    category: 'appsPages',
  },
  {
    id: 85,
    url: { name: 'wizard-examples-checkout' },
    icon: 'mdi-cart-outline',
    title: 'Wizard - Checkout',
    category: 'appsPages',
  },
  {
    id: 86,
    url: { name: 'wizard-examples-create-deal' },
    icon: 'mdi-gift-outline',
    title: 'Wizard - create deal',
    category: 'appsPages',
  },
  {
    id: 87,
    url: { name: 'wizard-examples-property-listing' },
    icon: 'mdi-home-outline',
    title: 'Wizard - Property Listing',
    category: 'appsPages',
  },
  {
    id: 88,
    url: { name: 'apps-roles' },
    icon: 'mdi-shield-account-outline',
    title: 'Roles',
    category: 'appsPages',
  },
  {
    id: 89,
    url: { name: 'apps-permissions' },
    icon: 'mdi-shield-account-outline',
    title: 'Permissions',
    category: 'appsPages',
  },
  {
    id: 90,
    url: { name: 'tables-data-table' },
    icon: 'mdi-table',
    title: 'Data Table',
    category: 'formsTables',
  },
  {
    id: 91,
    url: { name: 'tables-simple-table' },
    icon: 'mdi-table',
    title: 'Simple Table',
    category: 'formsTables',
  },
  {
    id: 92,
    url: { name: 'forms-form-wizard-numbered' },
    icon: 'mdi-align-vertical-distribute',
    title: 'Form Wizard Numbered',
    category: 'formsTables',
  },
  {
    id: 93,
    url: { name: 'forms-form-wizard-icons' },
    icon: 'mdi-align-vertical-distribute',
    title: 'Form Wizard Icons',
    category: 'formsTables',
  },
  {
    id: 94,
    url: { name: 'pages-cards-card-advance' },
    icon: 'mdi-card-bulleted-settings-outline',
    title: 'Card Advance',
    category: 'userInterface',
  },
  {
    id: 95,
    url: { name: 'pages-cards-card-widgets' },
    icon: 'mdi-view-grid-plus-outline',
    title: 'Card Widgets',
    category: 'userInterface',
  },
  {
    id: 96,
    url: { name: 'dashboards-ecommerce' },
    icon: 'mdi-cart-outline',
    title: 'eCommerce Dashboard',
    category: 'dashboards',
  },
]


// ** GET Search Data
// eslint-disable-next-line sonarjs/cognitive-complexity
mock.onGet('/app-bar/search').reply(config => {
  const { q = '' } = config.params
  const queryLowered = q.toLowerCase()

  const exactData = {
    dashboards: [],
    appsPages: [],
    userInterface: [],
    formsTables: [],
    chartsMisc: [],
  }

  const includeData = {
    dashboards: [],
    appsPages: [],
    userInterface: [],
    formsTables: [],
    chartsMisc: [],
  }

  database.forEach(obj => {
    const isMatched = obj.title.toLowerCase().startsWith(queryLowered)
    if (isMatched && exactData[obj.category].length < 5)
      exactData[obj.category].push(obj)
  })
  database.forEach(obj => {
    const isMatched = !obj.title.toLowerCase().startsWith(queryLowered) && obj.title.toLowerCase().includes(queryLowered)
    if (isMatched && includeData[obj.category].length < 5)
      includeData[obj.category].push(obj)
  })

  const categoriesCheck = []

  Object.keys(exactData).forEach(category => {
    if (exactData[category].length > 0)
      categoriesCheck.push(category)
  })
  if (categoriesCheck.length === 0) {
    Object.keys(includeData).forEach(category => {
      if (includeData[category].length > 0)
        categoriesCheck.push(category)
    })
  }
  const resultsLength = categoriesCheck.length === 1 ? 5 : 3
  const mergedData = []

  Object.keys(exactData).forEach(element => {
    if (exactData[element].length || includeData[element].length) {
      const r = exactData[element].concat(includeData[element]).slice(0, resultsLength)

      r.unshift({ header: element, title: element })
      mergedData.push(...r)
    }
  })
  
  return [200, [...mergedData]]
})
