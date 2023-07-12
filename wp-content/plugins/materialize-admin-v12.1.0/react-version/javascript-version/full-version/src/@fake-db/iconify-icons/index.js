// ** Mock Adapter
import mock from 'src/@fake-db/mock'

const icons = [
  { icon: 'airplane' },
  { icon: 'ab-testing' },
  { icon: 'widgets-outline' },
  { icon: 'whatsapp' },
  { icon: 'water-well-outline' }
]
mock.onGet('/api/icons/data').reply(() => {
  return [200, icons]
})
