<template>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-house-door-fill"></i> desino.be
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <!-- <a class="nav-link active" aria-current="page" href="#"><i class="bi bi-house"></i> Home</a> -->
                        <router-link class="nav-link" :class="{ active: isActive('dashboard') }"
                            :to="{ name: 'dashboard' }"><i class="bi bi-list"></i> Home</router-link>
                    </li>
                    <li class="nav-item">
                        <!-- <a class="nav-link" href="javascript:" data-bs-toggle="modal" data-bs-target="#creatteClientModal"><i class="bi bi-people"></i> Create Client</a> -->
                        <a class="nav-link" href="javascript:" @click="showCreateClientModal"><i
                                class="bi bi-people"></i> Create Client</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="javascript:" @click="showCreateInitiativeModal"><i
                                class="bi bi-gear"></i> Create Initiative</a>
                    </li>
                    <li class="nav-item">
                        <router-link class="nav-link" :class="{ active: isActive('opportunities') }"
                            :to="{ name: 'opportunities' }"><i class="bi bi-list"></i> Opportunites</router-link>
                    </li>
                </ul>
                <div class="me-2">
                    <select class="form-select form-select-sm" aria-label="Default select example">
                        <option selected>Open this select menu</option>
                        <option value="1">One</option>
                        <option value="2">Two</option>
                        <option value="3">Three</option>
                    </select>
                </div>
                <button class="btn btn-warning btn-sm me-2" type="button"><i class="bi bi-plus-circle"></i> Create new
                    task</button>
                <button class="btn btn-danger btn-sm" type="button" @click="logout"><i
                        class="bi bi-box-arrow-right"></i> {{ $t('logout_but_text') }}</button>
            </div>
        </div>
    </nav>

    <div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel"
        aria-hidden="true">
        <CreateClientModalComponent ref="createClientModalComponent" />
    </div>
    <div class="modal fade" id="createInitiativeModal" tabindex="-1" aria-labelledby="createInitiativeModalLabel"
        aria-hidden="true">
        <CreateInitiativeModalComponent ref="createInitiativeModalComponent" />
    </div>
</template>

<script>
import CreateClientModalComponent from '../../Page/Client/CreateClientModalComponent.vue';
import AuthService from './../../services/AuthService.js';
import { mapState, mapGetters } from 'vuex';
import { Modal } from 'bootstrap';
import CreateInitiativeModalComponent from '../../Page/Initiative/CreateInitiativeModalComponent.vue';
import HeaderService from '../../services/HeaderService.js';
import { useRoute } from 'vue-router';

export default {
    components: {
        CreateClientModalComponent,
        CreateInitiativeModalComponent
    },
    data() {
        return {
            initiatives: []
        }
    },
    setup() {
        const route = useRoute();

        const isActive = (name) => {
            return route.name === name;
        };

        return {
            isActive,
        };
    },
    methods: {
        logout() {
            AuthService.logout();
        },
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
        // async getInitiatives() {
        //     const response = await HeaderService.getInitiatives();
        //     // this.initiatives = response.data.content;
        //     // console.log(this.initiatives);
        // },
    },
    computed: {
        ...mapGetters(['user'])
    },
    mounted() {
        // this.getInitiatives();
    }
}
</script>
