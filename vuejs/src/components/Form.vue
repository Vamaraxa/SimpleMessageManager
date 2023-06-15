<template>
    <div>
        <b-form @reset="onReset" v-if="show">
            <label for="form-input-name">Content</label>
            <b-form-textarea id="form-input-content" class="mb-2 mr-sm-2 mb-sm-0" placeholder="Content"
                @input="formData.text = $event" :value="form.text">
            </b-form-textarea>
            <div class="mt-2">
                <b-button @click="onSubmit()" variant="primary">Submit</b-button>
                <b-button class="ml-2" @click="onReset()" variant="secondary">Reset</b-button>
            </div>
        </b-form>
    </div>
</template>

<script>
export default {
    name: 'FormTemplate',
    props: {
        form: {
            type: Object,
            default() {
                return {
                    uuid: '',
                    text: '',
                    dateOfCreated: ''
                }
            }
        },
    },
    data() {
        return {
            show: true,
            formData: this.form
        }
    },
    methods: {
        onReset(event) {
            event.preventDefault()
        },
        onSubmit() {
            this.$emit('changeFormData', this.form)
        }
    }
}
</script>