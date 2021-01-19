export const routes = [
    {
        path: '/',
        name: 'shows',
        component: () => import('./views/Shows.vue')
    },
    {
        path: '/events/:eventId/places',
        name: 'event.eventId',
        component: () => import('./views/Places.vue')
    },
    {
        path: '/shows/:showId/events',
        name: 'shows.eventId',
        component: () => import('./views/Events.vue')
    },
    {
        path: '/events/:eventId/reserve',
        name: 'places',
        component: () => import('./views/Reserve.vue')
    },
]