// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components
import CurrentPlanCard from 'src/views/pages/account-settings/billing/CurrentPlanCard'
import PaymentMethodCard from 'src/views/pages/account-settings/billing/PaymentMethodCard'
import BillingAddressCard from 'src/views/pages/account-settings/billing/BillingAddressCard'
import BillingHistoryTable from 'src/views/pages/account-settings/billing/BillingHistoryTable'

const TabBilling = ({ apiPricingPlanData }) => {
  return (
    <Grid container spacing={6}>
      <Grid item xs={12}>
        <CurrentPlanCard data={apiPricingPlanData} />
      </Grid>

      <Grid item xs={12}>
        <PaymentMethodCard />
      </Grid>

      <Grid item xs={12}>
        <BillingAddressCard />
      </Grid>

      <Grid item xs={12}>
        <BillingHistoryTable />
      </Grid>
    </Grid>
  )
}

export default TabBilling
