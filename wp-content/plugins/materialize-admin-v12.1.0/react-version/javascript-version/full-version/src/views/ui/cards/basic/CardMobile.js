// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Menu from '@mui/material/Menu'
import Button from '@mui/material/Button'
import MenuItem from '@mui/material/MenuItem'
import { styled } from '@mui/material/styles'
import Typography from '@mui/material/Typography'
import IconButton from '@mui/material/IconButton'
import CardContent from '@mui/material/CardContent'
import CardActions from '@mui/material/CardActions'
import Grid from '@mui/material/Grid'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// Styled Grid component
const StyledGrid = styled(Grid)(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  [theme.breakpoints.down('md')]: {
    borderBottom: `1px solid ${theme.palette.divider}`
  },
  [theme.breakpoints.up('md')]: {
    borderRight: `1px solid ${theme.palette.divider}`
  }
}))

const CardMobile = () => {
  // ** State
  const [anchorEl, setAnchorEl] = useState(null)
  const open = Boolean(anchorEl)

  const handleClick = event => {
    setAnchorEl(event.currentTarget)
  }

  const handleClose = () => {
    setAnchorEl(null)
  }

  return (
    <Card>
      <Grid container spacing={6}>
        <StyledGrid item md={5} xs={12}>
          <CardContent sx={{ display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <img width={137} height={176} alt='Apple iPhone 11 Pro' src='/images/cards/iPhone-11-pro.png' />
          </CardContent>
        </StyledGrid>
        <Grid
          item
          md={7}
          xs={12}
          sx={{
            pt: theme => ['0 !important', '0 !important', `${theme.spacing(6)} !important`],
            pl: theme => [`${theme.spacing(6)} !important`, `${theme.spacing(6)} !important`, '0 !important']
          }}
        >
          <CardContent>
            <Typography variant='h6' sx={{ mb: 2 }}>
              Apple iPhone 11 Pro
            </Typography>
            <Typography variant='body2' sx={{ mb: 2 }}>
              Apple iPhone 11 Pro smartphone. Announced Sep 2019. Features 5.8″ display Apple A13 Bionic
            </Typography>
            <Typography sx={{ mb: 2 }}>
              Price:{' '}
              <Box component='span' sx={{ fontWeight: 600 }}>
                $899
              </Box>
            </Typography>
          </CardContent>
          <CardActions className='card-action-dense'>
            <Box sx={{ display: 'flex', justifyContent: 'space-between', width: '100%' }}>
              <Button sx={{ '& svg': { mr: 2 } }}>
                <Icon icon='mdi:cart-plus' fontSize={20} />
                Add to Card
              </Button>
              <IconButton
                id='long-button'
                aria-label='share'
                aria-haspopup='true'
                onClick={handleClick}
                aria-controls='long-menu'
                aria-expanded={open ? 'true' : undefined}
              >
                <Icon icon='mdi:share-variant' fontSize={20} />
              </IconButton>
              <Menu
                open={open}
                id='long-menu'
                anchorEl={anchorEl}
                onClose={handleClose}
                MenuListProps={{
                  'aria-labelledby': 'long-button'
                }}
              >
                <MenuItem onClick={handleClose}>
                  <Icon icon='mdi:facebook' />
                </MenuItem>
                <MenuItem onClick={handleClose}>
                  <Icon icon='mdi:twitter' />
                </MenuItem>
                <MenuItem onClick={handleClose}>
                  <Icon icon='mdi:linkedin' />
                </MenuItem>
                <MenuItem onClick={handleClose}>
                  <Icon icon='mdi:google-plus' />
                </MenuItem>
              </Menu>
            </Box>
          </CardActions>
        </Grid>
      </Grid>
    </Card>
  )
}

export default CardMobile
