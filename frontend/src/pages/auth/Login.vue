<script setup>

import { ref, onMounted, onUnmounted } from "vue"
import { useRouter } from "vue-router"

import auth from "@/services/auth"

import SFXCLogoOnly from "@/assets/images/logo/sfxc-logo-only.png"
import SFXCTextOnly from "@/assets/images/logo/sfxc-text-only.png"


/*
|--------------------------------------------------------------------------
| Background Images
|--------------------------------------------------------------------------
*/

const backgroundImages = [

    new URL(
        "@/assets/images/login/login-image-01.jpg",
        import.meta.url
    ).href,

    new URL(
        "@/assets/images/login/login-image-02.jpg",
        import.meta.url
    ).href,

    new URL(
        "@/assets/images/login/login-image-03.jpg",
        import.meta.url
    ).href

]

const currentImageIndex = ref(0)

let slideshowInterval = null
let alertTimer = null


/*
|--------------------------------------------------------------------------
| Slideshow
|--------------------------------------------------------------------------
*/

onMounted(() => {

    slideshowInterval = setInterval(() => {

        currentImageIndex.value =
            (currentImageIndex.value + 1)
            %
            backgroundImages.length

    }, 5000)

})


onUnmounted(() => {

    if (slideshowInterval) {
        clearInterval(slideshowInterval)
    }

    if (alertTimer) {
        clearTimeout(alertTimer)
    }

})


/*
|--------------------------------------------------------------------------
| Router
|--------------------------------------------------------------------------
*/

const router = useRouter()


/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const email = ref("")
const password = ref("")

const showPassword = ref(false)

const isLoading = ref(false)


/*
|--------------------------------------------------------------------------
| Alert
|--------------------------------------------------------------------------
*/

const alert = ref({

    show: false,

    type: "",

    message: ""

})


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

    }, 5000)

}


/*
|--------------------------------------------------------------------------
| Login
|--------------------------------------------------------------------------
*/

async function handleLogin() {

    if (isLoading.value) {
        return
    }

    if (!email.value || !password.value) {

        showAlert(
            "error",
            "Please enter your email and password."
        )

        return
    }


    isLoading.value = true

    alert.value.show = false


    try {

        console.log("Attempting login...")
        console.log("Email:", email.value)


        const response = await auth.login({

            email: email.value,

            password: password.value

        })


        console.log("Login response:", response.data)


        /*
        |--------------------------------------------------------------------------
        | Check Backend Response
        |--------------------------------------------------------------------------
        */

        if (!response.data?.token) {

            throw new Error(
                "Login succeeded but no authentication token was returned."
            )

        }


        /*
        |--------------------------------------------------------------------------
        | Save Authentication
        |--------------------------------------------------------------------------
        */

        localStorage.setItem(
            "token",
            response.data.token
        )

        localStorage.setItem(
            "user",
            JSON.stringify(response.data.user)
        )


        console.log(
            "Token saved:",
            localStorage.getItem("token")
        )


        /*
        |--------------------------------------------------------------------------
        | Success Message
        |--------------------------------------------------------------------------
        */

        showAlert(
            "success",
            "Login successful! Redirecting..."
        )


        /*
        |--------------------------------------------------------------------------
        | Redirect
        |--------------------------------------------------------------------------
        */

        setTimeout(() => {

            const user = response.data.user

            if (user.role === "registrar") {

                router.push("/registrar/applications")

            }

            else if (user.role === "cashier") {

                router.push("/cashier/dashboard")

            }

            else if (user.role === "student") {

                router.push("/student/dashboard")

            }

            else if (user.role === "admin") {

                router.push("/admin/dashboard")

            }

            else {

                router.push("/")

            }

        }, 1000)


    }

    catch (error) {

        console.error("================================")
        console.error("LOGIN ERROR")
        console.error("================================")
        console.error(error)


        /*
        |--------------------------------------------------------------------------
        | Backend responded
        |--------------------------------------------------------------------------
        */

        if (error.response) {

            console.error(
                "Backend status:",
                error.response.status
            )

            console.error(
                "Backend response:",
                error.response.data
            )


            if (error.response.status === 401) {

                showAlert(
                    "error",
                    "Invalid email or password."
                )

            }

            else {

                showAlert(
                    "error",
                    error.response.data?.message ||
                    "The server returned an error. Please try again."
                )

            }

        }


        /*
        |--------------------------------------------------------------------------
        | Request was sent but no response
        |--------------------------------------------------------------------------
        */

        else if (error.request) {

            console.error(
                "No response received from Laravel."
            )

            showAlert(
                "error",
                "Cannot connect to the server. Please make sure your phone and computer are connected to the same Wi-Fi."
            )

        }


        /*
        |--------------------------------------------------------------------------
        | Axios / JavaScript Error
        |--------------------------------------------------------------------------
        */

        else {

            showAlert(
                "error",
                error.message ||
                "Something went wrong while logging in."
            )

        }

    }

    finally {

        isLoading.value = false

    }

}

