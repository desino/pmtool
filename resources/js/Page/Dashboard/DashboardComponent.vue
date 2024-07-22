<template>
You are logged in!
<button class="btn btn-primary btn-block" @click="logout">Logout</button>
</template>

<script>
import AuthService from './../../Services/AuthService.js';

export default {
    methods: {
        logout() {
            AuthService.logout();
        }
    }
}
</script>