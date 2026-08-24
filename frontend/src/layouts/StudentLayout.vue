<template>

<div class="student-layout">


    <!-- =========================================================
         MOBILE OVERLAY
    ========================================================== -->

    <div
        v-if="mobileMenu"
        class="overlay"
        @click="mobileMenu=false"
    ></div>




    <!-- =========================================================
         SIDEBAR
    ========================================================== -->

    <aside
        class="sidebar"
        :class="{
            'show': mobileMenu
        }"
    >


        <!-- SIDEBAR HEADER -->

        <div class="sidebar-header">


            <div class="logo-circle">

                SFXC

            </div>


            <h5>

                Student Portal

            </h5>


            <p>

                Enrollment System

            </p>


        </div>





        <!-- MENU -->

        <nav class="menu">


            <!-- DASHBOARD -->

            <router-link
                to="/student/dashboard"
                class="menu-item"
                active-class="active"
                @click="mobileMenu=false"
            >

                <i class="bi bi-speedometer2"></i>

                Dashboard

            </router-link>




            <!-- PROFILE -->

            <router-link
                to="/student/profile"
                class="menu-item"
                active-class="active"
                @click="mobileMenu=false"
            >

                <i class="bi bi-person"></i>

                Profile

            </router-link>




            <!-- ENROLLMENT -->

            <router-link
                to="/student/enrollment"
                class="menu-item"
                active-class="active"
                @click="mobileMenu=false"
            >

                <i class="bi bi-journal-text"></i>

                Enrollment

            </router-link>




            <!-- PAYMENT -->

            <router-link
                to="/student/payment"
                class="menu-item"
                active-class="active"
                @click="mobileMenu=false"
            >

                <i class="bi bi-wallet2"></i>

                Payment

            </router-link>




            <!-- RECEIPT -->

            <router-link
                v-if="showDocuments"
                to="/student/receipt"
                class="menu-item"
                active-class="active"
                @click="mobileMenu=false"
            >

                <i class="bi bi-receipt"></i>

                Receipt

            </router-link>




            <!-- DOCUMENTS -->

            <router-link
                v-if="showDocuments"
                to="/student/documents"
                class="menu-item"
                active-class="active"
                @click="mobileMenu=false"
            >

                <i class="bi bi-folder2-open"></i>

                Documents

            </router-link>


        </nav>





        <!-- SIDEBAR FOOTER -->

        <div class="sidebar-footer">


            <button
                class="logout-btn"
                @click="logout"
                :disabled="isLoggingOut"
            >

                <span
                    v-if="isLoggingOut"
                    class="spinner-border spinner-border-sm me-2"
                ></span>


                {{
                    isLoggingOut
                    ? 'Logging out...'
                    : 'Logout'
                }}

            </button>


        </div>


    </aside>





    <!-- =========================================================
         MAIN AREA
    ========================================================== -->

    <div class="main-area">



        <!-- =====================================================
             TOP NAVBAR
        ====================================================== -->

        <header class="top-navbar">



            <!-- MOBILE BUTTON -->

            <button
                class="mobile-btn"
                @click="mobileMenu=true"
            >

                <i class="bi bi-list"></i>

            </button>




            <!-- PAGE TITLE -->

            <div>

                <h5>

                    {{ pageTitle }}

                </h5>

            </div>





            <!-- =================================================
                 PROFILE AREA
            ================================================== -->

            <div class="profile-area">


                <!-- =================================================
                     NOTIFICATION
                ================================================== -->

                <div class="notification-wrapper">


                    <button
                        class="notification-btn"
                        @click.stop="toggleNotifications"
                    >

                        <i class="bi bi-bell"></i>


                        <!-- NOTIFICATION DOT -->

                        <span
                            v-if="notifications.length > 0"
                            class="notification-dot"
                        ></span>


                    </button>




                    <!-- =================================================
                         NOTIFICATION DROPDOWN
                    ================================================== -->

                    <div
                        v-if="showNotifications"
                        class="notification-dropdown"
                        @click.stop
                    >


                        <!-- HEADER -->

                        <div class="notification-header">


                            <strong>

                                Notifications

                            </strong>


                            <button
                                v-if="notifications.length > 0"
                                @click="clearNotifications"
                            >

                                Clear

                            </button>


                        </div>




                        <!-- =================================================
                             NOTIFICATION LIST
                        ================================================== -->

                        <div
                            v-if="notifications.length > 0"
                            class="notification-list"
                        >


                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="notification-item"
                                :class="notification.type"
                                @click="handleNotification(notification)"
                            >


                                <!-- ICON -->

                                <div class="notification-icon">

                                    <i
                                        class="bi"
                                        :class="notification.icon"
                                    ></i>

                                </div>




                                <!-- CONTENT -->

                                <div class="notification-content">


                                    <strong>

                                        {{ notification.title }}

                                    </strong>


                                    <p>

                                        {{ notification.message }}

                                    </p>


                                    <small>

                                        {{ notification.time }}

                                    </small>


                                </div>


                            </div>


                        </div>




                        <!-- =================================================
                             EMPTY
                        ================================================== -->

                        <div
                            v-else
                            class="notification-empty"
                        >

                            <i class="bi bi-bell-slash"></i>


                            <p>

                                No new notifications

                            </p>

                        </div>


                    </div>


                </div>




                <!-- =================================================
                     AVATAR
                ================================================== -->

                <div class="avatar">

                    {{ initials }}

                </div>




                <!-- =================================================
                     STUDENT INFO
                ================================================== -->

                <div class="student-info">


                    <strong class="student-name">

                        {{ fullName }}

                    </strong>


                    <small>

                        {{
                            student.student_number
                            || "No Student Number"
                        }}

                    </small>


                </div>




                <!-- =================================================
                     DROPDOWN
                ================================================== -->

                <button class="dropdown-btn">

                    <i class="bi bi-chevron-down"></i>

                </button>


            </div>


        </header>





        <!-- =========================================================
             PAGE CONTENT
        ========================================================== -->

        <main class="content">

            <router-view />

        </main>


    </div>


