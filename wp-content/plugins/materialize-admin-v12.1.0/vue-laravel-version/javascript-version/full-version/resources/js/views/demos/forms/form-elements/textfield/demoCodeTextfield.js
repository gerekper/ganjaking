export const basic = { ts: `<template>
  <VTextField
    label="Regular"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<template>
  <VTextField
    label="Regular"
    placeholder="Placeholder Text"
  />
</template>
` }

export const clearable = { ts: `<template>
  <VTextField
    placeholder="Placeholder Text"
    label="Regular"
    clearable
  />
</template>
`, js: `<template>
  <VTextField
    placeholder="Placeholder Text"
    label="Regular"
    clearable
  />
</template>
` }

export const counter = { ts: `<script lang="ts" setup>
const title = ref('Preliminary report')
const description = ref('California is a state in the western United States')
const rules = [(v: string) => v.length <= 25 || 'Max 25 characters']
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VTextField
        v-model="title"
        :rules="rules"
        counter="25"
        placeholder="Placeholder Text"
        hint="This field uses counter prop"
        label="Regular"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="description"
        :rules="rules"
        counter
        maxlength="25"
        placeholder="Placeholder Text"
        hint="This field uses maxlength attribute"
        label="Limit exceeded"
      />
    </VCol>
  </VRow>
</template>
`, js: `<script setup>
const title = ref('Preliminary report')
const description = ref('California is a state in the western United States')
const rules = [v => v.length <= 25 || 'Max 25 characters']
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VTextField
        v-model="title"
        :rules="rules"
        counter="25"
        placeholder="Placeholder Text"
        hint="This field uses counter prop"
        label="Regular"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="description"
        :rules="rules"
        counter
        maxlength="25"
        placeholder="Placeholder Text"
        hint="This field uses maxlength attribute"
        label="Limit exceeded"
      />
    </VCol>
  </VRow>
</template>
` }

export const customColors = { ts: `<template>
  <VTextField
    color="success"
    label="First name"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<template>
  <VTextField
    color="success"
    label="First name"
    placeholder="Placeholder Text"
  />
</template>
` }

export const density = { ts: `<template>
  <VTextField
    label="Compact"
    density="compact"
    placeholder="Placeholder Text"
  />
</template>
`, js: `<template>
  <VTextField
    label="Compact"
    density="compact"
    placeholder="Placeholder Text"
  />
</template>
` }

export const iconEvents = { ts: `<script lang="ts" setup>
const message = ref('Hey!')
const marker = ref(true)
const iconIndex = ref(0)

const toggleMarker = () => {
  marker.value = !marker.value
}

const clearMessage = () => {
  message.value = ''
}

const resetIcon = () => {
  iconIndex.value = 0
}

const sendMessage = () => {
  resetIcon()
  clearMessage()
}
</script>

<template>
  <VTextField
    v-model="message"
    clearable
    type="text"
    label="Message"
    color="primary"
    placeholder="Hey!!"
    clear-icon="mdi-close-circle-outline"
    :append-icon="message ? 'mdi-send-outline' : 'mdi-microphone'"
    :append-inner-icon="marker ? 'mdi-map-marker-outline' : 'mdi-map-marker-off-outline'"
    @click:append-inner="toggleMarker"
    @click:append="sendMessage"
    @click:clear="clearMessage"
  />
</template>
`, js: `<script setup>
const message = ref('Hey!')
const marker = ref(true)
const iconIndex = ref(0)

const toggleMarker = () => {
  marker.value = !marker.value
}

const clearMessage = () => {
  message.value = ''
}

const resetIcon = () => {
  iconIndex.value = 0
}

const sendMessage = () => {
  resetIcon()
  clearMessage()
}
</script>

<template>
  <VTextField
    v-model="message"
    clearable
    type="text"
    label="Message"
    color="primary"
    placeholder="Hey!!"
    clear-icon="mdi-close-circle-outline"
    :append-icon="message ? 'mdi-send-outline' : 'mdi-microphone'"
    :append-inner-icon="marker ? 'mdi-map-marker-outline' : 'mdi-map-marker-off-outline'"
    @click:append-inner="toggleMarker"
    @click:append="sendMessage"
    @click:clear="clearMessage"
  />
</template>
` }

