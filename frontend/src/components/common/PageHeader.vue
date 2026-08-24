<template>

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>

        <h2 class="fw-bold mb-1">
            {{ title }}
        </h2>

        <small class="text-muted">
            {{ subtitle }}
        </small>

    </div>

</div>

</template>


<script setup>

defineProps({

    title:{
        type:String,
        default:"Page Title"
    },

    subtitle:{
        type:String,
        default:""
    }

})

</script>