<template>
    <div class="app-content-header pb-0">
        <div class="container-fluid">
            <div class="row w-100">
                <div class="col-11">
                    <div class="w-100">
                        <h3 class="m-0">{{ ticketData.composed_name }}
                            <span>
                                <input id="btn-check" v-model="showTicketDropdown" autocomplete="off" class="btn-check"
                                    type="checkbox">
                                <label class="btn btn-outline-desino fw-bold border" for="btn-check">
                                    <i :class="{ 'bi-pencil-square': !showTicketDropdown, 'bi-x-lg text-danger': showTicketDropdown }"
                                        class="bi"></i>
                                </label>
                            </span>
                        </h3>
                    </div>
                    <div v-if="showTicketDropdown" class="w-100 py-2">
                        <multiselect v-model="selectedTaskObject" :multiple="false" :options="tasksForDropdown"
                            :searchable="true" deselect-label="Selected" label="composed_name"
                            placeholder="Search & Select Task" track-by="id" @input="onTaskSelect">
                        </multiselect>
                    </div>
                </div>
                <div class="col-1 text-end">
                    <div v-if="ticketData.asana_task_link" class="d-flex">
                        <a :href="ticketData.asana_task_link" class="btn btn-desino border-0 w-100 text-dark"
                            target="_blank">
                            <svg fill="none" height="21px" viewBox="0 0 24 24" width="21px"
                                xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd"
                                    d="M10.4693 3.55448C10.9546 3.35346 11.4747 3.25 12 3.25C12.5253 3.25 13.0454 3.35346 13.5307 3.55448C14.016 3.7555 14.457 4.05014 14.8284 4.42157C15.1999 4.79301 15.4945 5.23397 15.6955 5.71927C15.8965 6.20457 16 6.72471 16 7.25C16 7.77529 15.8965 8.29543 15.6955 8.78073C15.4945 9.26603 15.1999 9.70699 14.8284 10.0784C14.457 10.4499 14.016 10.7445 13.5307 10.9455C13.0454 11.1465 12.5253 11.25 12 11.25C11.4747 11.25 10.9546 11.1465 10.4693 10.9455C9.98396 10.7445 9.54301 10.4499 9.17157 10.0784C8.80014 9.70699 8.5055 9.26604 8.30448 8.78073C8.10346 8.29543 8 7.77529 8 7.25C8 6.72471 8.10346 6.20457 8.30448 5.71927C8.5055 5.23396 8.80014 4.79301 9.17157 4.42157C9.54301 4.05014 9.98396 3.7555 10.4693 3.55448ZM12 4.75C11.6717 4.75 11.3466 4.81466 11.0433 4.9403C10.74 5.06594 10.4644 5.25009 10.2322 5.48223C10.0001 5.71438 9.81594 5.98998 9.6903 6.29329C9.56466 6.59661 9.5 6.92169 9.5 7.25C9.5 7.5783 9.56466 7.90339 9.6903 8.20671C9.81594 8.51002 10.0001 8.78562 10.2322 9.01777C10.4644 9.24991 10.74 9.43406 11.0433 9.5597C11.3466 9.68534 11.6717 9.75 12 9.75C12.3283 9.75 12.6534 9.68534 12.9567 9.5597C13.26 9.43406 13.5356 9.24991 13.7678 9.01777C13.9999 8.78562 14.1841 8.51002 14.3097 8.20671C14.4353 7.90339 14.5 7.5783 14.5 7.25C14.5 6.9217 14.4353 6.59661 14.3097 6.29329C14.1841 5.98998 13.9999 5.71438 13.7678 5.48223C13.5356 5.25009 13.26 5.06594 12.9567 4.9403C12.6534 4.81466 12.3283 4.75 12 4.75Z"
                                    fill="#fff" fill-rule="evenodd" />
                                <path clip-rule="evenodd"
                                    d="M5.46927 12.5545C5.95457 12.3535 6.47471 12.25 7 12.25C7.52529 12.25 8.04543 12.3535 8.53073 12.5545C9.01604 12.7555 9.45699 13.0501 9.82843 13.4216C10.1999 13.793 10.4945 14.234 10.6955 14.7193C10.8965 15.2046 11 15.7247 11 16.25C11 16.7753 10.8965 17.2954 10.6955 17.7807C10.4945 18.266 10.1999 18.707 9.82843 19.0784C9.45699 19.4499 9.01604 19.7445 8.53073 19.9455C8.04543 20.1465 7.52529 20.25 7 20.25C6.47471 20.25 5.95457 20.1465 5.46927 19.9455C4.98396 19.7445 4.54301 19.4499 4.17157 19.0784C3.80014 18.707 3.5055 18.266 3.30448 17.7807C3.10346 17.2954 3 16.7753 3 16.25C3 15.7247 3.10346 15.2046 3.30448 14.7193C3.5055 14.234 3.80014 13.793 4.17157 13.4216C4.54301 13.0501 4.98396 12.7555 5.46927 12.5545ZM7 13.75C6.67169 13.75 6.34661 13.8147 6.04329 13.9403C5.73998 14.0659 5.46438 14.2501 5.23223 14.4822C5.00009 14.7144 4.81594 14.99 4.6903 15.2933C4.56466 15.5966 4.5 15.9217 4.5 16.25C4.5 16.5783 4.56466 16.9034 4.6903 17.2067C4.81594 17.51 5.00009 17.7856 5.23223 18.0178C5.46438 18.2499 5.73998 18.4341 6.04329 18.5597C6.34661 18.6853 6.67169 18.75 7 18.75C7.3283 18.75 7.65339 18.6853 7.95671 18.5597C8.26002 18.4341 8.53562 18.2499 8.76777 18.0178C8.99991 17.7856 9.18406 17.51 9.3097 17.2067C9.43534 16.9034 9.5 16.5783 9.5 16.25C9.5 15.9217 9.43534 15.5966 9.3097 15.2933C9.18406 14.99 8.99991 14.7144 8.76777 14.4822C8.53562 14.2501 8.26002 14.0659 7.95671 13.9403C7.65339 13.8147 7.3283 13.75 7 13.75Z"
                                    fill="#fff" fill-rule="evenodd" />
                                <path clip-rule="evenodd"
                                    d="M17 12.25C16.4747 12.25 15.9546 12.3535 15.4693 12.5545C14.984 12.7555 14.543 13.0501 14.1716 13.4216C13.8001 13.793 13.5055 14.234 13.3045 14.7193C13.1035 15.2046 13 15.7247 13 16.25C13 16.7753 13.1035 17.2954 13.3045 17.7807C13.5055 18.266 13.8001 18.707 14.1716 19.0784C14.543 19.4499 14.984 19.7445 15.4693 19.9455C15.9546 20.1465 16.4747 20.25 17 20.25C17.5253 20.25 18.0454 20.1465 18.5307 19.9455C19.016 19.7445 19.457 19.4499 19.8284 19.0784C20.1999 18.707 20.4945 18.266 20.6955 17.7807C20.8965 17.2954 21 16.7753 21 16.25C21 15.7247 20.8965 15.2046 20.6955 14.7193C20.4945 14.234 20.1999 13.793 19.8284 13.4216C19.457 13.0501 19.016 12.7555 18.5307 12.5545C18.0454 12.3535 17.5253 12.25 17 12.25ZM16.0433 13.9403C16.3466 13.8147 16.6717 13.75 17 13.75C17.3283 13.75 17.6534 13.8147 17.9567 13.9403C18.26 14.0659 18.5356 14.2501 18.7678 14.4822C18.9999 14.7144 19.1841 14.99 19.3097 15.2933C19.4353 15.5966 19.5 15.9217 19.5 16.25C19.5 16.5783 19.4353 16.9034 19.3097 17.2067C19.1841 17.51 18.9999 17.7856 18.7678 18.0178C18.5356 18.2499 18.26 18.4341 17.9567 18.5597C17.6534 18.6853 17.3283 18.75 17 18.75C16.6717 18.75 16.3466 18.6853 16.0433 18.5597C15.74 18.4341 15.4644 18.2499 15.2322 18.0178C15.0001 17.7856 14.8159 17.51 14.6903 17.2067C14.5647 16.9034 14.5 16.5783 14.5 16.25C14.5 15.9217 14.5647 15.5966 14.6903 15.2933C14.8159 14.99 15.0001 14.7144 15.2322 14.4822C15.4644 14.2501 15.74 14.0659 16.0433 13.9403Z"
                                    fill="#fff" fill-rule="evenodd" />
                            </svg>
                        </a>
                        <button v-if="user_actions_count > 0" class="btn btn-desino btn-sm mx-2"
                            @click="handleTimeBooking()" :title="$t('ticket_details.time_booking')">
                            <i class="bi bi-clock-history"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <GlobalMessage v-if="showMessage" />
    <div class="app-content border border-start-0 border-end-0 py-2 mt-2">
        <div class="row w-100">
            <div class="col-12 col-md-3 col-lg-3 col-xl-3 text-center mb-2 mb-md-0">
                <div class="card shadow-none h-100 border-0" :class="'bg-' + ticketData?.macro_status_label?.color">
                    <div class="card-body border-0 bg-transparent p-0 align-content-center">
                        <div class="text-light fw-bold">
                            {{ ticketData?.macro_status_label?.label }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-3 col-lg-2 col-xl-2 text-center mb-2 mb-md-0">
                <div class="card shadow-none h-100 border-0 bg-transparent">
                    <div class="card-header border-0 fw-bold bg-secondary text-white">
                        {{ $t('ticket_details.task_estimation') }}
                    </div>
                    <div class="card-body border-0 bg-transparent p-1 align-content-center">
                        <div class="badge rounded-3 bg-success-subtle text-success mb-0">
                            {{ ticketData.dev_estimation_time ?? ticketData.initial_dev_time }} hrs
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-3 col-lg-2 col-xl-2 text-center mb-2 mb-md-0">
                <div class="card shadow-none h-100 border-0 bg-transparent">
                    <div class="card-header border-0 fw-bold bg-secondary text-white">
                        {{ $t('ticket_details.task_current_action_owner_label_text') }}
                    </div>
                    <div class="card-body border-0 bg-transparent py-1 px-0 align-content-center">
                        <div v-if="currentAction">
                            <select v-model="currentActionFormData.user_id"
                                :disabled="!ticketData.is_disable_action_user" class="form-select"
                                @change="handleCurrentActionChangeUser($event.target.value)">
                                <option value="">---</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-4 col-md-3 col-lg-2 col-xl-2 text-center mb-2 mb-md-0">
                <div class="card shadow-none h-100 border-0 bg-transparent">
                    <div class="card-header border-0 fw-bold bg-secondary text-white">
                        {{ $t('ticket_details.task_next_action_label_text') }}
                    </div>
                    <div class="card-body border-0 bg-transparent p-1 align-content-center">
                        <div v-if="nextAction" class="badge rounded-3 bg-success-subtle text-success mb-0">{{
                            nextAction.action_name }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 col-lg-2 col-xl-3 text-center mb-2 mb-md-0">
                <div class="card shadow-none h-100 border-0 bg-transparent">
                    <div class="card-body border-0 bg-transparent p-1">
                        <a v-if="ticketData.is_show_mark_as_done_but" role="button"
                            class="btn btn-desino w-100 border-0 text-white mb-2"
                            :class="{ 'disabled': !ticketData.is_enable_mark_as_done_but }"
                            @click="handleCurrentActionChangeStatus()">
                            {{ $t('ticket_details.task_current_action_completed_but_text') }}
                        </a>
                        <!-- <a role="button" class="btn btn-warning w-100 border-0 text-dark"
                            v-if="previousAction && previousActionAllowOrNot()" @click="handlePreviousActionStatus()"
                            :title="$t('ticket_action.move_to_previous_action')">
                            {{ $t('ticket_details.task_previous_action_completed_but_text') }}
                        </a> -->
                        <a role="button" class="btn btn-warning w-100 border-0 text-dark"
                            v-if="ticketData.is_show_pre_action_but" @click="handlePreviousActionStatus()"
                            :title="$t('ticket_action.move_to_previous_action')">
                            {{ $t('ticket_details.task_previous_action_completed_but_text') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content mt-2">
        <hr>
        <div class="col-md-12">
            <ul id="custom-tabs-five-tab" class="nav nav-tabs border-bottom-0" role="tablist">
                <li class="nav-item">
                    <a id="ticket_detail_tab" aria-controls="ticket_detail_tab" aria-selected="true"
                        class="nav-link border active" data-bs-toggle="pill" href="#ticket_detail_tab_body"
                        role="tab">{{ $t('ticket_details.task_details') }}</a>
                </li>
                <li class="nav-item">
                    <a id="test_cases_tab" aria-controls="test_cases_tab" aria-selected="false" class="nav-link border"
                        data-bs-toggle="pill" href="#test_cases_tab_body" role="tab">{{
                            $t('ticket_details.test_cases')
                        }}</a>
                </li>
            </ul>
            <div id="custom-tabs-five-tabContent" class="tab-content">
                <div id="ticket_detail_tab_body" aria-labelledby="ticket_detail_tab_body"
                    class="tab-pane fade active show" role="tabpanel">
                    <div class="row w-100">
                        <div class="col-md-6 my-2">
                            <div class="card h-100">
                                <div class="card-header fw-bold" v-if="ticketData.display_functionality_name">
                                    <span>
                                        {{ ticketData.display_functionality_name }}
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div v-html="ticketData.functionality_description"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 my-2">
                            <div class="card">
                                <div class="card-header">
                                    {{ $t('ticket_details.client_release_notes') }}
                                </div>
                                <div class="card-body">
                                    <p> {{ $t('ticket_details.client_release_notes_description') }}</p>
                                    <TinyMceEditor v-model="releaseNoteForm.release_note" />
                                    <div v-if="errors.release_note" class="text-danger mt-2">
                                        <span v-for="(error, index) in errors.release_note" :key="index">{{
                                            error
                                        }}</span>
                                    </div>
                                    <button class="btn w-100 btn-desino text-white fw-bold m-2 rounded"
                                        @click="updateReleaseNote">
                                        {{ $t('ticket_details.update') }}
                                    </button>
                                </div>
                            </div>
                            <div class="card mt-2" v-if="ticketData.is_allow_dev_estimation_time">
                                <div class="card-header">
                                    {{ $t('ticket_details.estimated_hours') }}
                                </div>
                                <div class="card-body">
                                    <form @submit.prevent="updateTicketDetailEstimatedHours">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">{{
                                                $t('ticket_details_input_dev_estimation_time')
                                                }} <strong class="text-danger">*</strong>
                                            </label>
                                            <input v-model="estimatedHoursFormData.dev_estimation_time"
                                                :class="{ 'is-invalid': errors.dev_estimation_time }"
                                                class="form-control" type="text">
                                            <div v-if="errors.dev_estimation_time" class="invalid-feedback">
                                                <span v-for="(error, index) in errors.dev_estimation_time" :key="index">
                                                    {{ error }}
                                                </span>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn w-100 btn-desino fw-bold m-2 rounded">
                                            {{ $t('ticket_details.estimated_hours.update_but_text') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="test_cases_tab_body" aria-labelledby="test_cases_tab_body" class="tab-pane fade"
                    role="tabpanel">
                    <div class="col-md-12 my-2">
                        <div class="card">
                            <div class="card-header fw-bold">
                                <label class="mt-2">{{ $t('ticket_detail_test_case_section_detail_title') }}</label>
                                <div id="createTestCaseModal" aria-hidden="true"
                                    aria-labelledby="createTestCaseModalLabel" class="modal fade" tabindex="-1">
                                    <CreateTestCaseModalComponent ref="createTestCaseModalComponent"
                                        @stored-testcase="updateTestCaseList" :ticket_id="selectedTask"
                                        :initiative_id="localInitiativeId" />
                                </div>
                                <div id="updateTestCaseModal" aria-hidden="true"
                                    aria-labelledby="updateTestCaseModalLabel" class="modal fade" tabindex="-1">
                                    <UpdateTestCaseModalComponent ref="updateTestCaseModalComponent"
                                        :ticket_id="selectedTask" :initiative_id="localInitiativeId"
                                        @stored-testcase="updateTestCaseList" />
                                </div>

                                <button class="float-end btn btn-primary" @click="showTestCaseModal"
                                    v-if="is_allow_case_add_test_section"> {{
                                        $t('ticket_detail_test_case_add_test_section_but_text') }}
                                </button>
                            </div>
                            <div v-if="test_cases?.length > 0" class="card-body pt-0">
                                <div class="row">
                                    <div class="col-md-6 mb-3" v-for="(test_case, index) in test_cases" :key="index">
                                        <div class="card mt-2 mb-2">
                                            <div class="card-header bg-secondary text-white">
                                                <label class="fw-bold"> Test {{ index + 1 }}
                                                    <span class="badge rounded-pill"
                                                        :class="test_case.status === 1 ? 'bg-success' : 'bg-danger'">
                                                        {{ test_case.status !== -1 ? (test_case.status === 1 ? 'success'
                                                            : 'failed') : 'pending' }}</span>
                                                </label>
                                            </div>
                                            <div class="card-body max-h-250">
                                                <span class="bg-desino text-white rounded fw-bold p-2">
                                                    {{ $t('ticket_detail_test_case_section_expected_behaviour') }}
                                                </span>
                                                <div class="p-2" v-html="test_case.expected_behaviour"></div>
                                                <div v-if="test_case.observations">
                                                    <span class="bg-desino text-white rounded fw-bold p-2">{{
                                                        $t('ticket_detail_test_case_section_actual_behaviour')
                                                        }}</span>
                                                    <div class="p-2" v-html="test_case.observations">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer" v-if="is_allow_case_update_test_section">
                                                <div class="row w-100">
                                                    <div class="col-md-6">
                                                        <button class="btn btn-success btn-sm w-100"
                                                            :disabled="test_case.status === 1"
                                                            @click="handleTestCaseAction(test_case.id, 'success')">
                                                            <i class="bi-check-lg text-white"></i>
                                                        </button>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <button
                                                            :disabled="test_case.status !== 1 && test_case.status !== -1"
                                                            class="btn btn-danger btn-sm w-100"
                                                            @click="handleTestCaseAction(test_case.id, 'failed')">
                                                            <i class="bi-x-lg text-white"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div v-for="(test_case, index) in test_cases" :key="index">
                                    <div class="card mt-2 mb-2">
                                        <div class="card-header bg-secondary text-white">
                                            <h5 class="fw-bold"> Test {{ index + 1 }}
                                                <span class="badge rounded-pill"
                                                    :class="test_case.status === 1 ? 'bg-success' : 'bg-danger'">{{
                                                        test_case.status !== -1 ? (test_case.status === 1 ? 'success' :
                                                            'failed') : 'pending'
                                                    }}</span>
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <small class="fw-bold bg-desino text-white rounded p-2">
                                                {{ $t('ticket_detail_test_case_section_expected_behaviour') }}
                                            </small> <br>
                                            <div v-html="test_case.expected_behaviour" class="mt-2">
                                            </div>
                                        </div>
                                        <div class="card-footer" v-if="!is_allow_case_update_test_section">
                                            <div class="row w-100">
                                                <div class="col-md-6">
                                                    <button class="btn btn-success btn-sm w-100">
                                                        <i class="bi-check-lg text-white"
                                                            @click="handleTestCaseAction(test_case.id, 'success')"></i>
                                                    </button>
                                                </div>
                                                <div class="col-md-6">
                                                    <button class="btn btn-danger btn-sm w-100">
                                                        <i class="bi-x-lg text-white"
                                                            @click="handleTestCaseAction(test_case.id, 'failed')"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>                                 -->
                            </div>
                            <div v-else class="p-5 text-center fw-bold">
                                {{ $t('ticket_detail_test_case_section_no_test_case') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="timeBookingForTicketDetailModal" aria-hidden="true" aria-labelledby="timeBookingForTicketDetailLabel"
        class="modal fade" tabindex="-1">
        <TimeBookingForTicketDetailComponent ref="timeBookingForTicketDetailComponent" />
    </div>
</template>

<script>
import GlobalMessage from './../../../components/GlobalMessage.vue';
import Multiselect from 'vue-multiselect';
import TinyMceEditor from "./../../../components/TinyMceEditor.vue";
import ticketService from "../../../services/TicketService.js";
import messageService from "../../../services/messageService.js";
import showToast from "./../../../utils/toasts.js";
import { mapActions, mapGetters } from 'vuex';
import { Modal } from "bootstrap";
import CreateTestCaseModalComponent from "./../Ticket/TestCase/CreateTestCaseModalComponent.vue";
import UpdateTestCaseModalComponent from "./../Ticket/TestCase/UpdateTestCaseModelComponent.vue";
import testCaseService from "./../../../services/TestCaseService.js";
import eventBus from "./../../../eventBus.js";
import TimeBookingForTicketDetailComponent from './TimeBookingForTicketDetailComponent.vue';

export default {
    name: 'SolutionDesignComponent',
    components: {
        CreateTestCaseModalComponent,
        UpdateTestCaseModalComponent,
        TinyMceEditor,
        GlobalMessage,
        Multiselect,
        TimeBookingForTicketDetailComponent
    },
    props: ['initiative_id', 'ticket_id'],
    data() {
        return {
            showTicketDropdown: false,
            localInitiativeId: this.$route.params.initiative_id,
            localTicketId: this.$route.params.ticket_id,
            selectedTask: this.$route.params.ticket_id,
            estimatedHoursFormData: {
                dev_estimation_time: '',
                initiative_id: '',
                ticket_id: '',
            },
            ticketData: {
                name: '',
                initial_dev_time: '',
                dev_estimation_time: '',
                task_type: '',
                functionality_name: '',
                asana_task_link: '',
                status_label: '',
                functional_owner: '',
                functional_owner_id: '',
                quality_owner: '',
                quality_owner_id: '',
                technical_owner: '',
                technical_owner_id: '',
                macro_status_label: {},
                initiative_name: '',
                display_functionality_name: '',
                functionality_description: '',
                is_show_mark_as_done_but: false,
                is_enable_mark_as_done_but: false,
                is_show_pre_action_but: false,
                is_allow_dev_estimation_time: false,
                user_actions_count: 0,
            },
            currentActionFormData: {
                ticket_id: '',
                user_id: '',
                action: '',
                status: '',
            },
            nextActionFormData: {
                ticket_id: '',
                user_id: '',
                action: '',
                status: '',
            },
            previousActionFormData: {
                ticket_id: '',
                user_id: '',
                action: '',
                status: '',
            },
            currentAction: '',
            nextAction: '',
            previousAction: '',
            users: [],
            actionStatus: [],
            releaseNoteForm: {
                'release_note': '',
            },
            test_cases: [],
            tasksForDropdown: [],
            is_allow_case_add_test_section: false,
            is_allow_case_update_test_section: false,
            errors: {},
            showMessage: true,
        };
    },
    computed: {
        ...mapGetters(['user']),
        selectedTaskObject: {
            get() {
                return this.tasksForDropdown.find(task => task.id == this.selectedTask) || null;
            },
            set(newTask) {
                this.selectedTask = newTask ? newTask.id : this.localTicketId;
            }
        }
    },
    watch: {
        selectedTask(newTask) {
            // Update the URL when a task is selected
            if (newTask) {
                let param = {
                    initiative_id: this.localInitiativeId, ticket_id: newTask
                }
                this.$router.push({ name: 'task.detail', params: param });
                this.fetchTicketData(newTask);
            }
        }
    },
    methods: {
        ...mapActions(['setLoading', 'setServerError']),
        async fetchTicketData(id) {
            this.resetEstimatedHoursFormData();
            try {
                this.setLoading(true);
                let data = {
                    initiative_id: this.localInitiativeId,
                    ticket_id: id
                }
                const response = await ticketService.fetchTicket(data);
                if (!response.content) {
                    messageService.setMessage(response.message, 'danger');
                    this.$router.push({ name: 'home' });
                } else {
                    this.setData(response.content);
                    this.tasksForDropdown = response.meta_data.all_tickets;
                    this.users = response.meta_data.users;
                    this.actionStatus = response.meta_data.action_status;
                    this.is_allow_case_add_test_section = response.meta_data.is_allow_case_add_test_section;
                    this.is_allow_case_update_test_section = response.meta_data.is_allow_case_update_test_section;
                }
                this.setLoading(false);
            } catch (error) {
                console.error('An error occurred again:', error);
            }
        },
        async updateReleaseNote() {
            this.clearMessages();
            try {
                this.setLoading(true);
                let data = {
                    'release_note': this.releaseNoteForm.release_note,
                    'initiative_id': this.localInitiativeId,
                    'ticket_id': this.localTicketId
                }
                const response = await ticketService.updateReleaseNote(data);
                this.setData(response.content);
                showToast(response.message, 'success');
                this.setLoading(false);
            } catch (error) {
                this.handleError(error);
            }
        },
        showTestCaseModal(type = 'create', testCaseId = null, status = false) {
            let modalElement;
            if (type === 'create' || testCaseId === null) {
                this.$refs.createTestCaseModalComponent.resetForm();
                modalElement = document.getElementById('createTestCaseModal');
            } else {
                this.$refs.updateTestCaseModalComponent.resetForm();
                this.$refs.updateTestCaseModalComponent.getTestCaseData(testCaseId, status);
                modalElement = document.getElementById('updateTestCaseModal');
            }

            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        setData(content) {
            this.ticketData.id = content.id;
            this.ticketData.initiative_id = content.initiative_id;
            this.ticketData.name = content.name;
            this.ticketData.composed_name = content.composed_name;
            this.ticketData.initial_dev_time = content.initial_estimation_development_time;
            this.ticketData.dev_estimation_time = content.dev_estimation_time;
            this.ticketData.task_type = content.type_label;
            this.ticketData.status_label = content.status_label;
            this.ticketData.functionality_name = content?.functionality?.name.length > 0 ? content.initiative?.name + ' - ' + content?.functionality?.name : content.initiative?.name;
            this.ticketData.initiative_name = content.initiative?.name;
            this.ticketData.display_functionality_name = content?.functionality?.display_name;
            this.ticketData.functionality_description = content?.functionality?.description;
            this.ticketData.functional_owner = content.initiative?.functional_owner?.name;
            this.ticketData.functional_owner_id = content.initiative?.functional_owner?.id;
            this.ticketData.quality_owner = content.initiative?.quality_owner?.name;
            this.ticketData.quality_owner_id = content.initiative?.quality_owner?.id;
            this.ticketData.technical_owner = content.initiative?.technical_owner?.name;
            this.ticketData.technical_owner_id = content.initiative?.technical_owner?.id;
            this.ticketData.asana_task_link = content.asana_task_link;
            this.ticketData.auto_wait_for_client_approval = content.auto_wait_for_client_approval == 1 ? true : false;
            this.ticketData.macro_status_label = content.macro_status_label;
            this.ticketData.is_show_mark_as_done_but = content.is_show_mark_as_done_but;
            this.ticketData.is_enable_mark_as_done_but = content.is_enable_mark_as_done_but;
            this.ticketData.is_show_pre_action_but = content.is_show_pre_action_but;
            this.ticketData.is_allow_dev_estimation_time = content.is_allow_dev_estimation_time;
            this.ticketData.is_disable_action_user = content.is_disable_action_user;
            this.currentAction = content.current_action;
            this.currentActionFormData.user_id = content.current_action?.user_id;
            this.currentActionFormData.status = content.current_action?.status;
            this.nextAction = content.next_action;
            this.nextActionFormData.user_id = content.next_action?.user_id;
            this.nextActionFormData.status = content.next_action?.status;
            this.previousAction = content.previous_action;
            this.previousActionFormData.user_id = content?.previous_action?.user_id;
            this.previousActionFormData.status = content.previous_action?.status;
            this.releaseNoteForm.release_note = content.release_note;
            this.test_cases = content.test_cases;
            this.user_actions_count = content.actions_count;
        },
        onTaskSelect() {
            // Ensure the selected task is synced with the dropdown
            this.$nextTick(() => {
                if (this.selectedTaskObject) {
                    this.selectedTask = this.selectedTaskObject.id;
                }
            });
        },
        updateTestCaseList(response) {
            this.test_cases = response.content;
        },
        async handleTestCaseAction(testCaseId, status) {
            if (status === 'failed') {
                status = false;
            } else {
                status = true;
            }
            this.showTestCaseModal('update', testCaseId, status);
        },
        handleCurrentActionChangeUser(userId) {
            const previousUserId = this.currentAction?.user?.id;
            this.$swal({
                title: this.$t('ticket_detail.confirm_alert.current_action_change_user_title'),
                text: this.$t('ticket_detail.confirm_alert.current_action_change_user_text'),
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
                    this.currentActionFormData = {
                        user_id: userId,
                        action_id: this.currentAction.id,
                        action: this.currentAction.action,
                        ticket_id: this.localTicketId,
                        status: this.currentActionFormData.status,
                        initiative_id: this.localInitiativeId,
                        action_text: 'current_action',
                    }
                    this.changeActionUser(this.currentActionFormData);
                } else {
                    this.currentActionFormData.user_id = previousUserId;
                }
            }).catch(() => {
                this.currentActionFormData.user_id = previousUserId;
            });
        },
        handleCurrentActionChangeStatus() {
            if (!this.ticketData.is_enable_mark_as_done_but) {
                return false;
            }
            this.$swal({
                // title: this.$t('ticket_detail.confirm_alert.current_action_change_status_title'),
                title: this.$t('ticket_detail.confirm_alert.current_action_change_status_text'),
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
                    this.currentActionFormData = {
                        user_id: this.currentAction?.user?.id,
                        action_id: this.currentAction.id,
                        action: this.currentAction.action,
                        ticket_id: this.localTicketId,
                        status: this.currentAction.status,
                        initiative_id: this.localInitiativeId,
                        action_text: 'current_action',
                    }
                    this.changeActionStatus(this.currentActionFormData);
                } else {
                }
            }).catch(() => {
            });
        },
        handlePreviousActionStatus() {
            if (!this.ticketData.is_show_pre_action_but) {
                return false;
            }
            this.$swal({
                // title: this.$t('ticket_detail.confirm_alert.current_action_change_status_title'),
                title: this.$t('ticket_detail.confirm_alert.current_previous_action_status_text'),
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
                    this.previousActionFormData = {
                        user_id: this.currentAction?.user?.id,
                        action_id: this.currentAction.id,
                        action: this.currentAction.action,
                        ticket_id: this.localTicketId,
                        status: this.currentAction.status,
                        initiative_id: this.localInitiativeId,
                        action_text: 'previous_action',
                    }
                    this.changePreviousActionStatus(this.previousActionFormData);
                } else {
                }
            }).catch(() => {
            });
        },
        async changeActionUser(passData) {
            try {
                await this.setLoading(true);
                const { message, status } = await ticketService.changeActionUser(passData);
                showToast(message, 'success');
                await this.setLoading(false);
                this.fetchTicketData(this.localTicketId);
                this.clearMessages();
            } catch (error) {
                this.handleError(error);
                this.tasks[index].project = this.previousProject;
            }
        },
        async changeActionStatus(passData) {
            try {
                await this.setLoading(true);
                const { message } = await ticketService.changeActionStatus(passData);
                showToast(message, 'success');
                await this.setLoading(false);
                this.fetchTicketData(this.localTicketId);
            } catch (error) {
                this.handleError(error);
                this.tasks[index].project = this.previousProject;
            }
        },
        async changePreviousActionStatus(passData) {
            try {
                await this.setLoading(true);
                const { message } = await ticketService.changePreviousActionStatus(passData);
                showToast(message, 'success');
                await this.setLoading(false);
                this.fetchTicketData(this.localTicketId);
            } catch (error) {
                this.handleError(error);
                this.tasks[index].project = this.previousProject;
            }
        },
        showHideProcessingButton() {
            return true;
            let allowProcessTestCase = false;
            if (this.user?.id === this.ticketData.quality_owner_id && this.currentAction.action == 4 && this.currentAction.status == 1) {
                allowProcessTestCase = true;
            }
            return allowProcessTestCase;
        },
        async updateTicketDetailEstimatedHours() {
            this.clearMessages();
            try {
                this.setLoading(true);
                this.estimatedHoursFormData.initiative_id = this.localInitiativeId;
                this.estimatedHoursFormData.ticket_id = this.localTicketId;
                const { content, message } = await ticketService.updateTicketDetailEstimatedHours(this.estimatedHoursFormData);
                this.setLoading(false);
                showToast(message, 'success');
                this.resetEstimatedHoursFormData();
                this.fetchTicketData(this.localTicketId);
            } catch (error) {
                this.handleError(error);
            }
        },
        resetForm() {
            this.releaseNoteForm = {
                release_note: "",
            };
            this.errors = {};
        },
        resetEstimatedHoursFormData() {
            this.estimatedHoursFormData = {
                dev_estimation_time: "",
            };
            this.errors = {};
        },
        handleTimeBooking() {
            const modalElement = document.getElementById('timeBookingForTicketDetailModal');
            if (modalElement) {
                this.$refs.timeBookingForTicketDetailComponent.getTimeBookingForTicketDetailData(this.ticketData);
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        clearMessages() {
            this.errors = {};
            messageService.clearMessage();
        },
        handleError(error) {
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                messageService.setMessage(error.message, 'danger');
            }
            this.setLoading(false);
        },
        refreshTicketDetail() {
            this.fetchTicketData(this.localTicketId);
        }
    },
    mounted() {
        this.fetchTicketData(this.localTicketId);
        eventBus.$on('refreshTicketDetail', this.refreshTicketDetail);
    }
}
</script>
