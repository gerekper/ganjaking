// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CardStatsSalesMonth from 'src/views/ui/cards/statistics/CardStatsSalesMonth'
import CardStatsTotalVisits from 'src/views/ui/cards/statistics/CardStatsTotalVisits'
import CardStatsSalesProfit from 'src/views/ui/cards/statistics/CardStatsSalesProfit'
import CardStatsOrdersImpressions from 'src/views/ui/cards/statistics/CardStatsOrdersImpressions'

const CardStatisticsCharts2 = () => {
  return (
    <Grid container spacing={6}>
      <Grid item xs={12} sm={6} lg={3}>
        <CardStatsSalesProfit />
      </Grid>
      <Grid item xs={12} sm={6} lg={3}>
        <CardStatsTotalVisits />
      </Grid>
      <Grid item xs={12} sm={6} lg={3}>
        <CardStatsSalesMonth />
      </Grid>
      <Grid item xs={12} sm={6} lg={3}>
        <CardStatsOrdersImpressions />
      </Grid>
    </Grid>
  )
}

export default CardStatisticsCharts2
