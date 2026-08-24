<script setup>

import { reactive, ref } from "vue"
import { useRouter } from "vue-router"
import auth from "@/services/auth"

import SFXCLogoOnly from "@/assets/images/logo/sfxc-logo-only.png"
import SFXCTextOnly from "@/assets/images/logo/sfxc-text-only.png"


const router = useRouter()


const isLoading = ref(false)


const alert = ref({

    show: false,

    type: "",

    message: ""

})


const form = reactive({

    name: "",

    email: "",

    password: "",

    password_confirmation: ""

})


let alertTimer = null



/*
|--------------------------------------------------------------------------
| Alert
|--------------------------------------------------------------------------
*/

function showAlert(type, message) {

    alert.value = {

        show: true,

        type,

        message

    }


    if (alertTimer) {

        clearTimeout(alertTimer)

    }


    alertTimer = setTimeout(() => {

        alert.value.show = false

    }, 4000)

}



/*
|--------------------------------------------------------------------------
| Registration
|--------------------------------------------------------------------------
*/

async function submit() {

    if (isLoading.value) {

        return

    }


    isLoading.value = true


    try {

        await auth.register(form)


        showAlert(

            "success",

            "Registration successful! Redirecting..."

        )


        setTimeout(() => {

            router.push("/login")

        }, 1200)


    }

    catch (error) {

        console.error(
            "Registration error:",
            error
        )


        showAlert(

            "error",

            error.response?.data?.message
            ||
            "Registration failed."

        )

    }

    finally {

        isLoading.value = false

    }

}

</script>


<template>

<div class="register-page">


    <div class="register-card">


        <!-- =====================================================
             LOGO
        ====================================================== -->

        <div class="logo-area">


            <img
                :src="SFXCLogoOnly"
                class="school-logo"
                alt="SFXC Logo"
            >


            <img
                :src="SFXCTextOnly"
                class="school-text"
                alt="St. Francis Xavier College"
            >


        </div>



        <!-- =====================================================
             TITLE
        ====================================================== -->

        <h2 class="text-center">

            Student Registration

        </h2>


        <p class="subtitle text-center">

            Create your student account to access
            the enrollment portal.

        </p>



        <!-- =====================================================
             ALERT
        ====================================================== -->

        <div
            v-if="alert.show"
            class="alert-box"
            :class="alert.type"
        >

            {{ alert.message }}

        </div>



        <!-- =====================================================
             FORM
        ====================================================== -->

        <form
            @submit.prevent="submit"
            class="register-form"
        >


            <!-- NAME -->

            <div class="form-group">


                <label>

                    Name

                </label>


                <input
                    type="text"
                    class="form-control"
                    placeholder="Enter your full name"
                    v-model="form.name"
                    autocomplete="name"
                    required
                >


            </div>



            <!-- EMAIL -->

            <div class="form-group">


                <label>

                    Email Address

                </label>


                <input
                    type="email"
                    class="form-control"
                    placeholder="Enter your email"
                    v-model="form.email"
                    autocomplete="email"
                    required
                >


            </div>



            <!-- PASSWORD -->

            <div class="form-group">


                <label>

                    Password

                </label>


                <input
                    type="password"
                    class="form-control"
                    placeholder="Enter password"
                    v-model="form.password"
                    autocomplete="new-password"
                    required
                >


            </div>



            <!-- CONFIRM PASSWORD -->

            <div class="form-group confirm-group">


                <label>

                    Confirm Password

                </label>


                <input
                    type="password"
                    class="form-control"
                    placeholder="Confirm password"
                    v-model="form.password_confirmation"
                    autocomplete="new-password"
                    required
                >


            </div>



            <!-- REGISTER BUTTON -->

            <button
                type="submit"
                class="btn-register"
                :disabled="isLoading"
            >


                <span v-if="isLoading">

                    <span class="spinner"></span>

                    Registering...

                </span>


                <span v-else>

                    Create Account

                </span>


            </button>


        </form>



        <!-- =====================================================
             LOGIN LINK
        ====================================================== -->

        <div class="login-link">


            Already have an account?


            <router-link to="/login">

                Sign In

            </router-link>


        </div>


    </div>

</div>

</template>


<style scoped>


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

.register-page {

    min-height: 100vh;

    min-height: 100dvh;

    background: #f3f8f5;

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 30px;

    box-sizing: border-box;

}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.register-card {

    width: 450px;

    max-width: 100%;

    background: white;

    padding: 50px;

    border-radius: 25px;

    box-shadow:
        0 20px 60px rgba(0,0,0,.15);

    box-sizing: border-box;

}


/*
|--------------------------------------------------------------------------
| LOGO
|--------------------------------------------------------------------------
*/

.logo-area {

    display: flex;

    justify-content: center;

    align-items: center;

    gap: 15px;

    margin-bottom: 30px;

}


.school-logo {

    width: 70px;

    height: auto;

    object-fit: contain;

}


.school-text {

    width: 180px;

    max-width: 65%;

    height: auto;

    object-fit: contain;

}


/*
|--------------------------------------------------------------------------
| TITLE
|--------------------------------------------------------------------------
*/

