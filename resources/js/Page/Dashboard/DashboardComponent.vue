<template>
    <div class="d-flex vh-100">
        <div class="m-auto text-center">
            <h1 class="mb-4"><span class="text-primary">{{ user?.name }}</span> is logged in! </h1>            
            <button class="btn btn-danger btn-block" @click="logout">{{ $t('auth.logout') }}</button>
        </div>
    </div>
    You are logged in!
</template>

<script>
import AuthService from './../../Services/AuthService.js';
import { mapState, mapGetters } from 'vuex';

export default {
    methods: {
        logout() {
            AuthService.logout();
        }
    },
    computed: {
        ...mapState(['user']), 
        ...mapGetters(['isAuthenticated'])       
    },
}
</script>