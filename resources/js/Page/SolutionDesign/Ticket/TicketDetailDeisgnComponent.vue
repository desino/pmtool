<template>
    <div class="app-content-header pb-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">{{ ticketData.composed_name }}
                        <span>
                            <input type="checkbox" v-model="showTicketDropdown" class="btn-check" id="btn-check" autocomplete="off">
                            <label class="btn btn-desino-outline fw-bold border" for="btn-check">
                                <i class="bi" :class="{ 'bi-pencil-square': !showTicketDropdown, 'bi-x-lg text-danger': showTicketDropdown}">
                                </i>
                            </label>
                        </span>
                    </h3>
                    <div class="col-md-12 py-2" v-if="showTicketDropdown">
                        <multiselect v-model="selectedTaskObject" :multiple="false" :options="tasksForDropdown"
                                     :searchable="true" deselect-label="Selected" label="composed_name"
                                     placeholder="Search & Select Task" track-by="id" @input="onTaskSelect">
                        </multiselect>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div v-if="ticketData.asana_task_link" class="float-sm-end">
                        <a :href="ticketData.asana_task_link" class="btn btn-desino bg-desino text-white mt-2"
                           target="_blank">
                            <svg fill="none" height="21px" viewBox="0 0 24 24" width="21px"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path clip-rule="evenodd" d="M10.4693 3.55448C10.9546 3.35346 11.4747 3.25 12 3.25C12.5253 3.25 13.0454 3.35346 13.5307 3.55448C14.016 3.7555 14.457 4.05014 14.8284 4.42157C15.1999 4.79301 15.4945 5.23397 15.6955 5.71927C15.8965 6.20457 16 6.72471 16 7.25C16 7.77529 15.8965 8.29543 15.6955 8.78073C15.4945 9.26603 15.1999 9.70699 14.8284 10.0784C14.457 10.4499 14.016 10.7445 13.5307 10.9455C13.0454 11.1465 12.5253 11.25 12 11.25C11.4747 11.25 10.9546 11.1465 10.4693 10.9455C9.98396 10.7445 9.54301 10.4499 9.17157 10.0784C8.80014 9.70699 8.5055 9.26604 8.30448 8.78073C8.10346 8.29543 8 7.77529 8 7.25C8 6.72471 8.10346 6.20457 8.30448 5.71927C8.5055 5.23396 8.80014 4.79301 9.17157 4.42157C9.54301 4.05014 9.98396 3.7555 10.4693 3.55448ZM12 4.75C11.6717 4.75 11.3466 4.81466 11.0433 4.9403C10.74 5.06594 10.4644 5.25009 10.2322 5.48223C10.0001 5.71438 9.81594 5.98998 9.6903 6.29329C9.56466 6.59661 9.5 6.92169 9.5 7.25C9.5 7.5783 9.56466 7.90339 9.6903 8.20671C9.81594 8.51002 10.0001 8.78562 10.2322 9.01777C10.4644 9.24991 10.74 9.43406 11.0433 9.5597C11.3466 9.68534 11.6717 9.75 12 9.75C12.3283 9.75 12.6534 9.68534 12.9567 9.5597C13.26 9.43406 13.5356 9.24991 13.7678 9.01777C13.9999 8.78562 14.1841 8.51002 14.3097 8.20671C14.4353 7.90339 14.5 7.5783 14.5 7.25C14.5 6.9217 14.4353 6.59661 14.3097 6.29329C14.1841 5.98998 13.9999 5.71438 13.7678 5.48223C13.5356 5.25009 13.26 5.06594 12.9567 4.9403C12.6534 4.81466 12.3283 4.75 12 4.75Z"
                                      fill="#fff"
                                      fill-rule="evenodd"/>
                                <path clip-rule="evenodd" d="M5.46927 12.5545C5.95457 12.3535 6.47471 12.25 7 12.25C7.52529 12.25 8.04543 12.3535 8.53073 12.5545C9.01604 12.7555 9.45699 13.0501 9.82843 13.4216C10.1999 13.793 10.4945 14.234 10.6955 14.7193C10.8965 15.2046 11 15.7247 11 16.25C11 16.7753 10.8965 17.2954 10.6955 17.7807C10.4945 18.266 10.1999 18.707 9.82843 19.0784C9.45699 19.4499 9.01604 19.7445 8.53073 19.9455C8.04543 20.1465 7.52529 20.25 7 20.25C6.47471 20.25 5.95457 20.1465 5.46927 19.9455C4.98396 19.7445 4.54301 19.4499 4.17157 19.0784C3.80014 18.707 3.5055 18.266 3.30448 17.7807C3.10346 17.2954 3 16.7753 3 16.25C3 15.7247 3.10346 15.2046 3.30448 14.7193C3.5055 14.234 3.80014 13.793 4.17157 13.4216C4.54301 13.0501 4.98396 12.7555 5.46927 12.5545ZM7 13.75C6.67169 13.75 6.34661 13.8147 6.04329 13.9403C5.73998 14.0659 5.46438 14.2501 5.23223 14.4822C5.00009 14.7144 4.81594 14.99 4.6903 15.2933C4.56466 15.5966 4.5 15.9217 4.5 16.25C4.5 16.5783 4.56466 16.9034 4.6903 17.2067C4.81594 17.51 5.00009 17.7856 5.23223 18.0178C5.46438 18.2499 5.73998 18.4341 6.04329 18.5597C6.34661 18.6853 6.67169 18.75 7 18.75C7.3283 18.75 7.65339 18.6853 7.95671 18.5597C8.26002 18.4341 8.53562 18.2499 8.76777 18.0178C8.99991 17.7856 9.18406 17.51 9.3097 17.2067C9.43534 16.9034 9.5 16.5783 9.5 16.25C9.5 15.9217 9.43534 15.5966 9.3097 15.2933C9.18406 14.99 8.99991 14.7144 8.76777 14.4822C8.53562 14.2501 8.26002 14.0659 7.95671 13.9403C7.65339 13.8147 7.3283 13.75 7 13.75Z"
                                      fill="#fff"
                                      fill-rule="evenodd"/>
                                <path clip-rule="evenodd" d="M17 12.25C16.4747 12.25 15.9546 12.3535 15.4693 12.5545C14.984 12.7555 14.543 13.0501 14.1716 13.4216C13.8001 13.793 13.5055 14.234 13.3045 14.7193C13.1035 15.2046 13 15.7247 13 16.25C13 16.7753 13.1035 17.2954 13.3045 17.7807C13.5055 18.266 13.8001 18.707 14.1716 19.0784C14.543 19.4499 14.984 19.7445 15.4693 19.9455C15.9546 20.1465 16.4747 20.25 17 20.25C17.5253 20.25 18.0454 20.1465 18.5307 19.9455C19.016 19.7445 19.457 19.4499 19.8284 19.0784C20.1999 18.707 20.4945 18.266 20.6955 17.7807C20.8965 17.2954 21 16.7753 21 16.25C21 15.7247 20.8965 15.2046 20.6955 14.7193C20.4945 14.234 20.1999 13.793 19.8284 13.4216C19.457 13.0501 19.016 12.7555 18.5307 12.5545C18.0454 12.3535 17.5253 12.25 17 12.25ZM16.0433 13.9403C16.3466 13.8147 16.6717 13.75 17 13.75C17.3283 13.75 17.6534 13.8147 17.9567 13.9403C18.26 14.0659 18.5356 14.2501 18.7678 14.4822C18.9999 14.7144 19.1841 14.99 19.3097 15.2933C19.4353 15.5966 19.5 15.9217 19.5 16.25C19.5 16.5783 19.4353 16.9034 19.3097 17.2067C19.1841 17.51 18.9999 17.7856 18.7678 18.0178C18.5356 18.2499 18.26 18.4341 17.9567 18.5597C17.6534 18.6853 17.3283 18.75 17 18.75C16.6717 18.75 16.3466 18.6853 16.0433 18.5597C15.74 18.4341 15.4644 18.2499 15.2322 18.0178C15.0001 17.7856 14.8159 17.51 14.6903 17.2067C14.5647 16.9034 14.5 16.5783 14.5 16.25C14.5 15.9217 14.5647 15.5966 14.6903 15.2933C14.8159 14.99 15.0001 14.7144 15.2322 14.4822C15.4644 14.2501 15.74 14.0659 16.0433 13.9403Z"
                                      fill="#fff"
                                      fill-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <hr>

    <GlobalMessage v-if="showMessage"/>

    <div class="app-content mt-2">
        <div class="row">
            <div class="col-md-6 border-end">
                <div class="row row-cols-xl-3 row-cols-lg-3 row-cols-md-2 row-cols-2 g-2 g-lg-3">
                    <div class="col">
                        <div class="card border-0 h-100">
                            <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                                <div class="w-100 lh-1">
                                    <h6 class="fw-bold mx-1">Task Status</h6>
                                    <span class="badge rounded-3 bg-danger-subtle text-danger">{{
                                            ticketData.status_label
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 h-100">
                            <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                                <div class="w-100 lh-1">
                                    <h6 class="fw-bold mx-1">Functional Owner</h6>
                                    <span
                                        class="badge rounded-3 bg-desino text-white">{{ ticketData.functional_owner }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 h-100">
                            <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                                <div class="w-100 lh-1">
                                    <h6 class="fw-bold mx-1">Technical Owner</h6>
                                    <span
                                        class="badge rounded-3 bg-info-subtle text-info">{{ ticketData.technical_owner }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 h-100">
                            <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                                <div class="w-100 lh-1">
                                    <h6 class="fw-bold mx-1">Testing Owner</h6>
                                    <span
                                        class="badge rounded-3 bg-primary-subtle text-primary">{{ ticketData.quality_owner }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card border-0 h-100">
                            <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                                <div class="w-100 lh-1">
                                    <h6 class="fw-bold mx-1">Task Estimation</h6>
                                    <span class="badge rounded-3 bg-success-subtle text-success">{{
                                            ticketData.initial_dev_time
                                        }} hrs</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row row-cols-xl-3 row-cols-lg-3 row-cols-md-2 row-cols-2 g-2 g-lg-3">
                    <div class="col">
                        <strong>Current Action Name</strong>
                        <div class="mb-3">
                            <label for="">{{ currentAction.action_name }}</label>
                        </div>
                    </div>
                    <div class="col">
                        <strong>Current Action User</strong>
                        <div class="mb-3">
                            <select :value="getSelectedCurrentActionUserId(currentAction?.user?.id)" class="form-select"
                                    @change="handleCurrentActionChangeUser($event.target.value)">
                                <option value="">Select User</option>
                                <option v-for="user in users" :key="user.id" :value="user.id">
                                    {{ user.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <strong>Current Action Status</strong>
                        <div class="mb-3">
                            <select :value="getSelectedCurrentActionStatus(currentAction?.status)" class="form-select"
                                    @change="updateUser(action.id, $event.target.value)">
                                <option value="">Select Status</option>
                                <option v-for="action in actionStatus" :key="action.id" :value="action.id">
                                    {{ action.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row my-1">
            <div class="col-md-6 my-2">
                <div class="card">
                    <div class="card-header fw-bold">
                        {{ ticketData.functionality_name }}
                    </div>
                    <div class="card-body">
                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
                            been
                            the industry's standard dummy text ever since the 1500s, when an unknown printer took a
                            galley
                            of type and scrambled it to make a type specimen book. It has survived not only five
                            centuries,
                            but also the leap into electronic typesetting, remaining essentially unchanged. It was
                            popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
                            passages,
                            and more recently with desktop publishing software like Aldus PageMaker including versions
                            of
                            Lorem Ipsum</p>

                        <p>It is a long established fact that a reader will be distracted by the readable content of a
                            page
                            when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
                            normal
                            distribution of letters, as opposed to using 'Content here, content here', making it look
                            like
                            readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum
                            as
                            their default model text, and a search for 'lorem ipsum' will uncover many web sites still
                            in
                            their infancy. Various versions have evolved over the years, sometimes by accident,
                            sometimes on
                            purpose (injected humour and the like).</p>

                        <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
                            been
                            the industry's standard dummy text ever since the 1500s, when an unknown printer took a
                            galley
                            of type and scrambled it to make a type specimen book. It has survived not only five
                            centuries,
                            but also the leap into electronic typesetting, remaining essentially unchanged. It was
                            popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum
                            passages,
                            and more recently with desktop publishing software like Aldus PageMaker including versions
                            of
                            Lorem Ipsum</p>

                        <p>It is a long established fact that a reader will be distracted by the readable content of a
                            page
                            when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less
                            normal
                            distribution of letters, as opposed to using 'Content here, content here', making it look
                            like
                            readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum
                            as
                            their default model text, and a search for 'lorem ipsum' will uncover many web sites still
                            in
                            their infancy. Various versions have evolved over the years, sometimes by accident,
                            sometimes on
                            purpose (injected humour and the like).</p>

                    </div>
                </div>
            </div>
            <div class="col-md-6 my-2">
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header">
                            Release Notes For Client
                        </div>
                        <div class="card-body">
                            <p> Describe & Document the change done for the client. Use print-screen so that the client
                                has clarity on how the functionality has changed</p>
                            <TinyMceEditor v-model="releaseNoteForm.release_note"/>
                            <div v-if="errors.release_note" class="text-danger mt-2">
                                <span v-for="(error, index) in errors.release_note" :key="index">{{ error }}</span>
                            </div>
                            <button class="btn w-100 bg-desino text-white fw-bold m-2 rounded"
                                    @click="updateReleaseNote"> Update
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 mb-3">
                    <div class="card">
                        <div class="card-header fw-bold">
                            <label class="mt-2">Define Test</label>
                            <button class="float-end btn btn-primary"> Add Test Section</button>
                        </div>
                        <div class="card-body pt-0">
                            <div class="border-bottom py-3">
                                <label class="fw-bold h6"> Test 1 </label> <br>
                                <small class="mt-2 fw-bold">Expected Behaviour </small> <br>
                                <small>
                                    When Exporting File The file should be happy not sad.
                                </small>
                            </div>
                            <div class="border-bottom py-3">
                                <label class="fw-bold h6"> Test 2 </label> <br>
                                <small class="mt-2 fw-bold">Expected Behaviour </small> <br>
                                <small>
                                    When Exporting File The file should be crying not sad.
                                </small>
                            </div>
                            <div class="border-bottom py-3">
                                <label class="fw-bold h6"> Test 3 </label> <br>
                                <small class="mt-2 fw-bold">Expected Behaviour </small> <br>
                                <small>
                                    When Exporting File The file should be flying not sad.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import GlobalMessage from './../../../components/GlobalMessage.vue';
import Multiselect from 'vue-multiselect';
import TinyMceEditor from "./../../../components/TinyMceEditor.vue";
import ticketService from "../../../services/TicketService.js";
import messageService from "../../../services/messageService.js";
import showToast from "./../../../utils/toasts.js";
import {mapActions} from 'vuex';

export default {
    name: 'SolutionDesignComponent',
    components: {
        TinyMceEditor,
        GlobalMessage,
        Multiselect,
    },
    props: ['initiative_id', 'ticket_id'],
    data() {
        return {
            showTicketDropdown: false,
            localInitiativeId: this.$route.params.initiative_id,
            localTicketId: this.$route.params.ticket_id,
            selectedTask: this.$route.params.ticket_id,
            ticketData: {
                name: '',
                initial_dev_time: '',
                task_type: '',
                functionality_name: '',
                asana_task_link: '',
                status_label: '',
                functional_owner: '',
                quality_owner: '',
                technical_owner: '',
            },
            currentAction: '',
            users: [],
            actionStatus: [],
            releaseNoteForm: {
                'release_note': '',
            },
            tasksForDropdown: [],
            errors: {},
            showMessage: true,
        };
    },
    computed: {
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
                this.$router.push({name: 'task.detail', params: param});
                this.fetchTicketData(newTask);
            }
        }
    },
    methods: {
        ...mapActions(['setLoading']),
        async fetchTicketData(id) {
            try {
                this.setLoading(true);
                let data = {
                    initiative_id: this.localInitiativeId,
                    ticket_id: id
                }
                const response = await ticketService.fetchTicket(data);
                if (!response.content) {
                    messageService.setMessage(response.message, 'danger');
                    this.$router.push({name: 'home'});
                } else {
                    this.setData(response.content);
                    this.tasksForDropdown = response.meta_data.all_tickets;
                    this.users = response.meta_data.users;
                    this.actionStatus = response.meta_data.action_status;
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
        setData(content) {
            this.ticketData.name = content.name;
            this.ticketData.composed_name = content.composed_name;
            this.ticketData.initial_dev_time = content.initial_estimation_development_time;
            this.ticketData.task_type = content.type_label;
            this.ticketData.status_label = content.status_label;
            this.ticketData.functionality_name = content?.functionality?.name.length > 0 ? content.initiative?.name + ' - ' + content?.functionality?.name : content.initiative?.name;
            this.ticketData.functional_owner = content.initiative?.functional_owner?.name;
            this.ticketData.quality_owner = content.initiative?.quality_owner?.name;
            this.ticketData.technical_owner = content.initiative?.technical_owner?.name;
            this.ticketData.asana_task_link = content.asana_task_link;
            this.currentAction = content.current_action;
            this.releaseNoteForm.release_note = content.release_note;
        },
        onTaskSelect() {
            // Ensure the selected task is synced with the dropdown
            this.$nextTick(() => {
                if (this.selectedTaskObject) {
                    this.selectedTask = this.selectedTaskObject.id;
                }
            });
        },
        getSelectedCurrentActionUserId(userId) {
            const user = this.users?.find(a => a.id === userId);
            return user ? user.id : "";
        },
        handleCurrentActionChangeUser(userId) {

        },
        getSelectedCurrentActionStatus(statusId) {
            const actionStatus = this.actionStatus?.find(a => a.id === statusId);
            return actionStatus ? actionStatus.id : "";
        },
        handleCurrentActionChangeStatus(statusId) {

        },
        resetForm() {
            this.releaseNoteForm = {
                release_note: "",
            };
            this.errors = {};
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
    },
    mounted() {
        this.fetchTicketData(this.localTicketId);
    }
}
</script>
