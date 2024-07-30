<template>
    <div v-if="isAuthenticated">
        <header-component></header-component>
        <!-- <sidebar-component></sidebar-component> -->

        <!-- Global Loading Screen -->
        <loading-screen-component></loading-screen-component>

        <main class="app-main"> 
            <div class="container mt-4">
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
// import SidebarComponent from "./SidebarComponent.vue";
import FooterComponent from "./FooterComponent.vue";
import {mapGetters} from "vuex";
import AuthService from "../../Services/AuthService.js";
import LoadingScreenComponent from "./LoadingScreenComponent.vue";
import HeaderComponent from "./HeaderComponent.vue";
import globalMixin from '@/globalMixin';
export default {
    name: 'MainComponent',
    mixins: [globalMixin],
    components: {LoadingScreenComponent, HeaderComponent,FooterComponent},
    beforeMount() {
        AuthService.refreshUser();
    },
    computed: {    
        ...mapGetters(['isAuthenticated']),
    },
};
</script>
