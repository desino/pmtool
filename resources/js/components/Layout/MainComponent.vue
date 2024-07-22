<template>
    <div v-if="isAuthenticated" class="app-wrapper">
        <header-component></header-component>
        <!-- <sidebar-component></sidebar-component> -->

        <!-- Global Loading Screen -->
        <loading-screen-component></loading-screen-component>

        <main class="app-main">
            <router-view></router-view>
        </main>
        <footer-component></footer-component>
    </div>
    <div v-else class="login-page" style="min-height: 466px;">
        <div class="login-box">
            <router-view></router-view>
        </div>
    </div>
</template>


<script>
// import SidebarComponent from "./SidebarComponent.vue";
import FooterComponent from "./FooterComponent.vue";
import {mapGetters} from "vuex";
import AuthService from "../../services/AuthService.js";
import LoadingScreenComponent from "./LoadingScreenComponent.vue";
import HeaderComponent from "./HeaderComponent.vue";
export default {
name: 'MainComponent',
components: {LoadingScreenComponent, HeaderComponent,FooterComponent},
beforeMount() {
    AuthService.refreshUser();
},
computed: {
    ...mapGetters(['isAuthenticated']),
},
};
</script>
