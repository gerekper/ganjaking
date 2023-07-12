import mock from '@/@fake-db/mock'
import { genId } from '@/@fake-db/utils'
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'


// TODO: Use jsonwebtoken pkg
// ℹ️ Created from https://jwt.io/ using HS256 algorithm
// ℹ️ We didn't created it programmatically because jsonwebtoken package have issues with esm support. View Issues: https://github.com/auth0/node-jsonwebtoken/issues/655
const userTokens = [
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MX0.fhc3wykrAnRpcKApKhXiahxaOe8PSHatad31NuIZ0Zg',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6Mn0.cat2xMrZLn0FwicdGtZNzL7ifDTAKWB0k1RurSWjdnw',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6M30.PGOfMaZA_T9W05vMj5FYXG5d47soSPJD1WuxeUfw4L4',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6NH0.d_9aq2tpeA9-qpqO0X4AmW6gU2UpWkXwc04UJYFWiZE',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6NX0.ocO77FbjOSU1-JQ_BilEZq2G_M8bCiB10KYqtfkv1ss',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6Nn0.YgQILRqZy8oefhTZgJJfiEzLmhxQT_Bd2510OvrrwB8',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6N30.KH9RmOWIYv_HONxajg7xBIJXHEUvSdcBygFtS2if8Jk',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6OH0.shrp-oMHkVAkiMkv_aIvSx3k6Jk-X7TrH5UeufChz_g',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6OX0.9JD1MR3ZkwHzhl4mOHH6lGG8hOVNZqDNH6UkFzjCqSE',
  'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6MTB9.txWLuN4QT5PqTtgHmlOiNerIu5Do51PpYOiZutkyXYg',
]


// ❗ These two secrets shall be in .env file and not in any other file
// const jwtSecret = 'dd5f3089-40c3-403d-af14-d0c228b05cb4'
const database = [
  {
    id: 1,
    fullName: 'John Doe',
    username: 'johndoe',
    password: 'admin',
    avatar: avatar1,
    email: 'admin@demo.com',
    role: 'admin',
    abilities: [
      {
        action: 'manage',
        subject: 'all',
      },
    ],
  },
  {
    id: 2,
    fullName: 'Jane Doe',
    username: 'janedoe',
    password: 'client',
    avatar: avatar2,
    email: 'client@demo.com',
    role: 'client',
    abilities: [
      {
        action: 'read',
        subject: 'Auth',
      },
      {
        action: 'read',
        subject: 'AclDemo',
      },
    ],
  },
]

mock.onPost('/auth/login').reply(request => {
  const { email, password } = JSON.parse(request.data)
  let errors = {
    email: ['Something went wrong'],
  }
  const user = database.find(u => u.email === email && u.password === password)
  if (user) {
    try {
      const accessToken = userTokens[user.id]

      // We are duplicating user here
      const userData = { ...user }

      const userOutData = Object.fromEntries(Object.entries(userData)
        .filter(([key, _]) => !(key === 'password' || key === 'abilities')))

      const response = {
        userAbilities: userData.abilities,
        accessToken,
        userData: userOutData,
      }


      //   const accessToken = jwt.sign({ id: user.id }, jwtSecret)
      return [200, response]
    }
    catch (e) {
      errors = { email: [e] }
    }
  }
  else {
    errors = {
      email: ['Email or Password is Invalid'],
    }
  }
  
  return [400, { errors }]
})
mock.onPost('/auth/register').reply(request => {
  const { username, email, password } = JSON.parse(request.data)

  // If not any of data is missing return 400
  if (!(username && email && password))
    return [400]
  const isEmailAlreadyInUse = database.find(user => user.email === email)
  const isUsernameAlreadyInUse = database.find(user => user.username === username)

  const errors = {
    password: !password ? ['Please enter password'] : null,
    email: (() => {
      if (!email)
        return ['Please enter your email.']
      if (isEmailAlreadyInUse)
        return ['This email is already in use.']
      
      return null
    })(),
    username: (() => {
      if (!username)
        return ['Please enter your username.']
      if (isUsernameAlreadyInUse)
        return ['This username is already in use.']
      
      return null
    })(),
  }

  if (!errors.username && !errors.email) {
    // Calculate user id
    const userData = {
      id: genId(database),
      email,
      password,
      username,
      fullName: '',
      role: 'admin',
      abilities: [
        {
          action: 'manage',
          subject: 'all',
        },
      ],
    }

    database.push(userData)

    const accessToken = userTokens[userData.id]
    const { password: _, abilities, ...user } = userData

    const response = {
      userData: user,
      accessToken,
      userAbilities: abilities,
    }

    return [200, response]
  }
  
  return [400, { error: errors }]
})