</div>

</template>





<script setup>

import {
    ref,
    computed,
    onMounted,
    onUnmounted
} from "vue"

import {
    useRouter,
    useRoute
} from "vue-router"

import api from "@/services/api"





/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

const router = useRouter()

const route = useRoute()





/*
|--------------------------------------------------------------------------
| MOBILE MENU
|--------------------------------------------------------------------------
*/

const mobileMenu = ref(false)





/*
|--------------------------------------------------------------------------
| STUDENT
|--------------------------------------------------------------------------
*/

const student = ref({})





/*
|--------------------------------------------------------------------------
| DOCUMENTS
|--------------------------------------------------------------------------
*/

const showDocuments = ref(false)





/*
|--------------------------------------------------------------------------
| NOTIFICATIONS
|--------------------------------------------------------------------------
*/

const showNotifications = ref(false)

const notifications = ref([])





/*
|--------------------------------------------------------------------------
| STUDENT FULL NAME
|--------------------------------------------------------------------------
*/

const fullName = computed(() => {

    if (!student.value.first_name) {

        return "Student"

    }


    return `${student.value.first_name || ""} ${student.value.last_name || ""}`
        .trim()

})





/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

const initials = computed(() => {

    const first =
        student.value.first_name?.charAt(0)
        || ""


    const last =
        student.value.last_name?.charAt(0)
        || ""


    return `${first}${last}`.toUpperCase()

})





/*
|--------------------------------------------------------------------------
| COURSE
|--------------------------------------------------------------------------
*/

const courseName = computed(() => {

    return student.value.course?.name
        || "No Course"

})





/*
|--------------------------------------------------------------------------
| PAGE TITLE
|--------------------------------------------------------------------------
*/

