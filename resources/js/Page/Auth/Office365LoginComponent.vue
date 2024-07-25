<template>
    <!-- <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="login-container col-12 col-md-8 col-lg-4">
            <GlobalMessage v-if="showMessage" />
            <div class="card w-100">
                <div class="card-body"> -->
                    <!-- <h5 class="card-title text-center">{{ $t('auth.office365_login.title') }}</h5> -->
                    <button type="button" @click="loginWithOffice365" class="btn btn-primary w-100 mb-3">{{ $t('auth.office365_login.redirect_link_button') }}</button>
                <!-- </div>
            </div>
        </div>
    </div> -->
</template>

<script>
    import GlobalMessage from './../../Components/GlobalMessage.vue';
    import AuthService from './../../Services/AuthService.js';
    import globalMixin from '@/globalMixin';
    import messageService from './../../Services/messageService.js';
    
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