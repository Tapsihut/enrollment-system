<template>

<span
class="badge"
:class="statusClass">

{{ status }}

</span>

</template>


<script setup>

import {computed} from "vue"


const props = defineProps({

status:String

})


const statusClass = computed(()=>{

    switch(props.status){

        case "Approved":
            return "bg-success"

        case "Pending":
            return "bg-warning text-dark"

        case "Rejected":
            return "bg-danger"

        case "Paid":
            return "bg-success"

        case "Unpaid":
            return "bg-secondary"

        default:
            return "bg-primary"

    }

})

</script>