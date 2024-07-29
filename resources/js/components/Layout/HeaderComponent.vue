<template>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-house-door-fill"></i> desino.be
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#"><i class="bi bi-house"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <!-- <a class="nav-link" href="javascript:" data-bs-toggle="modal" data-bs-target="#creatteClientModal"><i class="bi bi-people"></i> Create Client</a> -->
                        <a class="nav-link" href="javascript:" @click="showCreateModal"><i class="bi bi-people"></i> Create Client</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-gear"></i> Create Initiative</a>
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
                <button class="btn btn-warning btn-sm me-2" type="button"><i class="bi bi-plus-circle"></i> Create new task</button>
                <button class="btn btn-danger btn-sm" type="button" @click="logout"><i class="bi bi-box-arrow-right"></i> {{ $t('auth.logout') }}</button>
            </div>
        </div>
    </nav>
     
    <div class="modal fade" id="createClientModal" tabindex="-1" aria-labelledby="createClientModalLabel" aria-hidden="true">    
        <CreateModalComponent/>
    </div>    
</template>

<script>    
    import CreateModalComponent from '../../Page/Client/CreateModalComponent.vue';
    import AuthService from './../../Services/AuthService.js';
    import { mapState, mapGetters } from 'vuex';  
    import { Modal } from 'bootstrap';

    export default {        
        components: {
            CreateModalComponent
        },
        methods: {
            logout() {
                AuthService.logout();
            },
            showCreateModal() {                
                const modalElement = document.getElementById('createClientModal');
                if (modalElement) {
                    const modal = new Modal(modalElement);
                    modal.show();
                }
            },
        },
        computed: {
            ...mapGetters(['user'])
        },
    }
</script>