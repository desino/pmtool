<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content my-3">
        <ul class="list-group list-group-flush mb-3 mt-2">
            <li class="font-weight-bold bg-desino text-white rounded-top list-group-item">
                <div class="row w-100 fw-bold">
                    <div class="col-md-3">
                        {{ $t('developer_workload_table.resource_th_text') }}
                    </div>
                    <div class="col-md-3">
                        {{ $t('developer_workload_table.visible_tickets_th_text') }}
                    </div>
                    <div class="col-md-3">
                        {{ $t('developer_workload_table.invisible_tickets_th_text') }}
                    </div>
                    <div class="col-md-3">
                        {{ $t('developer_workload_table.total_workload_th_text') }}
                    </div>
                </div>
            </li>
            <li v-for="(developerWorkload, index) in developerWorkloads" v-if="developerWorkloads.length > 0"
                :key="developerWorkload.id" class="border list-group-item list-group-item-action">
                <div class="row w-100">
                    <div class="col-md-3">
                        {{ developerWorkload.user_name }}
                    </div>
                    <div class="col-md-3">
                        <!-- <span v-if="developerWorkload.visible_tickets_count > 0">
                            <router-link
                                :to="{ name: 'all-ticket-without-initiative', query: { is_visible: true, user_id: developerWorkload?.user_id, filter_from: 'developer_workload' } }"
                                target="_blank" class="text-decoration-none" role="button">
                                {{ developerWorkload.display_visible_tickets_count_hours }}
                            </router-link>
                        </span>
                        <span v-else>
                            {{ developerWorkload.display_visible_tickets_count_hours }}
                        </span> -->
                        {{ developerWorkload.display_visible_tickets_count_hours }}
                    </div>
                    <div class="col-md-3">
                        {{ developerWorkload.display_invisible_tickets_count_hours }}
                    </div>
                    <div class="col-md-3">
                        {{ developerWorkload.display_total_tickets_count_hours }}
                    </div>
                </div>
            </li>
            <li v-else class="border list-group-item px-0 py-1 list-group-item-action">
                <div class="col h4 fw-bold text-center">
                    {{ $t('developer_workload_table.no_developer_workload_found_text') }}
                </div>
            </li>
        </ul>
    </div>
</template>

<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import store from '../../store';
import DeveloperWorkloadService from '../../services/DeveloperWorkloadService';
import messageService from '../../services/messageService';

export default {
    name: 'DevelopmentWorkloadComponent',
    components: {
        GlobalMessage
    },
    data() {
        return {
            developerWorkloads: [],
            showMessage: true,
            errors: {},
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async getDeveloperWorkloads() {
            this.clearMessages();
            try {
                this.setLoading(true);
                const { content: { developerWorkloads } } = await DeveloperWorkloadService.getDeveloperWorkloads();
                this.developerWorkloads = developerWorkloads;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
    },
    mounted() {
        this.getDeveloperWorkloads();
        const setHeaderData = {
            page_title: this.$t('developer_workload.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
    },
}
</script>