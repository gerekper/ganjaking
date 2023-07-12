export default [
  {
    title: 'Dashboards',
    icon: { icon: 'mdi-home-outline' },
    children: [
      {
        title: 'CRM',
        to: 'dashboards-crm',
        icon: { icon: 'mdi-monitor-dashboard' },
        action: 'read',
        subject: 'Auth',
      },
      {
        title: 'Analytics',
        to: 'dashboards-analytics',
        icon: { icon: 'mdi-chart-timeline-variant' },
      },
      {
        title: 'eCommerce',
        to: 'dashboards-ecommerce',
        icon: { icon: 'mdi-cart-outline' },
        action: 'read',
        subject: 'Admin',
      },
    ],
  },
]
