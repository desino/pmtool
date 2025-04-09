<template>
    <div class="app-content">
        <GlobalMessage v-if="showMessage" />
        <div class="row g-2">
            <div class="col-12 col-lg-6 border-end">
                <DeploymentCentreComponent />
            </div>
            <div class="col-12 col-lg-6">
                <MyActionsComponent />
            </div>
        </div>
    </div>
</template>

<script>
import AuthService from '../../services/AuthService.js';
import { mapState, mapGetters } from 'vuex';
import DeploymentCentreComponent from './DeploymentCenter/DeploymentCentreComponent.vue';
import MyActionsComponent from './MyActionsComponent.vue';
import GlobalMessage from '../../components/GlobalMessage.vue';
import store from '@/store';
import messageService from '../../services/messageService.js';

export default {
    name: 'HomeComponent',
    components: {
        DeploymentCentreComponent,
        MyActionsComponent,
        GlobalMessage
    },
    data() {
        return {
            showMessage: true
        }
    },
    methods: {
        showPermissionMessage() {
            const permissionMessage = store.getters.permissionMessage;
            if (permissionMessage) {
                messageService.setMessage(permissionMessage.message, permissionMessage.type);
                store.commit("setPermissionMessage", {});
            }
        }
    },
    computed: {
        ...mapGetters(['user', 'permissionMessage'])
    },
    mounted() {
        messageService.clearMessage();
        this.showPermissionMessage();
        const setHeaderData = {
            page_title: ''
        }
        store.commit("setHeaderData", setHeaderData);
    }
}
</script>
