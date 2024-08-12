<template>
    <nav class="app-header navbar navbar-expand">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" role="button" @click.prevent="toggleSidebar">
                        <i class="bi bi-list me-4"></i>
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <button class="btn btn-danger btn-sm" type="button" @click="logout"><i
                        class="bi bi-box-arrow-right"></i> {{ $t('logout_but_text') }}</button>
                </li>
            </ul>
        </div>
    </nav>
</template>

<script>
import AuthService from './../../services/AuthService.js';
import { mapGetters } from 'vuex';

export default {
    data() {
        return {
            sidebarBreakpoint: 992,
        }
    },
    methods: {
        logout() {
            AuthService.logout();
        },
        toggleSidebar() {
            const body = document.querySelector('body');
            body.classList.toggle('sidebar-collapse');
            body.classList.toggle('sidebar-open');
        },
        handleResize() {
            const body = document.querySelector('body');
            if (window.innerWidth > this.sidebarBreakpoint) {
                body.classList.add('sidebar-open');
                body.classList.remove('sidebar-collapse');
                body.classList.remove('sidebar-expand-lg');
            } else {
                body.classList.add('sidebar-expand-lg');
                body.classList.add('sidebar-collapse');
                body.classList.remove('sidebar-open');
            }
        },
    },
    computed: {
        ...mapGetters(['user'])
    },
    mounted() {
        this.handleResize();
        window.addEventListener('resize', this.handleResize);
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.handleResize);
    },
}
</script>
