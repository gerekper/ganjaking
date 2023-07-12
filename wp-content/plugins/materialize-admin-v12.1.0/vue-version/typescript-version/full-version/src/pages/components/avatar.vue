<script lang="ts" setup>
import * as demoCode from '@/views/demos/components/avatar/demoCodeAvatar'
</script>

<template>
  <VRow class="match-height">
    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Colors -->
      <AppCardCode
        title="Colors"
        :code="demoCode.colors"
      >
        <p>Use <code>color</code> prop to change the background color of avatar.</p>

        <DemoAvatarColors />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Initials -->
      <AppCardCode
        title="Tonal"
        :code="demoCode.tonal"
      >
        <p>Use <code>variant="tonal"</code> to create light background avatars.</p>

        <DemoAvatarTonal />
      </AppCardCode>
    </VCol>

    <VCol
      md="6"
      cols="12"
    >
      <!-- 👉 Sizes -->
      <AppCardCode
        title="Sizes"
        :code="demoCode.sizes"
      >
        <p>The <code>size</code> prop allows you to change the height and width of the avatar.</p>

        <DemoAvatarSizes />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Icons -->
      <AppCardCode
        title="Icons"
        :code="demoCode.icons"
      >
        <p>You can use <code>icon</code> prop of <code>v-avatar</code> component for rendering icons.</p>

        <DemoAvatarIcons />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Rounded -->
      <AppCardCode
        title="Rounded"
        :code="demoCode.rounded"
      >
        <p>The <code>rounded</code> prop can be used to change the border radius of <code>v-avatar</code>.</p>

        <DemoAvatarRounded />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Images -->
      <AppCardCode
        title="Images"
        :code="demoCode.images"
      >
        <p>You can use <code>image</code> prop of <code>v-avatar</code> component for rendering image.</p>

        <DemoAvatarImages />
      </AppCardCode>
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <!-- 👉 Group -->
      <AppCardCode
        title="Group"
        :code="demoCode.group"
      >
        <p>Use <code>v-avatar-group</code> class as a wrapper of avatars.</p>

        <DemoAvatarGroup />
      </AppCardCode>
    </VCol>
  </VRow>
</template>
