<template>
<div class="student-layout">
    <div v-if="mobileMenu" class="overlay" @click="mobileMenu=false"></div>

    <aside class="sidebar" :class="{show:mobileMenu}">
        <div class="sidebar-header">
            <div class="logo-circle">
                <img :src="SFXCLogo" alt="SFXC Logo">
            </div>
            <h5>Student Portal</h5>
            <p>Enrollment System</p>
        </div>

        <nav class="menu">
            <router-link to="/student/dashboard" class="menu-item" active-class="active" @click="mobileMenu=false">
                <i class="bi bi-speedometer2"></i>Dashboard
            </router-link>
            <router-link to="/student/profile" class="menu-item" active-class="active" @click="mobileMenu=false">
                <i class="bi bi-person"></i>Profile
            </router-link>
            <router-link to="/student/enrollment" class="menu-item" active-class="active" @click="mobileMenu=false">
                <i class="bi bi-journal-text"></i>Enrollment
            </router-link>
            <router-link to="/student/payment" class="menu-item" active-class="active" @click="mobileMenu=false">
                <i class="bi bi-wallet2"></i>Payment
            </router-link>
            <router-link v-if="showDocuments" to="/student/receipt" class="menu-item" active-class="active" @click="mobileMenu=false">
                <i class="bi bi-receipt"></i>Receipt
            </router-link>
            <router-link v-if="showDocuments" to="/student/documents" class="menu-item" active-class="active" @click="mobileMenu=false">
                <i class="bi bi-folder2-open"></i>Documents
            </router-link>
        </nav>

        <div class="sidebar-footer">
            <button class="logout-btn" @click="logout" :disabled="isLoggingOut">
                <span v-if="isLoggingOut" class="spinner-border spinner-border-sm me-2"></span>
                {{isLoggingOut?'Logging out...':'Logout'}}
            </button>
        </div>
    </aside>

    <div class="main-area">
        <header class="top-navbar">
            <button class="mobile-btn" @click="mobileMenu=true">
                <i class="bi bi-list"></i>
            </button>

            <div><h5>{{pageTitle}}</h5></div>

            <div class="profile-area">

                <!-- NOTIFICATIONS -->
                <div class="notification-wrapper">
                    <button class="notification-btn" @click.stop="toggleNotifications">
                        <i class="bi bi-bell"></i>
                        <span v-if="unreadCount>0" class="notification-dot"></span>
                        <span v-if="unreadCount>0" class="notification-count">
                            {{unreadCount>9?'9+':unreadCount}}
                        </span>
                    </button>

                    <div v-if="showNotifications" class="notification-dropdown" @click.stop>

                        <div class="notification-header">
                            <div>
                                <strong>Notifications</strong>
                                <small>{{unreadCount}} unread</small>
                            </div>

                            <div class="notification-actions">
                                <button
                                    v-if="unreadCount>0"
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
                                    <i class="bi" :class="loadingNotifications?'bi-arrow-repeat spin':'bi-arrow-clockwise'"></i>
                                </button>
                            </div>
                        </div>

                        <div v-if="loadingNotifications" class="notification-loading">
                            <div class="notification-spinner"></div>
                            <span>Loading notifications...</span>
                        </div>

                        <div v-else-if="notifications.length>0" class="notification-list">
                            <div
                                v-for="notification in notifications"
                                :key="notification.id"
                                class="notification-item"
                                :class="[notification.type,{unread:!notification.read}]"
                                @click="handleNotification(notification)"
                            >
                                <div class="notification-icon">
                                    <i class="bi" :class="notification.icon"></i>
                                </div>

                                <div class="notification-content">
                                    <strong>{{notification.title}}</strong>
                                    <p>{{notification.message}}</p>
                                    <small>{{notification.time}}</small>
                                </div>

                                <span v-if="!notification.read" class="unread-dot"></span>
                            </div>
                        </div>

                        <div v-else class="notification-empty">
                            <i class="bi bi-bell-slash"></i>
                            <strong>No notifications</strong>
                            <span>You're all caught up.</span>
                        </div>

                    </div>
                </div>

                <div class="avatar">{{initials}}</div>

                <div class="student-info">
                    <strong class="student-name">{{fullName}}</strong>
                    <small>{{student.student_number||"No Student Number"}}</small>
                </div>

                <button class="dropdown-btn">
                    <i class="bi bi-chevron-down"></i>
                </button>

            </div>
        </header>

        <main class="content">
            <router-view/>
        </main>
    </div>
