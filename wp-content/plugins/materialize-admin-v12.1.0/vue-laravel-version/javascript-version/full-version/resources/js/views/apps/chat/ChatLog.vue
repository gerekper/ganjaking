<script setup>
import { useChatStore } from '@/views/apps/chat/useChatStore'
import { formatDate } from '@core/utils/formatters'

const store = useChatStore()

const contact = computed(() => ({
  id: store.activeChat?.contact.id,
  avatar: store.activeChat?.contact.avatar,
}))

const resolveFeedbackIcon = feedback => {
  if (feedback.isSeen)
    return {
      icon: 'mdi-check-all',
      color: 'success',
    }
  else if (feedback.isDelivered)
    return {
      icon: 'mdi-check-all',
      color: undefined,
    }
  else
    return {
      icon: 'mdi-check',
      color: undefined,
    }
}

const msgGroups = computed(() => {
  let messages = []
  const _msgGroups = []
  if (store.activeChat.chat) {
    messages = store.activeChat.chat.messages
    let msgSenderId = messages[0].senderId
    let msgGroup = {
      senderId: msgSenderId,
      messages: [],
    }
    messages.forEach((msg, index) => {
      if (msgSenderId === msg.senderId) {
        msgGroup.messages.push({
          message: msg.message,
          time: msg.time,
          feedback: msg.feedback,
        })
      } else {
        msgSenderId = msg.senderId
        _msgGroups.push(msgGroup)
        msgGroup = {
          senderId: msg.senderId,
          messages: [{
            message: msg.message,
            time: msg.time,
            feedback: msg.feedback,
          }],
        }
      }
      if (index === messages.length - 1)
        _msgGroups.push(msgGroup)
    })
  }
  
  return _msgGroups
})
</script>

<template>
  <div class="chat-log pa-5">
    <div
      v-for="(msgGrp, index) in msgGroups"
      :key="msgGrp.senderId + String(index)"
      class="chat-group d-flex align-start"
      :class="[{
        'flex-row-reverse': msgGrp.senderId !== contact.id,
        'mb-8': msgGroups.length - 1 !== index,
      }]"
    >
      <div
        class="chat-avatar"
        :class="msgGrp.senderId !== contact.id ? 'ms-4' : 'me-4'"
      >
        <VAvatar size="32">
          <VImg :src="msgGrp.senderId === contact.id ? contact.avatar : store.profileUser?.avatar" />
        </VAvatar>
      </div>
      <div
        class="chat-body d-inline-flex flex-column"
        :class="msgGrp.senderId !== contact.id ? 'align-end' : 'align-start'"
      >
        <p
          v-for="(msgData, msgIndex) in msgGrp.messages"
          :key="msgData.time"
          class="chat-content text-sm py-3 px-4 elevation-1"
          :class="[
            msgGrp.senderId === contact.id ? 'bg-card text-high-emphasis chat-left' : 'bg-primary text-white chat-right',
            msgGrp.messages.length - 1 !== msgIndex ? 'mb-2' : 'mb-1',
          ]"
        >
          {{ msgData.message }}
        </p>
        <div :class="{ 'text-right': msgGrp.senderId !== contact.id }">
          <VIcon
            v-if="msgGrp.senderId !== contact.id"
            size="16"
            :color="resolveFeedbackIcon(msgGrp.messages[msgGrp.messages.length - 1].feedback).color"
          >
            {{ resolveFeedbackIcon(msgGrp.messages[msgGrp.messages.length - 1].feedback).icon }}
          </VIcon>
          <span class="text-xs ms-1 text-disabled">{{ formatDate(msgGrp.messages[msgGrp.messages.length - 1].time, { hour: 'numeric', minute: 'numeric' }) }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang=scss>
.chat-log {
  .chat-content {
    border-end-end-radius: 8px;
    border-end-start-radius: 8px;

    &.chat-left {
      border-start-end-radius: 8px;
    }

    &.chat-right {
      border-start-start-radius: 8px;
    }
  }
}
</style>
