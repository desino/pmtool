<template>
    <DeploymentCentreComponent></DeploymentCentreComponent>
</template>

<script>
import AuthService from '../../services/AuthService.js';
import { mapState, mapGetters } from 'vuex';
import DeploymentCentreComponent from './DeploymentCenter/DeploymentCentreComponent.vue';

export default {
    name: 'HomeComponent',
    components: {
        DeploymentCentreComponent
    },
    methods: {
        logout() {
            AuthService.logout();
        }
    },
    computed: {
        ...mapGetters(['user'])
    },
}
</script>
