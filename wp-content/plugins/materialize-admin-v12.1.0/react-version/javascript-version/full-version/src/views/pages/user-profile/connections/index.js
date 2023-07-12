// ** Next Import
import Link from 'next/link'

// ** MUI Components
import Box from '@mui/material/Box'
import Grid from '@mui/material/Grid'
import Card from '@mui/material/Card'
import Avatar from '@mui/material/Avatar'
import Button from '@mui/material/Button'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'
import OptionsMenu from 'src/@core/components/option-menu'

const Connections = ({ data }) => {
  return (
    <Grid container spacing={6}>
      {data &&
        Array.isArray(data) &&
        data.map((item, index) => {
          return (
            <Grid key={index} item xs={12} sm={6} md={4}>
              <Card sx={{ position: 'relative' }}>
                <OptionsMenu
                  iconButtonProps={{ size: 'small', sx: { top: 12, right: 12, position: 'absolute' } }}
                  options={[
                    'Share Connection',
                    'Block Connection',
                    { divider: true },
                    { text: 'Delete', menuItemProps: { sx: { color: 'error.main' } } }
                  ]}
                />
                <CardContent>
                  <Box sx={{ display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                    <Avatar src={item.avatar} sx={{ mb: 4, width: 100, height: 100 }} />
                    <Typography variant='h6' sx={{ fontWeight: 500 }}>
                      {item.name}
                    </Typography>
                    <Typography sx={{ mb: 4, color: 'text.secondary' }}>{item.designation}</Typography>
                    <Box sx={{ mb: 8, display: 'flex', alignItems: 'center' }}>
                      {item.chips &&
                        item.chips.map((chip, index) => (
                          <Box
                            href='/'
                            key={index}
                            component={Link}
                            onClick={e => e.preventDefault()}
                            sx={{
                              textDecoration: 'none',
                              '&:not(:last-of-type)': { mr: 3 },
                              '& .MuiChip-root': { cursor: 'pointer' }
                            }}
                          >
                            <CustomChip size='small' skin='light' color={chip.color} label={chip.title} />
                          </Box>
                        ))}
                    </Box>
                    <Box
                      sx={{
                        mb: 8,
                        gap: 2,
                        width: '100%',
                        display: 'flex',
                        flexWrap: 'wrap',
                        alignItems: 'center',
                        justifyContent: 'space-around'
                      }}
                    >
                      <Box sx={{ display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                        <Typography variant='h5'>{item.projects}</Typography>
                        <Typography sx={{ color: 'text.secondary' }}>Projects</Typography>
                      </Box>
                      <Box sx={{ display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                        <Typography variant='h5'>{item.tasks}</Typography>
                        <Typography sx={{ color: 'text.secondary' }}>Tasks</Typography>
                      </Box>
                      <Box sx={{ display: 'flex', alignItems: 'center', flexDirection: 'column' }}>
                        <Typography variant='h5'>{item.connections}</Typography>
                        <Typography sx={{ color: 'text.secondary' }}>Connections</Typography>
                      </Box>
                    </Box>
                    <Box sx={{ display: 'flex', alignItems: 'center' }}>
                      <Button
                        sx={{ mr: 4 }}
                        variant={item.isConnected ? 'contained' : 'outlined'}
                        startIcon={
                          <Icon
                            fontSize={20}
                            icon={item.isConnected ? 'mdi:account-check-outline' : 'mdi:account-plus-outline'}
                          />
                        }
                      >
                        {item.isConnected ? 'Connected' : 'Connect'}
                      </Button>
                      <Button variant='outlined' color='secondary' sx={{ p: 1.5, minWidth: 38 }}>
                        <Icon icon='mdi:email-outline' />
                      </Button>
                    </Box>
                  </Box>
                </CardContent>
              </Card>
            </Grid>
          )
        })}
    </Grid>
  )
}

export default Connections
