<template>
<div class="cashier-container">

    <!-- SIDEBAR -->
    <CashierSidebar :class="{ open: sidebarOpen }" />

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
                    <h5>Cashier Portal</h5>
                    <small>Manage student payments and collections</small>
                </div>

            </div>


            <div class="top-actions">

                <!-- NOTIFICATION -->
                <div class="notification-wrapper">

                    <button
                        class="icon-btn"
                        @click.stop="toggleNotifications"
                    >

                        <i class="bi bi-bell"></i>

                        <span
                            v-if="notifications.length"
                            class="notification-dot"
                        ></span>

                        <span
                            v-if="notifications.length"
                            class="notification-count"
                        >
                            {{ notifications.length }}
                        </span>

                    </button>


                    <!-- NOTIFICATION DROPDOWN -->
                    <div
                        v-if="showNotifications"
                        class="notification-dropdown"
                    >

                        <!-- HEADER -->
                        <div class="notification-header">

                            <div>
                                <strong>Notifications</strong>

                                <small>
                                    Cashier payment updates
                                </small>
                            </div>

                            <button
                                class="refresh-notification"
                                @click="loadNotifications"
                                :disabled="notificationLoading"
                            >
                                <i
                                    class="bi"
                                    :class="
                                        notificationLoading
                                        ? 'bi-arrow-repeat spin'
                                        : 'bi-arrow-clockwise'
                                    "
                                ></i>
                            </button>

                        </div>


                        <!-- LOADING -->
                        <div
                            v-if="notificationLoading"
                            class="notification-loading"
                        >

                            <div class="small-spinner"></div>

                            <span>
                                Loading...
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
                                @click="handleNotification(notification)"
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
                                @click="goToPayments"
                            >
                                View Payment Transactions
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
                            Cashier
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
    onMounted,
    onUnmounted
} from "vue"

import {
    useRouter
} from "vue-router"

import axios from "axios"

import api from "@/services/api"

import CashierSidebar
    from "@/components/layout/CashierSidebar.vue"


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

const notifications = ref([])

const showNotifications = ref(false)

