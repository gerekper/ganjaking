<script setup>
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import ComposeDialog from '@/views/apps/email/ComposeDialog.vue'
import EmailLeftSidebarContent from '@/views/apps/email/EmailLeftSidebarContent.vue'
import EmailView from '@/views/apps/email/EmailView.vue'
import { useEmail } from '@/views/apps/email/useEmail'
import { useEmailStore } from '@/views/apps/email/useEmailStore'
import { useResponsiveLeftSidebar } from '@core/composable/useResponsiveSidebar'
import { formatDateToMonthShort } from '@core/utils/formatters'

const { isLeftSidebarOpen } = useResponsiveLeftSidebar()

// Composables
const route = useRoute()
const store = useEmailStore()
const { labels, resolveLabelColor, emailMoveToFolderActions, shallShowMoveToActionFor, moveSelectedEmailTo } = useEmail()

// Compose dialog
const isComposeDialogVisible = ref(false)

// Ref
const q = ref('')

// ------------------------------------------------
const selectedEmails = ref([])

const toggleSelectedEmail = emailId => {
  const emailIndex = selectedEmails.value.indexOf(emailId)
  if (emailIndex === -1)
    selectedEmails.value.push(emailId)
  else
    selectedEmails.value.splice(emailIndex, 1)
}

const selectAllEmailCheckbox = computed(() => store.emails.length && store.emails.length === selectedEmails.value.length)
const isSelectAllEmailCheckboxIndeterminate = computed(() => Boolean(selectedEmails.value.length) && store.emails.length !== selectedEmails.value.length)

const isAllMarkRead = computed(() => {
  return selectedEmails.value.every(emailId => store.emails.find(email => email.id === emailId)?.isRead)
})

const selectAllCheckboxUpdate = () => {
  selectedEmails.value = !selectAllEmailCheckbox.value ? store.emails.map(email => email.id) : []
}

// Email View
const openedEmail = ref(null)

const emailViewMeta = computed(() => {
  const returnValue = {
    hasNextEmail: false,
    hasPreviousEmail: false,
  }

  if (openedEmail.value) {
    const openedEmailIndex = store.emails.findIndex(e => e.id === openedEmail.value.id)

    returnValue.hasNextEmail = !!store.emails[openedEmailIndex + 1]
    returnValue.hasPreviousEmail = !!store.emails[openedEmailIndex - 1]
  }
  
  return returnValue
})

// Fetch emails
const fetchEmails = async () => {
  selectedEmails.value = []
  await store.fetchEmails({
    q: q.value,
    filter: route.params.filter,
    label: route.params.label,
  })
}

const handleActionClick = async (action, emailIds = selectedEmails.value) => {
  if (!emailIds.length)
    return
  if (action === 'trash')
    store.updateEmails(emailIds, { isDeleted: true })
  else if (action === 'spam')
    store.updateEmails(emailIds, { folder: 'spam' })
  else if (action === 'unread')
    store.updateEmails(emailIds, { isRead: false })
  else if (action === 'read')
    store.updateEmails(emailIds, { isRead: true })
  else if (action === 'star')
    store.updateEmails(emailIds, { isStarred: true })
  else if (action === 'unstar')
    store.updateEmails(emailIds, { isStarred: false })
  await fetchEmails()
}

watch([
  q,
  () => route.params.filter,
  () => route.params.label,
], fetchEmails, { immediate: true })
watch([
  () => route.params.filter,
  () => route.params.label,
], () => {
  openedEmail.value = null
})

const handleMoveMailsTo = action => {
  moveSelectedEmailTo(action, selectedEmails.value)
  fetchEmails()
}

const updateLabel = label => {
  store.updateEmailLabels(selectedEmails.value, label)
  fetchEmails()
}

const changeOpenedEmail = dir => {
  if (!openedEmail.value)
    return
  const openedEmailIndex = store.emails.findIndex(e => e.id === openedEmail.value.id)
  const newEmailIndex = dir === 'previous' ? openedEmailIndex - 1 : openedEmailIndex + 1

  openedEmail.value = store.emails[newEmailIndex]
}

const openEmail = email => {
  openedEmail.value = email
  handleActionClick('read', [email.id])
}

const refreshOpenedEmail = async () => {
  await fetchEmails()
  if (openedEmail.value) {
    openedEmail.value = store.emails.find(e => e.id === openedEmail.value.id)
  }
}
</script>

