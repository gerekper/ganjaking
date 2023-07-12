<script setup lang="ts">
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar4 from '@images/avatars/avatar-4.png'
import avatar5 from '@images/avatars/avatar-5.png'
import avatar6 from '@images/avatars/avatar-6.png'
</script>

<template>
  <div class="v-avatar-group">
    <VAvatar :size="40">
      <VImg :src="avatar1" />
      <VTooltip
        activator="parent"
        location="top"
      >
        John Doe
      </VTooltip>
    </VAvatar>

    <VAvatar :size="40">
      <VImg :src="avatar2" />
      <VTooltip
        activator="parent"
        location="top"
      >
        Jennie Obrien
      </VTooltip>
    </VAvatar>

    <VAvatar :size="40">
      <VImg :src="avatar3" />
      <VTooltip
        activator="parent"
        location="top"
      >
        Peter Harper
      </VTooltip>
    </VAvatar>

    <VAvatar :size="40">
      <VImg :src="avatar4" />
      <VTooltip
        activator="parent"
        location="top"
      >
        Vivian Padilla
      </VTooltip>
    </VAvatar>

    <VAvatar :size="40">
      <VImg :src="avatar5" />
      <VTooltip
        activator="parent"
        location="top"
      >
        Scott Wells
      </VTooltip>
    </VAvatar>

    <VAvatar :size="40">
      <VImg :src="avatar6" />
      <VTooltip
        activator="parent"
        location="top"
      >
        Angel Bishop
      </VTooltip>
    </VAvatar>
  </div>
</template>
