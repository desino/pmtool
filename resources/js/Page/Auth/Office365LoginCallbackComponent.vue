<template>
    <div class="vh-100 d-flex align-items-center justify-content-center">
        <div class="login-container col-12 col-md-8 col-lg-4">
            <GlobalMessage v-if="showMessage" />            
        </div>
    </div>
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
            async handleLoginResponse() {                
                this.clearMessages();
                // try {
                //     const urlParams = new URLSearchParams(window.location.search);                    
                //     const code = urlParams.get('code');
                //     const credentials = { code };                    
                //     const response = await AuthService.handleProviderCallback(credentials);
                //     // console.log('response:: ',response);

                //     // this.$router.push({name: 'dashboard'});
                // } catch (error) {
                //     this.handleLoginError(error);
                // }

                // this.clearMessages();
                try {
                    console.log('dddd123');
                    const urlParams = new URLSearchParams(window.location.search);
                    const code = urlParams.get('code');
                    const state = urlParams.get('state');
                    const session_state = urlParams.get('session_state');
                    console.log('code:: ',code);
                    if (code) {
                        // const response = await axios.get('https://pmtool.local/api/office-365-login/graph/callback', {
                        //     params: { code },
                        //     headers: {
                        //         'X-Requested-With': 'XMLHttpRequest',
                        //         // 'X-Xsrf-Token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        //     }
                        // });
                        const response = await AuthService.handleProviderCallback({ code, state, session_state });
                        console.log('response: ',response);

                        // if (response.data.token) {
                        //     localStorage.setItem('token', response.data.token);
                        //     axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`;
                        // }

                        // if (response.data.message) {
                        //     messageService.setMessage(response.data.message, 'success');
                        // }

                        // // Optional: Redirect to dashboard or another route
                        // this.$router.push({ name: 'dashboard' });
                    } else {
                        throw new Error('Code parameter is missing in the URL');
                    }
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
        mounted() {
            this.handleLoginResponse();
        },
        beforeUnmount() {
            // Hide the message when the component is unmounted
            this.showMessage = false;
        }
    }
</script>