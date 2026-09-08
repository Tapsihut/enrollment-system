<template>
<div class="registrar-container">

    <RegistrarSidebar :class="{open:sidebarOpen}"/>
    <div v-if="sidebarOpen" class="overlay" @click="sidebarOpen=false"></div>

    <div class="main-content">

        <!-- TOPBAR -->
        <header class="topbar">

            <div class="page-info">
                <button class="menu-toggle" @click="sidebarOpen=!sidebarOpen">
                    <i class="bi bi-list"></i>
                </button>

                <div>
                    <h5>Registrar Portal</h5>
                    <small>Manage student enrollment and records</small>
                </div>
            </div>

            <div class="top-actions">

                <!-- NOTIFICATIONS -->
                <div class="notification-wrapper">

                    <button class="icon-btn" @click.stop="toggleNotifications">
                        <i class="bi bi-bell"></i>

                        <span v-if="unreadCount>0" class="notification-dot"></span>

                        <span v-if="unreadCount>0" class="notification-count">
                            {{unreadCount>9?'9+':unreadCount}}
                        </span>
                    </button>

                    <!-- PANEL -->
                    <div
                        v-if="showNotifications"
                        class="notification-panel"
                        @click.stop
                    >

                        <!-- HEADER -->
                        <div class="notification-header">

                            <div>
                                <strong>Notifications</strong>
                                <small>
                                    {{unreadCount}} unread
                                </small>
                            </div>

                            <div class="header-actions">

                                <button
                                    class="refresh-btn"
                                    @click="loadNotifications"
                                    :disabled="notificationLoading"
                                    title="Refresh"
                                >
                                    <i
                                        class="bi"
                                        :class="notificationLoading?'bi-arrow-repeat spin':'bi-arrow-clockwise'"
                                    ></i>
                                </button>

                                <button
                                    v-if="unreadCount>0"
                                    class="mark-read-btn"
                                    @click="markAllAsRead"
                                >
                                    Mark all as read
                                </button>

                            </div>

                        </div>

                        <!-- LOADING -->
                        <div
                            v-if="notificationLoading"
                            class="notification-loading"
                        >
                            <div class="spinner"></div>
                            <span>Loading notifications...</span>
                        </div>

                        <!-- LIST -->
                        <div
                            v-else-if="notifications.length"
                            class="notification-list"
                        >

                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="notification-item"
                                :class="{unread:!notification.read}"
                                @click="openNotification(notification)"
                            >

                                <div
                                    class="notification-icon"
                                    :class="notification.type"
                                >
                                    <i :class="notification.icon"></i>
                                </div>

                                <div class="notification-content">

                                    <strong>
                                        {{notification.title}}
                                    </strong>

                                    <p>
                                        {{notification.message}}
                                    </p>

                                    <small>
                                        {{notification.time}}
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
                            <strong>No notifications</strong>
                            <span>You're all caught up.</span>
                        </div>

                        <!-- FOOTER -->
                        <div class="notification-footer">

                            <button @click="goToApplications">
                                <i class="bi bi-folder2-open"></i>
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
                        <strong>{{user?.name||"Registrar"}}</strong>
                        <small>Registrar</small>
                    </div>

                    <button class="logout-btn" @click="logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>

                </div>

            </div>
        </header>

        <!-- PAGE -->
        <main class="page-content">
            <router-view/>
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

import {useRouter} from "vue-router"

import RegistrarSidebar
from "@/components/layout/RegistrarSidebar.vue"

import api
from "@/services/api"


const router=useRouter()


/* SIDEBAR */
const sidebarOpen=ref(false)


/* USER */
const user=ref(
    JSON.parse(localStorage.getItem("user"))||{}
)


/* NOTIFICATIONS */
const showNotifications=ref(false)
const notificationLoading=ref(false)
const notifications=ref([])

let notificationInterval=null


/* UNREAD COUNT */
const unreadCount=computed(()=>{
    return notifications.value.filter(
        notification=>!notification.read
    ).length
})


/* TOGGLE */
function toggleNotifications(){

    showNotifications.value=
        !showNotifications.value

    if(showNotifications.value){
        loadNotifications()
    }

}


