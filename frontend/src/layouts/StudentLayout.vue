<template>

    <div class="student-layout">

        <!-- =====================================================
             MOBILE OVERLAY
        ====================================================== -->

        <div
            v-if="mobileMenu"
            class="overlay"
            @click="mobileMenu = false"
        ></div>


        <!-- =====================================================
             SIDEBAR
        ====================================================== -->

        <aside
            class="sidebar"
            :class="{ show: mobileMenu }"
        >

            <!-- SIDEBAR HEADER -->

            <div class="sidebar-header">

                <div class="logo-circle">

                    <img
                        :src="SFXCLogo"
                        alt="SFXC Logo"
                    >

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

                <router-link
                    to="/student/dashboard"
                    class="menu-item"
                    active-class="active"
                    @click="mobileMenu = false"
                >

                    <i class="bi bi-speedometer2"></i>

                    <span>
                        Dashboard
                    </span>

                </router-link>


                <router-link
                    to="/student/profile"
                    class="menu-item"
                    active-class="active"
                    @click="mobileMenu = false"
                >

                    <i class="bi bi-person"></i>

                    <span>
                        Profile
                    </span>

                </router-link>


                <router-link
                    to="/student/enrollment"
                    class="menu-item"
                    active-class="active"
                    @click="mobileMenu = false"
                >

                    <i class="bi bi-journal-text"></i>

                    <span>
                        Enrollment
                    </span>

                </router-link>


                <router-link
                    to="/student/payment"
                    class="menu-item"
                    active-class="active"
                    @click="mobileMenu = false"
                >

                    <i class="bi bi-wallet2"></i>

                    <span>
                        Payment
                    </span>

                </router-link>


                <router-link
                    v-if="showDocuments"
                    to="/student/receipt"
                    class="menu-item"
                    active-class="active"
                    @click="mobileMenu = false"
                >

                    <i class="bi bi-receipt"></i>

                    <span>
                        Receipt
                    </span>

                </router-link>


                <router-link
                    v-if="showDocuments"
                    to="/student/documents"
                    class="menu-item"
                    active-class="active"
                    @click="mobileMenu = false"
                >

                    <i class="bi bi-folder2-open"></i>

                    <span>
                        Documents
                    </span>

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
                        class="spinner-border spinner-border-sm"
                    ></span>

                    <i
                        v-else
                        class="bi bi-box-arrow-right"
                    ></i>

                    <span>
                        {{
                            isLoggingOut
                                ? "Logging out..."
                                : "Logout"
                        }}
                    </span>

                </button>

            </div>

        </aside>



        <!-- =====================================================
             MAIN AREA
        ====================================================== -->

        <div class="main-area">


            <!-- =================================================
                 TOP NAVBAR
            ================================================== -->

            <header class="top-navbar">


                <!-- MOBILE MENU BUTTON -->

                <button
                    class="mobile-btn"
                    @click="mobileMenu = true"
                    aria-label="Open menu"
                >

                    <i class="bi bi-list"></i>

                </button>


                <!-- PAGE TITLE -->

                <div class="page-title">

                    <h5>
                        {{ pageTitle }}
                    </h5>

                    <small>
                        Student Portal
                    </small>

                </div>


                <!-- PROFILE AREA -->

                <div class="profile-area">


                    <!-- NOTIFICATIONS -->

                    <div class="notification-wrapper">


                        <button
                            class="notification-btn"
                            @click.stop="toggleNotifications"
                            aria-label="Notifications"
                        >

                            <i class="bi bi-bell"></i>


                            <span
                                v-if="unreadCount > 0"
                                class="notification-dot"
                            ></span>


                            <span
                                v-if="unreadCount > 0"
                                class="notification-count"
                            >

                                {{
                                    unreadCount > 9
                                        ? "9+"
                                        : unreadCount
                                }}

                            </span>

                        </button>



                        <!-- NOTIFICATION DROPDOWN -->

                        <div
                            v-if="showNotifications"
                            class="notification-dropdown"
                            @click.stop
                        >


                            <!-- HEADER -->

                            <div class="notification-header">

                                <div>

                                    <strong>
                                        Notifications
                                    </strong>

                                    <small>
                                        {{ unreadCount }} unread
                                    </small>

                                </div>


                                <div class="notification-actions">


                                    <button
                                        v-if="unreadCount > 0"
                                        class="mark-read-btn"
                                        @click="markAllAsRead"
                                    >

                                        Mark all as read

                                    </button>


                                    <button
                                        class="refresh-btn"
                                        @click="loadStudent"
                                        :disabled="loadingNotifications"
                                        title="Refresh"
                                    >

                                        <i
                                            class="bi"
                                            :class="
                                                loadingNotifications
                                                    ? 'bi-arrow-repeat spin'
                                                    : 'bi-arrow-clockwise'
                                            "
                                        ></i>

                                    </button>

                                </div>

                            </div>



                            <!-- LOADING -->

                            <div
                                v-if="loadingNotifications"
                                class="notification-loading"
                            >

                                <div class="notification-spinner"></div>

                                <span>
                                    Loading notifications...
                                </span>

                            </div>



                            <!-- LIST -->

                            <div
                                v-else-if="notifications.length > 0"
                                class="notification-list"
                            >

                                <div
                                    v-for="notification in notifications"
                                    :key="notification.id"
                                    class="notification-item"
                                    :class="[
                                        notification.type,
                                        {
                                            unread:
                                                !notification.read
                                        }
                                    ]"
                                    @click="
                                        handleNotification(notification)
                                    "
                                >

                                    <div class="notification-icon">

                                        <i
                                            class="bi"
                                            :class="notification.icon"
                                        ></i>

                                    </div>


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


                                    <span
                                        v-if="!notification.read"
                                        class="unread-dot"
                                    ></span>

                                </div>

                            </div>



                            <!-- EMPTY -->

                            <div
                                v-else
                                class="notification-empty"
                            >

                                <i class="bi bi-bell-slash"></i>

                                <strong>
                                    No notifications
                                </strong>

                                <span>
                                    You're all caught up.
                                </span>

                            </div>

                        </div>

                    </div>



                    <!-- AVATAR -->

                    <div class="avatar">

                        {{ initials || "S" }}

                    </div>



                    <!-- STUDENT INFORMATION -->

                    <div class="student-info">

                        <strong class="student-name">
                            {{ fullName }}
                        </strong>

                        <small>
                            {{
                                student.student_number ||
                                "No Student Number"
                            }}
                        </small>

                    </div>



                    <!-- DROPDOWN -->

                    <button
                        class="dropdown-btn"
                        aria-label="Profile menu"
                    >

                        <i class="bi bi-chevron-down"></i>

                    </button>

                </div>

            </header>



            <!-- =================================================
                 CONTENT
            ================================================== -->

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

