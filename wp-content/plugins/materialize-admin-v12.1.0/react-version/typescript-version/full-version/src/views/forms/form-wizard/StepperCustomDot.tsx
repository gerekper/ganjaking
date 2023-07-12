// ** MUI Imports
import MuiBox, { BoxProps } from '@mui/material/Box'
import { StepIconProps } from '@mui/material/StepIcon'
import { alpha, styled, useTheme } from '@mui/material/styles'

// ** Custom Icon Import
import Icon from 'src/@core/components/icon'

// Styled Box component
const Box = styled(MuiBox)<BoxProps>(() => ({
  width: 20,
  height: 20,
  borderWidth: 3,
  borderRadius: '50%',
  borderStyle: 'solid'
}))

const StepperCustomDot = (props: StepIconProps) => {
  // ** Props
  const { active, completed, error } = props

  // ** Hooks
  const theme = useTheme()

  if (error) {
    return <Icon icon='mdi:alert' fontSize={20} color={theme.palette.error.main} transform='scale(1.2)' />
  } else if (completed) {
    return <Icon icon='mdi:check-circle' fontSize={20} color={theme.palette.primary.main} transform='scale(1.2)' />
  } else {
    return (
      <Box
        sx={{
          borderColor: active ? 'primary.main' : alpha(theme.palette.primary.main, 0.3)
        }}
      />
    )
  }
}

export default StepperCustomDot