</div>
</template>

<script setup>
import {ref,computed,onMounted,onUnmounted} from "vue"
import {useRouter,useRoute} from "vue-router"
import api from "@/services/api"
import SFXCLogo from "@/assets/images/logo/sfxc-logo-only.png"

const router=useRouter()
const route=useRoute()

const mobileMenu=ref(false)
const student=ref({})
const enrollment=ref(null)
const showDocuments=ref(false)
const showNotifications=ref(false)
const notifications=ref([])
const isLoggingOut=ref(false)
const loadingNotifications=ref(false)

let notificationInterval=null

const fullName=computed(()=>{
    if(!student.value.first_name)return"Student"
    return`${student.value.first_name||""} ${student.value.last_name||""}`.trim()
})

const initials=computed(()=>{
    const first=student.value.first_name?.charAt(0)||""
    const last=student.value.last_name?.charAt(0)||""
    return`${first}${last}`.toUpperCase()
})

const pageTitle=computed(()=>{
    return route.meta.title||"Student Dashboard"
})

const unreadCount=computed(()=>{
    return notifications.value.filter(n=>!n.read).length
})

/* READ STORAGE */
function getReadNotifications(){
    try{
        return JSON.parse(
            localStorage.getItem("student_read_notifications")
        )||[]
    }catch{
        return[]
    }
}

function isRead(id){
    return getReadNotifications().includes(id)
}

/* LOAD STUDENT */
async function loadStudent(){
    try{
        const{data}=await api.get("/student/profile")

        student.value=data.student||{}
        enrollment.value=data.enrollment||null
        showDocuments.value=data.documents_available||false

        loadNotifications()
    }catch(error){
        console.error("Failed to load student:",error)
    }
}

