// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Radio from '@mui/material/Radio'
import Typography from '@mui/material/Typography'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Avatar Component
import CustomAvatar from 'src/@core/components/mui/avatar'

const TabFramework = () => {
  const [value, setValue] = useState('react')

  const handleChange = event => {
    setValue(event.target.value)
  }

  return (
    <div>
      <Typography variant='h6' sx={{ mb: 4 }}>
        Select Framework
      </Typography>
      <Box sx={{ mb: 8 }}>
        <Box
          onClick={() => setValue('react')}
          sx={{ mb: 6, cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <CustomAvatar skin='light' color='info' variant='rounded' sx={{ mr: 3, width: 48, height: 48 }}>
              <Icon icon='mdi:react' />
            </CustomAvatar>
            <div>
              <Typography sx={{ color: 'text.secondary' }}>React Native</Typography>
              <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                Create truly native apps
              </Typography>
            </div>
          </Box>
          <Radio value='react' onChange={handleChange} checked={value === 'react'} />
        </Box>

        <Box
          onClick={() => setValue('angular')}
          sx={{ mb: 6, cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <CustomAvatar skin='light' color='error' variant='rounded' sx={{ mr: 3, width: 48, height: 48 }}>
              <Icon icon='mdi:angular' />
            </CustomAvatar>
            <div>
              <Typography sx={{ color: 'text.secondary' }}>Angular</Typography>
              <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                Most suited for your application
              </Typography>
            </div>
          </Box>
          <Radio value='angular' onChange={handleChange} checked={value === 'angular'} />
        </Box>
        <Box
          onClick={() => setValue('vuejs')}
          sx={{ mb: 6, cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <CustomAvatar skin='light' color='success' variant='rounded' sx={{ mr: 3, width: 48, height: 48 }}>
              <Icon icon='mdi:vuejs' />
            </CustomAvatar>
            <div>
              <Typography sx={{ color: 'text.secondary' }}>Vue</Typography>
              <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                Progressive Framework
              </Typography>
            </div>
          </Box>
          <Radio value='vuejs' onChange={handleChange} checked={value === 'vuejs'} />
        </Box>
        <Box
          onClick={() => setValue('laravel')}
          sx={{ cursor: 'pointer', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}
        >
          <Box sx={{ display: 'flex', alignItems: 'center' }}>
            <CustomAvatar skin='light' color='warning' variant='rounded' sx={{ mr: 3, width: 48, height: 48 }}>
              <Icon icon='mdi:laravel' />
            </CustomAvatar>
            <div>
              <Typography sx={{ color: 'text.secondary' }}>Laravel</Typography>
              <Typography variant='caption' sx={{ color: 'text.disabled' }}>
                PHP web frameworks
              </Typography>
            </div>
          </Box>
          <Radio value='laravel' onChange={handleChange} checked={value === 'laravel'} />
        </Box>
      </Box>
    </div>
  )
}

export default TabFramework
