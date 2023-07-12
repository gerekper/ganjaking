// ** Type Import
import { OwnerStateThemeType } from './'

const Rating = () => {
  return {
    MuiRating: {
      styleOverrides: {
        root: ({ theme }: OwnerStateThemeType) => ({
          color: theme.palette.warning.main,
          '& svg': {
            flexShrink: 0
          }
        }),
        iconEmpty: ({ theme }: OwnerStateThemeType) => ({
          color: `rgba(${theme.palette.customColors.main}, 0.22)`
        })
      }
    }
  }
}

export default Rating
