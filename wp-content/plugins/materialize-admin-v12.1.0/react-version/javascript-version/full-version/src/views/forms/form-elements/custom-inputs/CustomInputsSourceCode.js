export const CustomCheckboxBasicJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomCheckboxBasic from 'src/@core/components/custom-checkbox/basic'

const data = [
  {
    meta: '20%',
    isSelected: true,
    value: 'discount',
    title: 'Discount',
    content: 'Wow! Get 20% off on your next purchase!'
  },
  {
    meta: 'Free',
    value: 'updates',
    title: 'Updates',
    content: 'Get Updates regarding related products.'
  }
]

const BasicCustomCheckbox = () => {
  const initialSelected = data.filter(item => item.isSelected).map(item => item.value)

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = value => {
    if (selected.includes(value)) {
      const updatedArr = selected.filter(item => item !== value)
      setSelected(updatedArr)
    } else {
      setSelected([...selected, value])
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomCheckboxBasic
          key={index}
          data={data[index]}
          selected={selected}
          handleChange={handleChange}
          name='custom-checkbox-basic'
          gridProps={{ sm: 6, xs: 12 }}
        />
      ))}
    </Grid>
  )
}

export default BasicCustomCheckbox
`}</code></pre>) 

export const CustomCheckboxIconsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomCheckboxIcons from 'src/@core/components/custom-checkbox/icons'

const data = [
  {
    value: 'backup',
    title: 'Backup',
    isSelected: true,
    content: 'Backup every file from your project.'
  },
  {
    value: 'encrypt',
    title: 'Encrypt',
    content: 'Translate your data to encrypted text.'
  },
  {
    value: 'site-lock',
    title: 'Site Lock',
    content: 'Security tool to protect your website.'
  }
]

const icons = [
  { icon: 'mdi:server', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } },
  { icon: 'mdi:shield-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } },
  { icon: 'mdi:lock-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } }
]

const CustomCheckboxWithIcons = () => {
  const initialSelected = data.filter(item => item.isSelected).map(item => item.value)

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = value => {
    if (selected.includes(value)) {
      const updatedArr = selected.filter(item => item !== value)
      setSelected(updatedArr)
    } else {
      setSelected([...selected, value])
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomCheckboxIcons
          key={index}
          data={data[index]}
          selected={selected}
          icon={icons[index].icon}
          handleChange={handleChange}
          name='custom-checkbox-icons'
          gridProps={{ sm: 4, xs: 12 }}
          iconProps={icons[index].iconProps}
        />
      ))}
    </Grid>
  )
}

export default CustomCheckboxWithIcons
`}</code></pre>) 

export const CustomCheckboxImgJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomCheckboxImg from 'src/@core/components/custom-checkbox/image'

const data = [
  {
    value: 'clock',
    isSelected: true,
    img: '/images/pages/background-3.jpg'
  },
  {
    value: 'donuts',
    img: '/images/pages/background-8.jpg'
  },
  {
    value: 'flowers',
    img: '/images/pages/background-5.jpg'
  }
]

const CustomCheckboxWithImages = () => {
  const initialSelected = data.filter(item => item.isSelected).map(item => item.value)

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = value => {
    if (selected.includes(value)) {
      const updatedArr = selected.filter(item => item !== value)
      setSelected(updatedArr)
    } else {
      setSelected([...selected, value])
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomCheckboxImg
          key={index}
          data={data[index]}
          selected={selected}
          name='custom-checkbox-img'
          handleChange={handleChange}
          gridProps={{ sm: 4, xs: 12 }}
        />
      ))}
    </Grid>
  )
}

export default CustomCheckboxWithImages
`}</code></pre>) 

export const CustomRadioIconsJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomRadioIcons from 'src/@core/components/custom-radio/icons'

const data = [
  {
    value: 'starter',
    title: 'Starter',
    isSelected: true,
    content: 'A simple start for everyone.'
  },
  {
    value: 'standard',
    title: 'Standard',
    content: 'For small to medium businesses.'
  },
  {
    value: 'enterprise',
    title: 'Enterprise',
    content: 'Solution for big organizations.'
  }
]

const icons = [
  { icon: 'mdi:rocket-launch-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } },
  { icon: 'mdi:account-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } },
  { icon: 'mdi:crown-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } }
]

const CustomRadioWithIcons = () => {
  const initialSelected = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1].value

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = prop => {
    if (typeof prop === 'string') {
      setSelected(prop)
    } else {
      setSelected(prop.target.value)
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomRadioIcons
          key={index}
          data={data[index]}
          selected={selected}
          icon={icons[index].icon}
          name='custom-radios-icons'
          handleChange={handleChange}
          gridProps={{ sm: 4, xs: 12 }}
          iconProps={icons[index].iconProps}
        />
      ))}
    </Grid>
  )
}

export default CustomRadioWithIcons
`}</code></pre>) 

export const CustomRadioBasicJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomRadioBasic from 'src/@core/components/custom-radio/basic'

const data = [
  {
    meta: 'Free',
    title: 'Basic',
    value: 'basic',
    isSelected: true,
    content: 'Get 1 project with 1 team member.'
  },
  {
    meta: '5.00',
    title: 'Premium',
    value: 'premium',
    content: 'Get 5 projects with 5 team members.'
  }
]

const BasicCustomRadio = () => {
  const initialSelected = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1].value

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = prop => {
    if (typeof prop === 'string') {
      setSelected(prop)
    } else {
      setSelected(prop.target.value)
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomRadioBasic
          key={index}
          data={data[index]}
          selected={selected}
          name='custom-radios-basic'
          handleChange={handleChange}
          gridProps={{ sm: 6, xs: 12 }}
        />
      ))}
    </Grid>
  )
}

export default BasicCustomRadio
`}</code></pre>) 

export const CustomRadioImgJSXCode = (<pre className='language-jsx'><code className='language-jsx'>{`// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Demo Components Imports
import CustomRadioImg from 'src/@core/components/custom-radio/image'

const data = [
  {
    value: 'clock',
    isSelected: true,
    img: '/images/pages/background-3.jpg'
  },
  {
    value: 'donuts',
    img: '/images/pages/background-8.jpg'
  },
  {
    value: 'flowers',
    img: '/images/pages/background-5.jpg'
  }
]

const CustomRadioWithImages = () => {
  const initialSelected = data.filter(item => item.isSelected)[data.filter(item => item.isSelected).length - 1].value

  // ** State
  const [selected, setSelected] = useState(initialSelected)

  const handleChange = prop => {
    if (typeof prop === 'string') {
      setSelected(prop)
    } else {
      setSelected(prop.target.value)
    }
  }

  return (
    <Grid container spacing={4}>
      {data.map((item, index) => (
        <CustomRadioImg
          key={index}
          data={data[index]}
          selected={selected}
          name='custom-radios-img'
          handleChange={handleChange}
          gridProps={{ sm: 4, xs: 12 }}
        />
      ))}
    </Grid>
  )
}

export default CustomRadioWithImages
`}</code></pre>) 

