<template>
    <aside ref="sidebar" class="app-sidebar bg-white border">
        <div class="sidebar-brand">
            <router-link class="brand-link" :to="{ name: 'home' }" @click="unselectHeaderInitiative">
                <img alt="Brand Image" class="brand-image"
                    src="https://www.desino.be/wp-content/uploads/2024/01/Logo_Finaloriginal-black.png" />
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
        <div class="p-2 border rounded m-2 shadow">
            <div class="form-group pb-0">
                <HeaderInitiativeDropBoxComponent />
            </div>
            <div
                v-if="isActive('solution-design') || isActive('tasks') || isActive('task.detail') || isActive('projects') || isActive('solution-design.detail') || isActive('solution-design.download')">
                <div class="shadow">
                    <div class="p-2 rounded-bottom">
                        <nav class="mt-1">
                            <ul class="nav sidebar-menu flex-column">
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="javascript:" @click="showCreateTicketModal">
                                        <i class="bi bi-plus-circle mx-2"></i>
                                        {{ $t('header.menu.create_ticket') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('solution-design') || isActive('solution-design.detail') }"
                                        href="javascript:" @click="showSolutionDesign"><i
                                            class="bi bi-file-pdf-fill mx-2"></i>
                                        {{ $t('header.menu.solution_design') }}
                                    </a>
                                </li>
                                <li class="nav-item" v-if="currentInitiative.id">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('solution-design.download') }"
                                        :to="{ name: 'tasks', params: { id: currentInitiative.id } }">
                                        <i class="bi bi-file-pdf-fill mx-2"></i>
                                        Solution PDF
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="javascript:" @click="showEditOpportunityModal">
                                        <i class="bi bi-pencil-square mx-2"></i>
                                        {{ $t('header.menu.edit_initiative') }}
                                    </a>
                                </li>
                                <li class="nav-item" v-if="currentInitiative.id">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('tasks') || isActive('task.detail') }"
                                        :to="{ name: 'tasks', params: { id: currentInitiative.id } }">
                                        <i class="bi bi-card-checklist mx-2"></i>
                                        {{ $t('header.menu.all_ticket') }}
                                    </router-link>
                                </li>
                                <li class="nav-item" v-if="currentInitiative.id">
                                    <router-link class="nav-link text-dark"
                                        :class="{ 'bg-opacity-25 bg-primary': isActive('projects') }"
                                        :to="{ name: 'projects', params: { id: currentInitiative.id } }">
                                        <i class="bi bi-boxes mx-2"></i>
                                        {{ $t('header.menu.projects') }}
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
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="javascript:" @click="showCreateClientModal"><i
                                class="bi bi-people mx-2"></i> {{
                                    $t('header.menu.create_client')
                                }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="javascript:" @click="showCreateInitiativeModal"><i
                                class="bi bi-gear mx-2"></i> {{
                                    $t('header.menu.create_initiative')
                                }}</a>
                    </li>
                    <li class="nav-item">
                        <router-link :class="{ 'bg-opacity-25 bg-primary': isActive('opportunities') }"
                            :to="{ name: 'opportunities' }" class="nav-link text-dark"
                            @click="unselectHeaderInitiative"><i class="bi bi-list mx-2"></i>
                            {{
                                $t('header.menu.opportunities')
                            }}
                        </router-link>
                    </li>
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
            }
        },
    },
    mounted() {
        eventBus.$on('sidebarSelectHeaderInitiativeId', this.sidebarSelectHeaderInitiativeId);
    }
};
</script>
