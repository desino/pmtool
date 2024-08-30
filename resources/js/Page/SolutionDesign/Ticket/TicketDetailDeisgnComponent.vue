<template>
    <div class="app-content-header pb-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">Task Name - {{ ticketData.composed_name }}</h3>
                    <div class="col-md-12 py-2">
                        <multiselect v-model="selectedTaskObject" :multiple="false" :options="tasksForDropdown"
                            :searchable="true" deselect-label="Can't remove this value" label="composed_name"
                            placeholder="Search & Select Task" track-by="id" @input="onTaskSelect">
                        </multiselect>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-end">
                        <a class="btn btn-desino bg-desino text-white mt-2" href="www.asana.com">Open Task Details in
                            Asana</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <GlobalMessage v-if="showMessage" />


    <div class="app-content mt-2">
        <div class="row row-cols-xl-6 row-cols-lg-3 row-cols-md-3 row-cols-2 g-2 g-lg-3">
            <div class="col">
                <div class="card border-0 h-100">
                    <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                        <div class="w-100 lh-1">
                            <h6 class="fw-bold mx-1">Task Type</h6>
                            <span class="badge rounded-3 bg-danger-subtle text-danger">{{ ticketData.task_type }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 h-100">
                    <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                        <div class="w-100 lh-1">
                            <h6 class="fw-bold mx-1">Current Stage</h6>
                            <span class="badge rounded-3 bg-warning-subtle text-warning">Develop</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 h-100">
                    <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                        <div class="w-100 lh-1">
                            <h6 class="fw-bold mx-1">Functional Owner</h6>
                            <span class="badge rounded-3 bg-desino text-white">Joren M.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 h-100">
                    <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                        <div class="w-100 lh-1">
                            <h6 class="fw-bold mx-1">Technical Owner</h6>
                            <span class="badge rounded-3 bg-info-subtle text-info">Mrunal P.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 h-100">
                    <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                        <div class="w-100 lh-1">
                            <h6 class="fw-bold mx-1">Testing Owner</h6>
                            <span class="badge rounded-3 bg-primary-subtle text-primary">Kalyani D.</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card border-0 h-100">
                    <div class="card-body p-2 px-4 text-left d-flex align-items-center">
                        <div class="w-100 lh-1">
                            <h6 class="fw-bold mx-1">Task Estimation</h6>
                            <span class="badge rounded-3 bg-success-subtle text-success">{{ ticketData.initial_dev_time
                                }} hrs</span>
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
                            <TinyMceEditor v-model="releaseNoteForm.release_note" />
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
import { mapActions } from 'vuex';

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
            localInitiativeId: this.$route.params.initiative_id,
            localTicketId: this.$route.params.ticket_id,
            selectedTask: this.$route.params.ticket_id,
            ticketData: {
                name: '',
                initial_dev_time: '',
                task_type: '',
                functionality_name: '',
            },
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
                this.$router.push({ name: 'task.detail', params: param });
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
                    this.$router.push({ name: 'home' });
                } else {
                    this.setData(response.content);
                    this.tasksForDropdown = response.meta_data.all_tickets;
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
            this.ticketData.functionality_name = content?.functionality?.name;
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
