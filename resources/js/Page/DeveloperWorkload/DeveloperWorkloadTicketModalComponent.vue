<template>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" v-if="type_of_ticket == 'visible'">
                    <span class='badge bg-secondary fs-5'>{{ user_name }}</span>
                    {{ $t('developer_workload_ticket_visible_modal_title') }}
                </h5>
                <h5 class="modal-title" v-if="type_of_ticket == 'invisible'">
                    <span class='badge bg-secondary fs-5'>{{ user_name }}</span>
                    {{ $t('developer_workload_ticket_invisible_modal_title') }}
                </h5>
                <h5 class="modal-title" v-if="type_of_ticket == 'all'">
                    <span class='badge bg-secondary fs-5'>{{ user_name }}</span>
                    {{ $t('developer_workload_ticket_all_modal_title') }}
                </h5>
            </div>
            <div class="modal-body py-0">
                <GlobalMessage v-if="showMessage" scope="modal" />
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                        <div class="row g-1 align-items-center">
                            <div class="col-9 col-lg-10">
                                {{ $t('developer_workload.ticket_modal.li.name.text') }}
                            </div>
                            <div class="col-3 col-lg-2 text-end">
                                {{ $t('developer_workload.ticket_modal.li.current_owner.text') }}
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item p-1 list-group-item-action" v-if="ticketList.length > 0"
                        v-for="ticket in ticketList" :key="ticket.id">
                        <div class="row g-1 align-items-center" style="min-height: 48px;">
                            <div class="col-9 col-lg-10">
                                {{ ticket?.composed_name }}
                                <router-link target="_blank"
                                    :to="{ name: 'task.detail', params: { initiative_id: ticket?.initiative_id, ticket_id: ticket?.id } }"
                                    class="ms-2">
                                    <i class="bi bi-link-45deg"></i>
                                </router-link>
                            </div>
                            <div class="col-3 col-lg-2 text-end">
                                {{ ticket?.current_action?.user?.name }}
                            </div>
                        </div>
                    </li>
                    <li v-else class="border border-top-0 list-group-item px-0 py-1 list-group-item-action">
                        <div class="row g-1 align-items-center" style="min-height: 48px;">
                            <div class="col-12 fw-bold fst-italic text-center">
                                {{ $t('developer_workload.ticket_modal.no_tickets.text') }}
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-0 p-0 d-block">
                <div class="row g-1 align-items-center">
                    <div class="col-12">
                        <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import DeveloperWorkloadService from '../../services/DeveloperWorkloadService';
import messageService from '../../services/messageService';
import { mapActions } from 'vuex';

export default {
    name: 'DeveloperWorkloadTicketModalComponent',
    components: {
        GlobalMessage
    },
    data() {
        return {
            user_name: '',
            type_of_ticket: '',
            ticketList: [],
            errors: {},
            showMessage: true
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async getDeveloperWorkloadTicketModalData(passDeveloperWorkload) {
            this.type_of_ticket = passDeveloperWorkload.type_of_tickets;
            this.user_name = passDeveloperWorkload.user_name;
            try {
                this.clearMessages();
                this.setLoading(true);
                const passData = {
                    user_id: passDeveloperWorkload.user_id,
                    type_of_tickets: passDeveloperWorkload.type_of_tickets
                }
                const { content: { tickets } } = await DeveloperWorkloadService.getDeveloperWorkloadTicketModalData(passData);
                this.ticketList = tickets;
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger', 'modal');
            }
            this.setLoading(false);
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage('modal');
        },
    },
    mounted() {
        this.clearMessages();
    }
}
</script>