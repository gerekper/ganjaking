const navigation = () => {
  return [
    {
      icon: 'mdi:home-outline',
      title: 'Dashboards',
      children: [
        {
          icon: 'mdi:chart-donut',
          title: 'CRM',
          path: '/dashboards/crm'
        },
        {
          icon: 'mdi:chart-timeline-variant',
          title: 'Analytics',
          path: '/dashboards/analytics'
        },
        {
          icon: 'mdi:cart-outline',
          title: 'eCommerce',
          path: '/dashboards/ecommerce'
        }
      ]
    },
    {
      icon: 'mdi:apps',
      title: 'Apps',
      children: [
        {
          title: 'Email',
          icon: 'mdi:email-outline',
          path: '/apps/email'
        },
        {
          title: 'Chat',
          icon: 'mdi:message-outline',
          path: '/apps/chat'
        },
        {
          title: 'Calendar',
          icon: 'mdi:calendar-blank-outline',
          path: '/apps/calendar'
        },
        {
          title: 'Invoice',
          icon: 'mdi:file-document-outline',
          children: [
            {
              title: 'List',
              path: '/apps/invoice/list'
            },
            {
              title: 'Preview',
              path: '/apps/invoice/preview'
            },
            {
              title: 'Edit',
              path: '/apps/invoice/edit'
            },
            {
              title: 'Add',
              path: '/apps/invoice/add'
            }
          ]
        },
        {
          title: 'User',
          icon: 'mdi:account-outline',
          children: [
            {
              title: 'List',
              path: '/apps/user/list'
            },
            {
              title: 'View',
              children: [
                {
                  title: 'Overview',
                  path: '/apps/user/view/overview'
                },
                {
                  title: 'Security',
                  path: '/apps/user/view/security'
                },
                {
                  title: 'Billing & Plans',
                  path: '/apps/user/view/billing-plan'
                },
                {
                  title: 'Notifications',
                  path: '/apps/user/view/notification'
                },
                {
                  title: 'Connection',
                  path: '/apps/user/view/connection'
                }
              ]
            }
          ]
        },
        {
          title: 'Roles & Permissions',
          icon: 'mdi:shield-outline',
          children: [
            {
              title: 'Roles',
              path: '/apps/roles'
            },
            {
              title: 'Permissions',
              path: '/apps/permissions'
            }
          ]
        }
      ]
    },
    {
      icon: 'mdi:palette-swatch-outline',
      title: 'UI',
      children: [
        {
          title: 'Typography',
          icon: 'mdi:format-letter-case',
          path: '/ui/typography'
        },
        {
          title: 'Icons',
          path: '/ui/icons',
          icon: 'mdi:google-circles-extended'
        },
        {
          
          title: 'Cards',
          icon: 'mdi:credit-card-outline',
          children: [
            {
              title: 'Basic',
              path: '/ui/cards/basic'
            },
            {
              title: 'Advanced',
              path: '/ui/cards/advanced'
            },
            {
              title: 'Statistics',
              path: '/ui/cards/statistics'
            },
            {
              title: 'Widgets',
              path: '/ui/cards/widgets'
            },
            {
              title: 'Gamification',
              path: '/ui/cards/gamification'
            },
            {
              title: 'Actions',
              path: '/ui/cards/actions'
            }
          ]
        },
        {
          title: 'Components',
          icon: 'mdi:archive-outline',
          children: [
            {
              title: 'Accordion',
              path: '/components/accordion'
            },
            {
              title: 'Alerts',
              path: '/components/alerts'
            },
            {
              title: 'Avatars',
              path: '/components/avatars'
            },
            {
              title: 'Badges',
              path: '/components/badges'
            },
            {
              title: 'Buttons',
              path: '/components/buttons'
            },
            {
              title: 'Button Group',
              path: '/components/button-group'
            },
            {
              title: 'Chips',
              path: '/components/chips'
            },
            {
              title: 'Dialogs',
              path: '/components/dialogs'
            },
            {
              title: 'List',
              path: '/components/list'
            },
            {
              title: 'Menu',
              path: '/components/menu'
            },
            {
              title: 'Pagination',
              path: '/components/pagination'
            },
            {
              title: 'Progress',
              path: '/components/progress'
            },
            {
              title: 'Ratings',
              path: '/components/ratings'
            },
            {
              title: 'Snackbar',
              path: '/components/snackbar'
            },
            {
              title: 'Swiper',
              path: '/components/swiper'
            },
            {
              title: 'Tabs',
              path: '/components/tabs'
            },
            {
              title: 'Timeline',
              path: '/components/timeline'
            },
            {
              title: 'Toasts',
              path: '/components/toast'
            },
            {
              title: 'Tree View',
              path: '/components/tree-view'
            },
            {
              title: 'More',
              path: '/components/more'
            },
          ]
        }
      ]
    },
    {
      icon: 'mdi:file-document-outline',
      title: 'Pages',
      children: [
        {
          title: 'User Profile',
          icon: 'mdi:card-account-details-outline',
          children: [
            {
              title: 'Profile',
              path: '/pages/user-profile/profile'
            },
            {
              title: 'Teams',
              path: '/pages/user-profile/teams'
            },
            {
              title: 'Projects',
              path: '/pages/user-profile/projects'
            },
            {
              title: 'Connections',
              path: '/pages/user-profile/connections'
            }
          ]
        },
        {
          icon: 'mdi:account-cog-outline',
          title: 'Account Settings',
          children: [
            {
              title: 'Account',
              path: '/pages/account-settings/account'
            },
            {
              title: 'Security',
              path: '/pages/account-settings/security'
            },
            {
              title: 'Billing',
              path: '/pages/account-settings/billing'
            },
            {
              title: 'Notifications',
              path: '/pages/account-settings/notifications'
            },
            {
              title: 'Connections',
              path: '/pages/account-settings/connections'
            }
          ]
        },
        {
          title: 'FAQ',
          path: '/pages/faq',
          icon: 'mdi:help-circle-outline'
        },
        {
          title: 'Help Center',
          icon: 'mdi:help-circle-outline',
          path: '/pages/help-center'
        },
        {
          title: 'Pricing',
          icon: 'mdi:currency-usd',
          path: '/pages/pricing'
        },
        {
          title: 'Miscellaneous',
          icon: 'mdi:file-outline',
          children: [
            {
              openInNewTab: true,
              title: 'Coming Soon',
              path: '/pages/misc/coming-soon'
            },
            {
              openInNewTab: true,
              title: 'Under Maintenance',
              path: '/pages/misc/under-maintenance'
            },
            {
              openInNewTab: true,
              title: 'Page Not Found - 404',
              path: '/pages/misc/404-not-found'
            },
            {
              openInNewTab: true,
              title: 'Not Authorized - 401',
              path: '/pages/misc/401-not-authorized'
            },
            {
              openInNewTab: true,
              title: 'Server Error - 500',
              path: '/pages/misc/500-server-error'
            }
          ]
        },
        {
          title: 'Auth Pages',
          icon: 'mdi:lock-outline',
          children: [
            {
              title: 'Login',
              children: [
                {
                  openInNewTab: true,
                  title: 'Login v1',
                  path: '/pages/auth/login-v1'
                },
                {
                  openInNewTab: true,
                  title: 'Login v2',
                  path: '/pages/auth/login-v2'
                },
                {
                  openInNewTab: true,
                  title: 'Login With AppBar',
                  path: '/pages/auth/login-with-appbar'
                }
              ]
            },
            {
              title: 'Register',
              children: [
                {
                  openInNewTab: true,
                  title: 'Register v1',
                  path: '/pages/auth/register-v1'
                },
                {
                  openInNewTab: true,
                  title: 'Register v2',
                  path: '/pages/auth/register-v2'
                },
                {
                  openInNewTab: true,
                  title: 'Register Multi-Steps',
                  path: '/pages/auth/register-multi-steps'
                }
              ]
            },
            {
              title: 'Verify Email',
              children: [
                {
                  openInNewTab: true,
                  title: 'Verify Email v1',
                  path: '/pages/auth/verify-email-v1'
                },
                {
                  openInNewTab: true,
                  title: 'Verify Email v2',
                  path: '/pages/auth/verify-email-v2'
                }
              ]
            },
            {
              title: 'Forgot Password',
              children: [
                {
                  openInNewTab: true,
                  title: 'Forgot Password v1',
                  path: '/pages/auth/forgot-password-v1'
                },
                {
                  openInNewTab: true,
                  title: 'Forgot Password v2',
                  path: '/pages/auth/forgot-password-v2'
                }
              ]
            },
            {
              title: 'Reset Password',
              children: [
                {
                  openInNewTab: true,
                  title: 'Reset Password v1',
                  path: '/pages/auth/reset-password-v1'
                },
                {
                  openInNewTab: true,
                  title: 'Reset Password v2',
                  path: '/pages/auth/reset-password-v2'
                }
              ]
            },
            {
              title: 'Two Steps',
              children: [
                {
                  openInNewTab: true,
                  title: 'Two Steps v1',
                  path: '/pages/auth/two-steps-v1'
                },
                {
                  openInNewTab: true,
                  title: 'Two Steps v2',
                  path: '/pages/auth/two-steps-v2'
                }
              ]
            }
          ]
        },
        {
          title: 'Wizard Examples',
          icon: 'mdi:transit-connection-horizontal',
          children: [
            {
              title: 'Checkout',
              path: '/pages/wizard-examples/checkout'
            },
            {
              title: 'Property Listing',
              path: '/pages/wizard-examples/property-listing'
            },
            {
              title: 'Create Deal',
              path: '/pages/wizard-examples/create-deal'
            }
          ]
        },
        {
          icon: 'mdi:vector-arrange-below',
          title: 'Dialog Examples',
          path: '/pages/dialog-examples'
        }
      ]
    },
    {
      title: 'Forms & Tables',
      icon: 'mdi:checkbox-marked-outline',
      children: [
        {
          title: 'Form Elements',
          icon: 'mdi:form-select',
          children: [
            {
              title: 'Text Field',
              path: '/forms/form-elements/text-field'
            },
            {
              title: 'Select',
              path: '/forms/form-elements/select'
            },
            {
              title: 'Checkbox',
              path: '/forms/form-elements/checkbox'
            },
            {
              title: 'Radio',
              path: '/forms/form-elements/radio'
            },
            {
              title: 'Custom Inputs',
              path: '/forms/form-elements/custom-inputs'
            },
            {
              title: 'Textarea',
              path: '/forms/form-elements/textarea'
            },
            {
              title: 'Autocomplete',
              path: '/forms/form-elements/autocomplete'
            },
            {
              title: 'Date Pickers',
              path: '/forms/form-elements/pickers'
            },
            {
              title: 'Switch',
              path: '/forms/form-elements/switch'
            },
            {
              title: 'File Uploader',
              path: '/forms/form-elements/file-uploader'
            },
            {
              title: 'Editor',
              path: '/forms/form-elements/editor'
            },
            {
              title: 'Slider',
              path: '/forms/form-elements/slider'
            },
            {
              title: 'Input Mask',
              path: '/forms/form-elements/input-mask'
            },
          ]
        },
        {
          icon: 'mdi:cube-outline',
          title: 'Form Layouts',
          path: '/forms/form-layouts'
        },
        {
          title: 'Form Validation',
          path: '/forms/form-validation',
          icon: 'mdi:checkbox-marked-circle-outline'
        },
        {
          title: 'Form Wizard',
          path: '/forms/form-wizard',
          icon: 'mdi:transit-connection-horizontal'
        },
        {
          title: 'Table',
          icon: 'mdi:grid-large',
          path: '/tables/mui'
        },
        {
          title: 'Mui DataGrid',
          icon: 'mdi:grid',
          path: '/tables/data-grid'
        }
      ]
    },
    {
      title: 'Charts',
      icon: 'mdi:chart-donut',
      children: [
        {
          title: 'Apex',
          icon: 'mdi:chart-line',
          path: '/charts/apex-charts'
        },
        {
          title: 'Recharts',
          icon: 'mdi:chart-bell-curve-cumulative',
          path: '/charts/recharts'
        },
        {
          title: 'ChartJS',
          path: '/charts/chartjs',
          icon: 'mdi:chart-bell-curve'
        }
      ]
    },
    {
      title: 'Others',
      icon: 'mdi:dots-horizontal',
      children: [
        {
          path: '/acl',
          action: 'read',
          subject: 'acl-page',
          icon: 'mdi:shield-outline',
          title: 'Access Control'
        },
        {
          title: 'Menu Levels',
          icon: 'mdi:menu',
          children: [
            {
              title: 'Menu Level 2.1'
            },
            {
              title: 'Menu Level 2.2',
              children: [
                {
                  title: 'Menu Level 3.1'
                },
                {
                  title: 'Menu Level 3.2'
                }
              ]
            }
          ]
        },
        {
          title: 'Disabled Menu',
          icon: 'mdi:eye-off-outline',
          disabled: true
        },
        {
          title: 'Raise Support',
          icon: 'mdi:lifebuoy',
          externalLink: true,
          openInNewTab: true,
          path: 'https://pixinvent.ticksy.com/'
        },
        {
          title: 'Documentation',
          icon: 'mdi:file-document-outline',
          externalLink: true,
          openInNewTab: true,
          path: 'https://demos.pixinvent.com/materialize-nextjs-admin-template/documentation'
        }
      ]
    }
  ]
}

export default navigation
