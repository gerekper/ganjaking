import type { UserAbility } from '@/plugins/casl/AppAbility';

// 👉 Help center
export type HelpCenterSubcategoryArticlesType = {
  slug: string
  title: string
  content: string
}
export type HelpCenterSubcategoriesType = {
  icon: string
  slug: string
  title: string
  articles: HelpCenterSubcategoryArticlesType[]
}
export type HelpCenterCategoriesType = {
  icon: string
  slug: string
  title: string
  avatarColor: string
  subCategories: HelpCenterSubcategoriesType[]
}
export type HelpCenterArticlesOverviewType = {
  img: string
  slug: string
  title: string
  subtitle: string
}

export interface Faq {
  question: string
  answer: string
}

export interface FaqCategory {
  faqTitle: string
  faqIcon: string
  faqSubtitle: string
  faqs: Faq[]
}

export type ProfileChip = {
  title: string
  color: string
}

export type ProfileTabCommon = {
  icon: string
  value: string
  property: string
}
export type ProfileTeams = ProfileTabCommon & { color: string }

export type ProfileConnections = {
  name: string
  avatar: string
  isFriend: boolean
  connections: string
}

export type ProfileAvatarGroup = {
  name: string
  avatar: string
}

export type ProfileTeamsTech = {
  title: string
  avatar: string
  members: number
  chipText: string
  ChipColor: string
}

export type ConnectionsTab = {
  name: string
  tasks: string
  avatar: string
  projects: string
  connections: string
  designation: string
  isConnected: boolean
  chips: ProfileChip[]
}

export type ProfileTab = {
  teams: ProfileTeams[]
  about: ProfileTabCommon[]
  contacts: ProfileTabCommon[]
  overview: ProfileTabCommon[]
  teamsTech: ProfileTeamsTech[]
  connections: ProfileConnections[]
}

export type ProfileHeader = {
  fullName: string
  coverImg: string
  location: string
  profileImg: string
  joiningDate: string
  designation: string
  designationIcon?: string
}

export type ProjectTableRow = {
  id: number
  date: string
  name: string
  leader: string
  status: number
  avatar?: string
  avatarGroup: string[]
  avatarColor?: string
}

export type ProjectsTab = {
  hours: string
  tasks: string
  title: string
  budget: string
  client: string
  avatar: string
  members: string
  daysLeft: number
  comments: number
  deadline: string
  startDate: string
  totalTask: number
  budgetSpent: string
  description: string
  chipColor: string
  completedTask: number
  avatarColor?: string
  avatarGroup: ProfileAvatarGroup[]
}

export type TeamsTab = {
  title: string
  avatar: string
  description: string
  extraMembers: number
  chips: ProfileChip[]
  avatarGroup: ProfileAvatarGroup[]
}

export type ProfileTab = {
  teams: ProfileTeams[]
  about: ProfileTabCommon[]
  contacts: ProfileTabCommon[]
  overview: ProfileTabCommon[]
  teamsTech: ProfileTeamsTech[]
  connections: ProfileConnections[]
}

// SECTION
// 👉 JWT

export interface User {
  id: number
  fullName?: string
  username: string
  password: string
  avatar?: string
  email: string
  role: string
  abilities: UserAbility[]
}

export interface UserOut {
  userAbilities: User['abilities']
  accessToken: string
  userData: Omit<User, 'abilities' | 'password'>
}

export interface LoginResponse {
  accessToken: string
  userData: AuthUserOut
  userAbilities: UserAbility[]
}

export interface RegisterResponse {
  accessToken: string
  userData: AuthUserOut
  userAbilities: UserAbility[]
}

// !SECTION

// SECTION
// App: User
export interface UserProperties {
  id: number
  fullName: string
  company: string
  role: string
  username: string
  country: string
  contact: string
  email: string
  currentPlan: string
  status: string
  avatar: string
}
// !SECTION

// SECTION App: Calendar
export interface CalendarEvent {
  id: string
  url: string
  title: string
  start: Date
  end: Date
  allDay: boolean
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  extendedProps: Record<string, any>
}
// !SECTION

// SECTION App: Invoice

