// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import { styled } from '@mui/material/styles'
import { useTheme } from '@mui/material/styles'
import Typography from '@mui/material/Typography'

// ** Custom Components Imports
import PageHeader from 'src/@core/components/page-header'

// ** Styled Component
import DatePickerWrapper from 'src/@core/styles/libs/react-datepicker'

// ** Demo Components Imports
import ChartjsBarChart from 'src/views/charts/chartjs/ChartjsBarChart'
import ChartjsLineChart from 'src/views/charts/chartjs/ChartjsLineChart'
import ChartjsAreaChart from 'src/views/charts/chartjs/ChartjsAreaChart'
import ChartjsRadarChart from 'src/views/charts/chartjs/ChartjsRadarChart'
import ChartjsBubbleChart from 'src/views/charts/chartjs/ChartjsBubbleChart'
import ChartjsScatterChart from 'src/views/charts/chartjs/ChartjsScatterChart'
import ChartjsPolarAreaChart from 'src/views/charts/chartjs/ChartjsPolarAreaChart'
import ChartjsHorizontalBarChart from 'src/views/charts/chartjs/ChartjsHorizontalBarChart'

// ** Third Party Styles Import
import 'chart.js/auto'

const LinkStyled = styled(Link)(({ theme }) => ({
  textDecoration: 'none',
  color: theme.palette.primary.main
}))

const ChartJS = () => {
  // ** Hook
  const theme = useTheme()

  // Vars
  const whiteColor = '#fff'
  const yellowColor = '#ffe802'
  const primaryColor = '#836af9'
  const areaChartBlue = '#2c9aff'
  const barChartYellow = '#ffcf5c'
  const polarChartGrey = '#4f5d70'
  const polarChartInfo = '#299aff'
  const lineChartYellow = '#d4e157'
  const polarChartGreen = '#28dac6'
  const lineChartPrimary = '#787EFF'
  const lineChartWarning = '#ff9800'
  const horizontalBarInfo = '#26c6da'
  const polarChartWarning = '#ff8131'
  const scatterChartGreen = '#28c76f'
  const warningColorShade = '#ffbd1f'
  const areaChartBlueLight = '#84d0ff'
  const areaChartGreyLight = '#edf1f4'
  const scatterChartWarning = '#ff9f43'
  const borderColor = theme.palette.divider
  const labelColor = theme.palette.text.disabled
  const legendColor = theme.palette.text.secondary

  return (
    <DatePickerWrapper>
      <Grid container spacing={6} className='match-height'>
        <PageHeader
          title={
            <Typography variant='h5'>
              <LinkStyled href='https://github.com/reactchartjs/react-chartjs-2' target='_blank'>
                React ChartJS 2
              </LinkStyled>
            </Typography>
          }
          subtitle={<Typography variant='body2'>React wrapper for Chart.js</Typography>}
        />
        <Grid item xs={12}>
          <ChartjsLineChart
            white={whiteColor}
            labelColor={labelColor}
            success={lineChartYellow}
            borderColor={borderColor}
            legendColor={legendColor}
            primary={lineChartPrimary}
            warning={lineChartWarning}
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <ChartjsRadarChart labelColor={labelColor} legendColor={legendColor} borderColor={borderColor} />
        </Grid>
        <Grid item xs={12} md={6}>
          <ChartjsPolarAreaChart
            yellow={yellowColor}
            info={polarChartInfo}
            grey={polarChartGrey}
            primary={primaryColor}
            green={polarChartGreen}
            legendColor={legendColor}
            warning={polarChartWarning}
          />
        </Grid>
        <Grid item xs={12}>
          <ChartjsBubbleChart
            yellow={yellowColor}
            primary={primaryColor}
            labelColor={labelColor}
            borderColor={borderColor}
          />
        </Grid>
        <Grid item xs={12}>
          <ChartjsScatterChart
            primary={primaryColor}
            labelColor={labelColor}
            green={scatterChartGreen}
            borderColor={borderColor}
            legendColor={legendColor}
            warning={scatterChartWarning}
          />
        </Grid>
        <Grid item xs={12}>
          <ChartjsAreaChart
            white={whiteColor}
            blue={areaChartBlue}
            labelColor={labelColor}
            borderColor={borderColor}
            legendColor={legendColor}
            blueLight={areaChartBlueLight}
            greyLight={areaChartGreyLight}
          />
        </Grid>
        <Grid item xs={12} md={6}>
          <ChartjsBarChart yellow={barChartYellow} labelColor={labelColor} borderColor={borderColor} />
        </Grid>
        <Grid item xs={12} md={6}>
          <ChartjsHorizontalBarChart
            labelColor={labelColor}
            info={horizontalBarInfo}
            borderColor={borderColor}
            legendColor={legendColor}
            warning={warningColorShade}
          />
        </Grid>
      </Grid>
    </DatePickerWrapper>
  )
}

export default ChartJS
