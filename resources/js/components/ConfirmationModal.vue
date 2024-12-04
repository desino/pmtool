<template>
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0">
                <div class="modal-header text-white bg-desino border-0 py-2 justify-content-center">
                    <h5 class="modal-title" id="confirmationModalLabel">{{ title }}</h5>
                </div>
                <div class="modal-body">
                    <div class="row w-100 g-1 text-center">
                        <span class="fw-bold align-middle ">
                            {{ message }}
                        </span>
                    </div>
                </div>
                <div class="modal-footer border-0 p-0 justify-content-center">
                    <div class="row w-100 g-1 align-items-center">
                        <div class="col-6">
                            <button type="button" class="btn btn-desino w-100 border-0" @click="confirm">
                                <i class="bi bi-check-lg"></i>
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-danger w-100 border-0" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Modal } from 'bootstrap';
export default {
    props: {
        title: {
            type: String,
            default: 'Confirm Action',
        },
        message: {
            type: String,
            default: 'Are you sure you want to proceed?',
        },
    },
    emits: ['confirm'],
    methods: {
        confirm() {
            this.$emit('confirm');
            this.hideModal();
        },
        showModal() {
            const modal = new Modal(document.getElementById('confirmationModal'));
            modal.show();
        },
        hideModal() {
            const modal = Modal.getInstance(document.getElementById('confirmationModal'));
            modal.hide();
        },
    },
};
</script>