// ** React Imports
import { useState } from 'react'

// ** MUI Imports
import Box from '@mui/material/Box'
import Card from '@mui/material/Card'
import Button from '@mui/material/Button'
import Stepper from '@mui/material/Stepper'
import { styled } from '@mui/material/styles'
import StepLabel from '@mui/material/StepLabel'
import Typography from '@mui/material/Typography'
import MuiStep, { StepProps } from '@mui/material/Step'
import CardContent, { CardContentProps } from '@mui/material/CardContent'

// ** Icon Imports
import Icon from 'src/@core/components/icon'

// ** Custom Components Imports
import StepperCustomDot from 'src/views/forms/form-wizard/StepperCustomDot'

// ** Step Components
import StepPropertyArea from 'src/views/pages/wizard-examples/property-listing/StepPropertyArea'
import StepPriceDetails from 'src/views/pages/wizard-examples/property-listing/StepPriceDetails'
import StepPropertyDetails from 'src/views/pages/wizard-examples/property-listing/StepPropertyDetails'
import StepPersonalDetails from 'src/views/pages/wizard-examples/property-listing/StepPersonalDetails'
import StepPropertyFeatures from 'src/views/pages/wizard-examples/property-listing/StepPropertyFeatures'

// ** Styled Components
import StepperWrapper from 'src/@core/styles/mui/stepper'

const steps = [
  {
    title: 'Personal Details',
    subtitle: 'Your Name/Email',
    icon: 'mdi:account-outline'
  },
  {
    icon: 'mdi:home-outline',
    title: 'Property Details',
    subtitle: 'Property Type'
  },
  {
    icon: 'mdi:star-outline',
    title: 'Property Features',
    subtitle: 'Bedrooms/Floor No'
  },
  {
    title: 'Property Area',
    subtitle: 'Covered Area',
    icon: 'mdi:map-marker-outline'
  },
  {
    title: 'Price Details',
    icon: 'mdi:currency-usd',
    subtitle: 'Expected Price'
  }
]

const Step = styled(MuiStep)<StepProps>(({ theme }) => ({
  '&:not(:last-of-type)': {
    marginBottom: theme.spacing(4)
  },
  '& .MuiStepLabel-root': {
    padding: 0
  }
}))

const StepperHeaderContainer = styled(CardContent)<CardContentProps>(({ theme }) => ({
  minWidth: 300,
  borderRight: `1px solid ${theme.palette.divider}`,
  [theme.breakpoints.down('lg')]: {
    borderRight: 0,
    borderBottom: `1px solid ${theme.palette.divider}`
  }
}))

const PropertyListingWizard = () => {
  // ** States
  const [activeStep, setActiveStep] = useState<number>(0)

  // Handle Stepper
  const handleNext = () => {
    setActiveStep(activeStep + 1)
  }
  const handlePrev = () => {
    if (activeStep !== 0) {
      setActiveStep(activeStep - 1)
    }
  }

  const getStepContent = (step: number) => {
    switch (step) {
      case 0:
        return <StepPersonalDetails />
      case 1:
        return <StepPropertyDetails />
      case 2:
        return <StepPropertyFeatures />
      case 3:
        return <StepPropertyArea />
      case 4:
        return <StepPriceDetails />
      default:
        return null
    }
  }

  const renderContent = () => {
    return getStepContent(activeStep)
  }

  const renderFooter = () => {
    const stepCondition = activeStep === steps.length - 1

    return (
      <Box sx={{ mt: 4, display: 'flex', justifyContent: 'space-between' }}>
        <Button
          color='secondary'
          variant='outlined'
          onClick={handlePrev}
          disabled={activeStep === 0}
          startIcon={<Icon icon='mdi:arrow-left' />}
        >
          Previous
        </Button>
        <Button
          variant='contained'
          color={stepCondition ? 'success' : 'primary'}
          {...(!stepCondition ? { endIcon: <Icon icon='mdi:arrow-right' /> } : {})}
          onClick={() => (stepCondition ? alert('Submitted..!!') : handleNext())}
        >
          {stepCondition ? 'Submit' : 'Next'}
        </Button>
      </Box>
    )
  }

  return (
    <Card sx={{ display: 'flex', flexDirection: { xs: 'column', lg: 'row' } }}>
      <StepperHeaderContainer>
        <StepperWrapper sx={{ height: '100%', '& .MuiStepLabel-label': { cursor: 'pointer' } }}>
          <Stepper connector={<></>} activeStep={activeStep} orientation='vertical'>
            {steps.map((step, index) => {
              return (
                <Step
                  key={index}
                  onClick={() => setActiveStep(index)}
                  sx={{ '&.Mui-completed + svg': { color: 'primary.main' } }}
                >
                  <StepLabel StepIconComponent={StepperCustomDot}>
                    <div className='step-label'>
                      <Typography className='step-number'>{`0${index + 1}`}</Typography>
                      <div>
                        <Typography className='step-title'>{step.title}</Typography>
                        <Typography className='step-subtitle'>{step.subtitle}</Typography>
                      </div>
                    </div>
                  </StepLabel>
                </Step>
              )
            })}
          </Stepper>
        </StepperWrapper>
      </StepperHeaderContainer>
      <div>
        <CardContent>
          {renderContent()}
          {renderFooter()}
        </CardContent>
      </div>
    </Card>
  )
}

export default PropertyListingWizard
