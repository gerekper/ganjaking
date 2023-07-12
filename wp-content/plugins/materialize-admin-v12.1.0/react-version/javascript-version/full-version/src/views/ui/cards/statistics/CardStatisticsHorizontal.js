// ** MUI Import
import Grid from '@mui/material/Grid'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CardStatisticsHorizontal from 'src/@core/components/card-statistics/card-stats-horizontal'

const CardStatsHorizontal = ({ data }) => {
  if (data) {
    return (
      <Grid container spacing={6}>
        {data.map((item, index) => {
          return (
            <Grid item xs={12} md={3} sm={6} key={index}>
              <CardStatisticsHorizontal {...item} icon={<Icon icon={item.icon} />} />
            </Grid>
          )
        })}
      </Grid>
    )
  } else {
    return null
  }
}

export default CardStatsHorizontal
