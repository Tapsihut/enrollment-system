<template>

    <router-view />

</template>


<script setup>

</script>


<style>

body {

    background:#f8f9fa;

    font-family:
    "Segoe UI",
    Arial,
    sans-serif;

}


.sidebar {

    width:260px;

    min-height:100vh;

    background:#0d6efd;

    color:white;

}


.sidebar a {

    display:block;

    padding:14px 20px;

    color:white;

    text-decoration:none;

}


.sidebar a:hover {

    background:rgba(255,255,255,.15);

}


.router-link-active {

    background:rgba(255,255,255,.2);

}


</style>