import SFXCLogo from "@/assets/images/logo/sfxc-logo-only.png"


/*
|--------------------------------------------------------------------------
| ROUTER
|--------------------------------------------------------------------------
*/

const router = useRouter()

const route = useRoute()


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const mobileMenu = ref(false)

const student = ref({})

const enrollment = ref(null)

const showDocuments = ref(false)

const showNotifications = ref(false)

const notifications = ref([])

const isLoggingOut = ref(false)

const loadingNotifications = ref(false)

let notificationInterval = null


/*
|--------------------------------------------------------------------------
| COMPUTED
|--------------------------------------------------------------------------
*/

const fullName = computed(() => {

    if (!student.value.first_name) {

        return "Student"

    }

    return `${student.value.first_name || ""} ${
        student.value.last_name || ""
    }`.trim()

})


const initials = computed(() => {

    const first =
        student.value.first_name?.charAt(0) || ""

    const last =
        student.value.last_name?.charAt(0) || ""

    return `${first}${last}`.toUpperCase()

})


const pageTitle = computed(() => {

    return route.meta.title || "Student Dashboard"

})


const unreadCount = computed(() => {

    return notifications.value.filter(
        notification => !notification.read
    ).length

})


/*
|--------------------------------------------------------------------------
| READ NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function getReadNotifications() {

    try {

        return JSON.parse(
            localStorage.getItem(
                "student_read_notifications"
            ) || "[]"
        )

    }
    catch {

        return []

    }

}


function isRead(id) {

    return getReadNotifications().includes(id)

}


/*
|--------------------------------------------------------------------------
| LOAD STUDENT
|--------------------------------------------------------------------------
*/

async function loadStudent() {

    try {

        const { data } =
            await api.get("/student/profile")


        student.value =
            data.student || {}


        enrollment.value =
            data.enrollment || null


        showDocuments.value =
            data.documents_available || false


        loadNotifications()

    }
    catch (error) {

        console.error(
            "Failed to load student:",
            error
        )

    }

}


