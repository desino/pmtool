import SolutionDesignComponent from "./SolutionDesignComponent.vue";
import DetailSolutionDesignComponent from "./DetailSolutionDesignComponent.vue";

export default [
    {
        path: '/solution-design/:id',
        name: 'solution-design',
        component: SolutionDesignComponent,
        meta: { requiresAuth: true, title: 'Solution Design' },
    },
    {
        path: '/solution-design/detail/:id',
        name: 'solution-design.detail',
        component: DetailSolutionDesignComponent,
        meta: { requiresAuth: true, title: 'Solution Design Detail' },
    }
];
