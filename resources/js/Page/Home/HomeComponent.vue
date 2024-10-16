<template>
    <div class="app-content mt-3">
        <GlobalMessage v-if="showMessage" />
        <div class="row w-100">
            <div class="col-md-4">
                <DeploymentCentreComponent />
            </div>
            <div class="col-md-4">
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
    }
}
</script>