// 👉 Client
export interface Client {
  address: string
  company: string
  companyEmail: string
  country: string
  contact: string
  name: string
}

// 👉 Invoice
export interface Invoice {
    id: number,
    issuedDate: string
    client: Client
    service: string
    total: number
    avatar: string
    invoiceStatus: string
    balance: number
    dueDate: string
}

// 👉 PaymentDetails
export interface PaymentDetails {
  totalDue: string
  bankName: string
  country: string,
  iban: string,
  swiftCode: string,
}

// !SECTION App: Invoice

// SECTION App: Email

export type EmailFolder = 'inbox' | 'sent' | 'draft' | 'spam'
export type EmailFilter = EmailFolder | 'trashed' | 'starred'
export type EmailLabel = 'personal' | 'company' | 'important' | 'private'

export interface EmailTo {
  email: string
  name: string
}

export interface EmailFrom {
  email: string
  name: string
  avatar: any
}

export interface EmailAttachment {
  fileName: string
  thumbnail: any
  url: string
  size: string
}

/*
  - You can have draft mail in your inbox
    - We can have flag isDraft for mail
  - You can't move sent mail to inbox
  - You can move sent mail to inbox

  --- above are gmail notes

  - We will provide inbox, spam & sent as folders
    - You can't move any mail in sent folder. Sent mail can be deleted or retrieved back
  - We will provide isDraft, isSpam, isTrash as flags
  - draft is flag
  - trash is flag
  - spam email can be moved to inbox only
  - We will provide isDeleted flag

  === this is too confusing 😔

  // this is final now 💯
  folders => inbox, sent, draft, spam
  flags: starred, trash
*/
export interface Email {
  id: number
  to: EmailTo[]
  from: EmailFrom
  subject: string
  cc: string[]
  bcc: string[]
  message: string
  attachments: EmailAttachment[]
  time: string
  replies: Email[]

  labels: EmailLabel[]

  folder: EmailFolder

  // Flags 🚩
  isRead: boolean
  isStarred: boolean
  isDeleted: boolean
}

export interface FetchEmailsPayload {
  q?: string
  filter?: EmailFilter
  label?: EmailLabel
}

// !SECTION Apps: Email

// SECTION App: Chat
export type ChatStatus = 'online' | 'offline' | 'busy' | 'away'

export interface ChatContact {
  id: number
  fullName: string
  role: string
  about: string
  avatar: string
  status: ChatStatus
}

export interface ChatMessage {
  message: string
  time: string
  senderId: number
  feedback: {
    isSent: boolean
    isDelivered: boolean
    isSeen: boolean
  }
}

export interface Chat {
  id: number
  userId: number
  unseenMsgs: number
  messages: ChatMessage[]
}

// ℹ️ This is chat type received in response of user chat
export interface ChatOut {
  id: Chat['id']
  unseenMsgs: Chat['unseenMsgs']
  messages: ChatMessage[]
  lastMessage: ChatMessage[number]
}

export interface ChatContactWithChat extends ChatContact {
  chat: ChatOut
}
// !SECTION App: Chat


// 👉 Template Search
//----------------
export type SearchItem = {
  id: number
  url: { name:string, params?: object}
  icon: string
  title: string
  category: string
}

export type SearchHeader = {
  header: string
  title:string
}


export type Permission = {
  id: number
  name: string
  createdDate: string
  assignedTo: string[]
}

export type Data = {
  responsive_id: string,
  id: number, 
  avatar: string,
  full_name: string,
  post: string,
  email: string,
  city: string,
  start_date: date,
  salary: number,
  age: string | number,
  experience: string, 
  status: number,
}

export interface Product {
  id: number,
  name: string,
  slug: string,
  brand: string,
  category: string,
  price: number,
  image: string,
  hasFreeShipping: boolean,
  rating: number,
  description: string,
}

export interface Buyer {
  name: string,
  avatar: string | null,
}

export interface Payment{
  total: number,
  received_payment_status: string,
  paid_amount: number,
  status: string,
}

export type SalesDetails= {
  product: Product,
  buyer: Buyer,
  date: string,
  payment: Payment,
}
