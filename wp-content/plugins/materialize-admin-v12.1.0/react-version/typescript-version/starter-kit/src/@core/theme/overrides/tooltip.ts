// ** Type Import
import { OwnerStateThemeType } from './'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const Tooltip = () => {
  return {
    MuiTooltip: {
      styleOverrides: {
        tooltip: ({ theme }: OwnerStateThemeType) => ({
          borderRadius: 6,
          lineHeight: 1.455,
          backgroundColor: hexToRGBA(theme.palette.customColors.tooltipBg, 0.9)
        }),
        arrow: ({ theme }: OwnerStateThemeType) => ({
          color: hexToRGBA(theme.palette.customColors.tooltipBg, 0.9)
        })
      }
    }
  }
}

export default Tooltip
