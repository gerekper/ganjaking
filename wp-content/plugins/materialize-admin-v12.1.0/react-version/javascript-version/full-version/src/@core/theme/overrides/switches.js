const Switch = () => {
  return {
    MuiSwitch: {
      styleOverrides: {
        root: ({ theme }) => ({
          '& .MuiSwitch-track': {
            borderRadius: theme.shape.borderRadius
          },
          '& .MuiSwitch-switchBase': {
            '&:not(.Mui-checked)': {
              '& .MuiSwitch-thumb': {
                color: theme.palette.grey[50]
              }
            }
          },
          '& .Mui-disabled + .MuiSwitch-track': {
            backgroundColor: `rgb(${theme.palette.customColors.main})`
          }
        })
      }
    }
  }
}

export default Switch
