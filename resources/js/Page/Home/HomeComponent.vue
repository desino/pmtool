<template>
    <DeploymentCentreComponent />
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

    },
    computed: {
        ...mapGetters(['user'])
    },
}
</script>
