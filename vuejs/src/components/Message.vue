<template>
    <div>
        <h5>Uuid:</h5>
        <p>
            {{ message.uuid }}
        </p>
        <h5>Content:</h5>
        <p>
            {{ message.text }}
        </p>
        <h5>Date of created:</h5>
        <p>
            {{ message.dateOfCreated }}
        </p>
    </div>
</template>

<script>
export default {
    name: 'ApiMessage',
    props: {
        message: Object
    }
}
</script>