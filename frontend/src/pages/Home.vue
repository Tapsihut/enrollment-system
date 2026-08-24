<template>

<div class="min-h-screen bg-gray-100">

    <nav class="bg-blue-800 text-white p-5 flex justify-between">

        <h1 class="text-2xl font-bold">

            Enrollment System

        </h1>

        <div>

            <RouterLink
                class="mr-5"
                to="/login">

                Login

            </RouterLink>

            <RouterLink
                to="/register">

                Register

            </RouterLink>

        </div>

    </nav>

    <div class="text-center mt-32">

        <h1 class="text-5xl font-bold">

            Online Enrollment

        </h1>

        <p class="mt-5">

            Enroll Anywhere Anytime

        </p>

        <RouterLink

            to="/register"

            class="bg-blue-700 px-8 py-3 rounded text-white inline-block mt-8"

        >

            Enroll Now

        </RouterLink>

    </div>

</div>

</template>