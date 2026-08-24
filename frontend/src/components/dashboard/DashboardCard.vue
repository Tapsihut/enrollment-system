<template>

<div class="card border-0 shadow-sm">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<h6 class="text-muted">

{{ title }}

</h6>

<h3>

{{ value }}

</h3>

</div>

<div>

<i
:class="icon"
class="fs-1 text-primary"
></i>

</div>

</div>

</div>

</div>

</template>

<script setup>

defineProps({

title:String,

value:String,

icon:String

})

</script>