/* LOAD */
async function loadNotifications(){

    if(notificationLoading.value)return

    notificationLoading.value=true

    try{

        const response=
            await api.get("/registrar/enrollments")

        const enrollments=
            response.data?.data||
            response.data||
            []

        if(!Array.isArray(enrollments)){

            notifications.value=[]

            return
        }


        const result=[]


        /*
        |--------------------------------------------------------------
        | SORT NEWEST FIRST
        |--------------------------------------------------------------
        */

        const sorted=
            [...enrollments].sort((a,b)=>{

                const dateA=
                    new Date(
                        a.updated_at||
                        a.created_at||
                        0
                    )

                const dateB=
                    new Date(
                        b.updated_at||
                        b.created_at||
                        0
                    )

                return dateB-dateA
            })


        /*
        |--------------------------------------------------------------
        | PROCESS RECENT APPLICATIONS
        |--------------------------------------------------------------
        */

        sorted.slice(0,15).forEach(
            (enrollment,index)=>{

                const student=
                    enrollment.student

                const studentName=
                    student
                    ? `${student.first_name||""} ${student.last_name||""}`.trim()
                    : "A student"


                const status=
                    String(
                        enrollment.status||""
                    ).toLowerCase()


                let notification=null


                /*
                |----------------------------------------------------------
                | PENDING
                |----------------------------------------------------------
                */

                if(
                    status==="pending"||
                    status===""
                ){

                    notification={
                        id:`pending-${enrollment.id}`,
                        enrollmentId:enrollment.id,
                        type:"application",
                        icon:"bi bi-person-plus-fill",
                        title:"New Enrollment Application",
                        message:`${studentName} submitted an enrollment application.`,
                        time:formatNotificationTime(
                            enrollment.created_at
                        )
                    }

                }


                /*
                |----------------------------------------------------------
                | APPROVED
                |----------------------------------------------------------
                */

                else if(status==="approved"){

                    notification={
                        id:`approved-${enrollment.id}`,
                        enrollmentId:enrollment.id,
                        type:"approved",
                        icon:"bi bi-check-circle-fill",
                        title:"Enrollment Approved",
                        message:`${studentName}'s enrollment has been approved.`,
                        time:formatNotificationTime(
                            enrollment.updated_at||
                            enrollment.created_at
                        )
                    }

                }


                /*
                |----------------------------------------------------------
                | REJECTED
                |----------------------------------------------------------
                */

                else if(status==="rejected"){

                    notification={
                        id:`rejected-${enrollment.id}`,
                        enrollmentId:enrollment.id,
                        type:"rejected",
                        icon:"bi bi-x-circle-fill",
                        title:"Enrollment Rejected",
                        message:`${studentName}'s enrollment was rejected.`,
                        time:formatNotificationTime(
                            enrollment.updated_at||
                            enrollment.created_at
                        )
                    }

                }


                /*
                |----------------------------------------------------------
                | PAID
                |----------------------------------------------------------
                */

                else if(status==="paid"){

                    notification={
                        id:`paid-${enrollment.id}`,
                        enrollmentId:enrollment.id,
                        type:"paid",
                        icon:"bi bi-cash-coin",
                        title:"Enrollment Payment Completed",
                        message:`${studentName} has completed the enrollment payment.`,
                        time:formatNotificationTime(
                            enrollment.updated_at||
                            enrollment.created_at
                        )
                    }

                }


                /*
                |----------------------------------------------------------
                | ENROLLED
                |----------------------------------------------------------
                */

                else if(status==="enrolled"){

                    notification={
                        id:`enrolled-${enrollment.id}`,
                        enrollmentId:enrollment.id,
                        type:"enrolled",
                        icon:"bi bi-person-check-fill",
                        title:"Student Enrolled",
                        message:`${studentName} is now officially enrolled.`,
                        time:formatNotificationTime(
                            enrollment.updated_at||
                            enrollment.created_at
                        )
                    }

                }


                /*
                |----------------------------------------------------------
                | MISSING REQUIREMENTS
                |----------------------------------------------------------
                */

                const hasMissingRequirements=
                    enrollment.missing_requirements||
                    enrollment.requirements_missing||
                    enrollment.incomplete_requirements


                if(hasMissingRequirements){

                    notification={
                        id:`requirements-${enrollment.id}`,
                        enrollmentId:enrollment.id,
                        type:"requirements",
                        icon:"bi bi-file-earmark-x-fill",
                        title:"Incomplete Requirements",
                        message:`${studentName}'s enrollment has incomplete requirements.`,
                        time:formatNotificationTime(
                            enrollment.updated_at||
                            enrollment.created_at
                        )
                    }

                }


                /*
                |----------------------------------------------------------
                | ADD NOTIFICATION
                |----------------------------------------------------------
                */

                if(notification){

                    notification.read=
                        getReadStatus(
                            notification.id
                        )

                    result.push(notification)
                }

            }
        )


        /*
        |--------------------------------------------------------------
        | REMOVE DUPLICATES
        |--------------------------------------------------------------
        */

        const unique=
            result.filter(
                (item,index,self)=>
                    index===
                    self.findIndex(
                        x=>x.id===item.id
                    )
            )


        notifications.value=
            unique.slice(0,10)

    }
    catch(error){

        console.error(
            "Failed to load registrar notifications:",
            error
        )

    }
    finally{

        notificationLoading.value=false

    }

}


