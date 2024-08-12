<template>
    <aside ref="sidebar" class="app-sidebar bg-white">
        <div class="sidebar-brand">
            <a class="brand-link" href="#">
                <img
                    alt="Brand Image"
                    class="brand-image"
                    src="https://www.desino.be/wp-content/uploads/2024/01/Logo_Finaloriginal-black.png"
                />
            </a>
        </div>
        <div class="p-2 border-bottom">
            <div class="my-3 d-flex ">
                <div class="image my-auto text-center">
                    <img alt="User Image"
                         class="user-image rounded-circle shadow w-75"
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
                <HeaderInitiativeDropBoxComponent/>
            </div>
            <div v-if="isActive('home')" >
                <div class="shadow">
                    <div class="p-2 rounded-bottom">
                        <nav class="mt-1">
                            <ul class="nav sidebar-menu flex-column">
                                <li class="nav-item">
                                    <router-link :class="{ 'active': isActive('home') }" :to="{ name: 'home' }"
                                                 class="nav-link text-dark" @click="unselectHeaderInitiative">
                                        <i class="bi bi-plus-circle mx-2"></i> {{ $t('Create Ticket') }}
                                    </router-link>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link text-dark" href="javascript:" @click="showCreateClientModal"><i
                                        class="bi bi-file-pdf-fill mx-2"></i> {{
                                            $t('Solution Design')
                                        }}</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <hr>
                <ul class="list-group border">
                    <li class="list-group-item fw-bold border-bottom">Initiative Links</li>
                    <a href="#" class="list-group-item border-0">Cloudways Acceptance</a>
                    <a href="#" class="list-group-item border-0">Cloudways Prod</a>
                    <a href="#" class="list-group-item border-0">IQVIA Acceptance</a>
                </ul>
            </div>

        </div>
        <hr>
        <div class="border rounded mx-2 shadow">
            <nav class="mt-1">
                <ul class="nav sidebar-menu flex-column">
                    <li class="nav-item">
                        <router-link :class="{ 'active': isActive('home') }" :to="{ name: 'home' }" class="nav-link text-dark"
                                     @click="unselectHeaderInitiative">
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
                        <router-link :class="{ active: isActive('opportunities') }" :to="{ name: 'opportunities' }"
                                     class="nav-link text-dark" @click="unselectHeaderInitiative"><i
                            class="bi bi-list mx-2"></i>
                            {{
                                $t('header.menu.opportunites')
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
        <CreateClientModalComponent ref="createClientModalComponent"/>
    </div>
    <div id="createInitiativeModal" aria-hidden="true" aria-labelledby="createInitiativeModalLabel" class="modal fade"
         tabindex="-1">
        <CreateInitiativeModalComponent ref="createInitiativeModalComponent"/>
    </div>
</template>


<script>
import {mapGetters} from "vuex";
import {useRoute} from "vue-router";
import CreateInitiativeModalComponent from "@/Page/Initiative/CreateInitiativeModalComponent.vue";
import CreateClientModalComponent from "@/Page/Client/CreateClientModalComponent.vue";
import HeaderInitiativeDropBoxComponent from "@/Page/Initiative/HeaderInitiativeDropBoxComponent.vue";
import {Modal} from "bootstrap";
import eventBus from "@/eventBus.js";

export default {
    name: 'SidebarComponent',
    components: {HeaderInitiativeDropBoxComponent, CreateClientModalComponent, CreateInitiativeModalComponent},
    setup() {
        const route = useRoute();

        const isActive = (name) => {
            return route.name === name;
        };

        return {
            isActive,
        };
    },
    computed: {
        ...mapGetters(['user'])
    },
    methods: {
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
        unselectHeaderInitiative() {
            eventBus.$emit('unselectHeaderInitiativeId');
        },
        collapseSidebar() {
            document.body.classList.remove('sidebar-open');
            document.body.classList.add('sidebar-collapse');
            this.$emit('collapse');
        }
    }
};
</script>
