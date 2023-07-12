// ** Demo Components Imports
import CreateDeal from 'src/views/pages/wizard-examples/create-deal'

// ** Custom Component
import DatePickerWrapper from 'src/@core/styles/libs/react-datepicker'

const WizardExamples = () => {
  return (
    <DatePickerWrapper>
      <CreateDeal />
    </DatePickerWrapper>
  )
}

export default WizardExamples
