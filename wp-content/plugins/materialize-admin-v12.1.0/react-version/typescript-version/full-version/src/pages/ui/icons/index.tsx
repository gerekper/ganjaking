// ** Next Import
import Link from 'next/link'

// ** MUI Imports
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import Tooltip from '@mui/material/Tooltip'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Custom Components Imports
import Icon from 'src/@core/components/icon'
import PageHeader from 'src/@core/components/page-header'

const icons = [
  'mdi:abacus',
  'mdi:account',
  'mdi:ab-testing',
  'mdi:account-box',
  'mdi:account-cog',
  'mdi:abjad-arabic',
  'mdi:abjad-hebrew',
  'mdi:abugida-thai',
  'mdi:access-point',
  'mdi:account-cash',
  'mdi:account-edit',
  'mdi:account-alert',
  'mdi:account-check',
  'mdi:account-child',
  'mdi:account-clock',
  'mdi:account-group',
  'mdi:account-cancel',
  'mdi:account-circle',
  'mdi:access-point-off',
  'mdi:account-convert',
  'mdi:account-details',
  'mdi:access-point-plus',
  'mdi:access-point-check',
  'mdi:access-point-minus',
  'mdi:account-arrow-left',
  'mdi:account-cowboy-hat',
  'mdi:abugida-devanagari',
  'mdi:access-point-remove',
  'mdi:account-arrow-right',
  'mdi:account-box-outline',
  'mdi:account-cog-outline',
  'mdi:access-point-network',
  'mdi:account-box-multiple',
  'mdi:account-cash-outline',
  'mdi:account-child-circle',
  'mdi:account-edit-outline',
  'mdi:account-alert-outline',
  'mdi:account-check-outline',
  'mdi:account-child-outline',
  'mdi:account-clock-outline',
  'mdi:account-cancel-outline',
  'mdi:account-circle-outline',
  'mdi:access-point-network-off',
  'mdi:account-convert-outline',
  'mdi:account-details-outline',
  'mdi:account-arrow-left-outline',
  'mdi:account-arrow-right-outline',
  'mdi:account-box-multiple-outline'
]

const LinkStyled = styled(Link)(({ theme }) => ({
  textDecoration: 'none',
  color: theme.palette.primary.main
}))

const Icons = () => {
  const renderIconGrids = () => {
    return icons.map((icon, index) => {
      return (
        <Grid item key={index}>
          <Tooltip arrow title={icon} placement='top'>
            <Card>
              <CardContent sx={{ display: 'flex' }}>
                <Icon icon={icon} />
              </CardContent>
            </Card>
          </Tooltip>
        </Grid>
      )
    })
  }

  return (
    <Grid container spacing={6}>
      <PageHeader
        title={
          <Typography variant='h5'>
            <LinkStyled href='https://iconify.design/' target='_blank'>
              Iconify Design
            </LinkStyled>
          </Typography>
        }
        subtitle={<Typography variant='body2'>Modern unified SVG framework</Typography>}
      />
      <Grid item xs={12}>
        <Grid container spacing={6}>
          {renderIconGrids()}
        </Grid>
      </Grid>
      <Grid item xs={12} sx={{ textAlign: 'center' }}>
        <Button
          target='_blank'
          rel='noreferrer'
          variant='contained'
          component={LinkStyled}
          href='https://icon-sets.iconify.design/'
        >
          View All Icons
        </Button>
      </Grid>
    </Grid>
  )
}

export default Icons
