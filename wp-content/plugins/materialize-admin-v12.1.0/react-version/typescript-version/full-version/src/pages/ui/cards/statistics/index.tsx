// ** Next Imports
import { GetStaticProps, InferGetStaticPropsType } from 'next/types'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Third Party Components
import axios from 'axios'

// ** Type Import
import { CardStatsType } from 'src/@fake-db/types'

// ** Demo Components Imports
import CardStatisticsSales from 'src/views/ui/cards/statistics/CardStatisticsSales'
import CardStatisticsCharts from 'src/views/ui/cards/statistics/CardStatisticsCharts'
import CardStatisticsCharts2 from 'src/views/ui/cards/statistics/CardStatisticsCharts2'
import CardStatisticsVertical from 'src/views/ui/cards/statistics/CardStatisticsVertical'
import CardStatisticsHorizontal from 'src/views/ui/cards/statistics/CardStatisticsHorizontal'
import CardStatisticsCharacters from 'src/views/ui/cards/statistics/CardStatisticsCharacters'
import CardStatisticsWeeklySales from 'src/views/ui/cards/statistics/CardStatisticsWeeklySales'
import CardStatisticsLiveVisitors from 'src/views/ui/cards/statistics/CardStatisticsLiveVisitors'
import CardStatisticsWeeklySalesBg from 'src/views/ui/cards/statistics/CardStatisticsWeeklySalesBg'
import CardStatisticsMarketingSales from 'src/views/ui/cards/statistics/CardStatisticsMarketingSales'

// ** Styled Component Import
import KeenSliderWrapper from 'src/@core/styles/libs/keen-slider'
import ApexChartWrapper from 'src/@core/styles/libs/react-apexcharts'

const CardStatistics = ({ apiData }: InferGetStaticPropsType<typeof getStaticProps>) => {
  return (
    <ApexChartWrapper>
      <KeenSliderWrapper>
        <Grid container spacing={6}>
          <Grid item xs={12}>
            <CardStatisticsHorizontal data={apiData.statsHorizontal} />
          </Grid>
          <Grid item xs={12}>
            <CardStatisticsCharacters data={apiData.statsCharacter} />
          </Grid>
          <Grid item xs={12}>
            <CardStatisticsVertical data={apiData.statsVertical} />
          </Grid>
          <Grid item xs={12}>
            <CardStatisticsCharts />
          </Grid>
          <Grid item xs={12}>
            <CardStatisticsCharts2 />
          </Grid>
          <Grid item xs={12} md={6}>
            <CardStatisticsWeeklySales />
          </Grid>
          <Grid item xs={12} md={6}>
            <CardStatisticsMarketingSales />
          </Grid>
          <Grid item xs={12} md={6}>
            <CardStatisticsWeeklySalesBg />
          </Grid>
          <Grid item xs={12} md={6}>
            <CardStatisticsSales />
          </Grid>
          <Grid item xs={12} md={6}>
            <CardStatisticsLiveVisitors />
          </Grid>
        </Grid>
      </KeenSliderWrapper>
    </ApexChartWrapper>
  )
}

export const getStaticProps: GetStaticProps = async () => {
  const res = await axios.get('/cards/statistics')
  const apiData: CardStatsType = res.data

  return {
    props: {
      apiData
    }
  }
}

export default CardStatistics
