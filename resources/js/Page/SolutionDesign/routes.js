import SolutionDesignComponent from "./SolutionDesignComponent.vue";

export default [
    {
        path: '/solution-design/:id',
        name: 'solution-design',
        component: SolutionDesignComponent,
        // component: OpportunityListComponent,
        meta: {requiresAuth: true, title: 'Solution Design'},
    }
];