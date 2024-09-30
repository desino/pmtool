<template>
    <div class="modal-dialog">
        <form @submit.prevent="submitProductionDeploymentTicket">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productionDeploymentTicketsModalLabel">{{
                        $t('home.deployment_center.production_deployment.ticket_modal.title')
                        }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <GlobalMessage v-if="showMessage" />
                    <ul class="list-group">
                        <li class="list-group-item fw-bold bg-desino text-white">
                            <div class="row w-100">
                                <div class="col-md-8">
                                    {{ $t('home.deployment_center.production_deployment.ticket_modal.li.name.text') }}
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item" v-for="ticket in ticketList" :key="ticket.id">
                            <div class="row w-100">
                                <div class="col-md-8" :for="'chk_production_deployment_ticket_' + ticket.ticket.id">
                                    {{ ticket?.ticket.composed_name }}
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-desino">{{
                        $t('home.deployment_center.production_deployment.ticket_modal.submit_but.text') }}</button>
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
            isChkAllProductionDeploymentTickets: false,
            ticketList: [],
            release: {},
            selectedProductionDeploymentTickets: [],
            initiativeId: "",
            errors: {},
            showMessage: true
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        async getProductionDeploymentTicketsModalData(productionDeployment) {
            this.selectedProductionDeploymentTickets = [];
            this.isChkAllProductionDeploymentTickets = false;
            this.initiativeId = productionDeployment.id;
            const passData = {
                initiative_id: productionDeployment.id,
            }
            const { content: { tickets, release } } = await DeploymentCenterService.getProductionDeploymentTicketsModalData(passData);
            this.release = release;
            this.ticketList = tickets.map(ticket => {
                const isSelected = false;
                this.selectedProductionDeploymentTickets.push(ticket.ticket.id);
                return {
                    ...ticket,
                    isChecked: isSelected,
                };
            });
        },
        async submitProductionDeploymentTicket() {
            this.clearMessages();
            this.$swal({
                title: this.$t('home.deployment_center.production_deployment.ticket_modal.submit.alert.text'),
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
                            ticketIds: this.selectedProductionDeploymentTickets,
                            release_id: this.release.id
                        }
                        await this.setLoading(true);
                        const { message } = await DeploymentCenterService.submitProductionDeploymentTicket(params);
                        this.hideProductionDeploymentModal();
                        showToast(message, 'success');
                        await this.setLoading(false);
                        this.$emit('pageUpdated');
                    } catch (error) {
                        this.handleError(error);
                        this.resetProductionDeploymentTicketList();
                    }
                } else {
                    this.resetProductionDeploymentTicketList();
                }
            }).catch(() => {
                this.resetProductionDeploymentTicketList();
            });
        },
        handleSelectAllProductionDeploymentTickets() {
            this.selectedProductionDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => {
                ticket.isChecked = this.isChkAllProductionDeploymentTickets;
                if (this.isChkAllProductionDeploymentTickets) {
                    this.selectedProductionDeploymentTickets.push(ticket.id);
                }
                return ticket;
            });
        },
        handleSelectProductionDeploymentTicket(ticket) {
            if (ticket.isChecked) {
                if (!this.selectedProductionDeploymentTickets.includes(ticket.id)) {
                    this.selectedProductionDeploymentTickets.push(ticket.id);
                }
            } else {
                this.selectedProductionDeploymentTickets = this.selectedProductionDeploymentTickets.filter(id => id !== ticket.id);
            }
            this.isChkAllProductionDeploymentTickets = this.ticketList.every(ticket => ticket.isChecked);
        },
        resetProductionDeploymentTicketList() {
            this.selectedProductionDeploymentTickets = [];
            this.ticketList = this.ticketList.map(ticket => ({
                ...ticket,
                isChecked: false,
            }));
            this.isChkAllProductionDeploymentTickets = false;
        },
        hideProductionDeploymentModal() {
            const modalElement = document.getElementById('productionDeploymentTicketsModal');
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
        this.clearMessages();
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
