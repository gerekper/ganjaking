export default [
  { heading: 'Apps & Pages' },
  {
    title: 'Email',
    icon: { icon: 'mdi-email-outline' },
    to: 'apps-email',
  },
  {
    title: 'Chat',
    icon: { icon: 'mdi-message-outline' },
    to: 'apps-chat',
  },
  {
    title: 'Calendar',
    icon: { icon: 'mdi-calendar-blank-outline' },
    to: 'apps-calendar',
  },
  {
    title: 'Invoice',
    icon: { icon: 'mdi-file-document-outline' },
    children: [
      { title: 'List', to: 'apps-invoice-list' },
      { title: 'Preview', to: { name: 'apps-invoice-preview-id', params: { id: '5036' } } },
      { title: 'Edit', to: { name: 'apps-invoice-edit-id', params: { id: '5036' } } },
      { title: 'Add', to: 'apps-invoice-add' },
    ],
  },
  {
    title: 'User',
    icon: { icon: 'mdi-account-outline' },
    children: [
      { title: 'List', to: 'apps-user-list' },
      { title: 'View', to: { name: 'apps-user-view-id', params: { id: 21 } } },
    ],
  },
  {
    title: 'Roles & Permissions',
    icon: { icon: 'mdi-shield-account-outline' },
    children: [
      { title: 'Roles', to: 'apps-roles' },
      { title: 'Permissions', to: 'apps-permissions' },
    ],
  },
  {
    title: 'Pages',
    icon: { icon: 'mdi-file-outline' },
    children: [
      { title: 'Help Center', to: 'pages-help-center' },
      { title: 'User Profile', to: { name: 'pages-user-profile-tab', params: { tab: 'profile' } } },
      { title: 'Account Settings', to: { name: 'pages-account-settings-tab', params: { tab: 'account' } } },
      { title: 'Pricing', to: 'pages-pricing' },
      { title: 'FAQ', to: 'pages-faq' },
      {
        title: 'Miscellaneous',
        children: [
          { title: 'Coming Soon', to: 'pages-misc-coming-soon', target: '_blank' },
          { title: 'Under Maintenance', to: 'pages-misc-under-maintenance', target: '_blank' },
          { title: 'Page Not Found - 404', to: 'pages-misc-not-found', target: '_blank' },
          { title: 'Not Authorized - 401', to: 'pages-misc-not-authorized', target: '_blank' },
          { title: 'Server Error - 500', to: 'pages-misc-internal-server-error', target: '_blank' },
        ],
      },
    ],
  },
  {
    title: 'Authentication',
    icon: { icon: 'mdi-lock-outline' },
    children: [
      {
        title: 'Login',
        children: [
          { title: 'Login v1', to: 'pages-authentication-login-v1', target: '_blank' },
          { title: 'Login v2', to: 'pages-authentication-login-v2', target: '_blank' },
        ],
      },
      {
        title: 'Register',
        children: [
          { title: 'Register v1', to: 'pages-authentication-register-v1', target: '_blank' },
          { title: 'Register v2', to: 'pages-authentication-register-v2', target: '_blank' },
          { title: 'Register Multi-Steps', to: 'pages-authentication-register-multi-steps', target: '_blank' },
        ],
      },
      {
        title: 'Verify Email',
        children: [
          { title: 'Verify Email v1', to: 'pages-authentication-verify-email-v1', target: '_blank' },
          { title: 'Verify Email v2', to: 'pages-authentication-verify-email-v2', target: '_blank' },
        ],
      },
      {
        title: 'Forgot Password',
        children: [
          { title: 'Forgot Password v1', to: 'pages-authentication-forgot-password-v1', target: '_blank' },
          { title: 'Forgot Password v2', to: 'pages-authentication-forgot-password-v2', target: '_blank' },
        ],
      },
      {
        title: 'Reset Password',
        children: [
          { title: 'Reset Password v1', to: 'pages-authentication-reset-password-v1', target: '_blank' },
          { title: 'Reset Password v2', to: 'pages-authentication-reset-password-v2', target: '_blank' },
        ],
      },
      {
        title: 'Two Steps',
        children: [
          { title: 'Two Steps v1', to: 'pages-authentication-two-steps-v1', target: '_blank' },
          { title: 'Two Steps v2', to: 'pages-authentication-two-steps-v2', target: '_blank' },
        ],
      },
    ],
  },
  {
    title: 'Wizard Examples',
    icon: { icon: 'mdi-ray-start-vertex-end' },
    children: [
      { title: 'Checkout', to: { name: 'wizard-examples-checkout' } },
      { title: 'Property Listing', to: { name: 'wizard-examples-property-listing' } },
      { title: 'Create Deal', to: { name: 'wizard-examples-create-deal' } },
    ],
  },
  {
    title: 'Dialog Examples',
    icon: { icon: 'mdi-square-outline' },
    to: 'pages-dialog-examples',
  },
]
