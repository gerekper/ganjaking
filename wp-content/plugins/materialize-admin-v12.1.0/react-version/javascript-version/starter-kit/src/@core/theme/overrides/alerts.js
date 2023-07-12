// ** MUI Imports
import { lighten, darken } from '@mui/material/styles'

// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const Alert = mode => {
  const getColor = mode === 'dark' ? lighten : darken

  return {
    MuiAlert: {
      styleOverrides: {
        root: ({ theme }) => ({
          borderRadius: 8,
          '& .MuiAlertTitle-root': {
            marginBottom: theme.spacing(1)
          },
          '& a': {
            fontWeight: 500,
            color: 'inherit'
          }
        }),
        standardSuccess: ({ theme }) => ({
          color: getColor(theme.palette.success.main, 0.1),
          backgroundColor: hexToRGBA(theme.palette.success.main, 0.12),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.success.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: getColor(theme.palette.success.main, 0.1)
          }
        }),
        standardInfo: ({ theme }) => ({
          color: getColor(theme.palette.info.main, 0.1),
          backgroundColor: hexToRGBA(theme.palette.info.main, 0.12),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.info.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: getColor(theme.palette.info.main, 0.1)
          }
        }),
        standardWarning: ({ theme }) => ({
          color: getColor(theme.palette.warning.main, 0.1),
          backgroundColor: hexToRGBA(theme.palette.warning.main, 0.12),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.warning.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: getColor(theme.palette.warning.main, 0.1)
          }
        }),
        standardError: ({ theme }) => ({
          color: getColor(theme.palette.error.main, 0.1),
          backgroundColor: hexToRGBA(theme.palette.error.main, 0.12),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.error.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: getColor(theme.palette.error.main, 0.1)
          }
        }),
        outlinedSuccess: ({ theme }) => ({
          borderColor: theme.palette.success.main,
          color: getColor(theme.palette.success.main, 0.1),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.success.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: theme.palette.success.main
          }
        }),
        outlinedInfo: ({ theme }) => ({
          borderColor: theme.palette.info.main,
          color: getColor(theme.palette.info.main, 0.1),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.info.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: theme.palette.info.main
          }
        }),
        outlinedWarning: ({ theme }) => ({
          borderColor: theme.palette.warning.main,
          color: getColor(theme.palette.warning.main, 0.1),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.warning.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: theme.palette.warning.main
          }
        }),
        outlinedError: ({ theme }) => ({
          borderColor: theme.palette.error.main,
          color: getColor(theme.palette.error.main, 0.1),
          '& .MuiAlertTitle-root': {
            color: getColor(theme.palette.error.main, 0.1)
          },
          '& .MuiAlert-icon': {
            color: theme.palette.error.main
          }
        }),
        filled: ({ theme }) => ({
          fontWeight: 400,
          color: theme.palette.common.white
        })
      }
    }
  }
}

export default Alert
