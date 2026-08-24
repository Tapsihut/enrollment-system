<template>

<div class="registrar-container">

    <!-- SIDEBAR -->

    <RegistrarSidebar
        :class="{ open: sidebarOpen }"
    />

    <!-- MOBILE OVERLAY -->

    <div
        v-if="sidebarOpen"
        class="overlay"
        @click="sidebarOpen=false"
    ></div>

    <!-- MAIN CONTENT -->

    <div class="main-content">

        <!-- TOPBAR -->

        <header class="topbar">

            <div class="page-info">

                <button
                    class="menu-toggle"
                    @click="sidebarOpen=!sidebarOpen"
                >
                    <i class="bi bi-list"></i>
                </button>

                <div>

                    <h5>
                        Registrar Portal
                    </h5>

                    <small>
                        Manage student enrollment and records
                    </small>

                </div>

            </div>

            <!-- RIGHT SIDE -->

            <div class="top-actions">

                <!-- NOTIFICATION -->

                <div class="notification-wrapper">

                    <button
                        class="icon-btn"
                        @click="toggleNotifications"
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
                            {{ unreadCount > 9 ? '9+' : unreadCount }}
                        </span>

                    </button>


                    <!-- NOTIFICATION PANEL -->

                    <div
                        v-if="showNotifications"
                        class="notification-panel"
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


                            <button
                                v-if="unreadCount > 0"
                                class="mark-read-btn"
                                @click="markAllAsRead"
                            >
                                Mark all as read
                            </button>

                        </div>


                        <!-- LOADING -->

                        <div
                            v-if="notificationLoading"
                            class="notification-loading"
                        >

                            <div class="spinner"></div>

                            <span>
                                Loading notifications...
                            </span>

                        </div>


                        <!-- NOTIFICATIONS -->

                        <div
                            v-else-if="notifications.length"
                            class="notification-list"
                        >

                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="notification-item"
                                :class="{
                                    unread: !notification.read
                                }"
                                @click="openNotification(notification)"
                            >

                                <div
                                    class="notification-icon"
                                    :class="notification.type"
                                >

                                    <i
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


                        <!-- FOOTER -->

                        <div class="notification-footer">

                            <button
                                @click="goToApplications"
                            >
                                View all applications
                            </button>

                        </div>

                    </div>

                </div>


                <!-- USER -->

                <div class="profile">

                    <div class="avatar">

                        <i class="bi bi-person"></i>

                    </div>

                    <div class="user-info">

                        <strong>
                            {{ user?.name }}
                        </strong>

                        <small>
                            Registrar
                        </small>

                    </div>

                    <button
                        class="logout-btn"
                        @click="logout"
                    >

                        <i class="bi bi-box-arrow-right"></i>

                    </button>

                </div>

            </div>

        </header>


        <!-- PAGE -->

        <main class="page-content">

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
    onBeforeUnmount
} from "vue"

import {
    useRouter
} from "vue-router"

import RegistrarSidebar
    from "@/components/layout/RegistrarSidebar.vue"

import api
    from "@/services/api"


const router = useRouter()


/*
|--------------------------------------------------------------------------
| SIDEBAR
|--------------------------------------------------------------------------
*/

const sidebarOpen = ref(false)


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

const user = ref(
    JSON.parse(
        localStorage.getItem("user")
    ) || {}
)


/*
|--------------------------------------------------------------------------
| NOTIFICATIONS
|--------------------------------------------------------------------------
*/

const showNotifications = ref(false)

const notificationLoading = ref(false)

const notifications = ref([])


/*
|--------------------------------------------------------------------------
| UNREAD COUNT
|--------------------------------------------------------------------------
*/

const unreadCount = computed(() => {

    return notifications.value.filter(
        notification => !notification.read
    ).length

})


/*
|--------------------------------------------------------------------------
| TOGGLE NOTIFICATIONS
|--------------------------------------------------------------------------
*/

function toggleNotifications() {

    showNotifications.value =
        !showNotifications.value


    if (showNotifications.value) {

        loadNotifications()

    }

}


/*
|--------------------------------------------------------------------------
| LOAD NOTIFICATIONS
|--------------------------------------------------------------------------
*/

