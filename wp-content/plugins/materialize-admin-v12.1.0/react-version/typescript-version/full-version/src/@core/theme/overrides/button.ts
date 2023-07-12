// ** Type Import
import { OwnerStateThemeType } from './'

// ** Theme Config Imports
import themeConfig from 'src/configs/themeConfig'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const Button = () => {
  return {
    MuiButton: {
      styleOverrides: {
        root: ({ ownerState, theme }: OwnerStateThemeType) => ({
          fontWeight: 500,
          borderRadius: 8,
          lineHeight: 1.715,
          ...(ownerState.size === 'medium' &&
            ownerState.variant === 'text' && {
              padding: `${theme.spacing(1.75, 3)}`
            }),
          '&.MuiButton-textPrimary:hover': {
            backgroundColor: hexToRGBA(theme.palette.primary.main, 0.08)
          },
          '&.MuiButton-textSecondary:hover': {
            backgroundColor: hexToRGBA(theme.palette.secondary.main, 0.08)
          },
          '&.MuiButton-textSuccess:hover': {
            backgroundColor: hexToRGBA(theme.palette.success.main, 0.08)
          },
          '&.MuiButton-textError:hover': {
            backgroundColor: hexToRGBA(theme.palette.error.main, 0.08)
          },
          '&.MuiButton-textWarning:hover': {
            backgroundColor: hexToRGBA(theme.palette.warning.main, 0.08)
          },
          '&.MuiButton-textInfo:hover': {
            backgroundColor: hexToRGBA(theme.palette.info.main, 0.08)
          }
        }),
        contained: ({ theme }: OwnerStateThemeType) => ({
          boxShadow: theme.shadows[3],
          padding: `${theme.spacing(1.75, 5.5)}`
        }),
        outlined: ({ theme }: OwnerStateThemeType) => ({
          lineHeight: 1.572,
          padding: `${theme.spacing(1.75, 5.25)}`,
          '&.MuiButton-outlinedPrimary:hover': {
            backgroundColor: hexToRGBA(theme.palette.primary.main, 0.08)
          },
          '&.MuiButton-outlinedSecondary:hover': {
            backgroundColor: hexToRGBA(theme.palette.secondary.main, 0.08)
          },
          '&.MuiButton-outlinedSuccess:hover': {
            backgroundColor: hexToRGBA(theme.palette.success.main, 0.08)
          },
          '&.MuiButton-outlinedError:hover': {
            backgroundColor: hexToRGBA(theme.palette.error.main, 0.08)
          },
          '&.MuiButton-outlinedWarning:hover': {
            backgroundColor: hexToRGBA(theme.palette.warning.main, 0.08)
          },
          '&.MuiButton-outlinedInfo:hover': {
            backgroundColor: hexToRGBA(theme.palette.info.main, 0.08)
          }
        }),
        sizeSmall: ({ ownerState, theme }: OwnerStateThemeType) => ({
          lineHeight: 1.693,
          ...(ownerState.variant === 'text' && {
            padding: `${theme.spacing(1, 2.25)}`
          }),
          ...(ownerState.variant === 'contained' && {
            padding: `${theme.spacing(1, 3.25)}`
          }),
          ...(ownerState.variant === 'outlined' && {
            lineHeight: 1.539,
            padding: `${theme.spacing(1, 3)}`
          })
        }),
        sizeLarge: ({ ownerState, theme }: OwnerStateThemeType) => ({
          lineHeight: 1.734,
          ...(ownerState.variant === 'text' && {
            padding: `${theme.spacing(2, 5.5)}`
          }),
          ...(ownerState.variant === 'contained' && {
            padding: `${theme.spacing(2, 6.5)}`
          }),
          ...(ownerState.variant === 'outlined' && {
            lineHeight: 1.6,
            padding: `${theme.spacing(2, 6.25)}`
          })
        })
      }
    },
    MuiButtonBase: {
      defaultProps: {
        disableRipple: themeConfig.disableRipple
      }
    }
  }
}

export default Button
