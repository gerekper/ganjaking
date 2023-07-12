// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Card from '@mui/material/Card'
import Step from '@mui/material/Step'
import Divider from '@mui/material/Divider'
import { styled } from '@mui/material/styles'
import StepLabel from '@mui/material/StepLabel'
import Typography from '@mui/material/Typography'
import CardContent from '@mui/material/CardContent'
import MuiStepper, { StepperProps } from '@mui/material/Stepper'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Step Components
import StepCart from 'src/views/pages/wizard-examples/checkout/StepCart'
import StepAddress from 'src/views/pages/wizard-examples/checkout/StepAddress'
import StepPayment from 'src/views/pages/wizard-examples/checkout/StepPayment'
import StepConfirmation from 'src/views/pages/wizard-examples/checkout/StepConfirmation'

// ** Styled Components
import StepperWrapper from 'src/@core/styles/mui/stepper'

const steps = [
  {
    title: 'Cart',
    icon: (
      <svg id='wizardCart' width='56' height='56' viewBox='0 0 58 54' xmlns='http://www.w3.org/2000/svg'>
        <g fillRule='nonzero'>
          <path d='M57.927 34.29V16.765a4 4 0 0 0-4-4h-4.836a.98.98 0 1 0 0 1.963h3.873a3 3 0 0 1 3 3v15.6a3 3 0 0 1-3 3H14.8a4 4 0 0 1-4-4v-14.6a3 3 0 0 1 3-3h3.873a.98.98 0 1 0 0-1.963H10.8V4.909a.98.98 0 0 0-.982-.982H7.715C7.276 2.24 5.752.982 3.927.982A3.931 3.931 0 0 0 0 4.909a3.931 3.931 0 0 0 3.927 3.927c1.825 0 3.35-1.256 3.788-2.945h1.121v38.29a.98.98 0 0 0 .982.983h6.903c-1.202.895-1.994 2.316-1.994 3.927A4.915 4.915 0 0 0 19.637 54a4.915 4.915 0 0 0 4.908-4.91c0-1.61-.79-3.03-1.994-3.926h17.734c-1.203.895-1.994 2.316-1.994 3.927A4.915 4.915 0 0 0 43.2 54a4.915 4.915 0 0 0 4.91-4.91c0-1.61-.792-3.03-1.995-3.926h5.921a.98.98 0 1 0 0-1.964H10.8v-4.91h43.127a4 4 0 0 0 4-4zm-54-27.417a1.966 1.966 0 0 1-1.963-1.964c0-1.083.88-1.964 1.963-1.964.724 0 1.35.398 1.691.982h-.709a.98.98 0 1 0 0 1.964h.709c-.34.584-.967.982-1.69.982zm15.71 45.163a2.949 2.949 0 0 1-2.946-2.945 2.949 2.949 0 0 1 2.945-2.946 2.95 2.95 0 0 1 2.946 2.946 2.949 2.949 0 0 1-2.946 2.945zm23.563 0a2.949 2.949 0 0 1-2.945-2.945 2.949 2.949 0 0 1 2.945-2.946 2.949 2.949 0 0 1 2.945 2.946 2.949 2.949 0 0 1-2.945 2.945z' />
          <path d='M33.382 27.49c7.58 0 13.745-6.165 13.745-13.745C47.127 6.165 40.961 0 33.382 0c-7.58 0-13.746 6.166-13.746 13.745 0 7.58 6.166 13.746 13.746 13.746zm0-25.526c6.497 0 11.782 5.285 11.782 11.781 0 6.497-5.285 11.782-11.782 11.782S21.6 20.242 21.6 13.745c0-6.496 5.285-11.781 11.782-11.781z' />
          <path d='M31.77 19.41c.064.052.136.083.208.117.03.015.056.039.086.05a.982.982 0 0 0 .736-.027c.049-.023.085-.066.13-.095.07-.046.145-.083.202-.149l.02-.021.001-.001.001-.002 7.832-8.812a.98.98 0 1 0-1.467-1.304l-7.222 8.126-5.16-4.3a.983.983 0 0 0-1.258 1.508l5.892 4.91z' />
        </g>
      </svg>
    )
  },
  {
    title: 'Address',
    icon: (
      <svg id='wizardCheckoutAddress' width='56' height='56' viewBox='0 0 54 54' xmlns='http://www.w3.org/2000/svg'>
        <g fillRule='nonzero'>
          <path d='M54 7.2V4a4 4 0 0 0-4-4H4a4 4 0 0 0-4 4v3.2h1.8v36H.9a.9.9 0 1 0 0 1.8h25.2v1.8c0 .042.019.08.024.12A3.596 3.596 0 0 0 23.4 50.4c0 1.985 1.615 3.6 3.6 3.6s3.6-1.615 3.6-3.6a3.596 3.596 0 0 0-2.724-3.48c.005-.04.024-.078.024-.12V45h25.2a.9.9 0 1 0 0-1.8h-.9v-36H54zM28.8 50.4c0 .993-.807 1.8-1.8 1.8s-1.8-.807-1.8-1.8.807-1.8 1.8-1.8 1.8.807 1.8 1.8zM5.4 1.8h43.2a3.6 3.6 0 0 1 3.6 3.6H1.8a3.6 3.6 0 0 1 3.6-3.6zm43 41.4H5.6a2 2 0 0 1-2-2v-32a2 2 0 0 1 2-2h42.8a2 2 0 0 1 2 2v32a2 2 0 0 1-2 2z' />
          <path d='M45 36.9H31.5a.9.9 0 1 0 0 1.8H45a.9.9 0 1 0 0-1.8zM9 32.4h9a.9.9 0 1 0 0-1.8H9a.9.9 0 1 0 0 1.8zM27 32.4h13.5a.9.9 0 1 0 0-1.8H27a.9.9 0 1 0 0 1.8zM21.861 30.861a.926.926 0 0 0-.261.639c0 .234.099.468.261.639a.946.946 0 0 0 .639.261.946.946 0 0 0 .639-.261.946.946 0 0 0 .261-.639.945.945 0 0 0-.261-.639c-.333-.333-.945-.333-1.278 0zM27 36.9H13.5a.9.9 0 1 0 0 1.8H27a.9.9 0 1 0 0-1.8zM9 38.7a.946.946 0 0 0 .639-.261.946.946 0 0 0 .261-.639.906.906 0 0 0-.261-.63c-.333-.342-.936-.342-1.278-.009a.945.945 0 0 0-.261.639c0 .234.099.468.261.639A.946.946 0 0 0 9 38.7zM44.361 30.861a.926.926 0 0 0-.261.639c0 .234.099.468.261.639A.946.946 0 0 0 45 32.4a.946.946 0 0 0 .639-.261.946.946 0 0 0 .261-.639.945.945 0 0 0-.261-.639c-.333-.333-.936-.333-1.278 0zM45 18H31.5a.9.9 0 1 0 0 1.8H45a.9.9 0 1 0 0-1.8zM45 24.3h-9a.9.9 0 1 0 0 1.8h9a.9.9 0 1 0 0-1.8zM27 26.1h1.8a.9.9 0 1 0 0-1.8H27a.9.9 0 1 0 0 1.8zM27 13.5h13.5a.9.9 0 1 0 0-1.8H27a.9.9 0 1 0 0 1.8zM45 13.5a.946.946 0 0 0 .639-.261.906.906 0 0 0 .261-.639.905.905 0 0 0-.261-.639c-.342-.333-.945-.333-1.278 0a.945.945 0 0 0-.261.639c0 .234.099.468.261.639A.946.946 0 0 0 45 13.5zM27.261 18.261A.926.926 0 0 0 27 18.9c0 .234.099.468.261.639a.946.946 0 0 0 .639.261.946.946 0 0 0 .639-.261.946.946 0 0 0 .261-.639.926.926 0 0 0-.261-.639.942.942 0 0 0-1.278 0zM31.761 24.561a.945.945 0 0 0-.261.639c0 .234.099.468.261.639a.946.946 0 0 0 .639.261.946.946 0 0 0 .639-.261.946.946 0 0 0 .261-.639.945.945 0 0 0-.261-.639c-.333-.333-.945-.333-1.278 0zM22.5 11.7H8.1v14.4h14.4V11.7zm-1.8 12.6H9.9V13.5h10.8v10.8z' />
        </g>
      </svg>
    )
  },
  {
    title: 'Payment',
    icon: (
      <svg id='wizardPayment' width='56' height='56' viewBox='0 0 58 54' xmlns='http://www.w3.org/2000/svg'>
        <g fillRule='nonzero'>
          <path d='M8.679 23.143h7.714V13.5H8.679v9.643zm1.928-7.714h3.857v5.785h-3.857V15.43zM8.679 34.714h7.714v-9.643H8.679v9.643zM10.607 27h3.857v5.786h-3.857V27zM8.679 46.286h7.714v-9.643H8.679v9.643zm1.928-7.715h3.857v5.786h-3.857v-5.786zM34.714 22.179a.963.963 0 0 0-.964.964v8.678a.963.963 0 1 0 1.929 0v-8.678a.963.963 0 0 0-.965-.964zM34.714 34.714a.963.963 0 0 0-.964.965v8.678a.963.963 0 1 0 1.929 0V35.68a.963.963 0 0 0-.965-.965zM29.893 22.179a.963.963 0 0 0-.964.964v.964a.963.963 0 1 0 1.928 0v-.964a.963.963 0 0 0-.964-.964zM29.893 27a.963.963 0 0 0-.964.964v1.929a.963.963 0 1 0 1.928 0v-1.929a.963.963 0 0 0-.964-.964zM29.893 32.786a.963.963 0 0 0-.964.964v.964a.963.963 0 1 0 1.928 0v-.964a.963.963 0 0 0-.964-.964zM29.893 37.607a.963.963 0 0 0-.964.964V40.5a.963.963 0 1 0 1.928 0v-1.929a.963.963 0 0 0-.964-.964zM29.208 43.672c-.174.183-.28.434-.28.685 0 .26.106.502.28.685.182.173.434.28.685.28.25 0 .501-.107.684-.28a.996.996 0 0 0 .28-.685c0-.25-.106-.502-.28-.684a1 1 0 0 0-1.369 0z' />
          <path d='M42.286 0H4a4 4 0 0 0-4 4v2.75h2.893v43.184A4.071 4.071 0 0 0 6.959 54h32.367a4.071 4.071 0 0 0 4.067-4.066V6.75h2.893V4a4 4 0 0 0-4-4zm-.822 49.934a2.14 2.14 0 0 1-2.138 2.137H6.96a2.14 2.14 0 0 1-2.138-2.137V4.82H8.68v6.75h7.714v-6.75h8.678v11.326c0 1.199.976 2.174 2.175 2.174h9.151a2.177 2.177 0 0 0 2.174-2.174V4.821h2.893v45.113zM10.607 4.82h3.857v4.822h-3.857V4.82zm22.179 0V6.75h-1.929V4.821h1.929zm3.857 0v1.954c-.082-.01-.162-.025-.246-.025h-1.683V4.821h1.929zm-9.397 11.572a.246.246 0 0 1-.246-.246v-3.636c.082.01.162.025.246.025h1.683v3.857h-1.683zm3.611-3.857h1.683c.136 0 .246.11.246.246v3.365c0 .084.015.164.025.246h-1.954v-3.857zm3.857 3.611v-3.365a2.177 2.177 0 0 0-2.174-2.175h-5.294a.246.246 0 0 1-.246-.246V8.924c0-.135.11-.245.246-.245h9.151c.136 0 .246.11.246.245v7.223c0 .136-.11.246-.246.246H34.96a.246.246 0 0 1-.246-.246zM28.93 6.75h-1.683c-.084 0-.164.015-.246.025V4.821h1.929V6.75zm15.428-1.929h-.964V2.893h-40.5V4.82h-.964V2.93a1 1 0 0 1 1-1h40.428a1 1 0 0 1 1 1V4.82z' />
          <path d='m57.575 31.14-5.785-5.785a.965.965 0 0 0-1.365 0L44.64 31.14a.963.963 0 1 0 1.363 1.363l4.14-4.14v24.673a.963.963 0 1 0 1.928 0V28.363l4.14 4.14a.962.962 0 0 0 1.364 0 .963.963 0 0 0 0-1.363z' />
        </g>
      </svg>
    )
  },
  {
    title: 'Confirmations',
    icon: (
      <svg id='wizardConfirm' width='56' height='56' viewBox='0 0 58 54' xmlns='http://www.w3.org/2000/svg'>
        <g fillRule='nonzero'>
          <path d='M7.2 14.4h13.5a.9.9 0 1 0 0-1.8H7.2a.9.9 0 1 0 0 1.8zM7.2 11.7h8.1a.9.9 0 1 0 0-1.8H7.2a.9.9 0 1 0 0 1.8zM21.6 16.2a.9.9 0 0 0-.9-.9H7.2a.9.9 0 1 0 0 1.8h13.5a.9.9 0 0 0 .9-.9z' />
          <path d='M49 3.6H27.9V.9a.9.9 0 1 0-1.8 0v2.7H5a5 5 0 0 0-5 5v27.8a5 5 0 0 0 5 5h19.827L13.764 52.464a.899.899 0 1 0 1.272 1.272L26.1 42.673V51.3a.9.9 0 1 0 1.8 0v-8.627l11.064 11.063a.898.898 0 0 0 1.272 0 .899.899 0 0 0 0-1.272L29.173 41.4H49a5 5 0 0 0 5-5V8.6a5 5 0 0 0-5-5zm-.8 36H5.8a4 4 0 0 1-4-4V9.4a4 4 0 0 1 4-4h42.4a4 4 0 0 1 4 4v26.2a4 4 0 0 1-4 4z' />
          <path d='M36.9 18h4.127L30.24 28.787l-7.464-7.463a.899.899 0 0 0-1.272 0l-11.34 11.34a.899.899 0 1 0 1.272 1.272L22.14 23.233l7.464 7.463a.898.898 0 0 0 1.272 0L42.3 19.273V23.4a.9.9 0 1 0 1.8 0v-6.3a.897.897 0 0 0-.9-.9h-6.3a.9.9 0 1 0 0 1.8z' />
        </g>
      </svg>
    )
  }
]

