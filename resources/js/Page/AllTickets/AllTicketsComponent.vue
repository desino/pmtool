<template>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content">
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.action_owner" class="form-select"
                            @change="getAllTicketsWithoutInitiative">
                            <option value="">{{ $t('all_ticket_without_initiative_list.filter.action_owner_placeholder')
                                }}
                            </option>
                            <option v-for="actionOwner in actionOwners" :key="actionOwner.id" :value="actionOwner.id">{{
                                actionOwner.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.initiative_id" class="form-select"
                            @change="getAllTicketsWithoutInitiative">
                            <option value="">{{ $t('all_ticket_without_initiative_list.filter.initiative_placeholder')
                                }}
                            </option>
                            <option v-for="initiative in initiatives" :key="initiative.id" :value="initiative.id">{{
                                initiative.name }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <select v-model="filter.visible" class="form-select" @change="getAllTicketsWithoutInitiative">
                            <option value="">{{ $t('all_ticket_without_initiative_list.filter.visible') }}
                            </option>
                            <option v-for="visible in visibleList" :key="visible.value" :value="visible.value">{{
                                visible.label }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <multiselect v-model="filter.macro_status" ref="multiselect" :multiple="true"
                            :options="macroStatus" :searchable="true" deselect-label="" label="name"
                            :placeholder="$t('all_ticket_without_initiative_list.filter.macro_status_placeholder')"
                            track-by="id" @select="getAllTicketsWithoutInitiative"
                            @Remove="getAllTicketsWithoutInitiative">
                            <template #tag="{ option, remove }">
                                <span class="multiselect__tag_for_macro_status" :class="option.color">
                                    <span>{{ option.name }}</span>
                                    <i tabindex="1" class="multiselect__tag-icon" @click="remove(option)"></i>
                                </span>
                            </template>
                        </multiselect>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="w-100 p-1">
                        <div class="form-check ms-auto">
                            <input v-model="filter.is_priority" @change="getAllTicketsWithoutInitiative"
                                class="form-check-input" type="checkbox" id="is_include_done">
                            <label class="form-check-label fw-bold" for="is_include_done">
                                {{ $t('all_ticket_without_initiative_list.filter.is_priority') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <div class="row g-1 align-items-center">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="w-100 p-1">
                        <button @click="showConfirmation('addPriorityConfirmation', addRemovePriority, 1)"
                            class="btn btn-desino w-100" :disabled="selectedTickets.length === 0" type="button">
                            {{ $t('all_ticket_without_initiative_list.add_priority.button_text') }}
                        </button>
                    </div>
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTickets.length === 0" type="button"
                            @click="showConfirmation('removePriorityConfirmation', addRemovePriority, 0)">
                            {{ $t('ticket.remove_priority.button_text') }}
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTickets.length === 0" type="button"
                            @click="showConfirmation('markAsVisibleConfirmation', markAsVisibleInvisible, 1)">
                            {{ $t('ticket.mark_as_visible.button_text') }}
                        </button>
                    </div>
                    <div class="w-100 p-1">
                        <button class="btn btn-desino w-100" :disabled="selectedTickets.length === 0" type="button"
                            @click="showConfirmation('markAsInvisibleConfirmation', markAsVisibleInvisible, 0)">
                            {{ $t('ticket.mark_as_invisible.button_text') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 mb-3">
            <ul class="list-group list-group-flush mb-3 mt-2">
                <li class="list-group-item bg-desino text-white border-0 rounded-top px-1 py-3">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-5 col-md-9 col-12 fw-bold small">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px"></div>
                                <div class="col-auto me-1" style="width:20px">
                                    <input class="form-check-input" type="checkbox" id="chk_all_tickets"
                                        v-model="isChkAllTickets" @change="handleSelectAllTickets">
                                </div>
                                <div class="col-auto" style="width: calc(100% - 40px)">
                                    {{ $t('all_ticket_without_initiative_list.list.column_ticket_name') }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-3 d-none d-md-block fw-bold small ">
                            {{ $t('all_ticket_without_initiative_list.list.column_macro_status') }}
                        </div>
                        <div class="col-lg-2 d-none d-lg-block fw-bold small">
                            {{ $t('all_ticket_without_initiative_list.estimated_hours') }}
                        </div>
                        <div class="col-lg-2 d-none d-lg-block fw-bold small">
                            {{ $t('all_ticket_without_initiative_list.current_owner') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold small text-lg-end">
                            {{ $t('all_ticket_without_initiative_list.column_action') }}
                        </div>
                    </div>
                </li>
                <li v-for="(ticket, index) in tickets" v-if="tickets.length > 0" :key="ticket.id"
                    class="list-group-item p-1 list-group-item-action" role="button"
                    @click="redirectTaskDetailPage(ticket)">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-lg-5 col-md-9 col-12">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-auto me-1" style="width:10px">
                                    <div class="position-absolute" :class="{
                                        'bg-secondary': !ticket.is_visible,
                                        'bg-warning': ticket.is_priority && ticket.is_visible,
                                        '': ticket.is_visible && !ticket.is_priority
                                    }" style="width: 10px; height: 100%; left: 0; top: 0;">
                                    </div>
                                </div>
                                <div class="col-auto me-1" style="width:20px">
                                    <input class="form-check-input" type="checkbox" :id="'chk_ticket_' + ticket.id"
                                        v-model="ticket.isChecked" @click.stop @change="handleSelectTickets(ticket)">
                                </div>
                                <div class="col-auto" style="width: calc(100% - 40px)" data-bs-toggle="tooltip"
                                    data-bs-html="true" data-bs-placement="bottom"
                                    :title="tooltipContentForTicketName(ticket)">
                                    <small class="badge bg-secondary">{{ ticket?.initiative?.name }}</small>
                                    {{ ticket.composed_name }}
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 offset-md-0 col-md-3 offset-1 col-5 text-center py-2 py-lg-0">
                            <span class="badge p-2 w-100 text-wrap" :class="ticket.macro_status_label?.color">{{
                                ticket.macro_status_label?.label }}
                            </span>
                        </div>
                        <div class="offset-lg-0 col-lg-2 offset-md-1 col-md-4 col-6 py-2 py-lg-0">
                            <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                {{ ticket.dev_estimation_time ?? ticket.initial_estimation_development_time }} hrs
                            </span>
                        </div>
                        <div class="col-lg-2 offset-md-0 col-md-4 offset-1 col-5 py-2 py-lg-0">
                            <span
                                class="badge text-desino d-inline-block d-lg-none px-0 py-2 fw-bold text-center rounded-top">
                                {{ $t('all_ticket_without_initiative_list.current_owner') }}
                            </span>
                            {{ ticket?.current_action?.user?.name }}
                        </div>
                        <div class="col-lg-1 col-md-3 col-6 text-end py-2 py-lg-0">
                            <a data-bs-toggle="tooltip" data-bs-placement="bottom"
                                :title="$t('ticket.list.column.action.copy_text')" class="text-primary me-1"
                                href="javascript:" @click.stop="copyToClipboard(ticket)">
                                <i class="bi bi-share"></i>
                            </a>
                            <a data-bs-toggle="tooltip" data-bs-placement="bottom"
                                :title="$t('ticket.list.column.action.edit_text')" class="text-desino me-1"
                                href="javascript:" @click.stop="editTicketPopup(ticket)">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <a data-bs-toggle="tooltip" data-bs-placement="bottom"
                                :title="$t('ticket.list.column.action.asana_text')" v-if="ticket.asana_task_link"
                                @click.stop :href="ticket.asana_task_link" target="_blank">
                                <svg fill="none" height="21px" viewBox="0 0 24 24" width="21px"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path clip-rule="evenodd"
                                        d="M10.4693 3.55448C10.9546 3.35346 11.4747 3.25 12 3.25C12.5253 3.25 13.0454 3.35346 13.5307 3.55448C14.016 3.7555 14.457 4.05014 14.8284 4.42157C15.1999 4.79301 15.4945 5.23397 15.6955 5.71927C15.8965 6.20457 16 6.72471 16 7.25C16 7.77529 15.8965 8.29543 15.6955 8.78073C15.4945 9.26603 15.1999 9.70699 14.8284 10.0784C14.457 10.4499 14.016 10.7445 13.5307 10.9455C13.0454 11.1465 12.5253 11.25 12 11.25C11.4747 11.25 10.9546 11.1465 10.4693 10.9455C9.98396 10.7445 9.54301 10.4499 9.17157 10.0784C8.80014 9.70699 8.5055 9.26604 8.30448 8.78073C8.10346 8.29543 8 7.77529 8 7.25C8 6.72471 8.10346 6.20457 8.30448 5.71927C8.5055 5.23396 8.80014 4.79301 9.17157 4.42157C9.54301 4.05014 9.98396 3.7555 10.4693 3.55448ZM12 4.75C11.6717 4.75 11.3466 4.81466 11.0433 4.9403C10.74 5.06594 10.4644 5.25009 10.2322 5.48223C10.0001 5.71438 9.81594 5.98998 9.6903 6.29329C9.56466 6.59661 9.5 6.92169 9.5 7.25C9.5 7.5783 9.56466 7.90339 9.6903 8.20671C9.81594 8.51002 10.0001 8.78562 10.2322 9.01777C10.4644 9.24991 10.74 9.43406 11.0433 9.5597C11.3466 9.68534 11.6717 9.75 12 9.75C12.3283 9.75 12.6534 9.68534 12.9567 9.5597C13.26 9.43406 13.5356 9.24991 13.7678 9.01777C13.9999 8.78562 14.1841 8.51002 14.3097 8.20671C14.4353 7.90339 14.5 7.5783 14.5 7.25C14.5 6.9217 14.4353 6.59661 14.3097 6.29329C14.1841 5.98998 13.9999 5.71438 13.7678 5.48223C13.5356 5.25009 13.26 5.06594 12.9567 4.9403C12.6534 4.81466 12.3283 4.75 12 4.75Z"
                                        fill="#ffc107" fill-rule="evenodd" />
                                    <path clip-rule="evenodd"
                                        d="M5.46927 12.5545C5.95457 12.3535 6.47471 12.25 7 12.25C7.52529 12.25 8.04543 12.3535 8.53073 12.5545C9.01604 12.7555 9.45699 13.0501 9.82843 13.4216C10.1999 13.793 10.4945 14.234 10.6955 14.7193C10.8965 15.2046 11 15.7247 11 16.25C11 16.7753 10.8965 17.2954 10.6955 17.7807C10.4945 18.266 10.1999 18.707 9.82843 19.0784C9.45699 19.4499 9.01604 19.7445 8.53073 19.9455C8.04543 20.1465 7.52529 20.25 7 20.25C6.47471 20.25 5.95457 20.1465 5.46927 19.9455C4.98396 19.7445 4.54301 19.4499 4.17157 19.0784C3.80014 18.707 3.5055 18.266 3.30448 17.7807C3.10346 17.2954 3 16.7753 3 16.25C3 15.7247 3.10346 15.2046 3.30448 14.7193C3.5055 14.234 3.80014 13.793 4.17157 13.4216C4.54301 13.0501 4.98396 12.7555 5.46927 12.5545ZM7 13.75C6.67169 13.75 6.34661 13.8147 6.04329 13.9403C5.73998 14.0659 5.46438 14.2501 5.23223 14.4822C5.00009 14.7144 4.81594 14.99 4.6903 15.2933C4.56466 15.5966 4.5 15.9217 4.5 16.25C4.5 16.5783 4.56466 16.9034 4.6903 17.2067C4.81594 17.51 5.00009 17.7856 5.23223 18.0178C5.46438 18.2499 5.73998 18.4341 6.04329 18.5597C6.34661 18.6853 6.67169 18.75 7 18.75C7.3283 18.75 7.65339 18.6853 7.95671 18.5597C8.26002 18.4341 8.53562 18.2499 8.76777 18.0178C8.99991 17.7856 9.18406 17.51 9.3097 17.2067C9.43534 16.9034 9.5 16.5783 9.5 16.25C9.5 15.9217 9.43534 15.5966 9.3097 15.2933C9.18406 14.99 8.99991 14.7144 8.76777 14.4822C8.53562 14.2501 8.26002 14.0659 7.95671 13.9403C7.65339 13.8147 7.3283 13.75 7 13.75Z"
                                        fill="#ffc107" fill-rule="evenodd" />
                                    <path clip-rule="evenodd"
                                        d="M17 12.25C16.4747 12.25 15.9546 12.3535 15.4693 12.5545C14.984 12.7555 14.543 13.0501 14.1716 13.4216C13.8001 13.793 13.5055 14.234 13.3045 14.7193C13.1035 15.2046 13 15.7247 13 16.25C13 16.7753 13.1035 17.2954 13.3045 17.7807C13.5055 18.266 13.8001 18.707 14.1716 19.0784C14.543 19.4499 14.984 19.7445 15.4693 19.9455C15.9546 20.1465 16.4747 20.25 17 20.25C17.5253 20.25 18.0454 20.1465 18.5307 19.9455C19.016 19.7445 19.457 19.4499 19.8284 19.0784C20.1999 18.707 20.4945 18.266 20.6955 17.7807C20.8965 17.2954 21 16.7753 21 16.25C21 15.7247 20.8965 15.2046 20.6955 14.7193C20.4945 14.234 20.1999 13.793 19.8284 13.4216C19.457 13.0501 19.016 12.7555 18.5307 12.5545C18.0454 12.3535 17.5253 12.25 17 12.25ZM16.0433 13.9403C16.3466 13.8147 16.6717 13.75 17 13.75C17.3283 13.75 17.6534 13.8147 17.9567 13.9403C18.26 14.0659 18.5356 14.2501 18.7678 14.4822C18.9999 14.7144 19.1841 14.99 19.3097 15.2933C19.4353 15.5966 19.5 15.9217 19.5 16.25C19.5 16.5783 19.4353 16.9034 19.3097 17.2067C19.1841 17.51 18.9999 17.7856 18.7678 18.0178C18.5356 18.2499 18.26 18.4341 17.9567 18.5597C17.6534 18.6853 17.3283 18.75 17 18.75C16.6717 18.75 16.3466 18.6853 16.0433 18.5597C15.74 18.4341 15.4644 18.2499 15.2322 18.0178C15.0001 17.7856 14.8159 17.51 14.6903 17.2067C14.5647 16.9034 14.5 16.5783 14.5 16.25C14.5 15.9217 14.5647 15.5966 14.6903 15.2933C14.8159 14.99 15.0001 14.7144 15.2322 14.4822C15.4644 14.2501 15.74 14.0659 16.0433 13.9403Z"
                                        fill="#ffc107" fill-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </li>
                <li v-else class="border border-top-0 list-group-item px-0 py-1 list-group-item-action">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fw-bold fst-italic text-center">
                            {{ $t('all_ticket_without_initiative_list.not_ticket') }}
                        </div>
                    </div>
                </li>
            </ul>
        </div>
        <PaginationComponent :currentPage="Number(currentPage)" :totalPages="Number(totalPages)"
            @page-changed="getAllTicketsWithoutInitiative" />

        <div id="editTicketFromListModal" aria-hidden="true" aria-labelledby="editTicketFromListModalLabel"
            class="modal fade" tabindex="-1">
            <EditTicketModalComponent ref="editTicketFromListModalComponent"
                @refreshTickets="getAllTicketsWithoutInitiative" />
        </div>
        <ConfirmationModal ref="dynamicConfirmationModal" :title="modalTitle" :message="modalMessage"
            @confirm="modalConfirmCallback" />
        <span id="copyableLink" style="cursor: pointer; text-decoration: underline; color: blue; display: none">
            <a v-bind:href="copyLink">{{ copyLabel }}</a>
        </span>
    </div>
</template>

<script>
import GlobalMessage from '../../components/GlobalMessage.vue';
import { mapActions, mapGetters } from 'vuex';
import store from '../../store';
import messageService from '../../services/messageService';
import PaginationComponent from '../../components/PaginationComponent.vue';
import AllTicketsWithoutInitiativeService from '../../services/AllTicketsWithoutInitiativeService';
import EditTicketModalComponent from '../SolutionDesign/Ticket/EditTicketModalComponent.vue';
import { Modal, Tooltip } from 'bootstrap';
import Multiselect from "vue-multiselect";
import MyTicketService from '../../services/MyTicketService';
import showToast from '../../utils/toasts';
export default {
    name: 'AllTicketsComponent',
    components: {
        Multiselect,
        GlobalMessage,
        PaginationComponent,
        EditTicketModalComponent
    },
    data() {
        return {
            isChkAllTickets: false,
            selectedTickets: [],
            filter: {
                action_owner: '',
                initiative_id: '',
                macro_status: '',
                visible: '',
                is_priority: false,
            },
            tickets: [],
            currentPage: "",
            totalPages: "",
            actionOwners: [],
            initiatives: [],
            macroStatus: [],
            visibleList: [],
            copyLabel: "",
            copyLink: "",
            modalTitle: '',
            modalMessage: '',
            modalConfirmCallback: null,
            showMessage: true,
            errors: {},
        }
    },
    computed: {
        ...mapGetters(['user']),
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchData() {
            try {
                await Promise.all([
                    this.getInitialData(),
                    this.getAllTicketsWithoutInitiative(),
                ]);
                this.clearMessages();
            } catch (error) {
                this.handleError(error);
            }
        },
        async getInitialData() {
            const { content: { users, initiatives, macroStatus, visibleList } } = await AllTicketsWithoutInitiativeService.getInitialData();
            this.actionOwners = users;
            this.initiatives = initiatives;
            this.macroStatus = macroStatus;
            this.visibleList = visibleList;
        },
        async getAllTicketsWithoutInitiative(page = 1) {
            this.isChkAllTickets = false;
            this.selectedTickets = [];
            this.clearMessages();
            try {
                this.setLoading(true);
                const params = {
                    page: page,
                    filters: this.filter
                }
                const { content: { data, current_page, last_page } } = await AllTicketsWithoutInitiativeService.getAllTicketsWithoutInitiative(params);
                this.tickets = data;
                this.currentPage = current_page;
                this.totalPages = last_page;
                await this.setLoading(false);
                this.initializeTooltips()
            } catch (error) {
                this.handleError(error);
            }
        },
        redirectTaskDetailPage(ticket) {
            const ticketDetailRoute = this.$router.resolve({ name: 'task.detail', params: { initiative_id: ticket.initiative_id, ticket_id: ticket.id } });
            window.open(ticketDetailRoute.href, '_blank');
        },
        editTicketPopup(ticket) {
            const passData = {
                task_id: ticket.id,
                initiative_id: ticket.initiative_id
            }
            this.$refs.editTicketFromListModalComponent.getSelectedTasksData(passData);
            const editTicketFromListModalElement = document.getElementById('editTicketFromListModal');
            if (editTicketFromListModalElement) {
                const editTicketFromListModal = new Modal(editTicketFromListModalElement);
                editTicketFromListModal.show();
            }
        },
        handleSelectAllTickets() {
            this.selectedTickets = [];
            this.tickets = this.tickets.map(ticket => {
                ticket.isChecked = this.isChkAllTickets;
                if (this.isChkAllTickets) {
                    this.selectedTickets.push(ticket.id);
                }
                return ticket;
            });
        },
        showConfirmation(modalType, callback, callbackParam) {
            if (modalType === 'addPriorityConfirmation') {
                this.modalTitle = this.$t('all_ticket_without_initiative_list.add_priority.button_text');
                this.modalMessage = this.$t('all_ticket_without_initiative_list.add_priority.conformation_popup_text');
            } else if (modalType === 'removePriorityConfirmation') {
                this.modalTitle = this.$t('ticket.remove_priority.button_text');
                this.modalMessage = this.$t('all_ticket_without_initiative_list.remove_priority.conformation_popup_text');
            }
            if (modalType === 'markAsVisibleConfirmation') {
                this.modalTitle = this.$t('ticket.mark_as_visible.button_text');
                this.modalMessage = this.$t('all_ticket_without_initiative_list.is_visible.conformation_popup_text');
            }
            if (modalType === 'markAsInvisibleConfirmation') {
                this.modalTitle = this.$t('ticket.mark_as_invisible.button_text');
                this.modalMessage = this.$t('all_ticket_without_initiative_list.is_invisible.conformation_popup_text');
            }

            this.modalConfirmCallback = () => callback(callbackParam);

            this.$refs.dynamicConfirmationModal.showModal();
        },
        async addRemovePriority(isPriority) {
            try {
                const passData = {
                    is_priority: isPriority,
                    ticket_ids: this.selectedTickets
                }
                this.setLoading(true);
                const { message, status } = await AllTicketsWithoutInitiativeService.addRemovePriority(passData);
                showToast(message, 'success');
                this.setLoading(false);
                this.clearMessages();
                this.getAllTicketsWithoutInitiative();
            } catch (error) {
                this.handleError(error);
            }
        },
        async markAsVisibleInvisible(isVisible) {
            try {
                const passData = {
                    is_visible: isVisible,
                    ticket_ids: this.selectedTickets
                }
                this.setLoading(true);
                const { message, status } = await AllTicketsWithoutInitiativeService.markAsVisibleInvisible(passData);
                showToast(message, 'success');
                this.setLoading(false);
                this.clearMessages();
                this.getAllTicketsWithoutInitiative();
            } catch (error) {
                this.handleError(error);
            }
        },
        handleSelectTickets(ticket) {
            if (ticket.isChecked) {
                if (!this.selectedTickets.includes(ticket.id)) {
                    this.selectedTickets.push(ticket.id);
                }
            } else {
                this.selectedTickets = this.selectedTickets.filter(id => id !== ticket.id);
            }
            this.isChkAllTickets = this.tickets.every(ticket => ticket.isChecked);
        },
        initializeTooltips() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach((tooltipTriggerEl) => {
                new Tooltip(tooltipTriggerEl);
            });
        },
        tooltipContentForTicketName(ticket) {
            const createdAtLabel = this.$t('ticket.list.row_hover_tooltip_created_at_text');
            const createdByLabel = this.$t('ticket.list.row_hover_tooltip_created_by_text');
            const commentLabel = this.$t('ticket.list.row_hover_tooltip_comment_text');
            const commentedAtLabel = this.$t('ticket.list.row_hover_tooltip_commented_at_text');

            let commentHtml = "";
            if (ticket?.latest_comment?.comment != null) {
                const comment = ticket?.latest_comment?.comment;
                const userName = ticket?.latest_comment?.created_updated_user_name;
                const createdAt = ticket?.latest_comment?.display_updated_at ?? ticket?.latest_comment?.display_created_at;
                commentHtml = `<strong class='small'>${commentLabel}</strong> <span class="badge bg-secondary d-block-inline text-wrap fst-italic"> ${userName}</span> : <span class="fst-italic">${comment}</span> <strong class='small fst-italic'>${commentedAtLabel}</strong> <span class="badge bg-secondary d-block-inline text-wrap fst-italic">${createdAt}</span>`;
            }
            return `<div class='row g-1 align-items-center small'>
                        <div class='col-12 text-start'>
                            <strong class='small'>${createdByLabel}</strong> <span>${ticket.display_created_by}</span> <strong class='small'>${createdAtLabel}</strong> <span>${ticket.display_created_at}</span>
                        </div>
                        <div class='col-12 text-start'>
                            ${commentHtml}
                        </div>
                    </div>`;
        },
        setActionOwnerForFilterFromDeveloperWorkload() {
            let queryParams = "";
            if ('user_id' in this.$route.query) {
                queryParams = this.$route.query;
                this.filter.action_owner = queryParams.user_id;
            }
        },
        copyToClipboard(ticket) {
            this.copyLink = `${window.location.origin}/solution-design/${ticket.initiative_id}/ticket-detail/${ticket.id}`;
            this.copyLabel = ticket.composed_name;

            this.$nextTick(() => {
                const linkElement = document.getElementById('copyableLink');
                if (linkElement) {
                    linkElement.style.display = 'inline';

                    const range = document.createRange();
                    range.selectNodeContents(linkElement);
                    const selection = window.getSelection();
                    selection.removeAllRanges();
                    selection.addRange(range);

                    try {
                        const successful = document.execCommand('copy');
                        if (successful) {
                            showToast(this.$t('ticket.link_copied_to_clipboard'), 'success');
                        } else {
                            showToast(this.$t('ticket.failed_to_copy_link'), 'danger');
                        }
                    } catch (error) {
                        showToast(this.$t('ticket.failed_to_copy_link'), 'danger');
                    }
                    linkElement.style.display = 'none';
                }
            });
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
        this.setActionOwnerForFilterFromDeveloperWorkload();
        this.fetchData();
        const setHeaderData = {
            page_title: this.$t('all_ticket_without_initiative_list.page_title'),
        }
        store.commit("setHeaderData", setHeaderData);
    },
    beforeRouteUpdate(to, from, next) {
        this.initiative_id = to.params.id;
        this.fetchData();
        next();
    },
}
</script>
