<template>
    <div v-if="isAuthenticated" class="app-wrapper" :class="isServerError ? 'app-main-error' : ''">
        <header-component></header-component>
         <sidebar-component></sidebar-component>


        <div v-if="!isServerError">
            <!-- Global Loading Screen -->
            <loading-screen-component></loading-screen-component>
        </div>


        <main class="app-main">
            <div v-if="isServerError">
                <not-found-component>
                </not-found-component>
            </div>
            <div v-show="!isServerError">
                <router-view></router-view>
            </div>
        </main>

        <footer-component></footer-component>
    </div>
    <div v-else class="login-page" style="min-height: 466px;">
        <div class="container mt-4">
            <router-view></router-view>
        </div>
    </div>
</template>


<script>
import FooterComponent from "./FooterComponent.vue";
import { mapGetters } from "vuex";
import AuthService from "../../services/AuthService.js";
import LoadingScreenComponent from "./LoadingScreenComponent.vue";
import HeaderComponent from "./HeaderComponent.vue";
import globalMixin from '@/globalMixin';
import SidebarComponent from "./SidebarComponent.vue";
import NotFoundComponent from "@/components/Errors/NotFoundComponent.vue";
export default {
    name: 'MainComponent',
    mixins: [globalMixin],
    components: {NotFoundComponent, SidebarComponent, LoadingScreenComponent, HeaderComponent, FooterComponent },
    beforeMount() {
        AuthService.refreshUser();
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    },
    computed: {
        ...mapGetters(['isAuthenticated','isServerError']),
    },
};
</script>