</script>


<template>

<div class="login-page">

    <div class="login-wrapper">


        <!-- =========================================================
             LOGIN SIDE
        ========================================================== -->

        <div class="login-box">


            <!-- LOGO -->

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


            <!-- TITLE -->

            <h2>
                Welcome Back!
            </h2>

            <p class="subtitle">
                Please enter your school credentials
                to access your account.
            </p>


            <!-- ALERT -->

            <div
                v-if="alert.show"
                class="alert-box"
                :class="alert.type"
            >
                {{ alert.message }}
            </div>


            <!-- LOGIN FORM -->

            <form
                @submit.prevent="handleLogin"
                class="login-form"
            >


                <!-- EMAIL -->

                <div class="form-group">

                    <label>
                        Email Address
                    </label>

                    <input
                        type="email"
                        class="form-control"
                        placeholder="Enter your email"
                        v-model="email"
                        autocomplete="email"
                        required
                        :disabled="isLoading"
                    >

                </div>


                <!-- PASSWORD -->

                <div class="form-group password-group">

                    <label>
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            :type="
                                showPassword
                                ? 'text'
                                : 'password'
                            "
                            class="form-control"
                            placeholder="Enter your password"
                            v-model="password"
                            autocomplete="current-password"
                            required
                            :disabled="isLoading"
                        >

                        <button
                            type="button"
                            class="show-password"
                            @click="
                                showPassword = !showPassword
                            "
                            :disabled="isLoading"
                        >
                            {{ showPassword ? 'Hide' : 'Show' }}
                        </button>

                    </div>

                </div>


                <!-- LOGIN BUTTON -->

                <button
                    type="submit"
                    class="btn-login"
                    :disabled="isLoading"
                >

                    <span v-if="isLoading">

                        <span class="spinner"></span>

                        Verifying...

                    </span>

                    <span v-else>

                        Sign In

                    </span>

                </button>

            </form>


            <!-- REGISTER -->

            <div class="register-link">

                Don't have an account?

                <router-link to="/register">
                    Create Account
                </router-link>

            </div>

        </div>


        <!-- =========================================================
             IMAGE SIDE
        ========================================================== -->

        <div class="image-box">

            <div
                v-for="(img, index) in backgroundImages"
                :key="img"
                class="background-image"
                :class="{
                    active: index === currentImageIndex
                }"
                :style="{
                    backgroundImage: `url(${img})`
                }"
            ></div>

            <div class="overlay"></div>

            <div class="content">

                <h5>
                    SFXC • Since 1991
                </h5>

                <h1>

                    <span>
                        EXCELLENCE
                    </span>

                    <br>

                    IN EDUCATION

                </h1>

                <p>
                    Nurturing minds, building futures,
                    and shaping leaders of tomorrow.
                </p>

                <blockquote>
                    "The beautiful thing about learning
                    is that no one can take it away from you."
                </blockquote>

            </div>

        </div>

    </div>

</div>

</template>


<style scoped>

.login-page {

    min-height: 100vh;
    min-height: 100dvh;

    background: #f3f8f5;

    display: flex;
    justify-content: center;
    align-items: center;

    padding: 30px;

    box-sizing: border-box;

}


.login-wrapper {

    width: 1100px;
    max-width: 100%;

    display: flex;

    background: white;

    border-radius: 25px;

    overflow: hidden;

    box-shadow:
        0 20px 60px rgba(0,0,0,.15);

}


.login-box {

    width: 50%;

    padding: 60px;

    box-sizing: border-box;

}


.logo-area {

    display: flex;
    align-items: center;

    gap: 15px;

    margin-bottom: 35px;

}


.school-logo {

    width: 75px;
    height: auto;

    object-fit: contain;

}


.school-text {

    width: 190px;
    max-width: 70%;

    height: auto;

    object-fit: contain;

}


h2 {

    font-size: 32px;
    font-weight: 800;

    margin-bottom: 10px;

}


.subtitle {

    color: #6b7280;

    line-height: 1.6;

    margin-bottom: 30px;

}


.form-group {

    margin-bottom: 20px;

}


.form-group label {

    display: block;

    font-weight: 600;

    margin-bottom: 8px;

    color: #374151;

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
        0 0 0 3px rgba(11,107,58,.10);

}


.password-wrapper {

    position: relative;

    width: 100%;

}


