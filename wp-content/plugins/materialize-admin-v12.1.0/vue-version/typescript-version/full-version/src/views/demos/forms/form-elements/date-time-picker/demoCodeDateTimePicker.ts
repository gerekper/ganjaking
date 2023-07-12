export const basic = {
  ts: `<script setup lang="ts">
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Default"
    placeholder="Select date"
  />
</template>
`,
  js: `<script setup>
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Default"
    placeholder="Select date"
  />
</template>
`,
}

export const dateAndTime = {
  ts: `<script setup lang="ts">
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Date & TIme"
    placeholder="Select date and time"
    :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
  />
</template>
`,
  js: `<script setup>
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Date & TIme"
    placeholder="Select date and time"
    :config="{ enableTime: true, dateFormat: 'Y-m-d H:i' }"
  />
</template>
`,
}

export const disabledRange = {
  ts: `<script setup lang="ts">
const now = new Date()
const currentMonth = now.toLocaleString('default', { month: '2-digit' })
const currentYear = now.getFullYear()
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Disabled Range"
    placeholder="Select date"
    :config="{ dateFormat: 'Y-m-d', disable: [{ from: \`\${currentYear}-\${currentMonth}-20\`, to: \`\${currentYear}-\${currentMonth}-25\` }] }"
  />
</template>
`,
  js: `<script setup>
const now = new Date()
const currentMonth = now.toLocaleString('default', { month: '2-digit' })
const currentYear = now.getFullYear()
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Disabled Range"
    placeholder="Select date"
    :config="{ dateFormat: 'Y-m-d', disable: [{ from: \`\${currentYear}-\${currentMonth}-20\`, to: \`\${currentYear}-\${currentMonth}-25\` }] }"
  />
</template>
`,
}

export const humanFriendly = {
  ts: `<script setup lang="ts">
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Human Friendly"
    placeholder="Select date"
    :config="{ altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }"
  />
</template>
`,
  js: `<script setup>
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Human Friendly"
    placeholder="Select date"
    :config="{ altInput: true, altFormat: 'F j, Y', dateFormat: 'Y-m-d' }"
  />
</template>
`,
}

export const inline = {
  ts: `<script setup lang="ts">
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Inline"
    placeholder="Select Date"
    :config="{ inline: true }"
  />
</template>
`,
  js: `<script setup>
const date = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="date"
    label="Inline"
    placeholder="Select Date"
    :config="{ inline: true }"
  />
</template>
`,
}

export const multipleDates = {
  ts: `<script setup lang="ts">
const multipleDate = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="multipleDate"
    label="Multiple Dates"
    placeholder="Select date"
    :config="{ mode: 'multiple', dateFormat: 'Y-m-d' }"
  />
</template>
`,
  js: `<script setup>
const multipleDate = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="multipleDate"
    label="Multiple Dates"
    placeholder="Select date"
    :config="{ mode: 'multiple', dateFormat: 'Y-m-d' }"
  />
</template>
`,
}

export const range = {
  ts: `<script setup lang="ts">
const dateRange = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="dateRange"
    label="Range"
    placeholder="Select date"
    :config="{ mode: 'range' }"
  />
</template>
`,
  js: `<script setup>
const dateRange = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="dateRange"
    label="Range"
    placeholder="Select date"
    :config="{ mode: 'range' }"
  />
</template>
`,
}

export const timePicker = {
  ts: `<script setup lang="ts">
const time = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="time"
    label="Time picker"
    placeholder="Select time"
    :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
  />
</template>
`,
  js: `<script setup>
const time = ref('')
</script>

<template>
  <AppDateTimePicker
    v-model="time"
    label="Time picker"
    placeholder="Select time"
    :config="{ enableTime: true, noCalendar: true, dateFormat: 'H:i' }"
  />
</template>
`,
}
