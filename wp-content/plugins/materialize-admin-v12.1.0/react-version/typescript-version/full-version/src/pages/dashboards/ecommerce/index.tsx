// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Custom Components Imports
import CardStatisticsCharacter from 'src/@core/components/card-statistics/card-stats-with-image'

// ** Styled Component Import
import KeenSliderWrapper from 'src/@core/styles/libs/keen-slider'
import ApexChartWrapper from 'src/@core/styles/libs/react-apexcharts'

// ** Demo Components Imports
import EcommerceTable from 'src/views/dashboards/ecommerce/EcommerceTable'
import EcommerceTotalVisits from 'src/views/dashboards/ecommerce/EcommerceTotalVisits'
import EcommerceVisitsByDay from 'src/views/dashboards/ecommerce/EcommerceVisitsByDay'
import EcommerceLiveVisitors from 'src/views/dashboards/ecommerce/EcommerceLiveVisitors'
import EcommerceSalesOverview from 'src/views/dashboards/ecommerce/EcommerceSalesOverview'
import EcommerceWeeklySalesBg from 'src/views/dashboards/ecommerce/EcommerceWeeklySalesBg'
import EcommerceSalesThisMonth from 'src/views/dashboards/ecommerce/EcommerceSalesThisMonth'
import EcommerceMarketingSales from 'src/views/dashboards/ecommerce/EcommerceMarketingSales'
import EcommerceActivityTimeline from 'src/views/dashboards/ecommerce/EcommerceActivityTimeline'
import EcommerceImpressionsOrders from 'src/views/dashboards/ecommerce/EcommerceImpressionsOrders'
import EcommerceSalesOverviewWithTabs from 'src/views/dashboards/ecommerce/EcommerceSalesOverviewWithTabs'

const EcommerceDashboard = () => {
  return (
    <ApexChartWrapper>
      <KeenSliderWrapper>
        <Grid container spacing={6} className='match-height'>
          <Grid item xs={12} md={6}>
            <EcommerceSalesOverview />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <CardStatisticsCharacter
              data={{
                stats: '8.14k',
                title: 'Ratings',
                chipColor: 'primary',
                trendNumber: '+15.6%',
                chipText: 'Year of 2022',
                src: '/images/cards/card-stats-img-1.png'
              }}
            />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <CardStatisticsCharacter
              data={{
                stats: '12.2k',
                trend: 'negative',
                title: 'Sessions',
                chipColor: 'success',
                trendNumber: '-25.5%',
                chipText: 'Last Month',
                src: '/images/cards/card-stats-img-2.png'
              }}
            />
          </Grid>
          <Grid item xs={12} md={6}>
            <EcommerceWeeklySalesBg />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <EcommerceTotalVisits />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <EcommerceSalesThisMonth />
          </Grid>
          <Grid item xs={12} md={6}>
            <EcommerceActivityTimeline />
          </Grid>
          <Grid item xs={12} md={6}>
            <EcommerceSalesOverviewWithTabs />
          </Grid>
          <Grid item xs={12} sm={6} md={3}>
            <EcommerceImpressionsOrders />
          </Grid>
          <Grid item xs={12} md={5} sx={{ order: [2, 2, 1] }}>
            <EcommerceMarketingSales />
          </Grid>
          <Grid item xs={12} sm={6} md={4} sx={{ order: [1, 1, 2] }}>
            <EcommerceLiveVisitors />
          </Grid>
          <Grid item xs={12} md={8} sx={{ order: 3 }}>
            <EcommerceTable />
          </Grid>
          <Grid item xs={12} md={4} sx={{ order: 3 }}>
            <EcommerceVisitsByDay />
          </Grid>
        </Grid>
      </KeenSliderWrapper>
    </ApexChartWrapper>
  )
}

export default EcommerceDashboard