.password-wrapper .form-control {

    padding-right: 65px;

}


.show-password {

    position: absolute;

    right: 10px;

    top: 50%;

    transform: translateY(-50%);

    border: none;

    background: none;

    color: #0B6B3A;

    font-weight: 600;

    cursor: pointer;

    padding: 8px;

}


.btn-login {

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


.btn-login:hover {

    background: #064E2A;

}


.btn-login:disabled {

    opacity: .7;

    cursor: not-allowed;

}


.spinner {

    display: inline-block;

    width: 15px;
    height: 15px;

    border: 2px solid white;

    border-top-color: transparent;

    border-radius: 50%;

    animation: spin .8s linear infinite;

    margin-right: 6px;

}


@keyframes spin {

    to {
        transform: rotate(360deg);
    }

}


.register-link {

    text-align: center;

    margin-top: 25px;

    font-size: 14px;

    color: #6b7280;

}


.register-link a {

    color: #0B6B3A;

    font-weight: bold;

    text-decoration: none;

}


.register-link a:hover {

    text-decoration: underline;

}


.image-box {

    width: 50%;

    min-height: 650px;

    position: relative;

    overflow: hidden;

    color: white;

}


.background-image {

    position: absolute;

    inset: 0;

    background-size: cover;

    background-position: center;

    opacity: 0;

    transition: opacity 1s;

}


.background-image.active {

    opacity: .55;

}


.overlay {

    position: absolute;

    inset: 0;

    background:
        linear-gradient(
            135deg,
            rgba(6,78,42,.85),
            rgba(11,107,58,.65)
        );

}


.content {

    position: relative;

    z-index: 2;

    height: 100%;

    padding: 60px;

    display: flex;

    justify-content: center;

    flex-direction: column;

}


.content h5 {

    margin-bottom: 15px;

    font-weight: 600;

}


.content h1 {

    font-size: 50px;

    line-height: 1.1;

    font-weight: 900;

}


.content span {

    color: #9cffc8;

}


.content p {

    font-size: 17px;

    line-height: 1.6;

    max-width: 450px;

}


blockquote {

    margin-top: 40px;

    border-left: 4px solid #9cffc8;

    padding-left: 20px;

    font-style: italic;

    max-width: 450px;

    line-height: 1.6;

}


.alert-box {

    padding: 15px;

    border-radius: 12px;

    margin-bottom: 20px;

    font-weight: 600;

    animation: slide .3s ease;

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


@keyframes slide {

    from {

        opacity: 0;
        transform: translateY(-10px);

    }

    to {

        opacity: 1;
        transform: translateY(0);

    }

}


@media (max-width: 900px) {

    .login-page {

        padding: 20px;

        align-items: center;

    }


    .login-wrapper {

        display: block;

        width: 100%;

        border-radius: 20px;

    }


    .login-box {

        width: 100%;

        padding: 45px 35px;

    }


    .image-box {

        display: none;

    }


    .logo-area {

        margin-bottom: 30px;

    }

}


@media (max-width: 576px) {

    .login-page {

        padding: 0;

        align-items: stretch;

        background: white;

    }


    .login-wrapper {

        min-height: 100vh;
        min-height: 100dvh;

        border-radius: 0;

        box-shadow: none;

    }


    .login-box {

        min-height: 100vh;
        min-height: 100dvh;

        padding: 35px 22px;

        display: flex;

        flex-direction: column;

        justify-content: center;

    }


    .logo-area {

        justify-content: center;

        gap: 10px;

        margin-bottom: 30px;

    }


    .school-logo {

        width: 60px;

    }


    .school-text {

        width: 170px;
        max-width: 65%;

    }


    h2 {

        font-size: 28px;
        text-align: center;

    }


    .subtitle {

        text-align: center;

        font-size: 14px;

        margin-bottom: 25px;

    }


    .form-group {

        margin-bottom: 18px;

    }


    .form-control {

        height: 54px;
        font-size: 16px;

    }


    .btn-login {

        height: 54px;
        font-size: 16px;

    }


    .register-link {

        margin-top: 22px;

        line-height: 1.6;

    }


    .alert-box {

        font-size: 14px;

        padding: 13px;

    }

}


@media (max-width: 360px) {

    .login-box {

        padding: 25px 18px;

    }


    .logo-area {

        margin-bottom: 22px;

    }


    .school-logo {

        width: 52px;

    }


    .school-text {

        width: 145px;

    }


    h2 {

        font-size: 25px;

    }


    .subtitle {

        font-size: 13px;

    }


    .form-control {

        height: 50px;

    }


    .btn-login {

        height: 50px;

    }

}

</style>
