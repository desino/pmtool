<template>
    <!-- <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="login-container col-12 col-md-8 col-lg-4">
            <GlobalMessage v-if="showMessage" />
            <div class="card w-100">
                <div class="card-body"> -->
                    <!-- <h5 class="card-title text-center">{{ $t('office365_login_page_title') }}</h5> -->
                    <button type="button" @click="loginWithOffice365" class="btn btn-desino w-100 mb-3">{{ $t('office365_login_redirect_link_button_text') }}</button>
                <!-- </div>
            </div>
        </div>
    </div> -->
</template>

<script>
    import GlobalMessage from './../../components/GlobalMessage.vue';
    import AuthService from './../../services/AuthService.js';
    import globalMixin from '@/globalMixin';
    import messageService from './../../services/messageService.js';

    export default {
        name: 'Office365LoginComponent',
        mixins: [globalMixin],
        components: {
            GlobalMessage
        },
        data() {
            return {
                provider: 'graph',
                errors: {},
                showMessage: true
            };
        },
        methods: {
            async loginWithOffice365() {
                this.clearMessages();
                try {
                    const credentials = {
                        provider: this.provider,
                    };
                    await AuthService.loginWithOffice365(credentials);
                } catch (error) {
                    this.handleLoginError(error);
                }
            },
            handleLoginError(error) {
                if (error.type === 'validation') {
                    this.errors = error.errors;
                } else {
                    messageService.setMessage(error.message, 'danger');
                }
            },
            clearMessages() {
                this.errors = {};
                messageService.clearMessage();
            },
        },
        beforeUnmount() {
            // Hide the message when the component is unmounted
            this.showMessage = false;
        }
    }
</script>