/* READ STORAGE */
function getReadNotifications(){

    return JSON.parse(
        localStorage.getItem(
            "registrar_read_notifications"
        )
    )||[]

}


/* CHECK READ */
function getReadStatus(id){

    return getReadNotifications().includes(id)

}


/* MARK SINGLE */
function markAsRead(notification){

    notification.read=true

    const readNotifications=
        getReadNotifications()

    if(
        !readNotifications.includes(
            notification.id
        )
    ){

        readNotifications.push(
            notification.id
        )

    }

    localStorage.setItem(
        "registrar_read_notifications",
        JSON.stringify(
            readNotifications
        )
    )

}


/* MARK ALL */
function markAllAsRead(){

    const readNotifications=
        getReadNotifications()

    notifications.value.forEach(
        notification=>{

            notification.read=true

            if(
                !readNotifications.includes(
                    notification.id
                )
            ){

                readNotifications.push(
                    notification.id
                )

            }

        }
    )

    localStorage.setItem(
        "registrar_read_notifications",
        JSON.stringify(
            readNotifications
        )
    )

}


/* OPEN */
function openNotification(notification){

    markAsRead(notification)

    showNotifications.value=false

    if(notification.enrollmentId){

        router.push(
            `/registrar/applications/${notification.enrollmentId}`
        )

    }

}


/* APPLICATIONS */
function goToApplications(){

    showNotifications.value=false

    router.push(
        "/registrar/applications"
    )

}


/* TIME */
function formatNotificationTime(date){

    if(!date)return "Recently"

    const created=
        new Date(date)

    if(
        Number.isNaN(
            created.getTime()
        )
    ){

        return "Recently"

    }

    const now=new Date()

    const difference=
        Math.floor(
            (now-created)/1000
        )


    if(difference<60)
        return "Just now"


    if(difference<3600)
        return `${Math.floor(difference/60)} minutes ago`


    if(difference<86400)
        return `${Math.floor(difference/3600)} hours ago`


    if(difference<604800)
        return `${Math.floor(difference/86400)} days ago`


    return created.toLocaleDateString(
        "en-PH",
        {
            month:"short",
            day:"numeric",
            year:"numeric"
        }
    )

}


/* OUTSIDE CLICK */
function handleOutsideClick(event){

    const wrapper=
        event.target.closest(
            ".notification-wrapper"
        )

    if(!wrapper){

        showNotifications.value=false

    }

}


/* LOGOUT */
async function logout(){

    if(
        !confirm(
            "Are you sure you want to logout?"
        )
    )return


    try{

        await api.post("/logout")

    }
    catch(error){

        console.error(
            "Logout error:",
            error
        )

    }
    finally{

        localStorage.removeItem("token")
        localStorage.removeItem("user")

        delete api.defaults.headers.common[
            "Authorization"
        ]

        sidebarOpen.value=false

        router.replace("/login")

    }

}


/* LIFECYCLE */
onMounted(()=>{

    document.addEventListener(
        "click",
        handleOutsideClick
    )

    loadNotifications()

    notificationInterval=
        setInterval(
            loadNotifications,
            30000
        )

})


onBeforeUnmount(()=>{

    document.removeEventListener(
        "click",
        handleOutsideClick
    )

    if(notificationInterval){

        clearInterval(
            notificationInterval
        )

    }

})

</script>


<style scoped>

.registrar-container{
    display:flex;
    width:100%;
    min-height:100vh;
    overflow:hidden;
    background:#f5f7fb;
}

.main-content{
    flex:1;
    min-width:0;
    display:flex;
    flex-direction:column;
}

.topbar{
    height:80px;
    flex-shrink:0;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 25px;
    border-bottom:1px solid #e5e7eb;
    box-shadow:0 2px 10px rgba(0,0,0,.05);
}

.page-info{
    display:flex;
    align-items:center;
    gap:12px;
}

.page-info h5{
    margin:0;
    font-size:18px;
    font-weight:800;
    color:#064E2A;
}

.page-info small{
    color:#6b7280;
    font-size:12px;
}

.menu-toggle{
    display:none;
    border:none;
    background:#f3f4f6;
    width:40px;
    height:40px;
    border-radius:10px;
    font-size:22px;
    color:#064E2A;
    cursor:pointer;
}

.top-actions{
    display:flex;
    align-items:center;
    gap:15px;
}

.notification-wrapper{
    position:relative;
}

.icon-btn{
    position:relative;
    width:40px;
    height:40px;
    border:none;
    border-radius:10px;
    background:#f3f4f6;
    color:#064E2A;
    font-size:19px;
    cursor:pointer;
}

.icon-btn:hover{
    background:#e5f5ec;
}

.notification-dot{
    position:absolute;
    top:6px;
    right:6px;
    width:8px;
    height:8px;
    background:#dc2626;
    border:2px solid #fff;
    border-radius:50%;
}

