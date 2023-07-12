export const basic = {
  ts: `<script lang="ts" setup>
const radioGroup = ref(1)
</script>

<template>
  <div class="">
    <VRadioGroup v-model="radioGroup">
      <VRadio
        v-for="n in 2"
        :key="n"
        :label="\`Radio \${n}\`"
        :value="n"
      />
    </VRadioGroup>
  </div>
</template>
`,
  js: `<script setup>
const radioGroup = ref(1)
</script>

<template>
  <div class="">
    <VRadioGroup v-model="radioGroup">
      <VRadio
        v-for="n in 2"
        :key="n"
        :label="\`Radio \${n}\`"
        :value="n"
      />
    </VRadioGroup>
  </div>
</template>
`,
}

export const colors = {
  ts: `<script lang="ts" setup>
const selectedRadio = ref('primary')
const colorsRadio = ['Primary', 'Secondary', 'Success', 'Info', 'Warning', 'Error']
</script>

<template>
  <VRadioGroup
    v-model="selectedRadio"
    inline
  >
    <VRadio
      v-for="radio in colorsRadio"
      :key="radio"
      :label="radio"
      :color="radio.toLocaleLowerCase()"
      :value="radio.toLocaleLowerCase()"
    />
  </VRadioGroup>
</template>
`,
  js: `<script setup>
const selectedRadio = ref('primary')

const colorsRadio = [
  'Primary',
  'Secondary',
  'Success',
  'Info',
  'Warning',
  'Error',
]
</script>

<template>
  <VRadioGroup
    v-model="selectedRadio"
    inline
  >
    <VRadio
      v-for="radio in colorsRadio"
      :key="radio"
      :label="radio"
      :color="radio.toLocaleLowerCase()"
      :value="radio.toLocaleLowerCase()"
    />
  </VRadioGroup>
</template>
`,
}

export const density = {
  ts: `<script lang="ts" setup>
const columnRadio = ref('radio-1')
const inlineRadio = ref('radio-1')
</script>

<template>
  <VRadioGroup v-model="columnRadio">
    <VRadio
      label="Option 1"
      value="radio-1"
      density="compact"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
      density="compact"
    />
  </VRadioGroup>

  <VDivider class="my-3" />

  <VRadioGroup
    v-model="inlineRadio"
    inline
  >
    <VRadio
      label="Option 1"
      value="radio-1"
      density="compact"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
      density="compact"
    />
  </VRadioGroup>
</template>
`,
  js: `<script setup>
const columnRadio = ref('radio-1')
const inlineRadio = ref('radio-1')
</script>

<template>
  <VRadioGroup v-model="columnRadio">
    <VRadio
      label="Option 1"
      value="radio-1"
      density="compact"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
      density="compact"
    />
  </VRadioGroup>

  <VDivider class="my-3" />

  <VRadioGroup
    v-model="inlineRadio"
    inline
  >
    <VRadio
      label="Option 1"
      value="radio-1"
      density="compact"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
      density="compact"
    />
  </VRadioGroup>
</template>
`,
}

export const icon = {
  ts: `<script lang="ts" setup>
const radioGroup = ref(1)
</script>

<template>
  <VRadioGroup
    v-model="radioGroup"
    false-icon="mdi-bell-off-outline"
    true-icon="mdi-bell-outline"
  >
    <VRadio
      v-for="n in 2"
      :key="n"
      :label="\`Radio \${n}\`"
      :value="n"
    />
  </VRadioGroup>
</template>
`,
  js: `<script setup>
const radioGroup = ref(1)
</script>

<template>
  <VRadioGroup
    v-model="radioGroup"
    false-icon="mdi-bell-off-outline"
    true-icon="mdi-bell-outline"
  >
    <VRadio
      v-for="n in 2"
      :key="n"
      :label="\`Radio \${n}\`"
      :value="n"
    />
  </VRadioGroup>
</template>
`,
}

export const inline = {
  ts: `<script lang="ts" setup>
const columnRadio = ref('radio-1')
const inlineRadio = ref('radio-1')
</script>

<template>
  <VRadioGroup v-model="columnRadio">
    <VRadio
      label="Option 1"
      value="radio-1"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
    />
  </VRadioGroup>

  <VDivider class="my-4" />

  <VRadioGroup
    v-model="inlineRadio"
    inline
  >
    <VRadio
      label="Option 1"
      value="radio-1"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
    />
  </VRadioGroup>
</template>
`,
  js: `<script setup>
const columnRadio = ref('radio-1')
const inlineRadio = ref('radio-1')
</script>

<template>
  <VRadioGroup v-model="columnRadio">
    <VRadio
      label="Option 1"
      value="radio-1"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
    />
  </VRadioGroup>

  <VDivider class="my-4" />

  <VRadioGroup
    v-model="inlineRadio"
    inline
  >
    <VRadio
      label="Option 1"
      value="radio-1"
    />
    <VRadio
      label="Option 2"
      value="radio-2"
    />
  </VRadioGroup>
</template>
`,
}

export const labelSlot = {
  ts: `<script lang="ts" setup>
const radios = ref('Duckduckgo')
</script>

<template>
  <VRadioGroup v-model="radios">
    <template #label>
      <div>Your favorite <strong>search engine</strong></div>
    </template>

    <VRadio value="Google">
      <template #label>
        <div>
          Of course it's <span class="text-success">
            Google
          </span>
        </div>
      </template>
    </VRadio>

    <VRadio value="Duckduckgo">
      <template #label>
        <div>
          Definitely <span class="text-primary">
            Duckduckgo
          </span>
        </div>
      </template>
    </VRadio>
  </VRadioGroup>
</template>
`,
  js: `<script setup>
const radios = ref('Duckduckgo')
</script>

<template>
  <VRadioGroup v-model="radios">
    <template #label>
      <div>Your favorite <strong>search engine</strong></div>
    </template>

    <VRadio value="Google">
      <template #label>
        <div>
          Of course it's <span class="text-success">
            Google
          </span>
        </div>
      </template>
    </VRadio>

    <VRadio value="Duckduckgo">
      <template #label>
        <div>
          Definitely <span class="text-primary">
            Duckduckgo
          </span>
        </div>
      </template>
    </VRadio>
  </VRadioGroup>
</template>
`,
}

export const validation = {
  ts: `<script lang="ts" setup>
const radioGroup = ref(1)
const rules = [(value: number) => (value !== 3 ? true : 'Do not select the third one!')]
</script>

<template>
  <VRadioGroup
    v-model="radioGroup"
    inline
    :rules="rules"
  >
    <VRadio
      v-for="n in 3"
      :key="n"
      :error="radioGroup === 3 "
      :label="\`Radio \${n}\`"
      :value="n"
    />
  </VRadioGroup>
</template>
`,
  js: `<script setup>
const radioGroup = ref(1)
const rules = [value => value !== 3 ? true : 'Do not select the third one!']
</script>

<template>
  <VRadioGroup
    v-model="radioGroup"
    inline
    :rules="rules"
  >
    <VRadio
      v-for="n in 3"
      :key="n"
      :error="radioGroup === 3 "
      :label="\`Radio \${n}\`"
      :value="n"
    />
  </VRadioGroup>
</template>
`,
}
