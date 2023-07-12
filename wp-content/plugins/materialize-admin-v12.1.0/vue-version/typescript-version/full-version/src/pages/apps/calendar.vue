<script setup lang="ts">
import FullCalendar from '@fullcalendar/vue3'
import { blankEvent, useCalendar } from '@/views/apps/calendar/useCalendar'
import { useCalendarStore } from '@/views/apps/calendar/useCalendarStore'
import { useResponsiveLeftSidebar } from '@core/composable/useResponsiveSidebar'

// Components
import CalendarEventHandler from '@/views/apps/calendar/CalendarEventHandler.vue'

// 👉 Store
const store = useCalendarStore()

// 👉 Event
const event = ref(structuredClone(blankEvent))
const isEventHandlerSidebarActive = ref(false)

watch(isEventHandlerSidebarActive, val => {
  if (!val)
    event.value = structuredClone(blankEvent)
})

const { isLeftSidebarOpen } = useResponsiveLeftSidebar()

// 👉 useCalendar
const { refCalendar, calendarOptions, addEvent, updateEvent, removeEvent } = useCalendar(event, isEventHandlerSidebarActive, isLeftSidebarOpen)

// SECTION Sidebar
// 👉 Check all
const checkAll = computed({
  /*
    GET: Return boolean `true` => if length of options matches length of selected filters => Length matches when all events are selected
    SET: If value is `true` => then add all available options in selected filters => Select All
          Else if => all filters are selected (by checking length of both array) => Empty Selected array  => Deselect All
  */
  get: () => store.selectedCalendars.length === store.availableCalendars.length,
  set: val => {
    if (val)
      store.selectedCalendars = store.availableCalendars.map(i => i.label)

    else if (store.selectedCalendars.length === store.availableCalendars.length)
      store.selectedCalendars = []
  },
})

// !SECTION
</script>

<template>
  <div>
    <VCard>
      <!-- `z-index: 0` Allows overlapping vertical nav on calendar -->
      <VLayout style="z-index: 0;">
        <!-- 👉 Navigation drawer -->
        <VNavigationDrawer
          v-model="isLeftSidebarOpen"
          width="250"
          absolute
          touchless
          location="start"
          class="calendar-add-event-drawer"
          :temporary="$vuetify.display.mdAndDown"
        >
          <div class="pa-5 d-flex flex-column gap-y-7">
            <VBtn
              block
              prepend-icon="mdi-plus"
              @click="isEventHandlerSidebarActive = true"
            >
              Add event
            </VBtn>
            <div>
              <p class="text-sm text-uppercase text-medium-emphasis mb-3">
                Calendars
              </p>

              <div class="d-flex flex-column calendars-checkbox">
                <VCheckbox
                  v-model="checkAll"
                  label="View all"
                  density="default"
                  color="secondary"
                />
                <VCheckbox
                  v-for="calendar in store.availableCalendars"
                  :key="calendar.label"
                  v-model="store.selectedCalendars"
                  :value="calendar.label"
                  :color="calendar.color"
                  :label="calendar.label"
                  density="default"
                />
              </div>
            </div>
          </div>
        </VNavigationDrawer>
        <VMain>
          <VCard flat>
            <FullCalendar
              ref="refCalendar"
              :options="calendarOptions"
            />
          </VCard>
        </VMain>
      </VLayout>
    </VCard>
    <CalendarEventHandler
      v-model:isDrawerOpen="isEventHandlerSidebarActive"
      :event="event"
      @add-event="addEvent"
      @update-event="updateEvent"
      @remove-event="removeEvent"
    />
  </div>
</template>

<style lang="scss">
@use "@core/scss/template/libs/full-calendar";

.calendars-checkbox {
  .v-label {
    color: rgba(var(--v-theme-on-surface), var(--v-high-emphasis-opacity));
    opacity: var(--v-high-emphasis-opacity);
  }
}

.calendar-add-event-drawer {
  &.v-navigation-drawer:not(.v-navigation-drawer--temporary) {
    border-end-start-radius: 0.375rem;
    border-start-start-radius: 0.375rem;
  }
}
</style>

<style lang="scss" scoped>
.v-layout {
  overflow: visible !important;
}
</style>
