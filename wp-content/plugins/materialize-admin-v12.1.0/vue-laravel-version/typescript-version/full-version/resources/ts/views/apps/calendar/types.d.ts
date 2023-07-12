import { CalendarEvent } from '@/@fake-db/types';
import type { Except } from 'type-fest';

export interface Event extends CalendarEvent {
  extendedProps: {
    calendar?: string
    location: string
    description: string
    guests: string[]
  }
}

export type NewEvent = Except<Event, 'id'>
