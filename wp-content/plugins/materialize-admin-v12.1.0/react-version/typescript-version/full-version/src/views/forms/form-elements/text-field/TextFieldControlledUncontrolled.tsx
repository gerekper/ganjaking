// ** React Imports
import { ChangeEvent, useState } from 'react'

// ** MUI Imports
import TextField from '@mui/material/TextField'

const TextFieldControlledUncontrolled = () => {
  // ** State
  const [name, setName] = useState<string>('Cat in the Hat')

  const handleChange = (event: ChangeEvent<HTMLInputElement>) => {
    setName(event.target.value)
  }

  return (
    <form className='demo-space-x' noValidate autoComplete='off'>
      <TextField value={name} label='Controlled' onChange={handleChange} id='controlled-text-field' />
      <TextField id='uncontrolled-text-field' label='Uncontrolled' defaultValue='foo' />
    </form>
  )
}

export default TextFieldControlledUncontrolled
