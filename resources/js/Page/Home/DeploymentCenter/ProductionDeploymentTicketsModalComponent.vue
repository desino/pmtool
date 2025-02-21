<template>
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0">
            <div class="modal-header modal-header text-white bg-desino border-0 py-2 justify-content-center">
                <h5 class="modal-title" id="productionDeploymentTicketsModalLabel"
                    v-html="formattedModalTitlePRDDeployment()"></h5>
            </div>
            <div class="modal-body py-0">
                <GlobalMessage v-if="showMessage" scope="modal" />
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino fw-bold">
                        <div class="row g-1 align-items-center">
                            <div class="col-9 col-lg-10">
                                {{ $t('home.deployment_center.production_deployment.ticket_modal.li.name.text') }}
                            </div>
                            <div class="col-3 col-lg-2 text-end">
                                {{
                                    $t('home.deployment_center.production_deployment.ticket_modal.li.develop_by.text')
                                }}
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item p-1 list-group-item-action" v-if="ticketList.length > 0"
                        v-for="ticket in ticketList" :key="ticket.id">
                        <div class="row g-1 align-items-center" style="min-height: 48px;">
                            <div class="col-9 col-lg-10" :for="'chk_production_deployment_ticket_' + ticket.ticket.id">
                                {{ ticket?.ticket.composed_name }}
                                <router-link target="_blank"
                                    :to="{ name: 'task.detail', params: { initiative_id: ticket?.ticket.initiative_id, ticket_id: ticket?.ticket.id } }"
                                    class="ms-2">
                                    <i class="bi bi-link-45deg"></i>
                                </router-link>
                            </div>
                            <div class="col-3 col-lg-2 text-end" :for="'chk_test_deployment_ticket_' + ticket.id">
                                {{ ticket?.ticket?.develop_action?.user?.name }}
                            </div>
                        </div>
                    </li>
                    <li v-else class="list-group-item p-1">
                        <div class="row g-1 align-items-center" style="min-height: 48px;">
                            <div class="col-12 fst-italic small text-secondary text-center">
                                {{ $t('home.deployment_center.production_deployment.ticket_modal.no_tickets.text') }}
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="modal-footer border-0 p-0 d-block">
                <div class="row g-1 align-items-center">
                    <div class="col-6 col-md-6 col-lg-6">
                        <button type="button" ref="popoverBtn" data-bs-toggle="popover"
                            :title="$t('home.deployment_center.production_deployment.ticket_modal.submit.alert.text')"
                            v-bind:data-bs-content="popoverContent" :disabled="!isAllowProcess"
                            class="btn btn-desino w-100 border-0">{{
                                $t('home.deployment_center.production_deployment.ticket_modal.submit_but.text')
                            }}</button>
                    </div>
                    <div class="col-6 col-md-6 col-lg-6">
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
import messageService from '../../../services/messageService';
import { Modal, Popover } from 'bootstrap';
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
            isAllowProcess: false,
            initiative: {},
            initiativeId: "",
            errors: {},
            showMessage: true,
            popoverContent: `
            <div class="text-center w-100">
                <a href="javascript:void(0)" id="yesPrdDeploymentButton" class="btn btn-desino w-100 border-0 my-1">
                    <i class="bi bi-check-lg"></i>
                </a>
            </div>`,
        };
    },
    methods: {
        ...mapActions(['setLoading']),
        formattedModalTitlePRDDeployment() {
            const title = this.$t('home.deployment_center.production_deployment.ticket_modal.title', { 'INITIATIVE_NAME': this.initiative?.name });
            return title.replace(this.initiative?.name, `<span class='badge bg-secondary'>${this.initiative?.name}</span>`);
        },
        async getProductionDeploymentTicketsModalData(productionDeployment) {
            this.selectedProductionDeploymentTickets = [];
            this.isChkAllProductionDeploymentTickets = false;
            this.initiativeId = productionDeployment.id;
            const passData = {
                initiative_id: productionDeployment.id,
            }
            const { content: { tickets, release, initiative, isAllowProcess } } = await DeploymentCenterService.getProductionDeploymentTicketsModalData(passData);
            this.release = release;
            this.initiative = initiative;
            this.isAllowProcess = isAllowProcess;
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
            console.log('this.release :: ', this.release);
            this.clearMessages();
            try {
                const params = {
                    initiative_id: this.initiativeId,
                    ticketIds: this.selectedProductionDeploymentTickets,
                    release_id: this.release?.id
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
        initializePopover() {
            const popoverButton = this.$refs.popoverBtn;
            if (popoverButton) {
                const popover = new Popover(popoverButton, {
                    html: true,
                    trigger: 'focus',
                });

                popoverButton.addEventListener('shown.bs.popover', () => {
                    const yesButton = document.getElementById('yesPrdDeploymentButton');
                    if (yesButton) {
                        yesButton.addEventListener('click', this.submitProductionDeploymentTicket);
                    }
                });
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
        this.$nextTick(() => {
            this.initializePopover();
        });
    },
    beforeUnmount() {
        this.showMessage = false;
    }
}
</script>
