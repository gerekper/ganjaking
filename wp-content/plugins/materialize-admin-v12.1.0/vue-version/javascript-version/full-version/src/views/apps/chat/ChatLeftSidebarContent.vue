<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useChat } from './useChat'
import ChatContact from '@/views/apps/chat/ChatContact.vue'
import { useChatStore } from '@/views/apps/chat/useChatStore'

const props = defineProps({
  search: {
    type: String,
    required: true,
  },
  isDrawerOpen: {
    type: Boolean,
    required: true,
  },
})

const emit = defineEmits([
  'openChatOfContact',
  'showUserProfile',
  'close',
  'update:search',
])

const { resolveAvatarBadgeVariant } = useChat()
const search = useVModel(props, 'search', emit)
const store = useChatStore()
</script>

<template>
  <!-- 👉 Chat list header -->
  <div
    v-if="store.profileUser"
    class="chat-list-header"
  >
    <VBadge
      dot
      location="bottom right"
      offset-x="3"
      offset-y="3"
      :color="resolveAvatarBadgeVariant(store.profileUser.status)"
      bordered
    >
      <VAvatar
        size="40"
        class="cursor-pointer"
        @click="$emit('showUserProfile')"
      >
        <VImg
          :src="store.profileUser.avatar"
          alt="John Doe"
        />
      </VAvatar>
    </VBadge>

    <VTextField
      v-model="search"
      density="compact"
      placeholder="Search..."
      prepend-inner-icon="mdi-magnify"
      class="ms-4 chat-list-search"
    />

    <IconBtn
      v-if="$vuetify.display.smAndDown"
      @click="$emit('close')"
    >
      <VIcon
        icon="mdi-close"
        class="text-medium-emphasis"
      />
    </IconBtn>
  </div>
  <VDivider />

  <PerfectScrollbar
    tag="ul"
    class="chat-contacts-list text-medium-emphasis px-3 gap-2"
    :options="{ wheelPropagation: false }"
  >
    <li>
      <span class="chat-contact-header d-block text-primary text-xl font-weight-medium">Chats</span>
    </li>
    <ChatContact
      v-for="(contact, index) in store.chatsContacts"
      :key="`chat-${contact.id}`"
      :user="contact"
      is-chat-contact
      :class="index > 0 ? 'mt-1' : ''"
      @click="$emit('openChatOfContact', contact.id)"
    />
    <span
      v-show="!store.chatsContacts.length"
      class="no-chat-items-text text-disabled"
    >No chats found</span>
    <li>
      <span class="chat-contact-header d-block text-primary text-xl font-weight-medium">Contacts</span>
    </li>
    <ChatContact
      v-for="(contact, index) in store.contacts"
      :key="`chat-${contact.id}`"
      :user="contact"
      :class="index > 0 ? 'mt-1' : ''"
      @click="$emit('openChatOfContact', contact.id)"
    />
    <span
      v-show="!store.contacts.length"
      class="no-chat-items-text text-disabled"
    >No contacts found</span>
  </PerfectScrollbar>
</template>

<style lang="scss">
.chat-contacts-list {
  --chat-content-spacing-x: 10px;

  padding-block-end: 0.75rem;

  .chat-contact-header {
    margin-block-end: 1rem;
    margin-block-start: 1.25rem;
  }

  .chat-contact-header,
  .no-chat-items-text {
    margin-inline: var(--chat-content-spacing-x);
  }
}
</style>