h2 {

    font-size: 32px;

    font-weight: 800;

    color: #064E2A;

    margin-bottom: 10px;

}


.subtitle {

    color: #6b7280;

    line-height: 1.6;

    margin-bottom: 30px;

}


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

.form-group {

    margin-bottom: 20px;

}


.form-group label {

    display: block;

    font-weight: 600;

    color: #374151;

    margin-bottom: 8px;

}


.form-control {

    width: 100%;

    height: 52px;

    border-radius: 12px;

    border: 1px solid #d1d5db;

    padding: 0 15px;

    font-size: 15px;

    box-sizing: border-box;

    outline: none;

    transition: .2s;

}


.form-control:focus {

    border-color: #0B6B3A;

    box-shadow:
        0 0 0 .2rem rgba(11,107,58,.15);

}


.form-control::placeholder {

    color: #9ca3af;

}


/*
|--------------------------------------------------------------------------
| REGISTER BUTTON
|--------------------------------------------------------------------------
*/

.btn-register {

    height: 52px;

    width: 100%;

    border: none;

    border-radius: 12px;

    background: #0B6B3A;

    color: white;

    font-weight: 700;

    font-size: 16px;

    cursor: pointer;

    transition: .3s;

}


.btn-register:hover {

    background: #064E2A;

}


.btn-register:disabled {

    opacity: .7;

    cursor: not-allowed;

}


/*
|--------------------------------------------------------------------------
| SPINNER
|--------------------------------------------------------------------------
*/

.spinner {

    display: inline-block;

    width: 15px;

    height: 15px;

    border: 2px solid white;

    border-top-color: transparent;

    border-radius: 50%;

    animation: spin .8s linear infinite;

    margin-right: 8px;

}


@keyframes spin {

    to {

        transform: rotate(360deg);

    }

}


/*
|--------------------------------------------------------------------------
| LOGIN LINK
|--------------------------------------------------------------------------
*/

.login-link {

    text-align: center;

    margin-top: 25px;

    color: #6b7280;

    font-size: 14px;

    line-height: 1.6;

}


.login-link a {

    color: #0B6B3A;

    font-weight: bold;

    text-decoration: none;

}


.login-link a:hover {

    text-decoration: underline;

}


/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/

.alert-box {

    padding: 15px;

    border-radius: 12px;

    margin-bottom: 20px;

    font-weight: 600;

    font-size: 14px;

}


.alert-box.success {

    background: #dcfce7;

    color: #166534;

    border: 1px solid #86efac;

}


.alert-box.error {

    background: #fee2e2;

    color: #991b1b;

    border: 1px solid #fca5a5;

}


/*
|--------------------------------------------------------------------------
| TABLET
|--------------------------------------------------------------------------
*/

@media (max-width: 600px) {

    .register-page {

        padding: 20px;

    }


    .register-card {

        width: 100%;

        padding: 40px 30px;

        border-radius: 20px;

    }


    .logo-area {

        gap: 12px;

        margin-bottom: 25px;

    }


    .school-logo {

        width: 60px;

    }


    .school-text {

        width: 165px;

    }


    h2 {

        font-size: 28px;

    }


    .subtitle {

        font-size: 14px;

    }


    .form-control {

        height: 54px;

        font-size: 16px;

    }


    .btn-register {

        height: 54px;

    }

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 480px) {

    .register-page {

        padding: 0;

        align-items: stretch;

        background: white;

    }


    .register-card {

        width: 100%;

        min-height: 100vh;

        min-height: 100dvh;

        border-radius: 0;

        box-shadow: none;

        padding: 30px 22px;

        display: flex;

        flex-direction: column;

        justify-content: center;

    }


    .logo-area {

        justify-content: center;

        gap: 10px;

        margin-bottom: 25px;

    }


    .school-logo {

        width: 58px;

    }


    .school-text {

        width: 165px;

        max-width: 65%;

    }


    h2 {

        font-size: 26px;

        line-height: 1.2;

    }


    .subtitle {

        font-size: 14px;

        line-height: 1.5;

        margin-bottom: 25px;

    }


    .form-group {

        margin-bottom: 18px;

    }


    .form-group label {

        font-size: 14px;

    }


    .form-control {

        height: 54px;

        font-size: 16px;

        border-radius: 12px;

    }


    .btn-register {

        height: 54px;

        font-size: 16px;

    }


    .login-link {

        margin-top: 22px;

    }


    .alert-box {

        font-size: 13px;

        padding: 13px;

    }

}


/*
|--------------------------------------------------------------------------
| VERY SMALL PHONES
|--------------------------------------------------------------------------
*/

@media (max-width: 360px) {

    .register-card {

        padding: 25px 18px;

    }


    .logo-area {

        margin-bottom: 20px;

    }


    .school-logo {

        width: 52px;

    }


    .school-text {

        width: 145px;

    }


    h2 {

        font-size: 24px;

    }


    .subtitle {

        font-size: 13px;

    }


    .form-control {

        height: 50px;

    }


    .btn-register {

        height: 50px;

    }

}

</style>