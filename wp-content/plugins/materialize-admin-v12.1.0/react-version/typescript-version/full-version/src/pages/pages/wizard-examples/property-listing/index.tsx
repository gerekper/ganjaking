// ** Demo Components Imports
import PropertyListing from 'src/views/pages/wizard-examples/property-listing'

// ** Custom Component
import DatePickerWrapper from 'src/@core/styles/libs/react-datepicker'

const WizardExamples = () => {
  return (
    <DatePickerWrapper>
      <PropertyListing />
    </DatePickerWrapper>
  )
}

export default WizardExamples
