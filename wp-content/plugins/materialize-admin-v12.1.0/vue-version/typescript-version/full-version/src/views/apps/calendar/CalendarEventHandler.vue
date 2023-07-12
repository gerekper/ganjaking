<script setup lang="ts">
import type { Options } from 'flatpickr'
import { PerfectScrollbar } from 'vue3-perfect-scrollbar'
import { VForm } from 'vuetify/components/VForm'

import type { Event, NewEvent } from './types'
import { useCalendarStore } from './useCalendarStore'
import avatar1 from '@images/avatars/avatar-1.png'
import avatar2 from '@images/avatars/avatar-2.png'
import avatar3 from '@images/avatars/avatar-3.png'
import avatar5 from '@images/avatars/avatar-5.png'
import avatar6 from '@images/avatars/avatar-6.png'
import avatar7 from '@images/avatars/avatar-7.png'

import { requiredValidator, urlValidator } from '@validators'

const props = defineProps<Props>()

const emit = defineEmits<{
  (e: 'update:isDrawerOpen', val: boolean): void
  (e: 'addEvent', val: NewEvent): void
  (e: 'updateEvent', val: Event): void
  (e: 'removeEvent', eventId: string): void
}>()

interface Props {
  isDrawerOpen: boolean
  event: (Event | NewEvent)
}

// 👉 store
const store = useCalendarStore()
const refForm = ref<VForm>()

// 👉 Event
const event = ref<Event | NewEvent>(JSON.parse(JSON.stringify(props.event)))

const resetEvent = () => {
  event.value = JSON.parse(JSON.stringify(props.event))
  nextTick(() => {
    refForm.value?.resetValidation()
  })
}

watch(() => props.isDrawerOpen, resetEvent)

const removeEvent = () => {
  emit('removeEvent', event.value.id)

  // Close drawer
  emit('update:isDrawerOpen', false)
}

const handleSubmit = () => {
  refForm.value?.validate()
    .then(({ valid }) => {
      if (valid) {
        // If id exist on id => Update event
        if ('id' in event.value)
          emit('updateEvent', event.value)

        // Else => add new event
        else emit('addEvent', event.value)

        // Close drawer
        emit('update:isDrawerOpen', false)
      }
    })
}

const guestsOptions = [
  { avatar: avatar1, name: 'Jane Foster' },
  { avatar: avatar3, name: 'Donna Frank' },
  { avatar: avatar5, name: 'Gabrielle Robertson' },
  { avatar: avatar7, name: 'Lori Spears' },
  { avatar: avatar6, name: 'Sandy Vega' },
  { avatar: avatar2, name: 'Cheryl May' },
]

// 👉 Form

const onCancel = () => {
  emit('update:isDrawerOpen', false)

  nextTick(() => {
    refForm.value?.reset()
    resetEvent()
    refForm.value?.resetValidation()
  })
}

const startDateTimePickerConfig = computed(() => {
  const config: Options = { enableTime: !event.value.allDay, dateFormat: `Y-m-d${event.value.allDay ? '' : ' H:i'}` }

  if (event.value.end)
    config.maxDate = event.value.end

  return config
})

const endDateTimePickerConfig = computed(() => {
  const config: Options = { enableTime: !event.value.allDay, dateFormat: `Y-m-d${event.value.allDay ? '' : ' H:i'}` }

  if (event.value.start)
    config.minDate = event.value.start

  return config
})
</script>

<template>
  <VNavigationDrawer
    temporary
    location="end"
    :model-value="props.isDrawerOpen"
    width="420"
    class="scrollable-content"
    @update:model-value="(val) => $emit('update:isDrawerOpen', val)"
  >
    <!-- 👉 Header -->
    <AppDrawerHeaderSection
      :title="event.id ? 'Update Event' : 'Add Event'"
      @cancel="$emit('update:isDrawerOpen', false)"
    >
      <template #beforeClose>
        <IconBtn
          v-show="event.id"
          @click="removeEvent"
        >
          <VIcon
            size="18"
            icon="mdi-trash-can-outline"
          />
        </IconBtn>
      </template>
    </AppDrawerHeaderSection>

    <PerfectScrollbar :options="{ wheelPropagation: false }">
      <VCard flat>
        <VCardText>
          <!-- SECTION Form -->
          <VForm
            ref="refForm"
            @submit.prevent="handleSubmit"
          >
            <VRow>
              <!-- 👉 Title -->
              <VCol cols="12">
                <VTextField
                  v-model="event.title"
                  label="Title"
                  placeholder="Meeting with Jane"
                  :rules="[requiredValidator]"
                />
              </VCol>

              <!-- 👉 Calendar -->
              <VCol cols="12">
                <VSelect
                  v-model="event.extendedProps.calendar"
                  label="Calendar"
                  placeholder="Select Event Type"
                  :rules="[requiredValidator]"
                  :items="store.availableCalendars"
                  :item-title="item => item.label"
                  :item-value="item => item.label"
                >
                  <template #selection="{ item }">
                    <div
                      v-show="event.extendedProps.calendar"
                      class="align-center"
                      :class="event.extendedProps.calendar ? 'd-flex' : ''"
                    >
                      <VBadge
                        :color="item.raw.color"
                        inline
                        dot
                        class="pa-1 mb-1"
                      />
                      <span>{{ item.raw.label }}</span>
                    </div>
                  </template>
                </VSelect>
              </VCol>

              <!-- 👉 Start date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(startDateTimePickerConfig)"
                  v-model="event.start"
                  :rules="[requiredValidator]"
                  label="Start date"
                  placeholder="Select Date"
                  :config="startDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 End date -->
              <VCol cols="12">
                <AppDateTimePicker
                  :key="JSON.stringify(endDateTimePickerConfig)"
                  v-model="event.end"
                  :rules="[requiredValidator]"
                  label="End date"
                  placeholder="Select End Date"
                  :config="endDateTimePickerConfig"
                />
              </VCol>

              <!-- 👉 All day -->
              <VCol cols="12">
                <VSwitch
                  v-model="event.allDay"
                  label="All day"
                />
              </VCol>

              <!-- 👉 Event URL -->
              <VCol cols="12">
                <VTextField
                  v-model="event.url"
                  label="Event URL"
                  placeholder="https://event.com/meeting"
                  :rules="[urlValidator]"
                  type="url"
                />
              </VCol>

              <!-- 👉 Guests -->
              <VCol cols="12">
                <VSelect
                  v-model="event.extendedProps.guests"
                  label="Guests"
                  placeholder="Select guests"
                  :items="guestsOptions"
                  :item-title="item => item.name"
                  :item-value="item => item.name"
                  chips
                  multiple
                  eager
                />
              </VCol>

              <!-- 👉 Location -->
              <VCol cols="12">
                <VTextField
                  v-model="event.extendedProps.location"
                  label="Location"
                  placeholder="Meeting room"
                />
              </VCol>

              <!-- 👉 Description -->
              <VCol cols="12">
                <VTextarea
                  v-model="event.extendedProps.description"
                  label="Description"
                  placeholder="Meeting description"
                />
              </VCol>

              <!-- 👉 Form buttons -->
              <VCol cols="12">
                <VBtn
                  type="submit"
                  class="me-3"
                >
                  Submit
                </VBtn>
                <VBtn
                  variant="outlined"
                  color="secondary"
                  @click="onCancel"
                >
                  Cancel
                </VBtn>
              </VCol>
            </VRow>
          </VForm>
        <!-- !SECTION -->
        </VCardText>
      </VCard>
    </PerfectScrollbar>
  </VNavigationDrawer>
</template>
