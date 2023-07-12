// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Grid from '@mui/material/Grid'

// ** Type Import
import { CustomCheckboxIconsData, CustomCheckboxIconsProps } from 'src/@core/components/custom-checkbox/types'

// ** Demo Components Imports
import CustomCheckboxIcons from 'src/@core/components/custom-checkbox/icons'

interface IconType {
  icon: CustomCheckboxIconsProps['icon']
  iconProps: CustomCheckboxIconsProps['iconProps']
}

const data: CustomCheckboxIconsData[] = [
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

const icons: IconType[] = [
  { icon: 'mdi:server', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } },
  { icon: 'mdi:shield-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } },
  { icon: 'mdi:lock-outline', iconProps: { fontSize: '2rem', style: { marginBottom: 8 } } }
]

const CustomCheckboxWithIcons = () => {
  const initialSelected: string[] = data.filter(item => item.isSelected).map(item => item.value)

  // ** State
  const [selected, setSelected] = useState<string[]>(initialSelected)

  const handleChange = (value: string) => {
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
