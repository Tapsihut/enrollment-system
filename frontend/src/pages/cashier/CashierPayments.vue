<template>

<div class="cashier-container">

    <!-- SIDEBAR -->

    <CashierSidebar
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
                        Cashier Portal
                    </h5>

                    <small>
                        Manage student enrollment payments
                    </small>

                </div>

            </div>


            <!-- RIGHT SIDE -->

            <div class="top-actions">

                <!-- NOTIFICATION -->

                <button class="icon-btn">

                    <i class="bi bi-bell"></i>

                    <span class="notification-dot"></span>

                </button>


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


                    <!-- LOGOUT -->

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

import { ref } from "vue"
import { useRouter } from "vue-router"
import axios from "axios"

import CashierSidebar
    from "@/components/layout/CashierSidebar.vue"


const router = useRouter()

const sidebarOpen = ref(false)


const user = ref(
    JSON.parse(
        localStorage.getItem("user")
    ) || {}
)


/* LOGOUT */

function logout(){

    const confirmLogout =
        confirm(
            "Are you sure you want to logout?"
        )


    if(!confirmLogout){
        return
    }


    localStorage.removeItem("token")

    localStorage.removeItem("user")


    delete axios.defaults.headers.common[
        "Authorization"
    ]


    sidebarOpen.value = false


    router.replace("/login")

}

</script>


<style scoped>

/* =========================================================
   MAIN
========================================================= */

.cashier-container{

    display:flex;

    width:100%;

    min-height:100vh;

    overflow:hidden;

    background:#f5f7fb;

}


/* =========================================================
   CONTENT
========================================================= */

.main-content{

    flex:1;

    min-width:0;

    display:flex;

    flex-direction:column;

}


/* =========================================================
   TOPBAR
========================================================= */

.topbar{

    height:75px;

    flex-shrink:0;

    background:white;

    display:flex;

    align-items:center;

    justify-content:space-between;

    padding:0 25px;

    border-bottom:1px solid #e5e7eb;

    box-shadow:
        0 2px 8px rgba(0,0,0,.04);

}


/* =========================================================
   PAGE INFO
========================================================= */

.page-info{

    display:flex;

    align-items:center;

    gap:12px;

}


.page-info h5{

    margin:0;

    font-weight:800;

    color:#064E2A;

}


.page-info small{

    color:#6b7280;

    font-size:12px;

}


/* =========================================================
   MOBILE MENU BUTTON
========================================================= */

.menu-toggle{

    display:none;

    border:none;

    background:#f3f4f6;

    width:40px;

    height:40px;

    border-radius:10px;

    font-size:21px;

    color:#064E2A;

}


/* =========================================================
   RIGHT SIDE
========================================================= */

.top-actions{

    display:flex;

    align-items:center;

    gap:15px;

}


/* =========================================================
   NOTIFICATION
========================================================= */

.icon-btn{

    position:relative;

    width:40px;

    height:40px;

    border:none;

    border-radius:10px;

    background:#f3f4f6;

    color:#064E2A;

    font-size:18px;

}


.notification-dot{

    position:absolute;

    top:7px;

    right:7px;

    width:7px;

    height:7px;

    background:#dc2626;

    border-radius:50%;

}


/* =========================================================
   PROFILE
========================================================= */

.profile{

    display:flex;

    align-items:center;

    gap:10px;

    padding-left:15px;

    border-left:1px solid #e5e7eb;

}


.avatar{

    width:40px;

    height:40px;

    border-radius:50%;

    background:#064E2A;

    color:white;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:18px;

}


.user-info{

    display:flex;

    flex-direction:column;

}


.user-info strong{

    font-size:13px;

    color:#1f2937;

}


.user-info small{

    color:#6b7280;

    font-size:11px;

}


/* =========================================================
   LOGOUT
========================================================= */

.logout-btn{

    width:36px;

    height:36px;

    border:none;

    border-radius:9px;

    background:#fee2e2;

    color:#dc2626;

    transition:.2s;

}


.logout-btn:hover{

    background:#dc2626;

    color:white;

}


/* =========================================================
   PAGE CONTENT
========================================================= */

.page-content{

    flex:1;

    padding:20px;

    width:100%;

    box-sizing:border-box;

    overflow-x:auto;

}


/* =========================================================
   MOBILE OVERLAY
========================================================= */

.overlay{

    display:none;

}


/* =========================================================
   TABLET / MOBILE
========================================================= */

@media(max-width:992px){

    .menu-toggle{

        display:flex;

        align-items:center;

        justify-content:center;

    }


    :deep(.sidebar){

        position:fixed;

        top:0;

        left:0;

        width:260px;

        height:100vh;

        z-index:1000;

        transform:translateX(-100%);

        transition:
            transform .25s ease;

    }


    :deep(.sidebar.open){

        transform:translateX(0);

    }


    .overlay{

        display:block;

        position:fixed;

        inset:0;

        background:
            rgba(0,0,0,.45);

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


/* =========================================================
   SMALL MOBILE
========================================================= */

@media(max-width:576px){

    .topbar{

        height:65px;

        padding:0 12px;

    }


    .page-info h5{

        font-size:15px;

    }


    .page-info small{

        display:none;

    }


    .icon-btn{

        display:none;

    }


    .page-content{

        padding:10px;

    }


    .logout-btn{

        width:34px;

        height:34px;

    }

}

</style>