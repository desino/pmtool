<template>
    <h1 class="primary">Oppeertunities</h1>
    <!-- <PaginationComponent
        :currentPage="Number(currentPage)"
        :totalPages="Number(totalPages)"
        @page-changed="fetchAllHotels"
    /> -->
</template>

<script>
    import globalMixin from '@/globalMixin';
    import PaginationComponent from '../../Components/PaginationComponent.vue';
    import messageService from '../../Services/messageService';
    import OpportunityService from '../../Services/OpportunityService';
    import { mapState } from 'vuex/dist/vuex.cjs.js';
    export default {
        name: 'OpportunityListComponjent',
        mixins: [globalMixin],
        components: {
            PaginationComponent
        },
        data() {
            return {
                oppertunities: [],
                errors: {},
                filter: {}
            }
        },
        methods: {
            ...mapState(['loading']),
            async fetchAllOppertunities(page=1) { 
                this.clearMessages();
                try {
                    const params = {
                        page: page,
                        ... this.filter
                    }
                    // await this.setLoading(true);
                    const response = await OpportunityService.fetchAllOppertunites(params);
                    console.log('response:: ',response);
                    // await this.setLoading(true);
                    // this.hotels = response.content;
                    // this.currentPage = response.paginate.current_page;
                    // this.totalPages = response.paginate.last_page;
                    // await this.setLoading(false);
                } catch (error) {
                    this.handleOpportunityError(error);
                }
            },
            handleOpportunityError(error) {
                if (error.type === 'validation') {
                    this.errors = error.errors;
                } else {
                    messageService.setMessage(error.message, 'danger');
                }
            },
            clearMessages() {
                this.errors = {};
                messageService.clearMessage();
            },
        },
        created() {
            this.fetchAllOppertunities();
        },
        // mounted() {
        //     this.fetchAllOppertunities();
        // }
    }
</script>