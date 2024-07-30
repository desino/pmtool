import OpportunityListComponent from './OpportunityListcomponent.vue';

export default [
    {
        path: '/opportunities',
        name: 'opportunities',
        component: OpportunityListComponent,
        meta: {requiresAuth: true, title: 'Opportunities'},        
    }
];