.notification-count{
    position:absolute;
    top:-7px;
    right:-7px;
    min-width:19px;
    height:19px;
    padding:0 5px;
    border-radius:20px;
    background:#dc2626;
    color:#fff;
    font-size:10px;
    font-weight:700;
    display:flex;
    align-items:center;
    justify-content:center;
}

.notification-panel{
    position:absolute;
    top:50px;
    right:0;
    width:380px;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:14px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);
    overflow:hidden;
    z-index:9999;
}

.notification-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    padding:13px 15px;
    border-bottom:1px solid #e5e7eb;
}

.notification-header strong{
    display:block;
    color:#064E2A;
    font-size:14px;
}

.notification-header small{
    color:#9ca3af;
    font-size:10px;
}

.header-actions{
    display:flex;
    align-items:center;
    gap:8px;
}

.refresh-btn{
    width:30px;
    height:30px;
    border:none;
    border-radius:7px;
    background:#f3f4f6;
    color:#064E2A;
    cursor:pointer;
}

.refresh-btn:hover{
    background:#e5f5ec;
}

.mark-read-btn{
    border:none;
    background:none;
    color:#0B6B3A;
    font-size:10px;
    font-weight:600;
    cursor:pointer;
    white-space:nowrap;
}

.mark-read-btn:hover{
    text-decoration:underline;
}

.notification-list{
    max-height:370px;
    overflow-y:auto;
}

.notification-item{
    position:relative;
    display:flex;
    align-items:flex-start;
    gap:10px;
    padding:11px 15px;
    border-bottom:1px solid #f1f5f9;
    cursor:pointer;
    transition:.15s;
}

.notification-item:hover{
    background:#f8faf9;
}

.notification-item.unread{
    background:#f0fdf4;
}

.notification-icon{
    width:36px;
    height:36px;
    min-width:36px;
    border-radius:9px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
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

.notification-icon.paid{
    background:#d1fae5;
    color:#047857;
}

.notification-icon.enrolled{
    background:#ede9fe;
    color:#7c3aed;
}

.notification-icon.requirements{
    background:#fef3c7;
    color:#b45309;
}

.notification-content{
    min-width:0;
    padding-right:8px;
}

.notification-content strong{
    display:block;
    color:#374151;
    font-size:12px;
    line-height:1.3;
}

.notification-content p{
    margin:2px 0;
    color:#6b7280;
    font-size:11px;
    line-height:1.35;
}

.notification-content small{
    color:#9ca3af;
    font-size:9px;
}

.unread-dot{
    position:absolute;
    top:17px;
    right:12px;
    width:7px;
    height:7px;
    background:#16a34a;
    border-radius:50%;
}

.notification-loading{
    height:150px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    gap:8px;
    color:#9ca3af;
    font-size:11px;
}

.spinner{
    width:22px;
    height:22px;
    border:3px solid #dcfce7;
    border-top-color:#0B6B3A;
    border-radius:50%;
    animation:spin .7s linear infinite;
}

.spin{
    animation:spin .7s linear infinite;
}

@keyframes spin{
    to{
        transform:rotate(360deg);
    }
}

.notification-empty{
    min-height:160px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    color:#9ca3af;
    padding:15px;
}

.notification-empty i{
    font-size:30px;
    margin-bottom:7px;
}

.notification-empty strong{
    color:#374151;
    font-size:12px;
}

.notification-empty span{
    margin-top:2px;
    font-size:10px;
}

.notification-footer{
    padding:9px;
    border-top:1px solid #e5e7eb;
    text-align:center;
}

.notification-footer button{
    border:none;
    background:#ecfdf5;
    color:#166534;
    padding:8px 15px;
    border-radius:7px;
    font-size:11px;
    font-weight:600;
    cursor:pointer;
}

.notification-footer button:hover{
    background:#d1fae5;
}

.profile{
    display:flex;
    align-items:center;
    gap:10px;
    padding-left:15px;
    border-left:1px solid #ddd;
}

.avatar{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#064E2A;
    color:#fff;
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
}

.user-info small{
    font-size:10px;
    color:#6b7280;
}

.logout-btn{
    width:36px;
    height:36px;
    border:none;
    border-radius:9px;
    background:#fee2e2;
    color:#dc2626;
    cursor:pointer;
}

.logout-btn:hover{
    background:#dc2626;
    color:#fff;
}

.page-content{
    flex:1;
    padding:25px;
    width:100%;
    overflow-x:auto;
}

.overlay{
    display:none;
}

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
        left:10px;
        right:10px;
        width:auto;
    }

    .page-content{
        padding:15px;
    }

    .logout-btn{
        width:34px;
        height:34px;
    }

}

</style>