const pageTitle = computed(() => {

    return route.meta.title
        || "Student Dashboard"

})





/*
|--------------------------------------------------------------------------
| LOAD STUDENT
|--------------------------------------------------------------------------
*/

async function loadStudent() {

    try {


        const { data } =
            await api.get(
                "/student/profile"
            )


        student.value =
            data.student || {}


        showDocuments.value =
            data.documents_available
            || false


        /*
        |--------------------------------------------------------------------------
        | LOAD NOTIFICATIONS
        |--------------------------------------------------------------------------
        */

        loadNotifications()


    }

    catch(error) {

        console.error(
            "Failed to load student:",
            error
        )

    }

}





/*
|--------------------------------------------------------------------------
| LOAD NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function loadNotifications() {


    const newNotifications = []



    /*
    |--------------------------------------------------------------------------
    | PROFILE NOTIFICATION
    |--------------------------------------------------------------------------
    */

    if (!student.value.first_name) {


        newNotifications.push({

            id: "profile",

            title: "Complete your profile",

            message:
                "Please complete your student profile.",

            icon: "bi-person",

            type: "info",

            route: "/student/profile",

            time: "Action required"

        })


    }



    /*
    |--------------------------------------------------------------------------
    | PAYMENT NOTIFICATION
    |--------------------------------------------------------------------------
    */

    newNotifications.push({

        id: "payment",

        title: "Enrollment Payment",

        message:
            "Check your enrollment payment status.",

        icon: "bi-wallet2",

        type: "payment",

        route: "/student/payment",

        time: "View payment"

    })



    /*
    |--------------------------------------------------------------------------
    | DOCUMENT NOTIFICATION
    |--------------------------------------------------------------------------
    */

    if (showDocuments.value) {


        newNotifications.push({

            id: "documents",

            title: "Enrollment Documents",

            message:
                "Your enrollment documents are available.",

            icon: "bi-folder2-open",

            type: "document",

            route: "/student/documents",

            time: "View documents"

        })


    }



    /*
    |--------------------------------------------------------------------------
    | SAVE
    |--------------------------------------------------------------------------
    */

    notifications.value =
        newNotifications

}





/*
|--------------------------------------------------------------------------
| TOGGLE NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function toggleNotifications() {

    showNotifications.value =
        !showNotifications.value

}





/*
|--------------------------------------------------------------------------
| CLEAR NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function clearNotifications() {

    notifications.value = []

    showNotifications.value = false

}





/*
|--------------------------------------------------------------------------
| HANDLE NOTIFICATION
|--------------------------------------------------------------------------
*/

function handleNotification(notification) {


    showNotifications.value = false


    if (notification.route) {

        router.push(
            notification.route
        )

    }

}





/*
|--------------------------------------------------------------------------
| CLOSE NOTIFICATION WHEN CLICKING OUTSIDE
|--------------------------------------------------------------------------
*/

function closeNotifications(event) {


    const wrapper =
        event.target.closest(
            ".notification-wrapper"
        )


    if (!wrapper) {

        showNotifications.value = false

    }

}





/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

const isLoggingOut = ref(false)


const logout = async () => {


    if (isLoggingOut.value) {

        return

    }


    isLoggingOut.value = true


    try {


        await api.post(
            "/logout"
        )


        localStorage.removeItem(
            "token"
        )


        localStorage.removeItem(
            "user"
        )


        router.push(
            "/login"
        )


    }

    catch(error) {


        console.error(
            "Logout error:",
            error
        )


        /*
        |--------------------------------------------------------------------------
        | Still logout locally
        |--------------------------------------------------------------------------
        */

        localStorage.removeItem(
            "token"
        )


        localStorage.removeItem(
            "user"
        )


        router.push(
            "/login"
        )


    }

    finally {

        isLoggingOut.value = false

    }

}





/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {


    loadStudent()


    document.addEventListener(
        "click",
        closeNotifications
    )


})





