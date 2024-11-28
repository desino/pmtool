<template>
    <aside ref="sidebar" class="app-sidebar bg-white border-end">
        <div class="sidebar-brand">
            <router-link class="brand-link" :to="{ name: 'home' }" @click="unselectHeaderInitiative">
                <img alt="Brand Image" class="brand-image" :src="logoPath" />
            </router-link>
        </div>
        <div class="p-2 border-bottom">
            <div class="my-3 d-flex ">
                <div class="image my-auto text-center">
                    <img alt="User Image" class="user-image rounded-circle shadow w-75"
                        src="https://avatars.githubusercontent.com/u/165763425?v=4&size=64">
                </div>
                <div v-if="user" class="info">
                    <span class="d-block">{{ user.name }}</span>
                    <span> {{ user.email }}</span>
                </div>
            </div>
        </div>
        <div class="p-2 border rounded m-1 shadow">
            <div class="form-group pb-0">
                <HeaderInitiativeDropBoxComponent />
            </div>
            <div
                v-if="isActive('solution-design') || isActive('tasks') || isActive('my-tickets') || isActive('task.detail') || isActive('projects') || isActive('solution-design.detail') || isActive('solution-design.download') || isActive('bulk-create-tickets') || isActive('deployments')">
                <div class="shadow">
                    <div class="p-2 rounded-bottom">
                        <nav class="mt-1">
                            <ul class="nav sidebar-menu flex-column">
                                <li class="nav-item"
                                    v-if="(user?.is_admin || initiativeData?.functional_owner_id === user?.id || initiativeData?.technical_owner_id === user?.id) && currentInitiative?.id">
                                    <a class="nav-link text-dark" href="javascript:" @click="showCreateTicketModal">
                                        <i class="bi bi-plus-circle"></i>
                                        {{ $t('header.menu.create_ticket') }}
                                    </a>
                                </li>
                                <li class="nav-item" v-if="user?.is_admin && currentInitiative?.id">
                                    <a class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('solution-design') }"
                                        href="javascript:" @click="showSolutionDesign"><i
                                            class="bi bi-file-pdf-fill"></i>
                                        {{ $t('header.menu.solution_design') }}
                                    </a>
                                </li>
                                <li class="nav-item" v-if="currentInitiative?.id">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('solution-design.detail') }"
                                        :to="{ name: 'solution-design.detail', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-file-pdf-fill"></i>
                                        {{ $t('header.menu.solution_design_detail') }}
                                    </router-link>
                                </li>
                                <!-- <li class="nav-item"
                                    v-if="currentInitiative?.id && (user?.is_admin || initiativeData?.functional_owner_id === user?.id || initiativeData?.technical_owner_id === user?.id)">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('solution-design.download') }"
                                        :to="{ name: 'solution-design.download', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-file-pdf-fill mx-2"></i>
                                        {{ $t('header.menu.solution_design_pdf') }}
                                    </router-link>
                                </li> -->
                                <li class="nav-item" v-if="user?.is_admin && currentInitiative?.id">
                                    <a class="nav-link text-dark" href="javascript:" @click="showEditOpportunityModal">
                                        <i class="bi bi-pencil-square"></i>
                                        {{ $t('header.menu.edit_initiative') }}
                                    </a>
                                </li>
                                <li class="nav-item" v-if="currentInitiative?.id && user?.is_admin">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('tasks') || (isActive('task.detail') && user?.is_admin) }"
                                        :to="{ name: 'tasks', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-card-checklist"></i>
                                        {{ $t('header.menu.all_ticket') }}
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="currentInitiative?.id">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('my-tickets') || (isActive('task.detail') && !user?.is_admin) }"
                                        :to="{ name: 'my-tickets', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-card-checklist"></i>
                                        {{ $t('header.menu.my_ticket') }}
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="currentInitiative?.id && user?.is_admin">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('projects') }"
                                        :to="{ name: 'projects', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-boxes"></i>
                                        {{ $t('header.menu.projects') }}
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="currentInitiative?.id && user?.is_admin">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('bulk-create-tickets') }"
                                        :to="{ name: 'bulk-create-tickets', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-ticket"></i>
                                        {{ $t('header.menu.bulk_create_tickets') }}
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="currentInitiative?.id && user?.is_admin">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('deployments') }"
                                        :to="{ name: 'deployments', params: { id: currentInitiative?.id } }">
                                        <i class="bi bi-card-list"></i>
                                        {{ $t('header.menu.deployments') }}
                                    </router-link>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <hr>
                <ul class="list-group border">
                    <li class="list-group-item bg-desino text-white border-desino fw-bold border-bottom">{{
                        $t('header.menu.initiative_links') }}</li>
                    <li v-if="initiativeData?.share_point_url" class="list-group-item border-0">
                        <a v-if="initiativeData?.share_point_url" :href="initiativeData?.share_point_url"
                            target="_blank" class="btn-link" style="text-decoration: none;">
                            <i class="bi bi-folder-symlink"></i>
                            {{ $t('header.menu.initiative.share_point_url') }}
                        </a>
                    </li>
                    <li class="list-group-item border-0" v-if="initiativeData?.initiative_environments"
                        v-for="environment in initiativeData?.initiative_environments">
                        <a :href="environment.url" target="_blank" style="text-decoration: none;" class="btn-link">
                            <i class="bi bi-hdd-rack-fill"></i>
                            {{ environment.name }}
                        </a>
                    </li>
                    <li v-if="initiativeData?.initiative_environments?.length === 0" class="list-group-item border-0">
                    </li>
                </ul>
            </div>
        </div>
        <hr>
        <div class="border rounded mx-2 shadow mb-3">
            <nav class="mt-1">
                <ul class="nav sidebar-menu flex-column">
                    <li class="nav-item">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('home') }" :to="{ name: 'home' }"
                            class="nav-link text-dark" @click="unselectHeaderInitiative">
                            <i class="bi bi-house mx-2"></i> {{ $t('header.menu.home') }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <a class="nav-link text-dark" href="javascript:" @click="showCreateClientModal"><i
                                class="bi bi-people mx-2"></i> {{
                                    $t('header.menu.create_client')
                                }} </a>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <a class="nav-link text-dark" href="javascript:" @click="showCreateInitiativeModal"><i
                                class="bi bi-gear mx-2"></i> {{
                                    $t('header.menu.create_initiative')
                                }}</a>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('opportunities') }"
                            :to="{ name: 'opportunities' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative"><i class="bi bi-list mx-2"></i>
                            {{
                                $t('header.menu.opportunities')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('time-booking.booking') }"
                            :to="{ name: 'time-booking.booking' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative">
                            <i class="bi bi-clock-history mx-2"></i>
                            {{
                                $t('header.menu.time_booking')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('planning') }"
                            :to="{ name: 'planning' }" class="nav-link text-dark" @click="unselectHeaderInitiative">
                            <i class="bi bi-calendar-week mx-2"></i>
                            {{
                                $t('header.menu.planning')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('time-mapping') }"
                            :to="{ name: 'time-mapping' }" class="nav-link text-dark" @click="unselectHeaderInitiative">
                            <i class="bi bi-clock-history mx-2"></i>
                            {{
                                $t('header.menu.time_mapping')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('all-ticket-without-initiative') }"
                            :to="{ name: 'all-ticket-without-initiative' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative">
                            <i class="bi bi-card-checklist mx-2"></i>
                            {{
                                $t('header.menu.all_ticket_without_initiative')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('developer-workload') }"
                            :to="{ name: 'developer-workload' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative">
                            <i class="bi bi-stack mx-2"></i>
                            {{
                                $t('header.menu.developer_workload')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('initiative-overview') }"
                            :to="{ name: 'initiative-overview' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative">
                            <i class="bi bi-hourglass mx-2"></i>
                            {{
                                $t('header.menu.initiatives_overview')
                            }}
                        </router-link>
                    </li>
                    <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('activity-logs') }"
                            :to="{ name: 'activity-logs' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative">
                            <i class="bi bi-activity mx-2"></i>
                            {{ $t('header.menu.activity_logs') }}
                        </router-link>
                    </li>
                    <!-- <li class="nav-item" v-if="user?.is_admin">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('profile') }"
                            :to="{ name: 'profile' }" class="nav-link text-dark" @click="unselectHeaderInitiative">
                            <i class="bi bi-person-lines-fill mx-2"></i>
                            {{
                                $t('header.menu.profile')
                            }}
                        </router-link>
                    </li> -->
                </ul>
            </nav>
        </div>
    </aside>
    <div class="sidebar-overlay" @click="collapseSidebar"></div>

    <div id="createClientModal" aria-hidden="true" aria-labelledby="createClientModalLabel" class="modal fade"
        tabindex="-1">
        <CreateClientModalComponent ref="createClientModalComponent" />
    </div>
    <div id="createInitiativeModal" aria-hidden="true" aria-labelledby="createInitiativeModalLabel" class="modal fade"
        tabindex="-1">
        <CreateInitiativeModalComponent ref="createInitiativeModalComponent" />
    </div>
    <div id="createTicketModal" aria-hidden="true" aria-labelledby="createTicketModalLabel" class="modal fade"
        tabindex="-1">
        <CreateTicketModalComponent ref="createTicketModalComponent" />
    </div>
    <!-- <div id="editOpportunityModal" aria-hidden="true" aria-labelledby="editOpportunityModalLabel" class="modal fade"
        tabindex="-1">
        <EditOpportunityModalComponent ref="editOpportunityModalComponent" @pageUpdated="getInitiativeData" />
    </div> -->
    <div id="editInitiativeModal" aria-hidden="true" aria-labelledby="editInitiativeModalLabel" class="modal fade"
        tabindex="-1">
        <EditOpportunityModalComponent ref="editInitiativeModalComponent" @pageUpdated="getInitiativeData" />
    </div>
</template>


<script>
import { mapActions, mapGetters } from "vuex";
import { RouterLink, useRoute } from "vue-router";
import CreateInitiativeModalComponent from "@/Page/Initiative/CreateInitiativeModalComponent.vue";
import CreateClientModalComponent from "@/Page/Client/CreateClientModalComponent.vue";
import CreateTicketModalComponent from "@/Page/SolutionDesign/Ticket/CreateTicketModalComponent.vue";
import HeaderInitiativeDropBoxComponent from "@/Page/Initiative/HeaderInitiativeDropBoxComponent.vue";
import EditOpportunityModalComponent from './../../Page/Opportunity/EditOpportunityModal.vue';
import { Modal } from "bootstrap";
import eventBus from "@/eventBus.js";
import OpportunityService from "../../services/OpportunityService";
import store from "../../store";

export default {
    name: 'SidebarComponent',
    components: { HeaderInitiativeDropBoxComponent, CreateClientModalComponent, CreateInitiativeModalComponent, CreateTicketModalComponent, EditOpportunityModalComponent },
    setup() {
        const route = useRoute();

        const isActive = (name) => {
            return route.name === name;
        };

        return {
            isActive,
        };
    },
    data() {
        return {
            sidebar_selected_initiative_id: null,
            initiativeData: "",
            logoPath: '/images/logo.png',
        }
    },
    computed: {
        ...mapGetters(['user', 'currentInitiative']),
    },
    methods: {
        ...mapActions(['setLoading']),
        showCreateClientModal() {
            this.$refs.createClientModalComponent.resetForm();
            const modalElement = document.getElementById('createClientModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        showCreateInitiativeModal() {
            this.$refs.createInitiativeModalComponent.resetForm();
            this.$refs.createInitiativeModalComponent.fetchClients();
            const modalElement = document.getElementById('createInitiativeModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        showCreateTicketModal() {
            this.$refs.createTicketModalComponent.resetForm();
            this.$refs.createTicketModalComponent.fetchData();
            const modalElement = document.getElementById('createTicketModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
        },
        async showEditOpportunityModal() {
            this.setLoading(true);
            const response = await OpportunityService.getOpportunity(this.sidebar_selected_initiative_id);
            this.$refs.editInitiativeModalComponent.getEditOpportunityFormData(response.content);
            const modalElement = document.getElementById('editInitiativeModal');
            if (modalElement) {
                const modal = new Modal(modalElement);
                modal.show();
            }
            this.setLoading(false);
        },
        showSolutionDesign() {
            if (this.sidebar_selected_initiative_id) {
                this.$router.push({ name: 'solution-design', params: { id: this.sidebar_selected_initiative_id } });
            }
        },
        unselectHeaderInitiative() {
            eventBus.$emit('unselectHeaderInitiativeId');
        },
        collapseSidebar() {
            document.body.classList.remove('sidebar-open');
            document.body.classList.add('sidebar-collapse');
            this.$emit('collapse');
        },
        sidebarSelectHeaderInitiativeId(id) {
            this.sidebar_selected_initiative_id = id;
            this.getInitiativeData();
        },
        async getInitiativeData() {
            if (this.sidebar_selected_initiative_id) {
                const response = await OpportunityService.getOpportunity(this.sidebar_selected_initiative_id);
                this.initiativeData = response.content;
                store.commit("setCurrentInitiative", response.content);
            }
        },
    },
    mounted() {
        eventBus.$on('sidebarSelectHeaderInitiativeId', this.sidebarSelectHeaderInitiativeId);
    }
};
</script>
