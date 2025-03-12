<template>
    <nav class="app-header navbar navbar-expand">
        <div class="container-fluid">
            <div class="row g-0 w-100 align-items-center justify-content-between">
                <div class="col-10 col-md-11">
                    <h3 class="m-0">
                        <a class="me-2" role="button" @click.prevent="toggleSidebar">
                            <i class="bi bi-list"></i>
                        </a>
                        <span class="me-2">
                            {{ headerData.page_title }}
                        </span>
                        <router-link class="me-2" v-if="headerData?.is_solution_design_detail_path"
                                     :to="{ name: 'solution-design.detail', params: { id: headerData.initiative_id } }">
                            <i class="bi bi-link-45deg"></i>
                        </router-link>
                        <router-link class="me-2" v-if="headerData?.is_solution_design_download"
                                     :to="{ name: 'solution-design.download', params: { id: headerData.initiative_id } }">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </router-link>
                    </h3>
                </div>
                <div class="col-auto">
                    <button class="btn btn-danger text-white" type="button" @click="logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
import AuthService from './../../services/AuthService.js';
import {mapGetters} from 'vuex';

export default {
    data() {
        return {
            sidebarBreakpoint: 992,
        }
    },
    computed: {
        ...mapGetters(['user', 'headerData']),
    },
    methods: {
        logout() {
            AuthService.logout();
        },
        toggleSidebar() {
            console.log("toggling sidebar:: inside header component");
    
            const body = document.querySelector('body');
            body.classList.toggle('sidebar-collapse');
            body.classList.toggle('sidebar-open');
        },
        handleResize() {
            const body = document.querySelector('body');
             console.log("toggling sidebar:: inside handle sizer");
             console.log(window.innerWidth > this.sidebarBreakpoint);
   
            if (window.innerWidth > this.sidebarBreakpoint) {
                body.classList.add('sidebar-open');
                body.classList.remove('sidebar-collapse');
                body.classList.remove('sidebar-expand-lg');
            } else {
                body.classList.add('sidebar-expand-lg');
                body.classList.add('sidebar-collapse');
                body.classList.remove('sidebar-open');
            }
        },
    },
    mounted() {
        console.log("mounted");
        this.handleResize();
        window.addEventListener('resize', this.handleResize);
    },
    beforeDestroy() {
          console.log("destroyed");
        window.removeEventListener('resize', this.handleResize);
    },
}
</script>
