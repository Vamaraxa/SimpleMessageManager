<template>
    <div>
        <BootstrapModal id="modal" :title="modalTitle" :show="showModal">
            <component :is="passedComponent" v-bind="passedProps" v-on="passEvents"></component>
        </BootstrapModal>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-inline-block">
                                <h3>Messages</h3>
                            </div>
                            <div class="float-right">
                                <b-button variant="success" v-b-modal.modal @click=popupForm()>
                                    Add
                                </b-button>
                            </div>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th @click="sortList('uuid')" role="button">
                                            Uuid
                                            <b-icon-arrow-down v-if="sortBy == 'uuid' && orderBy == 'desc'"></b-icon-arrow-down>
                                            <b-icon-arrow-up v-if="sortBy == 'uuid' && orderBy == 'asc'"></b-icon-arrow-up>
                                        </th>
                                        <th @click="sortList('dateOfCreated')" role="button">
                                            DateOfCreated
                                            <b-icon-arrow-down
                                                v-if="sortBy == 'dateOfCreated' && orderBy == 'desc'"></b-icon-arrow-down>
                                            <b-icon-arrow-up
                                                v-if="sortBy == 'dateOfCreated' && orderBy == 'asc'"></b-icon-arrow-up>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="row in rows" :key="row.id">
                                        <td>
                                            {{ row.uuid }}
                                        </td>
                                        <td>
                                            {{ row.dateOfCreated }}
                                        </td>
                                        <td>
                                            <b-button variant="primary" class="mr-3 btn-sm" v-b-modal.modal
                                                @click=popupDetails(row.uuid)>
                                                See details
                                            </b-button>
                                            <b-button class="mr-3 btn-sm" v-b-modal.modal @click=popupForm(row.uuid)>
                                                Edit
                                            </b-button>
                                            <b-button variant="danger" class="btn-sm" @click=deleteMessage(row.uuid)>
                                                Delete
                                            </b-button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios'

import BootstrapModal from './Modal.vue';

import FormTemplate from './Form.vue';

import ApiMessage from './Message.vue';

axios.defaults.baseURL = 'http://localhost:8010/';

export default {
    mounted() {
        this.loadList();
    },
    data() {
        return {
            rows: [],
            messageObj: {},
            modalTitle: 'Add',
            msgText: '',
            passedComponent: '',
            passedProps: {},
            passEvents: {},
            showModal: false,
            sortBy: 'uuid',
            orderBy: 'desc'
        };
    },
    methods: {
        popupDetails(uuid) {
            axios
                .get(`${uuid}`)
                .then(response => {
                    this.passedComponent = ApiMessage;
                    this.passedProps = {
                        message: response.data
                    };
                    this.modalTitle = `Details: ${response.data.uuid}`
                })
                .catch(error => {
                    alert(error.response.data.errors);
                })
        },
        popupForm(uuid) {
            if (uuid) {
                axios
                    .get(`${uuid}`)
                    .then(response => {
                        this.passedComponent = FormTemplate;
                        this.passedProps = {
                            form: response.data,
                        };
                        this.passEvents = {
                            changeFormData: this.editMessage
                        }
                        this.modalTitle = `Edit: ${response.data.uuid}`
                    })
                    .catch(error => {
                        alert(error.response.data.errors);
                    })
            } else {
                this.modalTitle = 'Add'
                this.passedComponent = FormTemplate;
                this.passedProps = {
                    form: {},
                };
                this.passEvents = {
                    changeFormData: this.createMessage
                }
            }
        },
        deleteMessage(uuid) {
            if (uuid) {
                axios.delete(`${uuid}`)
                    .then(response => {
                        console.log(response);
                        alert(`Addedd successfull for: ${uuid}`);
                        this.loadList();
                    }).catch(error => {
                        alert(error.response.data.errors)
                    })
            }
        },
        createMessage($data) {
            let formData = new FormData();
            formData.append('text', $data.text)
            axios.post(
                ``,
                formData
            ).then(response => {
                this.loadList();
                this.closeModal();
                alert(`Addedd successfull for: ${response.data}`);
            }).catch(error => {
                alert(error.response.data.errors);
            })
        },
        editMessage($data) {
            const params = new URLSearchParams();
            params.append('text', $data.text);
            axios
                .patch(
                    `${$data.uuid}`,
                    params
                ).then(response => {
                    alert(`Edit successfull for: ${response.data.uuid}`);
                    this.closeModal();
                }).catch(error => {
                    alert(error.response.data.errors);
                })
        },
        loadList() {
            const params = new URLSearchParams();
            if (this.sortBy && this.orderBy) {
                params.append('sortBy', this.sortBy);
                params.append('orderBy', this.orderBy);
            }

            axios
                .get('', { params })
                .then(response => {
                    this.rows = response.data;
                })
        },
        closeModal() {
            this.$root.$emit('bv::hide::modal', 'modal')
        },
        sortList(name) {
            this.sortBy = name;
            this.orderBy = this.orderBy == 'asc' ? 'desc' : 'asc';
            this.loadList(this.sortBy, this.orderBy)
        }
    },
    components: {
        FormTemplate,
        BootstrapModal,
        ApiMessage
    }
}
</script>