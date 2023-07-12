// ** Custom Components Imports
import CustomChip from 'src/@core/components/mui/chip'

const ChipsRounded = () => {
  return (
    <div className='demo-space-x'>
      <CustomChip rounded label='Primary' skin='light' color='primary' />
      <CustomChip rounded label='Secondary' skin='light' color='secondary' />
      <CustomChip rounded label='Success' skin='light' color='success' />
      <CustomChip rounded label='Error' skin='light' color='error' />
      <CustomChip rounded label='Warning' skin='light' color='warning' />
      <CustomChip rounded label='Info' skin='light' color='info' />
    </div>
  )
}

export default ChipsRounded