onUnmounted(() => {


    document.removeEventListener(
        "click",
        closeNotifications
    )


})

</script>





<style scoped>


/* =========================================================
   STUDENT LAYOUT
========================================================= */

.student-layout{

    min-height:100vh;

    background:#f3f8f5;

}





/* =========================================================
   STUDENT NAME
========================================================= */

.student-name{

    text-transform:capitalize;

    font-size:16px;

    font-weight:700;

    color:#064E2A;

}





/* =========================================================
   SIDEBAR
========================================================= */

.sidebar{

    position:fixed;

    left:0;

    top:0;

    bottom:0;

    width:260px;

    background:
        linear-gradient(
            180deg,
            #064E2A,
            #0B6B3A
        );

    color:white;

    padding:20px;

    display:flex;

    flex-direction:column;

    z-index:1000;

    overflow:hidden;

}





/* SIDEBAR HEADER */

.sidebar-header{

    text-align:center;

    padding-bottom:30px;

    border-bottom:
        1px solid rgba(255,255,255,.2);

}





/* LOGO */

.logo-circle{

    height:75px;

    width:75px;

    margin:auto;

    border-radius:50%;

    background:white;

    color:#0B6B3A;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:30px;

    font-weight:900;

    border:4px solid #9cffc8;

}





.sidebar-header h5{

    margin-top:15px;

    font-weight:700;

}





.sidebar-header p{

    font-size:13px;

    opacity:.7;

}





/* =========================================================
   MENU
========================================================= */

.menu{

    margin-top:25px;

    flex:1;

    overflow-y:auto;

    padding-right:5px;

}


.menu::-webkit-scrollbar{

    width:5px;

}


.menu::-webkit-scrollbar-thumb{

    background:
        rgba(255,255,255,.3);

    border-radius:10px;

}





.menu-item{

    display:flex;

    gap:15px;

    align-items:center;

    padding:14px;

    margin-bottom:10px;

    border-radius:15px;

    color:white;

    text-decoration:none;

    transition:.3s;

}





.menu-item i{

    font-size:20px;

}





.menu-item:hover{

    background:
        rgba(255,255,255,.15);

}





.menu-item.active{

    background:white;

    color:#0B6B3A;

    font-weight:bold;

}





/* =========================================================
   SIDEBAR FOOTER
========================================================= */

.sidebar-footer{

    margin-top:auto;

    border-top:
        1px solid rgba(255,255,255,.2);

    padding-top:20px;

    flex-shrink:0;

}





.logout-btn{

    width:100%;

    height:45px;

    border:none;

    border-radius:15px;

    background:
        rgba(255,255,255,.15);

    color:white;

    font-weight:600;

}





.logout-btn:hover{

    background:white;

    color:#0B6B3A;

}





/* =========================================================
   MAIN
========================================================= */

.main-area{

    margin-left:260px;

}





/* =========================================================
   TOP NAV
========================================================= */

.top-navbar{

    height:80px;

    background:white;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 35px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.05);

    position:sticky;

    top:0;

    z-index:500;

}





.top-navbar h5{

    font-weight:700;

    margin:0;

}





/* =========================================================
   PROFILE
========================================================= */

.profile-area{

    display:flex;

    align-items:center;

    gap:15px;

}





/* =========================================================
   AVATAR
========================================================= */

.avatar{

    height:45px;

    width:45px;

    border-radius:50%;

    background:#0B6B3A;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-weight:bold;

}





/* =========================================================
   STUDENT INFO
========================================================= */

.student-info{

    display:flex;

    flex-direction:column;

}





.student-info small{

    color:#6b7280;

}





/* =========================================================
   DROPDOWN
========================================================= */

.dropdown-btn{

    border:none;

    background:none;

}





/* =========================================================
   NOTIFICATION
========================================================= */

.notification-wrapper{

    position:relative;

}