/*
|--------------------------------------------------------------------------
| NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function loadNotifications() {

    loadingNotifications.value = true

    try {

        const newNotifications = []

        const current = enrollment.value

        const studentId =
            student.value.id || "student"


        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        if (!student.value.first_name) {

            newNotifications.push({

                id:
                    `profile-${studentId}`,

                title:
                    "Complete Your Profile",

                message:
                    "Please complete your student profile before continuing with enrollment.",

                icon:
                    "bi-person-fill",

                type:
                    "info",

                route:
                    "/student/profile",

                time:
                    "Action required"

            })

        }


        /*
        |--------------------------------------------------------------------------
        | NO ENROLLMENT
        |--------------------------------------------------------------------------
        */

        if (!current) {

            newNotifications.push({

                id:
                    `enrollment-start-${studentId}`,

                title:
                    "Start Your Enrollment",

                message:
                    "You have not submitted an enrollment application yet.",

                icon:
                    "bi-journal-plus",

                type:
                    "info",

                route:
                    "/student/enrollment",

                time:
                    "Action required"

            })

        }


        if (current) {

            const status =
                String(
                    current.status || ""
                )
                .trim()
                .toLowerCase()


            /*
            |--------------------------------------------------------------------------
            | PENDING
            |--------------------------------------------------------------------------
            */

            if (status === "pending") {

                newNotifications.push({

                    id:
                        `enrollment-pending-${current.id}`,

                    title:
                        "Enrollment Application Submitted",

                    message:
                        "Your enrollment application has been received. Please proceed with your enrollment payment.",

                    icon:
                        "bi-hourglass-split",

                    type:
                        "pending",

                    route:
                        "/student/payment",

                    time:
                        "Payment required"

                })

            }


            /*
            |--------------------------------------------------------------------------
            | PAID
            |--------------------------------------------------------------------------
            */

            if (status === "paid") {

                newNotifications.push({

                    id:
                        `enrollment-paid-${current.id}`,

                    title:
                        "Payment Received",

                    message:
                        "Your enrollment payment has been successfully received. Your enrollment is now waiting for processing.",

                    icon:
                        "bi-check-circle-fill",

                    type:
                        "success",

                    route:
                        "/student/payment",

                    time:
                        "Payment completed"

                })


                newNotifications.push({

                    id:
                        `enrollment-processing-wait-${current.id}`,

                    title:
                        "Enrollment Processing",

                    message:
                        "Your enrollment will be processed by the college. Please allow approximately 1–2 working days.",

                    icon:
                        "bi-clock-history",

                    type:
                        "processing",

                    route:
                        "/student/enrollment",

                    time:
                        "Please wait"

                })

            }


            /*
            |--------------------------------------------------------------------------
            | PROCESSING
            |--------------------------------------------------------------------------
            */

            if (status === "processing") {

                newNotifications.push({

                    id:
                        `enrollment-processing-${current.id}`,

                    title:
                        "Enrollment Is Being Processed",

                    message:
                        "Your enrollment is currently being processed. Please allow approximately 1–2 working days.",

                    icon:
                        "bi-arrow-repeat",

                    type:
                        "processing",

                    route:
                        "/student/enrollment",

                    time:
                        "Processing"

                })

            }


            /*
            |--------------------------------------------------------------------------
            | COMPLETED
            |--------------------------------------------------------------------------
            */

            if (status === "completed") {

                newNotifications.push({

                    id:
                        `enrollment-completed-${current.id}`,

                    title:
                        "Congratulations! Enrollment Completed",

                    message:
                        "Your enrollment has been completed. Your Study Load and Payment Receipt will be sent to your registered email.",

                    icon:
                        "bi-mortarboard-fill",

                    type:
                        "completed",

                    route:
                        "/student/receipt",

                    time:
                        "Enrollment completed"

                })


                if (showDocuments.value) {

                    newNotifications.push({

                        id:
                            `documents-${current.id}`,

                        title:
                            "Enrollment Documents Available",

                        message:
                            "Your enrollment documents are now available for viewing.",

                        icon:
                            "bi-folder-check",

                        type:
                            "document",

                        route:
                            "/student/documents",

                        time:
                            "View documents"

                    })

                }

            }


            /*
            |--------------------------------------------------------------------------
            | REJECTED
            |--------------------------------------------------------------------------
            */

            if (status === "rejected") {

                const reason =
                    current.rejection_reason ||
                    "Your enrollment application was rejected. Please review your application and contact the registrar if you need assistance."


                newNotifications.push({

                    id:
                        `enrollment-rejected-${current.id}`,

                    title:
                        "Enrollment Application Rejected",

                    message:
                        reason,

                    icon:
                        "bi-exclamation-triangle-fill",

                    type:
                        "rejected",

                    route:
                        "/student/enrollment",

                    time:
                        "Action required"

                })

            }


            /*
            |--------------------------------------------------------------------------
            | MISSING REQUIREMENTS
            |--------------------------------------------------------------------------
            */

            const missing =
                current.missing_requirements ||
                current.requirements_missing ||
                current.incomplete_requirements


            if (
                missing === true ||
                (
                    Array.isArray(missing) &&
                    missing.length > 0
                ) ||
                (
                    typeof missing === "string" &&
                    missing.trim().length > 0
                )
            ) {

                newNotifications.push({

                    id:
                        `requirements-${current.id}`,

                    title:
                        "Incomplete Requirements",

                    message:
                        "Some enrollment requirements are still incomplete. Please review your documents.",

                    icon:
                        "bi-file-earmark-x-fill",

                    type:
                        "document",

                    route:
                        "/student/documents",

                    time:
                        "Action required"

                })

            }

        }


        /*
        |--------------------------------------------------------------------------
        | APPLY READ STATUS
        |--------------------------------------------------------------------------
        */

        newNotifications.forEach(
            notification => {

                notification.read =
                    isRead(notification.id)

            }
        )


        notifications.value =
            newNotifications

    }
    finally {

        loadingNotifications.value = false

    }

}


