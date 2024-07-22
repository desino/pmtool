<template>    
    <div class="card card-outline card-desino">
        <div class="card-header text-center">
            <a class="h1" href="javascript;"><b>{{ appVariables.APP_NAME }}</b></a>
        </div>
        <div class="card-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <div v-if="systemMsg.message"
                 :class="['alert', {'alert-danger': systemMsg.type === 'danger', 'alert-success': systemMsg.type === 'success'}]"
                 class="alert alert-dismissible">
                <button aria-hidden="true" class="close" data-dismiss="alert" type="button">×</button>
                {{ systemMsg.message }}
            </div>
            <form @submit.prevent="loginUser">
                <div class="input-group mb-3">
                    <input v-model="email" :class="{'is-invalid': errors.email}" class="form-control" placeholder="Email"
                           type="email">
                    <div class="input-group-text">
                        <span class="bi bi-envelope-at-fill"></span>
                    </div>
                    <div v-if="errors.email" class="invalid-feedback">
                        <span v-for="(error, index) in errors.email" :key="index">{{ error }}</span>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input v-model="password" :class="{'is-invalid': errors.password}" class="form-control"
                           placeholder="Password" type="password">
                    <div class="input-group-text">
                        <span class="bi bi-person-fill-lock"></span>
                    </div>
                    <div v-if="errors.password" class="invalid-feedback">
                        <span v-for="(error, index) in errors.password" :key="index">{{ error }}</span>
                    </div>

                </div>
                <div class="row">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input id="remember" type="checkbox">
                            <label for="remember">
                                Remember Me
                            </label>
                        </div>
                    </div>

                    <div class="col-12 mb-2">
                        <button class="btn btn-primary btn-block" type="submit">Sign In</button>
                    </div>

                </div>
            </form>

            <p class="mb-1">
                 <a class="text-decoration-none" href="javascript:void(0)">I forgot my password</a>
            </p>
            <p class="mb-0">
                <a class="text-center" href="#">Register a new membership</a>
            </p>
        </div>

    </div>
</template>

<script>

import AuthService from "../../Services/AuthService.js";
import globalMixin from '@/globalMixin';

export default {
    name: 'LoginComponent',
    mixins: [globalMixin],
    data() {
        return {
            email: '',
            password: '',
            errors: {},
            systemMsg: {
                message: '',
                type: ''
            }
        };
    },    
    methods: {
        async loginUser() {
            this.clearMessages();
            try {
                const credentials = {
                    email: this.email,
                    password: this.password
                };
                await AuthService.loginUser(credentials);
                this.$router.push({name: 'dashboard'});
            } catch (error) {
                this.handleLoginError(error);
            }
        },
        handleLoginError(error) {
            this.systemMsg.type = 'danger';
            if (error.type === 'validation') {
                this.errors = error.errors;
            } else {
                this.systemMsg.message = error.message;
            }
        },
        clearMessages() {
            this.errors = {};
            this.systemMsg.message = '';
            this.systemMsg.type = '';
        },
    },
    mounted() {        
        // console.log('asdasd123:: ', this.appVariables);
        // console.log('import.meta.env.VITE_APP_NAME:: ', import.meta.env.VITE_APP_NAME);
    }
}
</script>
 