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
            <ul class="list-group list-group-flush">
                <li class="list-group-item px-1 border-0 border-5 border-bottom border-desino">
                    <div class="row g-1 align-items-center">
                        <div class="col-lg-7 col-md-9 col-12 fw-bold">
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
                        <div class="col-lg-2 col-md-3 d-none d-md-block fw-bold ">
                            {{ $t('all_ticket_without_initiative_list.list.column_macro_status') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold">
                            {{ $t('all_ticket_without_initiative_list.estimated_hours') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold">
                            {{ $t('all_ticket_without_initiative_list.current_owner') }}
                        </div>
                        <div class="col-lg-1 d-none d-lg-block fw-bold text-lg-end"></div>
                    </div>
                </li>
                <li v-for="(ticket, index) in tickets" v-if="tickets.length > 0" :key="ticket.id"
                    class="list-group-item p-1 list-group-item-action" role="button">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-lg-7 col-md-9 col-12" @click="redirectTaskDetailPage(ticket)">
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
                                <div class="col-auto" style="width: calc(100% - 40px)" 
                                    data-bs-toggle="tooltip" data-bs-html="true" data-bs-placement="bottom" :title="tooltipContentForTicketName(ticket)">
                                    <div class="w-100">
                                        <small class="badge bg-secondary">{{ ticket?.initiative?.name }}</small>
                                        {{ ticket.composed_name }}
                                    </div>
                                    <div class="w-100 fst-italic text-secondary small" v-if="ticket?.functionality?.display_name">
                                        ({{ ticket?.functionality?.display_name }})
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2 offset-md-0 col-md-3 offset-1 col-5 text-center py-2 py-lg-0" @click="redirectTaskDetailPage(ticket)">
                            <span class="badge p-2 w-100 text-wrap" :class="ticket.macro_status_label?.color">
                                {{ ticket.macro_status_label?.label }}
                            </span>
                        </div>
                        <div class="offset-lg-0 col-lg-1 offset-md-1 col-md-4 col-6 py-2 py-lg-0" @click="redirectTaskDetailPage(ticket)">
                            <span class="badge rounded-3 bg-success-subtle text-success mb-0">
                                {{ ticket.dev_estimation_time ?? ticket.initial_estimation_development_time }} hrs
                            </span>
                        </div>
                        <div class="col-lg-1 offset-md-0 col-md-4 offset-1 col-5 py-2 py-lg-0" @click="redirectTaskDetailPage(ticket)">
                            <span
                                class="badge text-desino d-inline-block d-lg-none px-0 py-2 fw-bold text-center rounded-top">
                                {{ $t('all_ticket_without_initiative_list.current_owner') }}
                            </span>
                            {{ ticket?.current_action?.user?.name }}
                        </div>
                        <div class="col-lg-1 col-md-3 col-6 text-end py-2 py-lg-0">
                            <div class="dropdown">
                                <button class="btn btn-secondary border-0 btn-sm dropdown-toggle dropdown-toggle-split" type="button" data-bs-toggle="dropdown" aria-expanded="false"></button>
                                <ul class="dropdown-menu shadow border-0 p-2">
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="copyToClipboard(ticket)">
                                            {{ $t('ticket.list.column.action.copy_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1" v-if="ticket.asana_task_link">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" :href="ticket.asana_task_link" target="_blank" >
                                            {{ $t('ticket.list.column.action.asana_text') }}
                                        </a>
                                    </li>
                                    <li class="small pb-1">
                                        <a role="button" class="btn btn-sm btn-desino w-100 small" href="javascript:" @click="editTicketPopup(ticket)" >
                                            {{ $t('ticket.list.column.action.edit_text') }}
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </li>
                <li v-else class="list-group-item p-1">
                    <div class="row g-1 align-items-center" style="min-height: 48px;">
                        <div class="col-12 fst-italic small text-secondary text-center">
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
