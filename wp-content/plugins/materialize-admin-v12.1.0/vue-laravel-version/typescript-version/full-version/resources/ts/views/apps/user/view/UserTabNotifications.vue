<script lang="ts" setup>
const notifications = ref([
  {
    type: 'New for you',
    email: true,
    browser: false,
    app: false,
  },
  {
    type: 'Account activity',
    email: false,
    browser: true,
    app: true,
  },
  {
    type: 'A new browser used to sign in',
    email: true,
    browser: true,
    app: true,
  },
  {
    type: 'A new device is linked',
    email: false,
    browser: true,
    app: false,
  },
])
</script>

<template>
  <VCard
    title="Notifications"
    class="user-tab-notification"
  >
    <VDivider />

    <VCardText class="text-high-emphasis font-weight-medium">
      You will receive notification for the below selected items.
    </VCardText>

    <VDivider />

    <VTable class="text-no-wrap text-high-emphasis text-sm">
      <thead>
        <tr>
          <th scope="col">
            TYPE
          </th>
          <th scope="col">
            EMAIL
          </th>
          <th scope="col">
            BROWSER
          </th>
          <th scope="col">
            APP
          </th>
        </tr>
      </thead>

      <tbody>
        <tr
          v-for="notification in notifications"
          :key="notification.type"
        >
          <td>{{ notification.type }}</td>
          <td>
            <VCheckbox v-model="notification.email" />
          </td>
          <td>
            <VCheckbox v-model="notification.browser" />
          </td>
          <td>
            <VCheckbox v-model="notification.app" />
          </td>
        </tr>
      </tbody>
    </VTable>

    <VDivider />

    <VCardText class="d-flex flex-wrap gap-4">
      <VBtn>Save changes</VBtn>
      <VBtn
        color="secondary"
        variant="outlined"
      >
        Discard
      </VBtn>
    </VCardText>
  </VCard>
</template>