/*
|--------------------------------------------------------------------------
| TOGGLE NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function toggleNotifications() {

    showNotifications.value =
        !showNotifications.value


    if (showNotifications.value) {

        loadStudent()

    }

}


/*
|--------------------------------------------------------------------------
| MARK SINGLE AS READ
|--------------------------------------------------------------------------
*/

function markAsRead(notification) {

    notification.read = true

    const read =
        getReadNotifications()


    if (!read.includes(notification.id)) {

        read.push(notification.id)

    }


    localStorage.setItem(
        "student_read_notifications",
        JSON.stringify(read)
    )

}


/*
|--------------------------------------------------------------------------
| MARK ALL AS READ
|--------------------------------------------------------------------------
*/

function markAllAsRead() {

    const read =
        getReadNotifications()


    notifications.value.forEach(
        notification => {

            notification.read = true


            if (!read.includes(notification.id)) {

                read.push(notification.id)

            }

        }
    )


    localStorage.setItem(
        "student_read_notifications",
        JSON.stringify(read)
    )

}


/*
|--------------------------------------------------------------------------
| HANDLE NOTIFICATION
|--------------------------------------------------------------------------
*/

function handleNotification(notification) {

    markAsRead(notification)

    showNotifications.value = false

    if (notification.route) {

        router.push(
            notification.route
        )

    }

}


/*
|--------------------------------------------------------------------------
| OUTSIDE CLICK
|--------------------------------------------------------------------------
*/