<template>
  <VLayout class="email-app-layout">
    <VNavigationDrawer
      v-model="isLeftSidebarOpen"
      absolute
      touchless
      location="start"
      :temporary="$vuetify.display.mdAndDown"
    >
      <EmailLeftSidebarContent @toggle-compose-dialog-visibility="isComposeDialogVisible = !isComposeDialogVisible" />
    </VNavigationDrawer>
    <EmailView
      :email="openedEmail"
      :email-meta="emailViewMeta"
      @refresh="refreshOpenedEmail"
      @navigated="changeOpenedEmail"
      @close="openedEmail = null"
      @remove="handleActionClick('trash', openedEmail ? [openedEmail.id] : [])"
      @unread="handleActionClick('unread', openedEmail ? [openedEmail.id] : [])"
      @star="handleActionClick('star', openedEmail ? [openedEmail.id] : [])"
      @unstar="handleActionClick('unstar', openedEmail ? [openedEmail.id] : [])"
    />
    <VMain>
      <VCard
        flat
        class="email-content-list h-100 d-flex flex-column"
      >
        <div class="d-flex align-center">
          <IconBtn
            class="d-lg-none ms-3"
            @click="isLeftSidebarOpen = true"
          >
            <VIcon icon="mdi-menu" />
          </IconBtn>
          <!-- 👉 Search -->
          <VTextField
            v-model="q"
            density="default"
            class="email-search px-1 flex-grow-1"
            prepend-inner-icon="mdi-magnify"
            placeholder="Search email"
          />
        </div>

        <VDivider />

        <!-- 👉 Action bar -->
        <div class="py-2 px-5 d-flex align-center">
          <!-- TODO: Make checkbox primary on indeterminate state -->
          <VCheckbox
            :model-value="selectAllEmailCheckbox"
            :indeterminate="isSelectAllEmailCheckboxIndeterminate"
            @update:model-value="selectAllCheckboxUpdate"
          />

          <div
            class="w-100 d-sm-flex align-center action-bar-actions"
            :style="{
              visibility:
                isSelectAllEmailCheckboxIndeterminate || selectAllEmailCheckbox
                  ? undefined
                  : 'hidden',
            }"
          >
            <!-- Trash -->
            <IconBtn
              v-show="$route.params.filter !== 'trashed'"
              @click="handleActionClick('trash')"
            >
              <VIcon icon="mdi-delete-outline" />
              <VTooltip
                activator="parent"
                location="top"
              >
                Delete Mail
              </VTooltip>
            </IconBtn>

            <!-- Mark unread/read -->
            <IconBtn @click="isAllMarkRead ? handleActionClick('unread') : handleActionClick('read') ">
              <VIcon :icon="isAllMarkRead ? 'tabler-mail' : 'tabler-mail-opened'" />
              <VTooltip
                activator="parent"
                location="top"
              >
                {{ isAllMarkRead ? 'Mark as Unread' : 'Mark as Read' }}
              </VTooltip>
            </IconBtn>

            <!-- Move to folder -->
            <IconBtn>
              <VIcon icon="mdi-folder-outline" />
              <VTooltip
                activator="parent"
                location="top"
              >
                Folder
              </VTooltip>

              <VMenu activator="parent">
                <VList density="compact">
                  <template
                    v-for="moveTo in emailMoveToFolderActions"
                    :key="moveTo.title"
                  >
                    <VListItem
                      :class="
                        shallShowMoveToActionFor(moveTo.action) ? 'd-flex' : 'd-none'
                      "
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
                    @click="updateLabel(label.title)"
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
          </div>
          <VSpacer />
          <IconBtn @click="fetchEmails">
            <VIcon icon="mdi-refresh" />
          </IconBtn>
          <MoreBtn />
        </div>
        <VDivider />

        <!-- 👉 Emails list -->
        <PerfectScrollbar
          tag="ul"
          :options="{ wheelPropagation: false }"
          class="email-list"
        >
          <li
            v-for="email in store.emails"
            v-show="store.emails.length"
            :key="email.id"
            class="email-item d-flex align-center py-3 px-5 cursor-pointer"
            :class="[{ 'email-read': email.isRead }]"
            @click="openEmail(email)"
          >
            <VCheckbox
              :model-value="selectedEmails.includes(email.id)"
              class="flex-shrink-0"
              @update:model-value="toggleSelectedEmail(email.id)"
              @click.stop
            />
            <IconBtn
              :color="email.isStarred ? 'warning' : 'default'"
              @click.stop=" handleActionClick(email.isStarred ? 'unstar' : 'star', [email.id])"
            >
              <VIcon icon="mdi-star-outline" />
            </IconBtn>
            <VAvatar
              class="mx-2"
              size="32"
            >
              <VImg
                :src="email.from.avatar"
                :alt="email.from.name"
              />
            </VAvatar>
            <h6 class="mx-2 text-body-1 font-weight-medium text-high-emphasis">
              {{ email.from.name }}
            </h6>
            <span class="text-sm truncate">{{ email.subject }}</span>
            <VSpacer />
            <div
              class="email-meta"
              :class="$vuetify.display.xs ? 'd-none' : 'd-block'"
            >
              <VBadge
                v-for="label in email.labels"
                :key="label"
                inline
                :color="resolveLabelColor(label)"
                dot
              />
              <span class="text-xs text-disabled ms-2">{{
                formatDateToMonthShort(email.time)
              }}</span>
            </div>

            <!-- 👉 Email actions -->
            <div class="email-actions d-none">
              <IconBtn @click.stop="handleActionClick('trash', [email.id])">
                <VIcon icon="mdi-delete-outline" />
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  Delete Mail
                </VTooltip>
              </IconBtn>
              <IconBtn @click.stop=" handleActionClick(email.isRead ? 'unread' : 'read', [email.id])">
                <VIcon :icon="email.isRead ? 'mdi-email-outline' : 'mdi-email-open-outline'" />
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  {{ email.isRead ? 'Mark as Unread' : 'Mark as Read' }}
                </VTooltip>
              </IconBtn>
              <IconBtn @click.stop="handleActionClick('spam', [email.id])">
                <VIcon icon="mdi-alert-octagon-outline" />
                <VTooltip
                  activator="parent"
                  location="top"
                >
                  Move to Spam
                </VTooltip>
              </IconBtn>
            </div>
          </li>
          <li
            v-show="!store.emails.length"
            class="py-4 px-5 text-center"
          >
            <span class="text-high-emphasis">No items found.</span>
          </li>
        </PerfectScrollbar>
      </VCard>
      <ComposeDialog
        v-show="isComposeDialogVisible"
        @close="isComposeDialogVisible = false"
      />
    </VMain>
  </VLayout>
