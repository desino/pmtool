<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush mb-3 mt-2">
                <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                    <div class="row g-1 align-items-center">
                        <div class="col-3 fw-bold small">
                            {{ $t('developer_workload_table.resource_th_text') }}
                        </div>
                        <div class="col-3 fw-bold small">
                            {{ $t('developer_workload_table.visible_tickets_th_text') }}
                        </div>
                        <div class="col-3 fw-bold small">
                            {{ $t('developer_workload_table.invisible_tickets_th_text') }}
                        </div>
                        <div class="col-3 fw-bold small text-end">
                            {{ $t('developer_workload_table.total_workload_th_text') }}
                        </div>
                    </div>
                </li>
                <li v-for="(developerWorkload, index) in developerWorkloads" v-if="developerWorkloads.length > 0"
                    :key="developerWorkload.id" class="list-group-item p-1 list-group-item-action">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-3">
                            <router-link
                                :to="{ name: 'all-ticket-without-initiative', query: { user_id: developerWorkload?.user_id } }"
                                target="_blank" class="text-decoration-none" role="button">
                                {{ developerWorkload.user_name }}
                            </router-link>
                        </div>
                        <div class="col-3">
                            <span :class="{ 'link-desino': developerWorkload.visible_tickets_count > 0 }"
                                :role="developerWorkload.visible_tickets_count > 0 ? 'button' : ''"
                                @click="openTicketsModal(developerWorkload, 'visible')">
                                {{ developerWorkload.visible_tickets_count }}
                                <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                    {{ developerWorkload.visible_tickets_hours }} hrs
                                </span>
                            </span>
                        </div>
                        <div class="col-3">
                            <span :class="{ 'link-desino': developerWorkload.invisible_tickets_count > 0 }"
                                :role="developerWorkload.invisible_tickets_count > 0 ? 'button' : ''"
                                @click="openTicketsModal(developerWorkload, 'invisible')">
                                {{ developerWorkload.invisible_tickets_count }}
                                <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                    {{ developerWorkload.invisible_tickets_hours }} hrs
                                </span>
                            </span>
                        </div>
                        <div class="col-3 text-end">
                            <span :class="{ 'link-desino': developerWorkload.total_tickets_count > 0 }"
                                :role="developerWorkload.total_tickets_count > 0 ? 'button' : ''"
                                @click="openTicketsModal(developerWorkload, 'all')">
                                {{ developerWorkload.total_tickets_count }}
                                <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                    {{ developerWorkload.total_tickets_hours }} hrs
                                </span>
                            </span>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
                            {{ $t('developer_workload_table.no_developer_workload_found_text') }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div id="developerWorkloadTicketModalModal" aria-hidden="true"
        aria-labelledby="developerWorkloadTicketModalModalLabel" class="modal fade" tabindex="-1">
        <DeveloperWorkloadTicketModalComponent ref="developerWorkloadTicketModalModalComponent" />
    </div>
</template>

<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import store from '../../store';
import DeveloperWorkloadService from '../../services/DeveloperWorkloadService';
import messageService from '../../services/messageService';
import DeveloperWorkloadTicketModalComponent from './DeveloperWorkloadTicketModalComponent.vue';
import { Modal } from 'bootstrap';

export default {
    name: 'DevelopmentWorkloadComponent',
    components: {
        GlobalMessage,
        DeveloperWorkloadTicketModalComponent
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
        async openTicketsModal(developerWorkload, typeOfTickets) {
            const passDeveloperWorkload = {
                user_name: developerWorkload.user_name,
                user_id: developerWorkload.user_id,
                type_of_tickets: typeOfTickets
            }
            this.$refs.developerWorkloadTicketModalModalComponent.getDeveloperWorkloadTicketModalData(passDeveloperWorkload);
            const modalElement = document.getElementById('developerWorkloadTicketModalModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
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