export const iconSlots = { ts: `<script lang="ts" setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const message = ref('Hey!')
const loading = ref(false)

const clickMe = () => {
  loading.value = true
  message.value = 'Wait for it...'

  setTimeout(() => {
    loading.value = false
    message.value = 'You\'ve clicked me!'
  }, 2000)
}
</script>

<template>
  <VTextField
    v-model="message"
    clearable
    clear-icon="mdi-close-circle-outline"
    label="Message"
    placeholder="Hey!!"
    type="text"
  >
    <!-- Prepend -->
    <template #prepend>
      <VTooltip location="bottom">
        <template #activator="{ props }">
          <VIcon
            v-bind="props"
            icon="mdi-help-circle-outline"
          />
        </template>
        I'm a tooltip
      </VTooltip>
    </template>

    <!-- AppendInner -->
    <template #append-inner>
      <VFadeTransition leave-absolute>
        <VProgressCircular
          v-if="loading"
          size="24"
          color="info"
          indeterminate
        />

        <VNodeRenderer
          v-else
          :nodes="themeConfig.app.logo"
        />
      </VFadeTransition>
    </template>

    <!-- Append -->
    <template #append>
      <VBtn
        :size="$vuetify.display.smAndDown ? 'small' : 'large'"
        :class="$vuetify.display.smAndDown ? 'mt-n2' : 'mt-n3'"
        :icon="$vuetify.display.smAndDown"
        @click="clickMe"
      >
        <VIcon icon="mdi-target" />
        <span
          v-if="$vuetify.display.mdAndUp"
          class="ms-3"
        >Click me</span>
      </VBtn>
    </template>
  </VTextField>
</template>
`, js: `<script setup>
import { VNodeRenderer } from '@layouts/components/VNodeRenderer'
import { themeConfig } from '@themeConfig'

const message = ref('Hey!')
const loading = ref(false)

const clickMe = () => {
  loading.value = true
  message.value = 'Wait for it...'
  setTimeout(() => {
    loading.value = false
    message.value = 'You\'ve clicked me!'
  }, 2000)
}
</script>

<template>
  <VTextField
    v-model="message"
    clearable
    clear-icon="mdi-close-circle-outline"
    label="Message"
    placeholder="Hey!!"
    type="text"
  >
    <!-- Prepend -->
    <template #prepend>
      <VTooltip location="bottom">
        <template #activator="{ props }">
          <VIcon
            v-bind="props"
            icon="mdi-help-circle-outline"
          />
        </template>
        I'm a tooltip
      </VTooltip>
    </template>

    <!-- AppendInner -->
    <template #append-inner>
      <VFadeTransition leave-absolute>
        <VProgressCircular
          v-if="loading"
          size="24"
          color="info"
          indeterminate
        />

        <VNodeRenderer
          v-else
          :nodes="themeConfig.app.logo"
        />
      </VFadeTransition>
    </template>

    <!-- Append -->
    <template #append>
      <VBtn
        :size="$vuetify.display.smAndDown ? 'small' : 'large'"
        :class="$vuetify.display.smAndDown ? 'mt-n2' : 'mt-n3'"
        :icon="$vuetify.display.smAndDown"
        @click="clickMe"
      >
        <VIcon icon="mdi-target" />
        <span
          v-if="$vuetify.display.mdAndUp"
          class="ms-3"
        >Click me</span>
      </VBtn>
    </template>
  </VTextField>
</template>
` }

export const icons = { ts: `<template>
  <VRow>
    <VCol cols="12">
      <VTextField
        label="Prepend"
        prepend-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        label="Prepend Inner"
        prepend-inner-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        label="Append"
        append-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        label="Append Inner"
        append-inner-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>
  </VRow>
</template>
`, js: `<template>
  <VRow>
    <VCol cols="12">
      <VTextField
        label="Prepend"
        prepend-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        label="Prepend Inner"
        prepend-inner-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        label="Append"
        append-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        label="Append Inner"
        append-inner-icon="mdi-map-marker-outline"
        placeholder="Placeholder Text"
      />
    </VCol>
  </VRow>
</template>
` }

export const labelSlot = { ts: `<template>
  <VTextField placeholder="Placeholder Text">
    <template #label>
      What about &nbsp;<strong>icon</strong>&nbsp;here?
      <VIcon icon="mdi-file-find-outline" />
    </template>
  </VTextField>
</template>
`, js: `<template>
  <VTextField placeholder="Placeholder Text">
    <template #label>
      What about &nbsp;<strong>icon</strong>&nbsp;here?
      <VIcon icon="mdi-file-find-outline" />
    </template>
  </VTextField>
</template>
` }

