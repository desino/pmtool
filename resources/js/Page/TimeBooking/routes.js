import BookingComponent from "./BookingComponent.vue";

export default [
    {
        path: '/time-booking',
        name: 'time-booking.booking',
        component: BookingComponent,
        meta: { requiresAuth: true, title: 'Time Booking' },
    }
];