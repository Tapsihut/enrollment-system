<script setup>

import { ref } from "vue";

import { useRouter } from "vue-router";

import { useAuthStore } from "../stores/auth";

const auth = useAuthStore();

const router = useRouter();

const form = ref({

    name: "",

    email: "",

    password: ""

});

const register = async () => {

    try {

        await auth.register(form.value);

        router.push("/dashboard");

    } catch (error) {

        console.log(error);

        console.log(error.response);

        console.log(error.response.data);

        alert(JSON.stringify(error.response.data));

    }

}

</script>

<template>

<div class="flex justify-center items-center h-screen bg-gray-100">

<div class="bg-white p-8 rounded shadow w-96">

<h2 class="text-3xl font-bold mb-5">

Register

</h2>

<input

v-model="form.name"

class="border p-3 w-full mb-3"

placeholder="Name"
/>

<input

v-model="form.email"

class="border p-3 w-full mb-3"

placeholder="Email"
/>

<input

type="password"

v-model="form.password"

class="border p-3 w-full mb-5"

placeholder="Password"
/>

<button

@click="register"

class="bg-blue-700 text-white w-full p-3 rounded"

>

Register

</button>

</div>

</div>

</template>