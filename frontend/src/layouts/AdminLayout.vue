<template>

<div class="d-flex">


<AdminSidebar/>


<div class="flex-grow-1">


<Navbar/>


<div class="p-4">

<router-view/>

</div>


</div>


</div>

</template>


<script setup>

import AdminSidebar from '@/components/layout/AdminSidebar.vue'

import Navbar from '@/components/layout/Navbar.vue'


</script>