<template>
    <div class="app-content-header pb-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="m-0">Task Name - {{ ticketData.name }}</h3>
                    <div class="col-md-12 py-2">
                        <multiselect v-model="selectedTaskObject" :multiple="false" :options="tasksForDropdown"
                                     :searchable="true" deselect-label="Can't remove this value"
                                     label="name" placeholder="Search & Select Task" track-by="id"
                                     @input="onTaskSelect">
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

    <GlobalMessage v-if="showMessage"/>


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
                            <span
                                class="badge rounded-3 bg-success-subtle text-success">{{ ticketData.initial_dev_time }} hrs</span>
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
                            <TinyMceEditor v-model="test_desc"/>
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

export default {
    name: 'SolutionDesignComponent',
    components: {
        TinyMceEditor,
        GlobalMessage,
        Multiselect
    },
    props: ['id'],
    data() {
        return {
            ticket_id: this.$route.params.id,
            selectedTask: this.$route.params.id, // Initialize with the current route param
            ticketData: {
                name: '',
                initial_dev_time: '',
                task_type: '',
                functionality_name: '',
            },
            test_desc: '',
            tasksForDropdown: []
        };
    },
    computed: {
        selectedTaskObject: {
            get() {
                return this.tasksForDropdown.find(task => task.id === this.selectedTask) || null;
            },
            set(newTask) {
                this.selectedTask = newTask ? newTask.id : this.ticket_id;
            }
        }
    },
    watch: {
        selectedTask(newTask) {
            // Update the URL when a task is selected
            if (newTask) {
                this.$router.push({ name: 'task.detail', params: { id: newTask } });
                this.fetchTicketData(newTask);
            }
        }
    },
    methods: {
        async fetchTicketData(id) {
            const response = await ticketService.fetchTicket(id);
            if (!response.content) {
                messageService.setMessage(response.message, 'danger');
                this.$router.push({name: 'home'});
            } else {
                this.setData(response.content);
            }
        },
        async fetchAllTicketForDropDown() {
            const response = await ticketService.fetchAllTicketForDropDown();
            if (!response.content) {
                messageService.setMessage(response.message, 'danger');
                this.$router.push({name: 'home'});
            } else {
                this.tasksForDropdown = response.content;

                // Set the default task in the dropdown based on the route param
                const defaultTask = this.tasksForDropdown.find(task => task.id == this.ticket_id);
                if (defaultTask) {
                    this.selectedTaskObject = defaultTask;
                }
            }
        },
        setData(content) {
            this.ticketData.name = content.name;
            this.ticketData.initial_dev_time = content.initial_estimation_development_time;
            this.ticketData.task_type = content.type_label;
            this.ticketData.functionality_name = content.functionality.name;
        },
        onTaskSelect() {
            // Ensure the selected task is synced with the dropdown
            this.$nextTick(() => {
                if (this.selectedTaskObject) {
                    this.selectedTask = this.selectedTaskObject.id;
                }
            });
        }
    },
    mounted() {
        this.fetchAllTicketForDropDown();
        this.fetchTicketData(this.ticket_id);
    }
}
</script>
