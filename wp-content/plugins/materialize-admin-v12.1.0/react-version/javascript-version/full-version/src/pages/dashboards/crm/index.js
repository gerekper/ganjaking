// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Component Import
import CardStatisticsVertical from 'src/@core/components/card-statistics/card-stats-vertical'

// ** Styled Component Import
import ApexChartWrapper from 'src/@core/styles/libs/react-apexcharts'

// ** Demo Components Imports
import CrmAward from 'src/views/dashboards/crm/CrmAward'
import CrmTable from 'src/views/dashboards/crm/CrmTable'
import CrmTotalGrowth from 'src/views/dashboards/crm/CrmTotalGrowth'
import CrmTotalProfit from 'src/views/dashboards/crm/CrmTotalProfit'
import CrmMonthlyBudget from 'src/views/dashboards/crm/CrmMonthlyBudget'
import CrmExternalLinks from 'src/views/dashboards/crm/CrmExternalLinks'
import CrmWeeklyOverview from 'src/views/dashboards/crm/CrmWeeklyOverview'
import CrmPaymentHistory from 'src/views/dashboards/crm/CrmPaymentHistory'
import CrmOrganicSessions from 'src/views/dashboards/crm/CrmOrganicSessions'
import CrmProjectTimeline from 'src/views/dashboards/crm/CrmProjectTimeline'
import CrmMeetingSchedule from 'src/views/dashboards/crm/CrmMeetingSchedule'
import CrmSocialNetworkVisits from 'src/views/dashboards/crm/CrmSocialNetworkVisits'
import CrmMostSalesInCountries from 'src/views/dashboards/crm/CrmMostSalesInCountries'

const CrmDashboard = () => {
  return (
    <ApexChartWrapper>
      <Grid container spacing={6} className='match-height'>
        <Grid item xs={12} md={4}>
          <CrmAward />
        </Grid>
        <Grid item xs={6} sm={3} md={2}>
          <CardStatisticsVertical
            stats='155k'
            color='primary'
            trendNumber='+22%'
            title='Total Orders'
            chipText='Last 4 Month'
            icon={<Icon icon='mdi:cart-plus' />}
          />
        </Grid>
        <Grid item xs={6} sm={3} md={2}>
          <CardStatisticsVertical
            stats='$13.4k'
            color='success'
            trendNumber='+38%'
            title='Total Sales'
            chipText='Last Six Month'
            icon={<Icon icon='mdi:currency-usd' />}
          />
        </Grid>
        <Grid item xs={6} sm={3} md={2}>
          <CrmTotalProfit />
        </Grid>
        <Grid item xs={6} sm={3} md={2}>
          <CrmTotalGrowth />
        </Grid>
        <Grid item xs={12} md={4}>
          <CrmOrganicSessions />
        </Grid>
        <Grid item xs={12} md={8}>
          <CrmProjectTimeline />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <CrmWeeklyOverview />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <CrmSocialNetworkVisits />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <CrmMonthlyBudget />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <CrmMeetingSchedule />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <CrmExternalLinks />
        </Grid>
        <Grid item xs={12} sm={6} md={4}>
          <CrmPaymentHistory />
        </Grid>
        <Grid item xs={12} md={4}>
          <CrmMostSalesInCountries />
        </Grid>
        <Grid item xs={12} md={8}>
          <CrmTable />
        </Grid>
      </Grid>
    </ApexChartWrapper>
  )
}

export default CrmDashboard
