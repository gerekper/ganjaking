<script setup>
import { useChat } from '@/views/apps/chat/useChat'
import { useChatStore } from '@/views/apps/chat/useChatStore'
import {
  avatarText,
  formatDateToMonthShort,
} from '@core/utils/formatters'

const props = defineProps({
  isChatContact: {
    type: Boolean,
    required: false,
    default: false,
  },
  user: {
    type: null,
    required: true,
  },
})

const store = useChatStore()
const { resolveAvatarBadgeVariant } = useChat()

const isChatContactActive = computed(() => {
  const isActive = store.activeChat?.contact.id === props.user.id
  if (!props.isChatContact)
    return !store.activeChat?.chat && isActive
  
  return isActive
})
</script>

<template>
  <li
    :key="store.chatsContacts.length"
    class="chat-contact cursor-pointer d-flex align-center"
    :class="{ 'chat-contact-active': isChatContactActive }"
    :data-x="store.chatsContacts.length"
  >
    <VBadge
      dot
      location="bottom right"
      offset-x="3"
      offset-y="3"
      :color="resolveAvatarBadgeVariant(props.user.status)"
      bordered
      :model-value="props.isChatContact"
    >
      <VAvatar
        size="40"
        :variant="!props.user.avatar ? 'tonal' : undefined"
        :color="!props.user.avatar ? resolveAvatarBadgeVariant(props.user.status) : undefined"
      >
        <VImg
          v-if="props.user.avatar"
          :src="props.user.avatar"
          alt="John Doe"
        />
        <span v-else>{{ avatarText(user.fullName) }}</span>
      </VAvatar>
    </VBadge>
    <div class="flex-grow-1 ms-4 overflow-hidden">
      <span class="text-high-emphasis font-weight-medium">{{ props.user.fullName }}</span>
      <span class="d-block text-sm text-truncate">{{ props.isChatContact && 'chat' in props.user ? props.user.chat.lastMessage.message : props.user.about }}</span>
    </div>
    <div
      v-if="props.isChatContact && 'chat' in props.user"
      class="d-flex flex-column align-self-start"
    >
      <span class="d-block whitespace-no-wrap">{{ formatDateToMonthShort(props.user.chat.lastMessage.time) }}</span>
      <VBadge
        v-if="props.user.chat.unseenMsgs"
        color="error"
        inline
        :content="props.user.chat.unseenMsgs"
        class="ms-auto"
      />
    </div>
  </li>
</template>

<style lang="scss">
@use "@styles/variables/_vuetify.scss";
@use "@core-scss/base/mixins";
@use "vuetify/lib/styles/tools/states" as vuetifyStates;

.chat-contact {
  border-radius: 0.5rem;
  padding-block: 0.75rem;
  padding-inline: var(--chat-content-spacing-x);

  @include mixins.before-pseudo;
  @include vuetifyStates.states($active: false);

  &.chat-contact-active {
    background: linear-gradient(270deg, rgb(var(--v-theme-primary)) 0%, #fff 300%);
    color: #fff;

    --v-theme-on-background: #fff;

    .v-avatar {
      background: #fff;
    }
  }

  .v-badge--bordered .v-badge__badge::after {
    color: #fff;
  }

  &.chat-contact-active {
    background: rgb(var(--v-theme-primary));
    color: #fff;

    --v-theme-on-background: #fff;
  }
}
</style>