export const passwordInput = { ts: `<script lang="ts" setup>
const show1 = ref(false)
const show2 = ref(true)
const password = ref('Password')
const confirmPassword = ref('wqfasds')

const rules = {
  required: (value: string) => !!value || 'Required.',
  min: (v: string) => v.length >= 8 || 'Min 8 characters',
}
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      sm="6"
    >
      <VTextField
        v-model="password"
        :append-inner-icon="show1 ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        :rules="[rules.required, rules.min]"
        :type="show1 ? 'text' : 'password'"
        name="input-10-1"
        label="Normal with hint text"
        hint="At least 8 characters"
        placeholder="············"
        counter
        @click:append-inner="show1 = !show1"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextField
        v-model="confirmPassword"
        :rules="[rules.required, rules.min]"
        :append-inner-icon="show2 ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        :type="show2 ? 'text' : 'password'"
        name="input-10-2"
        placeholder="············"
        label="Visible"
        hint="At least 8 characters"
        @click:append-inner="show2 = !show2"
      />
    </VCol>
  </VRow>
</template>
`, js: `<script setup>
const show1 = ref(false)
const show2 = ref(true)
const password = ref('Password')
const confirmPassword = ref('wqfasds')

const rules = {
  required: value => !!value || 'Required.',
  min: v => v.length >= 8 || 'Min 8 characters',
}
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      sm="6"
    >
      <VTextField
        v-model="password"
        :append-inner-icon="show1 ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        :rules="[rules.required, rules.min]"
        :type="show1 ? 'text' : 'password'"
        name="input-10-1"
        label="Normal with hint text"
        hint="At least 8 characters"
        placeholder="············"
        counter
        @click:append-inner="show1 = !show1"
      />
    </VCol>

    <VCol
      cols="12"
      sm="6"
    >
      <VTextField
        v-model="confirmPassword"
        :rules="[rules.required, rules.min]"
        :append-inner-icon="show2 ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
        :type="show2 ? 'text' : 'password'"
        name="input-10-2"
        placeholder="············"
        label="Visible"
        hint="At least 8 characters"
        @click:append-inner="show2 = !show2"
      />
    </VCol>
  </VRow>
</template>
` }

export const prefixesAndSuffixes = { ts: `<script setup lang="ts">
const amount = ref(10.05)
const weight = ref(28.02)
const email = ref('example')
const time = ref('04:56')
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VTextField
        v-model="amount"
        label="Amount"
        prefix="$"
        type="number"
        placeholder="10.05"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="weight"
        label="Weight"
        suffix="lbs"
        type="number"
        placeholder="28.02"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="email"
        label="Email address"
        suffix="@gmail.com"
        placeholder="example"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="time"
        label="Label Text"
        type="time"
        suffix="PST"
        placeholder="04:56"
      />
    </VCol>
  </VRow>
</template>
`, js: `<script setup>
const amount = ref(10.05)
const weight = ref(28.02)
const email = ref('example')
const time = ref('04:56')
</script>

<template>
  <VRow>
    <VCol cols="12">
      <VTextField
        v-model="amount"
        label="Amount"
        prefix="$"
        type="number"
        placeholder="10.05"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="weight"
        label="Weight"
        suffix="lbs"
        type="number"
        placeholder="28.02"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="email"
        label="Email address"
        suffix="@gmail.com"
        placeholder="example"
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        v-model="time"
        label="Label Text"
        type="time"
        suffix="PST"
        placeholder="04:56"
      />
    </VCol>
  </VRow>
</template>
` }

export const singleLine = { ts: `<template>
  <VTextField
    label="Regular"
    placeholder="Placeholder Text"
    single-line
  />
</template>
`, js: `<template>
  <VTextField
    label="Regular"
    placeholder="Placeholder Text"
    single-line
  />
</template>
` }

export const state = { ts: `<template>
  <VRow>
    <VCol>
      <VTextField
        label="Disabled"
        placeholder="Placeholder Text"
        disabled
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        placeholder="Placeholder Text"
        label="Readonly"
        readonly
      />
    </VCol>
  </VRow>
</template>
`, js: `<template>
  <VRow>
    <VCol>
      <VTextField
        label="Disabled"
        placeholder="Placeholder Text"
        disabled
      />
    </VCol>

    <VCol cols="12">
      <VTextField
        placeholder="Placeholder Text"
        label="Readonly"
        readonly
      />
    </VCol>
  </VRow>
</template>
` }

export const validation = { ts: `<script lang="ts" setup>
import { emailValidator, requiredValidator } from '@validators'

const email = ref('')
</script>

<template>
  <VForm>
    <VTextField
      v-model="email"
      :rules="[requiredValidator, emailValidator]"
      placeholder="johnedoe@email.com"
      label="E-mail"
    />
  </VForm>
</template>
`, js: `<script setup>
import {
  emailValidator,
  requiredValidator,
} from '@validators'

const email = ref('')
</script>

<template>
  <VForm>
    <VTextField
      v-model="email"
      :rules="[requiredValidator, emailValidator]"
      placeholder="johnedoe@email.com"
      label="E-mail"
    />
  </VForm>
</template>
` }

export const variant = { ts: `<template>
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Outlined"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Filled"
        variant="filled"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Solo"
        variant="solo"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Plain"
        variant="plain"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Underlined"
        variant="underlined"
        placeholder="Placeholder Text"
      />
    </VCol>
  </VRow>
</template>
`, js: `<template>
  <VRow>
    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Outlined"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Filled"
        variant="filled"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Solo"
        variant="solo"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Plain"
        variant="plain"
        placeholder="Placeholder Text"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        label="Underlined"
        variant="underlined"
        placeholder="Placeholder Text"
      />
    </VCol>
  </VRow>
</template>
` }

