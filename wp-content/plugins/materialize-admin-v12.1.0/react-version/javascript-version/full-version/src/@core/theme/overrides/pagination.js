// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const Pagination = () => {
  return {
    MuiPaginationItem: {
      styleOverrides: {
        outlined: ({ theme }) => ({
          borderColor: `rgba(${theme.palette.customColors.main}, 0.22)`
        }),
        outlinedPrimary: ({ theme }) => ({
          '&.Mui-selected': {
            backgroundColor: hexToRGBA(theme.palette.primary.main, 0.12),
            '&:hover': {
              backgroundColor: `${hexToRGBA(theme.palette.primary.main, 0.24)} !important`
            }
          }
        }),
        outlinedSecondary: ({ theme }) => ({
          '&.Mui-selected': {
            backgroundColor: hexToRGBA(theme.palette.secondary.main, 0.12),
            '&:hover': {
              backgroundColor: `${hexToRGBA(theme.palette.secondary.main, 0.24)} !important`
            }
          }
        }),
        rounded: {
          borderRadius: 8
        }
      }
    }
  }
}

export default Pagination
