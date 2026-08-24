<script setup>

import { useRouter } from "vue-router";

import { useAuthStore } from "../stores/auth";

const auth = useAuthStore();

const router = useRouter();

const logout = async()=>{

await auth.logout();

router.push("/login");

}

</script>

<template>

<div class="min-h-screen bg-gray-100">

<nav class="bg-blue-700 text-white p-5 flex justify-between">

<h2>

Student Dashboard

</h2>

<button

@click="logout"

>

Logout

</button>

</nav>

<div class="p-10">

<h1 class="text-4xl font-bold">

Welcome

</h1>

<p class="mt-3">

You are successfully logged in.

</p>

</div>

</div>

</template>