/* NOTIFICATIONS */
function loadNotifications(){

    loadingNotifications.value=true

    try{

        const newNotifications=[]
        const current=enrollment.value
        const studentId=student.value.id||"student"

        /* PROFILE */
        if(!student.value.first_name){

            newNotifications.push({
                id:`profile-${studentId}`,
                title:"Complete Your Profile",
                message:"Please complete your student profile to continue.",
                icon:"bi-person-fill",
                type:"info",
                route:"/student/profile",
                time:"Action required"
            })

        }

        /* NO ENROLLMENT */
        if(!current){

            newNotifications.push({
                id:`enrollment-start-${studentId}`,
                title:"Start Your Enrollment",
                message:"You have not submitted an enrollment application yet.",
                icon:"bi-journal-plus",
                type:"info",
                route:"/student/enrollment",
                time:"Action required"
            })

        }

        if(current){

            const status=
                String(current.status||"").toLowerCase()

            /* PENDING */
            if(status==="pending"){

                newNotifications.push({
                    id:`pending-${current.id}`,
                    title:"Application Under Review",
                    message:"Your enrollment application is currently being reviewed by the registrar.",
                    icon:"bi-hourglass-split",
                    type:"pending",
                    route:"/student/enrollment",
                    time:"Under review"
                })

            }

            /* APPROVED */
            if(status==="approved"){

                newNotifications.push({
                    id:`approved-${current.id}`,
                    title:"Enrollment Application Approved",
                    message:"Your enrollment application has been approved.",
                    icon:"bi-check-circle-fill",
                    type:"success",
                    route:"/student/payment",
                    time:"Action required"
                })

                newNotifications.push({
                    id:`payment-required-${current.id}`,
                    title:"Payment Required",
                    message:"Please proceed with your enrollment payment.",
                    icon:"bi-credit-card-fill",
                    type:"payment",
                    route:"/student/payment",
                    time:"Action required"
                })

            }

            /* REJECTED */
            if(status==="rejected"){

                newNotifications.push({
                    id:`rejection-${current.id}`,
                    title:"Enrollment Application Rejected",
                    message:current.rejection_reason||
                        "Your enrollment application was rejected. Please review and update your application.",
                    icon:"bi-exclamation-triangle-fill",
                    type:"rejected",
                    route:"/student/enrollment",
                    time:"Action required"
                })

            }

            /* PAID */
            if(status==="paid"){

                newNotifications.push({
                    id:`payment-success-${current.id}`,
                    title:"Payment Successful",
                    message:"Your enrollment payment has been successfully recorded.",
                    icon:"bi-check-circle-fill",
                    type:"success",
                    route:"/student/receipt",
                    time:"Payment completed"
                })

            }

            /* ENROLLED */
            if(status==="enrolled"){

                newNotifications.push({
                    id:`enrolled-${current.id}`,
                    title:"Enrollment Completed",
                    message:"Congratulations! Your enrollment has been successfully completed.",
                    icon:"bi-mortarboard-fill",
                    type:"enrolled",
                    route:"/student/receipt",
                    time:"Completed"
                })

            }

            /* MISSING REQUIREMENTS */
            const missing=
                current.missing_requirements||
                current.requirements_missing||
                current.incomplete_requirements

            if(
                missing===true||
                (Array.isArray(missing)&&missing.length>0)||
                (typeof missing==="string"&&missing.length>0)
            ){

                newNotifications.push({
                    id:`requirements-${current.id}`,
                    title:"Incomplete Requirements",
                    message:"Some enrollment requirements are still incomplete. Please review your documents.",
                    icon:"bi-file-earmark-x-fill",
                    type:"document",
                    route:"/student/documents",
                    time:"Action required"
                })

            }

            /* DOCUMENTS AVAILABLE */
            if(
                showDocuments.value&&
                !(
                    missing===true||
                    (Array.isArray(missing)&&missing.length>0)||
                    (typeof missing==="string"&&missing.length>0)
                )
            ){

                newNotifications.push({
                    id:`documents-${current.id}`,
                    title:"Enrollment Documents Available",
                    message:"Your enrollment documents are now available.",
                    icon:"bi-folder-check",
                    type:"document",
                    route:"/student/documents",
                    time:"View documents"
                })

            }

        }

        /* APPLY READ STATUS */
        newNotifications.forEach(notification=>{
            notification.read=isRead(notification.id)
        })

        notifications.value=newNotifications

    }finally{

        loadingNotifications.value=false

    }
}

/* TOGGLE */
function toggleNotifications(){
    showNotifications.value=!showNotifications.value

    if(showNotifications.value){
        loadStudent()
    }
}

/* MARK SINGLE READ */
function markAsRead(notification){

    notification.read=true

    const read=getReadNotifications()

    if(!read.includes(notification.id)){
        read.push(notification.id)
    }

    localStorage.setItem(
        "student_read_notifications",
        JSON.stringify(read)
    )
}

/* MARK ALL READ */
function markAllAsRead(){

    const read=getReadNotifications()

    notifications.value.forEach(notification=>{

        notification.read=true

        if(!read.includes(notification.id)){
            read.push(notification.id)
        }

    })

    localStorage.setItem(
        "student_read_notifications",
        JSON.stringify(read)
    )
}

/* HANDLE */
function handleNotification(notification){

    markAsRead(notification)
    showNotifications.value=false

    if(notification.route){
        router.push(notification.route)
    }

}

/* OUTSIDE CLICK */
function closeNotifications(event){

    if(!event.target.closest(".notification-wrapper")){
        showNotifications.value=false
    }

}