.notification-btn{

    position:relative;

    border:none;

    background:none;

    font-size:22px;

    color:#064E2A;

    cursor:pointer;

    width:42px;

    height:42px;

    border-radius:10px;

    display:flex;

    align-items:center;

    justify-content:center;

    transition:.2s;

}





.notification-btn:hover{

    background:#f0f5f2;

}





/* RED DOT */

.notification-dot{

    position:absolute;

    top:7px;

    right:7px;

    width:9px;

    height:9px;

    background:#dc2626;

    border:2px solid white;

    border-radius:50%;

}





/* =========================================================
   NOTIFICATION DROPDOWN
========================================================= */

.notification-dropdown{

    position:absolute;

    top:55px;

    right:0;

    width:350px;

    background:white;

    border-radius:15px;

    box-shadow:
        0 15px 40px rgba(0,0,0,.15);

    border:1px solid #e5e7eb;

    overflow:hidden;

    z-index:2000;

}





/* HEADER */

.notification-header{

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:15px 18px;

    border-bottom:1px solid #e5e7eb;

}





.notification-header strong{

    color:#064E2A;

    font-size:16px;

}





.notification-header button{

    border:none;

    background:none;

    color:#dc2626;

    font-size:12px;

    font-weight:600;

    cursor:pointer;

}





/* =========================================================
   NOTIFICATION LIST
========================================================= */

.notification-list{

    max-height:350px;

    overflow-y:auto;

}





/* NOTIFICATION ITEM */

.notification-item{

    display:flex;

    gap:12px;

    padding:15px 18px;

    border-bottom:1px solid #f0f0f0;

    cursor:pointer;

    transition:.2s;

}





.notification-item:hover{

    background:#f8faf9;

}





/* ICON */

.notification-icon{

    width:40px;

    height:40px;

    min-width:40px;

    border-radius:10px;

    background:#e7f7ee;

    color:#0B6B3A;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:18px;

}





/* CONTENT */

.notification-content{

    flex:1;

}





.notification-content strong{

    display:block;

    font-size:13px;

    color:#1f2937;

    margin-bottom:3px;

}





.notification-content p{

    margin:0;

    font-size:12px;

    color:#6b7280;

    line-height:1.4;

}





.notification-content small{

    display:block;

    margin-top:5px;

    font-size:10px;

    color:#9ca3af;

}





/* =========================================================
   EMPTY
========================================================= */

.notification-empty{

    padding:40px 20px;

    text-align:center;

    color:#9ca3af;

}





.notification-empty i{

    font-size:30px;

}





.notification-empty p{

    margin-top:10px;

    margin-bottom:0;

    font-size:13px;

}





/* =========================================================
   PAGE CONTENT
========================================================= */

.content{

    padding:30px;

    min-height:
        calc(100vh - 80px);

}





/* =========================================================
   MOBILE BUTTON
========================================================= */

.mobile-btn{

    display:none;

    border:none;

    background:none;

    font-size:28px;

    color:#0B6B3A;

}





/* =========================================================
   OVERLAY
========================================================= */

.overlay{

    display:none;

}





/* =========================================================
   TABLET
========================================================= */

@media(max-width:900px){


    .sidebar{

        transform:
            translateX(-100%);

        transition:.3s;

    }


    .sidebar.show{

        transform:
            translateX(0);

    }


    .main-area{

        margin-left:0;

    }


    .mobile-btn{

        display:block;

    }


    .student-info{

        display:none;

    }


    .overlay{

        display:block;

        position:fixed;

        inset:0;

        background:
            rgba(0,0,0,.4);

        z-index:900;

    }


}





/* =========================================================
   MOBILE
========================================================= */

@media(max-width:576px){


    .top-navbar{

        height:70px;

        padding:0 15px;

    }


    .notification-dropdown{

        position:fixed;

        top:70px;

        left:15px;

        right:15px;

        width:auto;

    }


    .content{

        padding:15px;

    }


    .dropdown-btn{

        display:none;

    }


}


</style>