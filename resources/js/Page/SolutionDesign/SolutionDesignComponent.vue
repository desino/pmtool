<template>    
    <h1 class="primary">{{ $t('solution_design.page_title') }} - {{ initiativeData.name }}</h1>
    <h5><span class="badge rounded-pill bg-primary text-light">Development Ballpark: {{ initiativeData.ballpark_development_hours }} hours</span></h5>
    <GlobalMessage v-if="showMessage" />    
    <div class="row mt-4">
        <div class="col-md-4">
            <div v-if="sectionsWithFunctionalities.length > 0" v-for="sectionsWithFunctionality in sectionsWithFunctionalities" :key="sectionsWithFunctionality.id" class="functionality-section">
                <h5>
                    {{ sectionsWithFunctionality.name }}                     
                    <a href="javascript:" class="fw-bold fs-4 custom-hover mr-5"><i class="bi bi-plus-circle"></i></a>                    
                    <a href="javascript:" class=" fs-4 custom-hover ml-5"><i class="bi bi-three-dots"></i></a>                    
                </h5>
                <ul class="functionality-list list-group">
                    <li class="list-group-item functionality-selected">
                        <input type="text" class="form-control">
                    </li>
                    <li class="list-group-item functionality-selected">
                        <span>Category Master data</span>
                        <span class="badge bg-secondary">2</span>
                    </li>
                    <!-- Add more list items here as needed -->
                </ul>
            </div>            
        </div>
        <div class="col-md-8">
            <div class="mb-3">
                <label for="functionalityName" class="form-label">Functionality name</label>
                <input type="text" class="form-control" id="functionalityName" placeholder="Enter value">
            </div>
            <div class="mb-3">
                <label for="functionalityDescription" class="form-label">Functionality description</label>
                <!-- <textarea class="form-control" id="functionalityDescription" :init="editorSettings" rows="6"></textarea> -->
                <!-- <TinyMceEditor v-model="form1Content" /> -->
                <!-- <Editor :init="editorSettings" v-model="content"></Editor> -->                
                <TinyMceEditor/>
            </div>
            <div class="mb-3">
                <button class="btn btn-primary w-100">Save</button>
            </div>
        </div>
    </div>    
    <AddNewSectionComponent :initiativeData="initiativeData" @sectionAdded="handleSectionAdded"/>            

</template>

<script>
    import globalMixin from '@/globalMixin';
    import GlobalMessage from './../../components/GlobalMessage.vue';
    import messageService from './../../services/messageService';
    import SolutionDesignService from './../../services/SolutionDesignService'; 
    import AddNewSectionComponent from './Section/AddNewSectionComponent.vue'   
    import TinyMceEditor from './../../components/TinyMceEditor.vue';           
    export default {
        name: 'SolutionDesignComponent',
        mixins: [globalMixin],
        components: {
            GlobalMessage, 
            AddNewSectionComponent, 
            TinyMceEditor                     
        },        
        data() {                        
            return {                                 
                initiativeId: this.$route.params.id,
                initiativeData:{},
                sectionsWithFunctionalities: [],
                errors: {},
                showMessage: true
            }
        },
        mounted() {
            this.getSectionsWithFunctionalities();
            this.getInitiativeData();
            this.clearMessages();
        },
        methods: {
            async getInitiativeData() {
                try {
                    const credentials = {
                        initiative_id: this.initiativeId,
                    };
                    const response = await SolutionDesignService.getInitiativeData(credentials);
                    this.initiativeData = response.content;                                        
                } catch (error) {
                    this.handleError(error);
                }
            },
            async getSectionsWithFunctionalities() {
                try {  
                    const credentials = {
                        initiative_id: this.initiativeId,
                    };                  
                    const response = await SolutionDesignService.getSectionsWithFunctionalities(credentials);
                    this.sectionsWithFunctionalities = response.content;                    
                } catch (error) {
                    this.handleError(error);
                }
            },

            handleSectionAdded(newSection){
                this.sectionsWithFunctionalities.push(newSection);
            },


            handleError(error) {
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
            this.showMessage = false;
        }
    }
</script>