</template>

<route lang="yaml">
meta:
  layoutWrapperClasses: layout-content-height-fixed
</route>

<style lang="scss">
@use "@styles/variables/_vuetify.scss";
@use "@core-scss/base/_mixins.scss";

// ℹ️ Remove border. Using variant plain cause UI issue, caret isn't align in center
.email-search {
  .v-field__outline {
    display: none;
  }
}

.email-app-layout {
  border-radius: vuetify.$card-border-radius;

  @include mixins.elevation(vuetify.$card-elevation);

  $sel-email-app-layout: &;

  @at-root {
    .skin--bordered {
      @include mixins.bordered-skin($sel-email-app-layout);
    }
  }
}

.email-content-list {
  border-end-start-radius: 0;
  border-start-start-radius: 0;
}

.email-list {
  white-space: nowrap;

  .email-item {
    transition: all 0.2s ease-in-out;
    will-change: transform, box-shadow;

    &.email-read {
      background-color: rgba(var(--v-theme-on-surface), var(--v-hover-opacity));
    }

    & + .email-item {
      border-block-start: 1px solid rgba(var(--v-border-color), var(--v-border-opacity));
    }
  }

  .email-item:hover {
    transform: translateY(-2px);

    @include mixins.elevation(3);

    .email-actions {
      display: block !important;
    }

    @media screen and (max-width: 600px) {
      .email-actions {
        display: none !important;
      }
    }

    .email-meta {
      display: none;
    }

    + .email-item {
      border-color: transparent;
    }
  }
}

.email-compose-dialog {
  position: absolute;
  inset-block-end: 0;
  inset-inline-end: 0;
  min-inline-size: 100%;

  @media only screen and (min-width: 800px) {
    min-inline-size: 712px;
  }
}
</style>
