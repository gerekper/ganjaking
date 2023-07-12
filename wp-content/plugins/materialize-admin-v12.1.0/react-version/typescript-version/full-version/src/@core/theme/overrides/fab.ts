// ** Type Import
import { OwnerStateThemeType } from './'

const Button = () => {
  return {
    MuiFab: {
      styleOverrides: {
        root: ({ theme }: OwnerStateThemeType) => ({
          boxShadow: theme.shadows[5]
        })
      }
    }
  }
}

export default Button
