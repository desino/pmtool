<template>
<!--    <div id="loading_screen" v-if="loading">-->
<!--        <img src="../../assets/images/loading.svg" alt="Loading" />-->
<!--    </div>-->
</template>

<script>
import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState(['loading'])
    }
};
</script>

<style scoped>
#loading_screen {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 100%;
    background: rgba(255, 255, 255, 0.79);
    /* z-index : 1051; */
    z-index: 9999999;
}

#loading_screen img {
    position: absolute;
    top: calc(50% - 100px);
    left: calc(50% - 100px);
}
</style>
