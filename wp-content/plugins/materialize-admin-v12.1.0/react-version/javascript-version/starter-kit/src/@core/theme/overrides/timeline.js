// ** Util Import
import { hexToRGBA } from 'src/@core/utils/hex-to-rgba'

const Timeline = () => {
  return {
    MuiTimelineItem: {
      styleOverrides: {
        root: ({ theme }) => ({
          '&:not(:last-of-type)': {
            '& .MuiTimelineContent-root': {
              marginBottom: theme.spacing(4)
            }
          }
        })
      }
    },
    MuiTimelineConnector: {
      styleOverrides: {
        root: ({ theme }) => ({
          backgroundColor: theme.palette.divider
        })
      }
    },
    MuiTimelineContent: {
      styleOverrides: {
        root: ({ theme }) => ({
          marginTop: theme.spacing(0.5)
        })
      }
    },
    MuiTimelineDot: {
      styleOverrides: {
        filledPrimary: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.primary.main, 0.12)}`
        }),
        filledSecondary: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.secondary.main, 0.12)}`
        }),
        filledSuccess: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.success.main, 0.12)}`
        }),
        filledError: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.error.main, 0.12)}`
        }),
        filledWarning: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.warning.main, 0.12)}`
        }),
        filledInfo: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.info.main, 0.12)}`
        }),
        filledGrey: ({ theme }) => ({
          boxShadow: `0 0 0 3px ${hexToRGBA(theme.palette.grey[400], 0.12)}`
        }),
        outlinedPrimary: ({ theme }) => ({
          '& svg': { color: theme.palette.primary.main }
        }),
        outlinedSecondary: ({ theme }) => ({
          '& svg': { color: theme.palette.secondary.main }
        }),
        outlinedSuccess: ({ theme }) => ({
          '& svg': { color: theme.palette.success.main }
        }),
        outlinedError: ({ theme }) => ({
          '& svg': { color: theme.palette.error.main }
        }),
        outlinedWarning: ({ theme }) => ({
          '& svg': { color: theme.palette.warning.main }
        }),
        outlinedInfo: ({ theme }) => ({
          '& svg': { color: theme.palette.info.main }
        }),
        outlinedGrey: ({ theme }) => ({
          '& svg': { color: theme.palette.grey[400] }
        })
      }
    }
  }
}

export default Timeline