const Stepper = styled(MuiStepper)<StepperProps>(({ theme }) => ({
  margin: 'auto',
  maxWidth: 800,
  justifyContent: 'space-around',

  '& .MuiStep-root': {
    cursor: 'pointer',
    textAlign: 'center',
    paddingBottom: theme.spacing(8),
    '& .step-title': {
      fontSize: '1rem'
    },
    '&.Mui-completed + svg': {
      color: theme.palette.primary.main
    },
    '& + svg': {
      display: 'none',
      color: theme.palette.text.disabled
    },

    '& .MuiStepLabel-label': {
      display: 'flex',
      cursor: 'pointer',
      alignItems: 'center',
      svg: {
        marginRight: theme.spacing(1.5),
        fill: theme.palette.text.primary
      },
      '&.Mui-active, &.Mui-completed': {
        '& .MuiTypography-root': {
          color: theme.palette.primary.main
        },
        '& svg': {
          fill: theme.palette.primary.main
        }
      }
    },

    [theme.breakpoints.up('md')]: {
      paddingBottom: 0,
      '& + svg': {
        display: 'block'
      },
      '& .MuiStepLabel-label': {
        display: 'block'
      }
    }
  }
}))

const CheckoutWizard = () => {
  // ** States
  const [activeStep, setActiveStep] = useState<number>(0)

  // Handle Stepper
  const handleNext = () => {
    setActiveStep(activeStep + 1)
  }

  const getStepContent = (step: number) => {
    switch (step) {
      case 0:
        return <StepCart handleNext={handleNext} />
      case 1:
        return <StepAddress handleNext={handleNext} />
      case 2:
        return <StepPayment handleNext={handleNext} />
      case 3:
        return <StepConfirmation />
      default:
        return null
    }
  }

  const renderContent = () => {
    return getStepContent(activeStep)
  }

  return (
    <Card>
      <CardContent sx={{ py: 5.375 }}>
        <StepperWrapper>
          <Stepper activeStep={activeStep} connector={<Icon icon='mdi:chevron-right' />}>
            {steps.map((step, index) => {
              return (
                <Step key={index} onClick={() => setActiveStep(index)} sx={{}}>
                  <StepLabel icon={<></>}>
                    {step.icon}
                    <Typography className='step-title'>{step.title}</Typography>
                  </StepLabel>
                </Step>
              )
            })}
          </Stepper>
        </StepperWrapper>
      </CardContent>

      <Divider sx={{ m: '0 !important' }} />

      <CardContent>{renderContent()}</CardContent>
    </Card>
  )
}

export default CheckoutWizard
