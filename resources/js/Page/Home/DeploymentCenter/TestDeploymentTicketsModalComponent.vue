<template>
    <div class="modal-dialog modal-lg">
        <form @submit.prevent="submitTestDeploymentTicket">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="testDeploymentTicketsModalLabel"
                        v-html="formattedModalTitleTestDeployment()"></h5>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" scope="modal" />
                    <ul class="list-group mb-3 mt-2">
                        <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                            <div class="row g-1 w-100 align-items-center">
                                <div class="col-1 col-md-1" v-if="ticketList.length > 0">
                                    <input class="form-check-input" type="checkbox"
                                        v-model="isChkAllTestDeploymentTickets"
                                        @change="handleSelectAllTestDeploymentTickets">
                                </div>
                                <div class="col-6 col-md-8 fw-bold small">
                                    {{ $t('home.deployment_center.test_deployment.ticket_modal.li.name.text') }}
                                </div>
                                <div class="col-5 col-md-3 fw-bold small text-end">
                                    {{ $t('home.deployment_center.test_deployment.ticket_modal.li.develop_by.text') }}
                                </div>
                            </div>
                        </li>
                        <li class="border list-group-item p-1 list-group-item-action border-top-0"
                            v-if="ticketList.length > 0" v-for="ticket in ticketList" :key="ticket.id">
                            <div class="row g-1 w-100 align-items-center">
                                <div class="col-1 col-md-1">
                                    <input class="form-check-input" type="checkbox"
                                        :id="'chk_test_deployment_ticket_' + ticket.id" v-model="ticket.isChecked"
                                        @change="handleSelectTestDeploymentTicket(ticket)">
                                </div>
                                <div class="col-6 col-md-8" :for="'chk_test_deployment_ticket_' + ticket.id">
                                    {{ ticket?.composed_name }}
                                    <router-link target="_blank"
                                        :to="{ name: 'task.detail', params: { initiative_id: ticket.initiative_id, ticket_id: ticket.id } }"
                                        class="ms-2">
                                        <i class="bi bi-link-45deg"></i>
                                    </router-link>
                                </div>
                                <div class="col-5 col-md-3 text-end" :for="'chk_test_deployment_ticket_' + ticket.id">
                                    {{ ticket?.develop_action?.user?.name }}
                                </div>
                            </div>
                        </li>
                        <li v-else class="border list-group-item p-1 list-group-item-action border-top-0">
                            <div class="row w-100">
                                <div class="col-12 text-center">
                                    {{ $t('home.deployment_center.test_deployment.ticket_modal.no_tickets.text') }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1 align-items-center">
                        <div class="col-6">
                            <button type="submit" class="btn btn-desino w-100 border-0"
                                :disabled="selectedTestDeploymentTickets.length > 0 && isAllowProcess ? false : true">{{
                                    $t('home.deployment_center.test_deployment.ticket_modal.submit_but.text') }}</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import messageService from '../../../services/messageService';
import { Modal } from 'bootstrap';
import showToast from '../../../utils/toasts';
import { mapActions } from 'vuex';
import eventBus from "@/eventBus.js";
import GlobalMessage from '../../../components/GlobalMessage.vue';
import DeploymentCenterService from '../../../services/Home/DeploymentCenterService';
export default {
    name: 'EditOpportunityModal',
    components: {
        GlobalMessage,
    },
    data() {
        return {
            isChkAllTestDeploymentTickets: false,
            ticketList: [],
            selectedTestDeploymentTickets: [],
            isAllowProcess: false,
            initiativeId: "",
            initiative: {},
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        formattedModalTitleTestDeployment() {
            const title = this.$t('home.deployment_center.test_deployment.ticket_modal.title', { 'INITIATIVE_NAME': this.initiative?.name });
            return title.replace(this.initiative?.name, `<span class='badge bg-secondary'>${this.initiative?.name}</span>`);
        },
        async getTestDeploymentTicketsModalData(testDeployment) {
            this.selectedTestDeploymentTickets = [];
            this.isChkAllTestDeploymentTickets = false;
            this.initiativeId = testDeployment.id;
            const passData = {
                initiative_id: testDeployment.id,
            }
            const { content: { tickets, initiative, isAllowProcess } } = await DeploymentCenterService.getTestDeploymentTicketsModalData(passData);
            this.initiative = initiative;
            this.isAllowProcess = isAllowProcess;
            this.ticketList = tickets.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
        },
        async submitTestDeploymentTicket() {
            this.clearMessages();
            if (this.selectedTestDeploymentTickets.length == 0) {
                return false;
            }
            this.$swal({
                title: this.$t('home.deployment_center.test_deployment.ticket_modal.submit.alert.text'),
                showCancelButton: true,
                confirmButtonColor: '#1e6abf',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="bi bi-check-lg"></i>',
                cancelButtonText: '<i class="bi bi-x-lg"></i>',
                customClass: {
                    confirmButton: 'btn-desino',
                },
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const params = {
                            initiative_id: this.initiativeId,
                            ticketIds: this.selectedTestDeploymentTickets,
                        }
                        await this.setLoading(true);
                        const { message } = await DeploymentCenterService.submitTestDeploymentTicket(params);
                        this.hideTestDeploymentModal();
                        showToast(message, 'success');
                        await this.setLoading(false);
                        this.$emit('pageUpdated');
                    } catch (error) {
                        this.handleError(error);
                        this.resetTestDeploymentTicketList();
                    }
                } else {
                    this.resetTestDeploymentTicketList();
                }
            }).catch(() => {
                this.resetTestDeploymentTicketList();
            });
        },
        handleSelectAllTestDeploymentTickets() {
            this.selectedTestDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => {
                ticket.isChecked = this.isChkAllTestDeploymentTickets;
                if (this.isChkAllTestDeploymentTickets) {
                    this.selectedTestDeploymentTickets.push(ticket.id);
                }
                return ticket;
            });
        },
        handleSelectTestDeploymentTicket(ticket) {
            if (ticket.isChecked) {
                if (!this.selectedTestDeploymentTickets.includes(ticket.id)) {
                    this.selectedTestDeploymentTickets.push(ticket.id);
                }
            } else {
                this.selectedTestDeploymentTickets = this.selectedTestDeploymentTickets.filter(id => id !== ticket.id);
            }
            this.isChkAllTestDeploymentTickets = this.ticketList.every(ticket => ticket.isChecked);
        },
        resetTestDeploymentTicketList() {
            this.selectedTestDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
            this.isChkAllTestDeploymentTickets = false;
        },
        hideTestDeploymentModal() {
            const modalElement = document.getElementById('testDeploymentTicketsModal');
            if (modalElement) {
                const modal = Modal.getInstance(modalElement);
                if (modal) {
                    this.setLoading(false);
                    modal.hide();
                }
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
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
