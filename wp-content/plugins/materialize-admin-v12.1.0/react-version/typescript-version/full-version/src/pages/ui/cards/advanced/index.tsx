// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CardFinanceApp from 'src/views/ui/cards/advanced/CardFinanceApp'
import CardPlanUpgrade from 'src/views/ui/cards/advanced/CardPlanUpgrade'
import CardTransactions from 'src/views/ui/cards/advanced/CardTransactions'
import CardTotalEarnings from 'src/views/ui/cards/advanced/CardTotalEarings'
import CardPaymentHistory from 'src/views/ui/cards/advanced/CardPaymentHistory'
import CardMeetingSchedule from 'src/views/ui/cards/advanced/CardMeetingSchedule'
import CardActivityTimeline from 'src/views/ui/cards/advanced/CardActivityTimeline'
import CardSalesInCountries from 'src/views/ui/cards/advanced/CardSalesInCountries'
import CardGeneralStatistics from 'src/views/ui/cards/advanced/CardGeneralStatistics'
import CardProjectStatistics from 'src/views/ui/cards/advanced/CardProjectStatistics'
import CardTopReferralSources from 'src/views/ui/cards/advanced/CardTopReferralSources'
import CardSocialNetworkVisits from 'src/views/ui/cards/advanced/CardSocialNetworkVisits'
import CardSubscribersByCountries from 'src/views/ui/cards/advanced/CardSubscribersByCountries'

const CardsAdvanced = () => {
  return (
    <Grid container spacing={6} className='match-height'>
      <Grid item xs={12} md={6} lg={4}>
        <CardTransactions />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardPlanUpgrade />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardMeetingSchedule />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardProjectStatistics />
      </Grid>
      <Grid item xs={12} lg={8}>
        <CardTopReferralSources />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardTotalEarnings />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardSocialNetworkVisits />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardGeneralStatistics />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardSalesInCountries />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardPaymentHistory />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardSubscribersByCountries />
      </Grid>
      <Grid item xs={12} md={6} lg={8}>
        <CardActivityTimeline />
      </Grid>
      <Grid item xs={12} md={6} lg={4}>
        <CardFinanceApp />
      </Grid>
    </Grid>
  )
}

export default CardsAdvanced