function closeNotifications(event) {

    if (
        !event.target.closest(
            ".notification-wrapper"
        )
    ) {

        showNotifications.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

async function logout() {

    if (isLoggingOut.value) {

        return

    }


    isLoggingOut.value = true


    try {

        await api.post("/logout")

    }
    catch (error) {

        console.error(
            "Logout error:",
            error
        )

    }
    finally {

        localStorage.removeItem("token")

        localStorage.removeItem("user")

        delete api
            .defaults
            .headers
            .common
            .Authorization


        router.push("/login")

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


    notificationInterval =
        setInterval(
            loadStudent,
            30000
        )

})


onUnmounted(() => {

    document.removeEventListener(
        "click",
        closeNotifications
    )


    if (notificationInterval) {

        clearInterval(
            notificationInterval
        )

        notificationInterval = null

    }

})

</script>


<style scoped>

* {
    box-sizing: border-box;
}


/* =========================================================
   LAYOUT
========================================================= */

.student-layout {

    min-height: 100vh;

    background: #F3F8F5;

}


/* =========================================================
   SIDEBAR
========================================================= */

.sidebar {

    position: fixed;

    left: 0;
    top: 0;
    bottom: 0;

    width: 255px;

    background: linear-gradient(
        180deg,
        #064E2A,
        #0B6B3A
    );

    color: white;

    padding: 18px;

    display: flex;

    flex-direction: column;

    z-index: 1000;

    overflow: hidden;

    box-shadow:
        5px 0 20px rgba(0,0,0,.08);

}


/* =========================================================
   SIDEBAR HEADER
========================================================= */

.sidebar-header {

    text-align: center;

    padding-bottom: 22px;

    border-bottom:
        1px solid rgba(255,255,255,.18);

}


.logo-circle {

    height: 70px;

    width: 70px;

    margin: auto;

    border-radius: 50%;

    background: white;

    display: flex;

    align-items: center;

    justify-content: center;

    border: 4px solid #9CFFC8;

    overflow: hidden;

}


.logo-circle img {

    width: 100%;

    height: 100%;

    object-fit: contain;

    padding: 5px;

}


.sidebar-header h5 {

    margin: 12px 0 3px;

    font-size: 17px;

    font-weight: 800;

}


.sidebar-header p {

    font-size: 11px;

    opacity: .72;

    margin: 0;

}


/* =========================================================
   MENU
========================================================= */

.menu {

    margin-top: 18px;

    flex: 1;

    overflow-y: auto;

    padding-right: 3px;

}


.menu::-webkit-scrollbar {

    width: 4px;

}


.menu::-webkit-scrollbar-thumb {

    background:
        rgba(255,255,255,.25);

    border-radius: 10px;

}


.menu-item {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 11px 13px;

    margin-bottom: 6px;

    border-radius: 11px;

    color: white;

    text-decoration: none;

    font-size: 13px;

    font-weight: 500;

    transition: .2s;

}


.menu-item i {

    width: 22px;

    text-align: center;

    font-size: 17px;

}


.menu-item:hover {

    background:
        rgba(255,255,255,.13);

    color: white;

}


.menu-item.active {

    background: white;

    color: #0B6B3A;

    font-weight: 700;

    box-shadow:
        0 4px 12px rgba(0,0,0,.08);

}


/* =========================================================
   SIDEBAR FOOTER
========================================================= */

.sidebar-footer {

    margin-top: auto;

    border-top:
        1px solid rgba(255,255,255,.18);

    padding-top: 14px;

    flex-shrink: 0;

}


.logout-btn {

    width: 100%;

    min-height: 42px;

    border: none;

    border-radius: 11px;

    background:
        rgba(255,255,255,.12);

    color: white;

    font-size: 13px;

    font-weight: 600;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    transition: .2s;

}


.logout-btn:hover {

    background: white;

    color: #0B6B3A;

}


.logout-btn:disabled {

    opacity: .7;

    cursor: not-allowed;

}


/* =========================================================
   MAIN
========================================================= */

.main-area {

    margin-left: 255px;

    min-width: 0;

}


/* =========================================================
   TOP NAVBAR
========================================================= */

.top-navbar {

    height: 72px;

    background: white;

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 0 25px;

    box-shadow:
        0 4px 18px rgba(0,0,0,.05);

    position: sticky;

    top: 0;

    z-index: 500;

}


.page-title {

    min-width: 0;

    margin-right: auto;

}


.page-title h5 {

    font-size: 16px;

    font-weight: 800;

    color: #1F2937;

    margin: 0;

}


.page-title small {

    display: block;

    color: #9CA3AF;

    font-size: 9px;

    margin-top: 2px;

}


/* =========================================================
   PROFILE AREA
========================================================= */

.profile-area {

    display: flex;

    align-items: center;

    gap: 10px;

}


.avatar {

    height: 39px;

    width: 39px;

    min-width: 39px;

    border-radius: 50%;

    background: #0B6B3A;

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 12px;

    font-weight: 800;

}


.student-info {

    display: flex;

    flex-direction: column;

    min-width: 0;

}


.student-name {

    text-transform: capitalize;

    font-size: 13px;

    font-weight: 700;

    color: #064E2A;

    white-space: nowrap;

}


.student-info small {

    color: #6B7280;

    font-size: 9px;

    margin-top: 1px;

}


.dropdown-btn {

    border: none;

    background: none;

    color: #6B7280;

    font-size: 12px;

    padding: 5px;

}


/* =========================================================
   MOBILE BUTTON
========================================================= */

.mobile-btn {

    display: none;

    border: none;

    background: #E8F5EE;

    color: #0B6B3A;

    width: 38px;

    height: 38px;

    border-radius: 10px;

    font-size: 21px;

    align-items: center;

    justify-content: center;

    margin-right: 10px;

}


/* =========================================================
   NOTIFICATION BUTTON
========================================================= */

.notification-wrapper {

    position: relative;

}


.notification-btn {

    position: relative;

    width: 38px;

    height: 38px;

    border: none;

    background: transparent;

    color: #064E2A;

    cursor: pointer;

    border-radius: 10px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 20px;

    transition: .2s;

}


.notification-btn:hover {

    background: #F0F5F2;

}


.notification-dot {

    position: absolute;

    top: 5px;

    right: 5px;

    width: 8px;

    height: 8px;

    background: #DC2626;

    border: 2px solid white;

    border-radius: 50%;

}


.notification-count {

    position: absolute;

    top: -5px;

    right: -6px;

    min-width: 17px;

    height: 17px;

    padding: 0 4px;

    border-radius: 20px;

    background: #DC2626;

    color: white;

    font-size: 8px;

    font-weight: 700;

    display: flex;

    align-items: center;

    justify-content: center;

}


/* =========================================================
   NOTIFICATION DROPDOWN
========================================================= */

.notification-dropdown {

    position: absolute;

    top: 49px;

    right: 0;

    width: 370px;

    background: white;

    border-radius: 14px;

    box-shadow:
        0 15px 40px rgba(0,0,0,.14);

    border: 1px solid #E5E7EB;

    overflow: hidden;

    z-index: 2000;

}


.notification-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 10px;

    padding: 12px 14px;

    border-bottom:
        1px solid #E5E7EB;

}


.notification-header strong {

    display: block;

    color: #064E2A;

    font-size: 13px;

}


.notification-header small {

    display: block;

    color: #9CA3AF;

    font-size: 9px;

    margin-top: 2px;

}


.notification-actions {

    display: flex;

    align-items: center;

    gap: 6px;

}


.mark-read-btn {

    border: none;

    background: none;

    color: #0B6B3A;

    font-size: 9px;

    font-weight: 600;

    cursor: pointer;

    white-space: nowrap;

}


.mark-read-btn:hover {

    text-decoration: underline;

}


.refresh-btn {

    width: 28px;

    height: 28px;

    border: none;

    border-radius: 7px;

    background: #F3F4F6;

    color: #064E2A;

    cursor: pointer;

}


.refresh-btn:hover {

    background: #E5F5EC;

}


.refresh-btn:disabled {

    opacity: .5;

    cursor: not-allowed;

}


/* =========================================================
   NOTIFICATION LIST
========================================================= */

.notification-list {

    max-height: 370px;

    overflow-y: auto;

}


.notification-item {

    position: relative;

    display: flex;

    align-items: flex-start;

    gap: 9px;

    padding: 11px 13px;

    border-bottom:
        1px solid #F0F0F0;

    cursor: pointer;

    transition: .2s;

}


.notification-item:hover {

    background: #F8FAF9;

}


.notification-item.unread {

    background: #F0FDF4;

}


.notification-icon {

    width: 36px;

    height: 36px;

    min-width: 36px;

    border-radius: 9px;

    background: #E7F7EE;

    color: #0B6B3A;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 15px;

}


.notification-item.pending
.notification-icon {

    background: #FEF3C7;

    color: #B45309;

}


.notification-item.success
.notification-icon {

    background: #DCFCE7;

    color: #15803D;

}


.notification-item.processing
.notification-icon {

    background: #DBEAFE;

    color: #2563EB;

}


.notification-item.rejected
.notification-icon {

    background: #FEE2E2;

    color: #DC2626;

}


.notification-item.document
.notification-icon {

    background: #FEF3C7;

    color: #B45309;

}


.notification-item.completed
.notification-icon {

    background: #EDE9FE;

    color: #7C3AED;

}


.notification-item.info
.notification-icon {

    background: #E0F2FE;

    color: #0369A1;

}


.notification-content {

    flex: 1;

    min-width: 0;

    padding-right: 6px;

}


.notification-content strong {

    display: block;

    font-size: 11px;

    color: #1F2937;

    margin-bottom: 3px;

    line-height: 1.3;

}


.notification-content p {

    margin: 0;

    font-size: 10px;

    color: #6B7280;

    line-height: 1.4;

}


.notification-content small {

    display: block;

    margin-top: 4px;

    font-size: 8px;

    color: #9CA3AF;

}


.unread-dot {

    position: absolute;

    right: 11px;

    top: 17px;

    width: 6px;

    height: 6px;

    background: #16A34A;

    border-radius: 50%;

}


/* =========================================================
   LOADING
========================================================= */

.notification-loading {

    height: 145px;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    gap: 7px;

    color: #9CA3AF;

    font-size: 10px;

}


.notification-spinner {

    width: 21px;

    height: 21px;

    border: 3px solid #DCFCE7;

    border-top-color: #0B6B3A;

    border-radius: 50%;

    animation:
        spin .7s linear infinite;

}


.spin {

    animation:
        spin .7s linear infinite;

}


@keyframes spin {

    to {
        transform: rotate(360deg);
    }

}


/* =========================================================
   EMPTY
========================================================= */

.notification-empty {

    padding: 35px 20px;

    text-align: center;

    color: #9CA3AF;

    display: flex;

    flex-direction: column;

    align-items: center;

}


.notification-empty i {

    font-size: 28px;

    margin-bottom: 6px;

}


.notification-empty strong {

    color: #374151;

    font-size: 11px;

}


.notification-empty span {

    font-size: 9px;

    margin-top: 2px;

}


/* =========================================================
   CONTENT
========================================================= */

.content {

    padding: 25px;

    min-height:
        calc(100vh - 72px);

}


/* =========================================================
   TABLET
========================================================= */

@media(max-width:900px) {

    .sidebar {

        width: 250px;

        transform:
            translateX(-100%);

        transition:
            transform .25s ease;

    }


    .sidebar.show {

        transform:
            translateX(0);

    }


    .main-area {

        margin-left: 0;

    }


    .mobile-btn {

        display: flex;

    }


    .overlay {

        display: block;

        position: fixed;

        inset: 0;

        background:
            rgba(0,0,0,.42);

        backdrop-filter:
            blur(1px);

        z-index: 900;

    }


    .content {

        padding: 20px;

    }


    .student-info {

        display: none;

    }


    .dropdown-btn {

        display: none;

    }

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:576px) {

    .top-navbar {

        height: 64px;

        padding: 0 11px;

    }


    .mobile-btn {

        width: 36px;

        height: 36px;

        min-width: 36px;

        margin-right: 7px;

        font-size: 20px;

    }


    .page-title h5 {

        font-size: 14px;

    }


    .page-title small {

        font-size: 8px;

    }


    .profile-area {

        gap: 5px;

    }


    .avatar {

        width: 34px;

        height: 34px;

        min-width: 34px;

        font-size: 10px;

    }


    .notification-btn {

        width: 35px;

        height: 35px;

        font-size: 18px;

    }


    .notification-count {

        top: -4px;

        right: -5px;

    }


    .notification-dropdown {

        position: fixed;

        top: 64px;

        left: 8px;

        right: 8px;

        width: auto;

        max-width: none;

        border-radius: 13px;

    }


    .notification-list {

        max-height:
            calc(100vh - 145px);

    }


    .content {

        padding: 13px 10px;

        min-height:
            calc(100vh - 64px);

    }


    .sidebar {

        width: min(
            275px,
            88vw
        );

        padding: 15px;

    }


    .logo-circle {

        width: 62px;

        height: 62px;

    }


    .sidebar-header {

        padding-bottom: 18px;

    }


    .sidebar-header h5 {

        margin-top: 9px;

        font-size: 15px;

    }


    .sidebar-header p {

        font-size: 10px;

    }


    .menu {

        margin-top: 14px;

    }


    .menu-item {

        padding: 10px 12px;

        margin-bottom: 5px;

        border-radius: 10px;

        font-size: 12px;

    }


    .menu-item i {

        font-size: 16px;

    }


    .logout-btn {

        min-height: 40px;

        border-radius: 10px;

        font-size: 12px;

    }

}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media(max-width:380px) {

    .top-navbar {

        padding: 0 8px;

    }


    .page-title h5 {

        font-size: 13px;

    }


    .page-title small {

        display: none;

    }


    .content {

        padding: 10px 7px;

    }


    .avatar {

        width: 32px;

        height: 32px;

        min-width: 32px;

    }


    .notification-btn {

        width: 33px;

        height: 33px;

    }


    .notification-dropdown {

        left: 5px;

        right: 5px;

    }

}

</style>