const notificationLoading = ref(false)


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

        const { data } =
            await api.get(
                "/cashier/dashboard"
            )


        const statistics =
            data.statistics || {}


        const recent =
            data.recent || []


        const result = []


        /*
        |------------------------------------------------------------------
        | PENDING PAYMENTS
        |------------------------------------------------------------------
        */

        if (
            Number(
                statistics.pending || 0
            ) > 0
        ) {

            result.push({

                id: "pending",

                type: "pending",

                icon: "bi bi-clock-fill",

                title: "Pending Payments",

                message:
                    `${statistics.pending} payment transaction(s) are waiting for payment.`,

                time: "Payment monitoring",

                action: "payments"

            })

        }


        /*
        |------------------------------------------------------------------
        | FAILED PAYMENTS
        |------------------------------------------------------------------
        */

        if (
            Number(
                statistics.failed || 0
            ) > 0
        ) {

            result.push({

                id: "failed",

                type: "failed",

                icon: "bi bi-x-circle-fill",

                title: "Failed Payments",

                message:
                    `${statistics.failed} payment transaction(s) failed.`,

                time: "Requires attention",

                action: "payments"

            })

        }


        /*
        |------------------------------------------------------------------
        | RECENT PAID PAYMENT
        |------------------------------------------------------------------
        */

        const latestPaid =
            recent.find(
                payment =>
                    String(
                        payment.status || ""
                    ).toLowerCase()
                    === "paid"
            )


        if (latestPaid) {

            result.push({

                id:
                    `paid-${latestPaid.id}`,

                type: "paid",

                icon:
                    "bi bi-check-circle-fill",

                title:
                    "Payment Received",

                message:
                    `${getStudentName(latestPaid.student)} has a paid transaction.`,

                time:
                    formatDate(
                        latestPaid.created_at
                    ),

                action:
                    "payment",

                paymentId:
                    latestPaid.id

            })

        }


        notifications.value = result


    }
    catch(error) {

        console.error(
            "Failed to load cashier notifications:",
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
| STUDENT NAME
|--------------------------------------------------------------------------
*/

function getStudentName(student) {

    if (!student) {
        return "Unknown Student"
    }


    return (
        `${student.first_name || ""} ${student.last_name || ""}`
    ).trim()
    || "Unknown Student"

}


/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

function formatDate(date) {

    if (!date) {
        return "Recently"
    }


    const parsed =
        new Date(date)


    if (
        Number.isNaN(
            parsed.getTime()
        )
    ) {

        return "Recently"

    }


    return parsed.toLocaleDateString(
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
| NOTIFICATION CLICK
|--------------------------------------------------------------------------
*/

function handleNotification(notification) {

    showNotifications.value = false


    if (
        notification.action ===
        "payments"
    ) {

        router.push(
            "/cashier/payments"
        )

        return

    }


    if (
        notification.action ===
        "payment" &&
        notification.paymentId
    ) {

        router.push(
            `/cashier/payments/${notification.paymentId}`
        )

    }

}


/*
|--------------------------------------------------------------------------
| GO TO PAYMENTS
|--------------------------------------------------------------------------
*/

function goToPayments() {

    showNotifications.value = false

    router.push(
        "/cashier/payments"
    )

}


/*
|--------------------------------------------------------------------------
| CLOSE NOTIFICATION WHEN CLICKING OUTSIDE
|--------------------------------------------------------------------------
*/

function handleOutsideClick(event) {

    const target =
        event.target


    if (
        !target.closest(
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


    delete axios.defaults.headers
        .common["Authorization"]


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


onUnmounted(() => {

    document.removeEventListener(
        "click",
        handleOutsideClick
    )

})

</script>


<style scoped>

/* MAIN WRAPPER */

.cashier-container {
    display: flex;
    width: 100%;
    min-height: 100vh;
    overflow: hidden;
    background: #f5f7fb;
}


/* CONTENT */

.main-content {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
}


/* TOPBAR */

.topbar {
    height: 80px;
    flex-shrink: 0;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 25px;
    border-bottom: 1px solid #e5e7eb;
    box-shadow: 0 2px 10px rgba(0,0,0,.05);
}


/* PAGE INFO */

.page-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.page-info h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
    color: #064E2A;
}

.page-info small {
    color: #6b7280;
    font-size: 12px;
}


/* MENU */

.menu-toggle {
    display: none;
    border: none;
    background: #f3f4f6;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    font-size: 22px;
    color: #064E2A;
}


/* RIGHT AREA */

.top-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}


/* NOTIFICATION WRAPPER */

.notification-wrapper {
    position: relative;
}


/* NOTIFICATION BUTTON */

.icon-btn {
    position: relative;
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 10px;
    background: #f3f4f6;
    color: #064E2A;
    font-size: 19px;
    cursor: pointer;
}

.icon-btn:hover {
    background: #e5f5ec;
}


/* DOT */

.notification-dot {
    position: absolute;
    top: 7px;
    right: 7px;
    width: 7px;
    height: 7px;
    background: #dc2626;
    border-radius: 50%;
}


/* COUNT */

.notification-count {
    position: absolute;
    top: -7px;
    right: -7px;
    min-width: 19px;
    height: 19px;
    padding: 0 5px;
    border-radius: 20px;
    background: #dc2626;
    color: white;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}


/* NOTIFICATION DROPDOWN */

.notification-dropdown {
    position: absolute;
    top: 50px;
    right: 0;
    width: 360px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 15px 40px rgba(0,0,0,.15);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    z-index: 9999;
}


/* HEADER */

.notification-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px 17px;
    border-bottom: 1px solid #e5e7eb;
}

.notification-header strong {
    display: block;
    color: #064E2A;
    font-size: 15px;
}

.notification-header small {
    display: block;
    margin-top: 2px;
    color: #9ca3af;
    font-size: 11px;
}


/* REFRESH */

.refresh-notification {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 8px;
    background: #f3f4f6;
    color: #064E2A;
}

.refresh-notification:hover {
    background: #e5f5ec;
}


/* LIST */

.notification-list {
    max-height: 350px;
    overflow-y: auto;
}


/* ITEM */

.notification-item {
    display: flex;
    gap: 12px;
    padding: 14px 17px;
    border-bottom: 1px solid #f1f5f9;
    cursor: pointer;
    transition: .2s;
}

.notification-item:hover {
    background: #f8fafc;
}


/* ICON */

.notification-icon {
    width: 38px;
    height: 38px;
    min-width: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.notification-icon.pending {
    background: #fef3c7;
    color: #b45309;
}

.notification-icon.paid {
    background: #dcfce7;
    color: #15803d;
}

.notification-icon.failed {
    background: #fee2e2;
    color: #dc2626;
}


/* CONTENT */

.notification-content {
    min-width: 0;
}

.notification-content strong {
    display: block;
    color: #374151;
    font-size: 13px;
}

.notification-content p {
    margin: 3px 0;
    color: #6b7280;
    font-size: 12px;
    line-height: 1.4;
}

.notification-content small {
    color: #9ca3af;
    font-size: 10px;
}


/* LOADING */

.notification-loading {
    height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    color: #6b7280;
    font-size: 12px;
}

.small-spinner {
    width: 18px;
    height: 18px;
    border: 2px solid #d1fae5;
    border-top-color: #166534;
    border-radius: 50%;
    animation: spin .7s linear infinite;
}


/* EMPTY */

.notification-empty {
    min-height: 170px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    padding: 20px;
}

.notification-empty i {
    font-size: 30px;
    margin-bottom: 8px;
}

.notification-empty strong {
    color: #374151;
    font-size: 13px;
}

.notification-empty span {
    margin-top: 3px;
    font-size: 11px;
}


/* FOOTER */

.notification-footer {
    padding: 10px;
    border-top: 1px solid #e5e7eb;
}

.notification-footer button {
    width: 100%;
    border: none;
    background: #ecfdf5;
    color: #166534;
    padding: 9px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.notification-footer button:hover {
    background: #d1fae5;
}


/* SPIN */

.spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }
}


/* PROFILE */

.profile {
    display: flex;
    align-items: center;
    gap: 10px;
    padding-left: 15px;
    border-left: 1px solid #ddd;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #064E2A;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.user-info {
    display: flex;
    flex-direction: column;
}

.user-info strong {
    font-size: 13px;
}

.user-info small {
    font-size: 11px;
    color: #6b7280;
}


/* LOGOUT */

.logout-btn {
    width: 36px;
    height: 36px;
    border: none;
    border-radius: 9px;
    background: #fee2e2;
    color: #dc2626;
}

.logout-btn:hover {
    background: #dc2626;
    color: white;
}


/* PAGE */

.page-content {
    flex: 1;
    padding: 25px;
    width: 100%;
    overflow-x: auto;
}


/* OVERLAY */

.overlay {
    display: none;
}


/* TABLET */

@media (max-width: 992px) {

    .menu-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    :deep(.sidebar) {
        position: fixed;
        top: 0;
        left: -270px;
        width: 270px;
        height: 100vh;
        z-index: 1000;
        transition: .3s;
    }

    :deep(.sidebar.open) {
        left: 0;
    }

    .overlay {
        display: block;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,.45);
        z-index: 999;
    }

    .user-info {
        display: none;
    }

    .profile {
        border-left: none;
        padding-left: 0;
    }

}


/* MOBILE */

@media (max-width: 576px) {

    .topbar {
        height: 70px;
        padding: 0 15px;
    }

    .page-info h5 {
        font-size: 16px;
    }

    .page-info small {
        display: none;
    }

    .notification-dropdown {
        position: fixed;
        top: 70px;
        left: 15px;
        right: 15px;
        width: auto;
    }

    .page-content {
        padding: 15px;
    }

    .logout-btn {
        width: 34px;
        height: 34px;
    }

}

</style>