async function loadNotifications() {

    if (notificationLoading.value) {
        return
    }

    notificationLoading.value = true

    try {

        const response =
            await api.get(
                "/registrar/enrollments"
            )


        const enrollments =
            response.data?.data ||
            response.data ||
            []


        if (!Array.isArray(enrollments)) {

            notifications.value = []

            return

        }


        /*
        |----------------------------------------------------------------------
        | Convert recent enrollment records into notifications
        |----------------------------------------------------------------------
        */

        notifications.value =
            enrollments
                .slice(0, 10)
                .map((enrollment, index) => {

                    const student =
                        enrollment.student


                    const studentName =
                        student
                        ? `${student.first_name || ""} ${student.last_name || ""}`.trim()
                        : "A student"


                    const status =
                        String(
                            enrollment.status || ""
                        ).toLowerCase()


                    let title =
                        "New Enrollment Application"

                    let message =
                        `${studentName} submitted an enrollment application.`

                    let icon =
                        "bi bi-person-plus"

                    let type =
                        "application"


                    if (status === "approved") {

                        title =
                            "Enrollment Approved"

                        message =
                            `${studentName}'s enrollment has been approved.`

                        icon =
                            "bi bi-check-circle"

                        type =
                            "approved"

                    }


                    if (status === "rejected") {

                        title =
                            "Enrollment Rejected"

                        message =
                            `${studentName}'s enrollment was rejected.`

                        icon =
                            "bi bi-x-circle"

                        type =
                            "rejected"

                    }


                    return {

                        id:
                            enrollment.id
                            || index,

                        enrollmentId:
                            enrollment.id,

                        title,

                        message,

                        icon,

                        type,

                        read:
                            getReadStatus(
                                enrollment.id
                            ),

                        time:
                            formatNotificationTime(
                                enrollment.created_at
                            )

                    }

                })

    }

    catch(error) {

        console.error(
            "Failed to load notifications:",
            error
        )

        notifications.value = []

    }

    finally {

        notificationLoading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| READ STATUS
|--------------------------------------------------------------------------
*/

function getReadStatus(id) {

    const readNotifications =
        JSON.parse(
            localStorage.getItem(
                "registrar_read_notifications"
            )
        ) || []


    return readNotifications.includes(id)

}


/*
|--------------------------------------------------------------------------
| MARK SINGLE NOTIFICATION AS READ
|--------------------------------------------------------------------------
*/

function markAsRead(notification) {

    notification.read = true


    const readNotifications =
        JSON.parse(
            localStorage.getItem(
                "registrar_read_notifications"
            )
        ) || []


    if (
        !readNotifications.includes(
            notification.enrollmentId
        )
    ) {

        readNotifications.push(
            notification.enrollmentId
        )

    }


    localStorage.setItem(
        "registrar_read_notifications",
        JSON.stringify(
            readNotifications
        )
    )

}


/*
|--------------------------------------------------------------------------
| MARK ALL AS READ
|--------------------------------------------------------------------------
*/

function markAllAsRead() {

    notifications.value.forEach(
        notification => {

            notification.read = true

        }
    )


    const ids =
        notifications.value.map(
            notification =>
                notification.enrollmentId
        )


    localStorage.setItem(
        "registrar_read_notifications",
        JSON.stringify(ids)
    )

}


/*
|--------------------------------------------------------------------------
| OPEN NOTIFICATION
|--------------------------------------------------------------------------
*/

function openNotification(notification) {

    markAsRead(notification)

    showNotifications.value = false

    if (notification.enrollmentId) {

        router.push(
            `/registrar/applications/${notification.enrollmentId}`
        )

    }

}


/*
|--------------------------------------------------------------------------
| VIEW APPLICATIONS
|--------------------------------------------------------------------------
*/

function goToApplications() {

    showNotifications.value = false

    router.push(
        "/registrar/applications"
    )

}


/*
|--------------------------------------------------------------------------
| NOTIFICATION TIME
|--------------------------------------------------------------------------
*/

function formatNotificationTime(date) {

    if (!date) {
        return "Recently"
    }


    const created =
        new Date(date)


    if (Number.isNaN(
        created.getTime()
    )) {

        return "Recently"

    }


    const now =
        new Date()


    const difference =
        Math.floor(
            (now - created) / 1000
        )


    if (difference < 60) {

        return "Just now"

    }


    if (difference < 3600) {

        return `${Math.floor(
            difference / 60
        )} minutes ago`

    }


    if (difference < 86400) {

        return `${Math.floor(
            difference / 3600
        )} hours ago`

    }


    if (difference < 604800) {

        return `${Math.floor(
            difference / 86400
        )} days ago`

    }


    return created.toLocaleDateString(
        "en-PH",
        {
            month: "short",
            day: "numeric",
            year: "numeric"
        }
    )

}


/*
|--------------------------------------------------------------------------
| CLOSE NOTIFICATION WHEN CLICKING OUTSIDE
|--------------------------------------------------------------------------
*/

function handleOutsideClick(event) {

    const wrapper =
        document.querySelector(
            ".notification-wrapper"
        )


    if (
        wrapper &&
        !wrapper.contains(
            event.target
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

function logout() {

    const confirmLogout =
        confirm(
            "Are you sure you want to logout?"
        )


    if (!confirmLogout) {
        return
    }


    localStorage.removeItem(
        "token"
    )

    localStorage.removeItem(
        "user"
    )


    delete api.defaults.headers.common[
        "Authorization"
    ]


    sidebarOpen.value = false


    router.replace(
        "/login"
    )

}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    document.addEventListener(
        "click",
        handleOutsideClick
    )

})


onBeforeUnmount(() => {

    document.removeEventListener(
        "click",
        handleOutsideClick
    )

})

</script>


<style scoped>

/* MAIN WRAPPER */

.registrar-container{
    display:flex;
    width:100%;
    min-height:100vh;
    overflow:hidden;
    background:#f5f7fb;
}


/* CONTENT AREA */

.main-content{
    flex:1;
    min-width:0;
    display:flex;
    flex-direction:column;
}


/* TOPBAR */

.topbar{
    height:80px;
    flex-shrink:0;
    background:white;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 30px;
    border-bottom:1px solid #e5e7eb;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
}


.page-info{
    display:flex;
    align-items:center;
    gap:15px;
}


.page-info h5{
    margin:0;
    font-weight:800;
    color:#064E2A;
}


.page-info small{
    color:#6b7280;
}


.menu-toggle{
    display:none;
    border:none;
    background:#f3f4f6;
    width:42px;
    height:42px;
    border-radius:12px;
    font-size:22px;
    color:#064E2A;
}


/* RIGHT AREA */

.top-actions{
    display:flex;
    align-items:center;
    gap:20px;
}


/* NOTIFICATION */

.notification-wrapper{
    position:relative;
}


.icon-btn{
    position:relative;
    width:42px;
    height:42px;
    border:none;
    border-radius:12px;
    background:#f3f4f6;
    color:#064E2A;
    font-size:20px;
    cursor:pointer;
    transition:.2s;
}


.icon-btn:hover{
    background:#e5f5ec;
}


.notification-dot{
    position:absolute;
    top:7px;
    right:8px;
    width:9px;
    height:9px;
    background:#dc2626;
    border:2px solid white;
    border-radius:50%;
}


.notification-count{
    position:absolute;
    top:-6px;
    right:-6px;
    min-width:18px;
    height:18px;
    padding:0 4px;
    background:#dc2626;
    color:white;
    border-radius:10px;
    font-size:9px;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
}


/* NOTIFICATION PANEL */

.notification-panel{
    position:absolute;
    top:52px;
    right:0;
    width:380px;
    background:white;
    border-radius:15px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
    border:1px solid #e5e7eb;
    overflow:hidden;
    z-index:2000;
}


/* NOTIFICATION HEADER */

.notification-header{
    padding:16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    border-bottom:1px solid #e5e7eb;
}


.notification-header strong{
    display:block;
    color:#064E2A;
    font-size:15px;
}


.notification-header small{
    color:#9ca3af;
    font-size:11px;
}


.mark-read-btn{
    border:none;
    background:none;
    color:#0B6B3A;
    font-size:11px;
    font-weight:600;
    cursor:pointer;
}


.mark-read-btn:hover{
    text-decoration:underline;
}


/* LIST */

.notification-list{
    max-height:380px;
    overflow-y:auto;
}


.notification-item{
    position:relative;
    display:flex;
    gap:11px;
    padding:14px 16px;
    cursor:pointer;
    border-bottom:1px solid #f1f5f9;
    transition:.2s;
}


.notification-item:hover{
    background:#f8faf9;
}


.notification-item.unread{
    background:#f0fdf4;
}


.notification-icon{
    width:38px;
    height:38px;
    min-width:38px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:16px;
}


.notification-icon.application{
    background:#dbeafe;
    color:#2563eb;
}


.notification-icon.approved{
    background:#dcfce7;
    color:#15803d;
}


.notification-icon.rejected{
    background:#fee2e2;
    color:#dc2626;
}


.notification-content{
    min-width:0;
    padding-right:10px;
}


.notification-content strong{
    display:block;
    font-size:12px;
    color:#374151;
}


.notification-content p{
    margin:3px 0;
    color:#6b7280;
    font-size:11px;
    line-height:1.4;
}


.notification-content small{
    color:#9ca3af;
    font-size:10px;
}


.unread-dot{
    position:absolute;
    right:14px;
    top:18px;
    width:7px;
    height:7px;
    border-radius:50%;
    background:#16a34a;
}


/* LOADING */

.notification-loading{
    min-height:180px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:10px;
    color:#9ca3af;
    font-size:12px;
}


.spinner{
    width:24px;
    height:24px;
    border:3px solid #dcfce7;
    border-top-color:#0B6B3A;
    border-radius:50%;
    animation:spin .7s linear infinite;
}


@keyframes spin{
    to{
        transform:rotate(360deg);
    }
}


/* EMPTY */

.notification-empty{
    min-height:180px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    color:#9ca3af;
}


.notification-empty i{
    font-size:32px;
    margin-bottom:8px;
}


.notification-empty strong{
    color:#374151;
    font-size:13px;
}


.notification-empty span{
    font-size:11px;
    margin-top:3px;
}


/* FOOTER */

.notification-footer{
    padding:12px;
    border-top:1px solid #e5e7eb;
    text-align:center;
}


.notification-footer button{
    border:none;
    background:none;
    color:#0B6B3A;
    font-size:12px;
    font-weight:700;
    cursor:pointer;
}


.notification-footer button:hover{
    text-decoration:underline;
}


/* PROFILE */

.profile{
    display:flex;
    align-items:center;
    gap:12px;
    padding-left:20px;
    border-left:1px solid #ddd;
}


.avatar{
    width:45px;
    height:45px;
    border-radius:50%;
    background:#064E2A;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}


.user-info{
    display:flex;
    flex-direction:column;
}


.user-info small{
    color:#6b7280;
}


.logout-btn{
    width:40px;
    height:40px;
    border:none;
    border-radius:10px;
    background:#fee2e2;
    color:#dc2626;
    cursor:pointer;
}


.logout-btn:hover{
    background:#dc2626;
    color:white;
}


/* PAGE CONTENT */

.page-content{
    flex:1;
    padding:30px;
    width:100%;
    overflow-x:auto;
}


/* MOBILE OVERLAY */

.overlay{
    display:none;
}


/* TABLET + MOBILE */

@media(max-width:992px){

    .menu-toggle{
        display:flex;
        align-items:center;
        justify-content:center;
    }


    :deep(.sidebar){
        position:fixed;
        top:0;
        left:-270px;
        width:270px;
        height:100vh;
        z-index:1000;
        transition:.3s;
    }


    :deep(.sidebar.open){
        left:0;
    }


    .overlay{
        display:block;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.45);
        z-index:999;
    }


    .user-info{
        display:none;
    }


    .profile{
        border-left:none;
        padding-left:0;
    }

}


@media(max-width:576px){

    .topbar{
        height:70px;
        padding:0 15px;
    }


    .page-info h5{
        font-size:16px;
    }


    .page-info small{
        display:none;
    }


    .notification-panel{
        position:fixed;
        top:70px;
        right:10px;
        left:10px;
        width:auto;
    }


    .icon-btn{
        display:flex;
        align-items:center;
        justify-content:center;
    }


    .page-content{
        padding:15px;
    }


    .logout-btn{
        width:36px;
        height:36px;
    }

}

</style>