/* LOGOUT */
async function logout(){

    if(isLoggingOut.value)return

    isLoggingOut.value=true

    try{
        await api.post("/logout")
    }catch(error){
        console.error("Logout error:",error)
    }finally{

        localStorage.removeItem("token")
        localStorage.removeItem("user")

        delete api.defaults.headers.common["Authorization"]

        router.push("/login")

        isLoggingOut.value=false
    }

}

/* LIFECYCLE */
onMounted(()=>{

    loadStudent()

    document.addEventListener(
        "click",
        closeNotifications
    )

    notificationInterval=setInterval(
        loadStudent,
        30000
    )

})

onUnmounted(()=>{

    document.removeEventListener(
        "click",
        closeNotifications
    )

    if(notificationInterval){
        clearInterval(notificationInterval)
    }

})
</script>

<style scoped>
.student-layout{min-height:100vh;background:#f3f8f5}
.student-name{text-transform:capitalize;font-size:16px;font-weight:700;color:#064E2A}

/* SIDEBAR */
.sidebar{position:fixed;left:0;top:0;bottom:0;width:260px;background:linear-gradient(180deg,#064E2A,#0B6B3A);color:white;padding:20px;display:flex;flex-direction:column;z-index:1000;overflow:hidden}
.sidebar-header{text-align:center;padding-bottom:30px;border-bottom:1px solid rgba(255,255,255,.2)}
.logo-circle{height:75px;width:75px;margin:auto;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;border:4px solid #9cffc8;overflow:hidden}
.logo-circle img{width:100%;height:100%;object-fit:contain;padding:5px}
.sidebar-header h5{margin-top:15px;font-weight:700}
.sidebar-header p{font-size:13px;opacity:.7;margin-bottom:0}

/* MENU */
.menu{margin-top:25px;flex:1;overflow-y:auto;padding-right:5px}
.menu::-webkit-scrollbar{width:5px}
.menu::-webkit-scrollbar-thumb{background:rgba(255,255,255,.3);border-radius:10px}
.menu-item{display:flex;gap:15px;align-items:center;padding:14px;margin-bottom:10px;border-radius:15px;color:white;text-decoration:none;transition:.3s}
.menu-item i{font-size:20px}
.menu-item:hover{background:rgba(255,255,255,.15)}
.menu-item.active{background:white;color:#0B6B3A;font-weight:bold}

/* FOOTER */
.sidebar-footer{margin-top:auto;border-top:1px solid rgba(255,255,255,.2);padding-top:20px;flex-shrink:0}
.logout-btn{width:100%;height:45px;border:none;border-radius:15px;background:rgba(255,255,255,.15);color:white;font-weight:600}
.logout-btn:hover{background:white;color:#0B6B3A}

/* MAIN */
.main-area{margin-left:260px}
.top-navbar{height:80px;background:white;display:flex;align-items:center;justify-content:space-between;padding:0 35px;box-shadow:0 5px 20px rgba(0,0,0,.05);position:sticky;top:0;z-index:500}
.top-navbar h5{font-weight:700;margin:0}

/* PROFILE */
.profile-area{display:flex;align-items:center;gap:15px}
.avatar{height:45px;width:45px;border-radius:50%;background:#0B6B3A;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold}
.student-info{display:flex;flex-direction:column}
.student-info small{color:#6b7280}
.dropdown-btn{border:none;background:none}

/* NOTIFICATION */
.notification-wrapper{position:relative}
.notification-btn{position:relative;border:none;background:none;font-size:22px;color:#064E2A;cursor:pointer;width:42px;height:42px;border-radius:10px;display:flex;align-items:center;justify-content:center;transition:.2s}
.notification-btn:hover{background:#f0f5f2}
.notification-dot{position:absolute;top:6px;right:6px;width:9px;height:9px;background:#dc2626;border:2px solid white;border-radius:50%}
.notification-count{position:absolute;top:-6px;right:-7px;min-width:18px;height:18px;padding:0 5px;border-radius:20px;background:#dc2626;color:white;font-size:9px;font-weight:700;display:flex;align-items:center;justify-content:center}
.notification-dropdown{position:absolute;top:55px;right:0;width:380px;background:white;border-radius:15px;box-shadow:0 15px 40px rgba(0,0,0,.15);border:1px solid #e5e7eb;overflow:hidden;z-index:2000}
.notification-header{display:flex;align-items:center;justify-content:space-between;padding:13px 16px;border-bottom:1px solid #e5e7eb}
.notification-header strong{display:block;color:#064E2A;font-size:14px}
.notification-header small{display:block;color:#9ca3af;font-size:10px;margin-top:2px}
.notification-actions{display:flex;align-items:center;gap:7px}
.mark-read-btn{border:none;background:none;color:#0B6B3A;font-size:10px;font-weight:600;cursor:pointer;white-space:nowrap}
.mark-read-btn:hover{text-decoration:underline}
.refresh-btn{width:30px;height:30px;border:none;border-radius:7px;background:#f3f4f6;color:#064E2A;cursor:pointer}
.refresh-btn:hover{background:#e5f5ec}
.refresh-btn:disabled{opacity:.5;cursor:not-allowed}
.notification-list{max-height:370px;overflow-y:auto}
.notification-item{position:relative;display:flex;gap:10px;padding:12px 15px;border-bottom:1px solid #f0f0f0;cursor:pointer;transition:.2s}
.notification-item:hover{background:#f8faf9}
.notification-item.unread{background:#f0fdf4}
.notification-icon{width:38px;height:38px;min-width:38px;border-radius:10px;background:#e7f7ee;color:#0B6B3A;display:flex;align-items:center;justify-content:center;font-size:16px}
.notification-item.pending .notification-icon{background:#fef3c7;color:#b45309}
.notification-item.success .notification-icon{background:#dcfce7;color:#15803d}
.notification-item.payment .notification-icon{background:#dbeafe;color:#2563eb}
.notification-item.rejected .notification-icon{background:#fee2e2;color:#dc2626}
.notification-item.rejected strong{color:#b91c1c}
.notification-item.document .notification-icon{background:#fef3c7;color:#b45309}
.notification-item.enrolled .notification-icon{background:#ede9fe;color:#7c3aed}
.notification-content{flex:1;min-width:0;padding-right:7px}
.notification-content strong{display:block;font-size:12px;color:#1f2937;margin-bottom:3px;line-height:1.3}
.notification-content p{margin:0;font-size:11px;color:#6b7280;line-height:1.4}
.notification-content small{display:block;margin-top:5px;font-size:9px;color:#9ca3af}
.unread-dot{position:absolute;right:12px;top:18px;width:7px;height:7px;background:#16a34a;border-radius:50%}
.notification-loading{height:150px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;color:#9ca3af;font-size:11px}
.notification-spinner{width:22px;height:22px;border:3px solid #dcfce7;border-top-color:#0B6B3A;border-radius:50%;animation:spin .7s linear infinite}
.spin{animation:spin .7s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.notification-empty{padding:40px 20px;text-align:center;color:#9ca3af;display:flex;flex-direction:column;align-items:center}
.notification-empty i{font-size:30px;margin-bottom:7px}
.notification-empty strong{color:#374151;font-size:12px}
.notification-empty span{font-size:10px;margin-top:2px}

/* CONTENT */
.content{padding:30px;min-height:calc(100vh - 80px)}

/* MOBILE */
.mobile-btn{display:none;border:none;background:none;font-size:28px;color:#0B6B3A}
.overlay{display:none}

@media(max-width:900px){
    .sidebar{transform:translateX(-100%);transition:.3s}
    .sidebar.show{transform:translateX(0)}
    .main-area{margin-left:0}
    .mobile-btn{display:block}
    .student-info{display:none}
    .overlay{display:block;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:900}
}

@media(max-width:576px){
    .top-navbar{height:70px;padding:0 15px}
    .notification-dropdown{position:fixed;top:70px;left:10px;right:10px;width:auto}
    .content{padding:15px}
    .dropdown-btn{display:none}
}
</style>