<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { useTheme } from 'vuetify'
import { useEmail } from '@/views/apps/email/useEmail'
import { useEmailStore } from '@/views/apps/email/useEmailStore'
import { formatDate } from '@core/utils/formatters'

const props = defineProps({
  email: {
    type: null,
    required: true,
  },
  emailMeta: {
    type: Object,
    required: true,
  },
})

const emit = defineEmits([
  'refresh',
  'navigated',
  'close',
  'trash',
  'unread',
  'read',
  'star',
  'unstar',
])

const store = useEmailStore()
const { current } = useTheme()
const { labels, resolveLabelColor, emailMoveToFolderActions, shallShowMoveToActionFor, moveSelectedEmailTo } = useEmail()

const handleMoveMailsTo = action => {
  moveSelectedEmailTo(action, [props.email.id])
  emit('refresh')
  emit('close')
}

const updateMailLabel = label => {
  store.updateEmailLabels([props.email.id], label)
  emit('refresh')
}
</script>

<template>
  <!-- ℹ️ calc(100% - 256px) => 265px is left sidebar width -->
  <VNavigationDrawer
    temporary
    :model-value="!!props.email"
    location="right"
    :scrim="false"
    floating
    class="email-view"
  >
    <template v-if="props.email">
      <!-- 👉 header -->

      <div class="email-view-header d-flex align-center px-5 py-3">
        <IconBtn
          class="me-4 flip-in-rtl"
          @click="$emit('close')"
        >
          <VIcon
            size="32"
            icon="mdi-chevron-left"
          />
        </IconBtn>

        <div class="d-flex align-center flex-wrap flex-grow-1 overflow-hidden gap-2">
          <h2 class="text-body-1 text-high-emphasis text-truncate">
            {{ props.email.subject }}
          </h2>

          <div class="d-flex flex-wrap gap-1">
            <VChip
              v-for="label in props.email.labels"
              :key="label"
              :color="resolveLabelColor(label)"
              density="comfortable"
              class="px-2 text-capitalize me-2 flex-shrink-0"
            >
              {{ label }}
            </VChip>
          </div>
        </div>

        <div class="d-flex align-center">
          <IconBtn
            :disabled="!props.emailMeta.hasPreviousEmail"
            class="flip-in-rtl"
            @click="$emit('navigated', 'previous')"
          >
            <VIcon icon="mdi-chevron-left" />
          </IconBtn>
          <IconBtn
            class="flip-in-rtl"
            :disabled="!props.emailMeta.hasNextEmail"
            @click="$emit('navigated', 'next')"
          >
            <VIcon icon="mdi-chevron-right" />
          </IconBtn>
        </div>
      </div>

      <VDivider />

      <!-- 👉 Action bar -->
      <div class="email-view-action-bar d-flex align-center text-medium-emphasis px-5">
        <!-- Trash -->
        <IconBtn
          v-show="!props.email.isDeleted"
          @click="$emit('trash'); $emit('close')"
        >
          <VIcon icon="mdi-delete-outline" />
          <VTooltip
            activator="parent"
            location="top"
          >
            Delete Mail
          </VTooltip>
        </IconBtn>

        <!-- Read/Unread -->
        <IconBtn @click.stop="$emit('unread'); $emit('close')">
          <VIcon icon="mdi-email-outline" />
          <VTooltip
            activator="parent"
            location="top"
          >
            Mark as Unread
          </VTooltip>
        </IconBtn>

        <!-- Move to folder -->
        <IconBtn>
          <VIcon icon="mdi-folder-outline" />
          <VTooltip
            activator="parent"
            location="top"
          >
            Move to
          </VTooltip>

          <VMenu activator="parent">
            <VList density="compact">
              <template
                v-for="moveTo in emailMoveToFolderActions"
                :key="moveTo.title"
              >
                <VListItem
                  :class="shallShowMoveToActionFor(moveTo.action) ? 'd-flex' : 'd-none'"
                  class="align-center"
                  href="#"
                  @click="handleMoveMailsTo(moveTo.action)"
                >
                  <template #prepend>
                    <VIcon
                      :icon="moveTo.icon"
                      class="me-2"
                      size="20"
                    />
                  </template>
                  <VListItemTitle class="text-capitalize">
                    {{ moveTo.action }}
                  </VListItemTitle>
                </VListItem>
              </template>
            </VList>
          </VMenu>
        </IconBtn>

        <!-- Update labels -->
        <IconBtn>
          <VIcon icon="mdi-label-outline" />
          <VTooltip
            activator="parent"
            location="top"
          >
            Label
          </VTooltip>

          <VMenu activator="parent">
            <VList density="compact">
              <VListItem
                v-for="label in labels"
                :key="label.title"
                href="#"
                @click.stop="updateMailLabel(label.title)"
              >
                <template #prepend>
                  <VBadge
                    inline
                    :color="resolveLabelColor(label.title)"
                    dot
                  />
                </template>
                <VListItemTitle class="ms-2 text-capitalize">
                  {{ label.title }}
                </VListItemTitle>
              </VListItem>
            </VList>
          </VMenu>
        </IconBtn>

        <VSpacer />

        <!-- Star/Unstar -->
        <IconBtn
          :color="props.email.isStarred ? 'warning' : 'default'"
          @click="props.email?.isStarred ? $emit('unstar') : $emit('star'); $emit('refresh')"
        >
          <VIcon icon="mdi-star-outline" />
        </IconBtn>

        <!-- Dots vertical -->
        <MoreBtn />
      </div>

      <VDivider />

      <!-- 👉 Mail Content -->
      <PerfectScrollbar
        tag="div"
        class="mail-content-container flex-grow-1"
        :options="{ wheelPropagation: false }"
        :style="current.dark ? '--v-email-content-bg:#3E4461' : '--v-email-content-bg:#F9F8F9'"
      >
        <VCard class="border ma-5">
          <VCardText class="mail-header">
            <div class="d-flex align-start align-sm-center">
              <VAvatar
                size="38"
                class="me-3"
              >
                <VImg
                  :src="props.email.from.avatar"
                  :alt="props.email.from.name"
                />
              </VAvatar>

              <div class="d-flex flex-wrap flex-grow-1 overflow-hidden">
                <div class="text-truncate">
                  <span class="text-base d-block font-weight-medium text-truncate">{{ props.email.from.name }}</span>
                  <span class="text-sm text-disabled">{{ props.email.from.email }}</span>
                </div>

                <VSpacer />

                <div class="d-flex align-center">
                  <span class="me-2">{{ formatDate(props.email.time) }}</span>
                  <IconBtn v-show="props.email.attachments.length">
                    <VIcon icon="mdi-attachment" />
                  </IconBtn>
                </div>
              </div>
              <MoreBtn class="align-self-sm-center" />
            </div>
          </VCardText>

          <VDivider />

          <VCardText>
            <!-- eslint-disable vue/no-v-html -->
            <div
              class="text-base"
              v-html="props.email.message"
            />
            <!-- eslint-enable -->
          </VCardText>

          <template v-if="props.email.attachments.length">
            <VDivider />

            <VCardText class="d-flex flex-column gap-y-4">
              <span>Attachments</span>
              <div
                v-for="attachment in props.email.attachments"
                :key="attachment.fileName"
                class="d-flex align-center"
              >
                <VImg
                  :src="attachment.thumbnail"
                  :alt="attachment.fileName"
                  aspect-ratio="1"
                  max-height="24"
                  max-width="24"
                  class="me-2"
                />
                <span>{{ attachment.fileName }}</span>
              </div>
            </VCardText>
          </template>
        </VCard>

        <VCard class="border mx-5 mb-5">
          <VCardText>
            <div class="text-base">
              Click here to <span class="text-primary cursor-pointer">
                Reply
              </span> or <span class="text-primary cursor-pointer">
                Forward
              </span>
            </div>
          </VCardText>
        </VCard>
      </PerfectScrollbar>
    </template>
  </VNavigationDrawer>
</template>

<style lang="scss">
.email-view {
  inline-size: 100% !important;

  @media only screen and (min-width: 1280px) {
    inline-size: calc(100% - 256px) !important;
  }

  .v-navigation-drawer__content {
    display: flex;
    flex-direction: column;
  }
}

.email-view-action-bar {
  min-block-size: 56px;
}

.mail-content-container {
  background-color: var(--v-email-content-bg);

  .mail-header {
    min-block-size: 84px;
  }

  .v-card {
    border: 1px solid rgba(var(--v-theme-on-surface), var(--v-border-opacity));
  }
}
</style>
