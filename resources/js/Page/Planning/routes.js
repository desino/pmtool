import PlanningComponent from "./PlanningComponent.vue";

export default [
    {
        path: '/planning',
        name: 'planning',
        component: PlanningComponent,
        meta: { requiresAuth: true, title: 'Planning' },
    }
];