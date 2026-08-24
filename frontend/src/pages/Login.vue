<script setup>

import { ref } from "vue";

import { useRouter } from "vue-router";

import { useAuthStore } from "../stores/auth";

const auth = useAuthStore();

const router = useRouter();

const form = ref({

email:"",

password:""

});

const login = async()=>{

try{

await auth.login(form.value);

router.push("/dashboard");

}

catch(e){

alert("Invalid Credentials");

}

}

</script>

<template>

<div class="flex justify-center items-center h-screen bg-gray-100">

<div class="bg-white p-8 rounded shadow w-96">

<h2 class="text-3xl font-bold mb-5">

Login

</h2>

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

@click="login"

class="bg-blue-700 text-white w-full p-3 rounded"

>

Login

</button>

</div>

</div>

</template>