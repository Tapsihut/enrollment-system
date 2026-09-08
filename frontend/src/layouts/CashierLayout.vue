<template>
<div class="cashier-container">
    <CashierSidebar :class="{ open: sidebarOpen }"/>
    <div v-if="sidebarOpen" class="overlay" @click="sidebarOpen=false"></div>

    <div class="main-content">
        <header class="topbar">
            <div class="page-info">
                <button class="menu-toggle" @click="sidebarOpen=!sidebarOpen">
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <h5>Cashier Portal</h5>
                    <small>Manage student payments and collections</small>
                </div>
            </div>

            <div class="top-actions">
                <!-- NOTIFICATIONS -->
                <div class="notification-wrapper">
                    <button class="icon-btn" @click.stop="toggleNotifications">
                        <i class="bi bi-bell"></i>
                        <span v-if="notifications.length" class="notification-dot"></span>
                        <span v-if="notifications.length" class="notification-count">
                            {{ notifications.length > 9 ? "9+" : notifications.length }}
                        </span>
                    </button>

                    <div v-if="showNotifications" class="notification-dropdown" @click.stop>
                        <div class="notification-header">
                            <div>
                                <strong>Notifications</strong>
                                <small>Cashier payment updates</small>
                            </div>
                            <button class="refresh-notification" @click="loadNotifications" :disabled="notificationLoading">
                                <i class="bi" :class="notificationLoading?'bi-arrow-repeat spin':'bi-arrow-clockwise'"></i>
                            </button>
                        </div>

                        <div v-if="notificationLoading" class="notification-loading">
                            <div class="small-spinner"></div>
                            <span>Loading...</span>
                        </div>

                        <div v-else-if="notifications.length" class="notification-list">
                            <div v-for="notification in notifications"
                                :key="notification.id"
                                class="notification-item"
                                @click="handleNotification(notification)">

                                <div class="notification-icon" :class="notification.type">
                                    <i :class="notification.icon"></i>
                                </div>

                                <div class="notification-content">
                                    <strong>{{ notification.title }}</strong>
                                    <p>{{ notification.message }}</p>
                                    <small>{{ notification.time }}</small>
                                </div>
                            </div>
                        </div>

                        <div v-else class="notification-empty">
                            <i class="bi bi-bell-slash"></i>
                            <strong>No notifications</strong>
                            <span>You're all caught up.</span>
                        </div>

                        <div class="notification-footer">
                            <button v-if="notifications.length" @click="markAllAsRead">
                                Mark all as read
                            </button>
                            <button @click="goToPayments">
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
                        <strong>{{ user?.name || "Cashier" }}</strong>
                        <small>Cashier</small>
                    </div>
                    <button class="logout-btn" @click="logout">
                        <i class="bi bi-box-arrow-right"></i>
                    </button>
                </div>
            </div>
        </header>

        <main class="page-content">
            <router-view/>
        </main>
    </div>
</div>
</template>

<script setup>
import { ref,onMounted,onUnmounted } from "vue"
import { useRouter } from "vue-router"
import api from "@/services/api"
import CashierSidebar from "@/components/layout/CashierSidebar.vue"

const router=useRouter()
const sidebarOpen=ref(false)

const user=ref(JSON.parse(localStorage.getItem("user"))||{})

const notifications=ref([])
const showNotifications=ref(false)
const notificationLoading=ref(false)

let notificationInterval=null

/* NOTIFICATION TOGGLE */
function toggleNotifications(){
    showNotifications.value=!showNotifications.value
    if(showNotifications.value) loadNotifications()
}

/* LOAD NOTIFICATIONS */
async function loadNotifications(){
    if(notificationLoading.value) return

    notificationLoading.value=true

    try{
        const {data}=await api.get("/cashier/dashboard")

        const statistics=data.statistics||{}
        const recent=data.recent||[]
        const result=[]

        /* PENDING PAYMENTS */
        const pending=Number(statistics.pending||0)

        if(pending>0){
            result.push({
                id:"pending",
                type:"pending",
                icon:"bi bi-clock-fill",
                title:"Pending Payments",
                message:`${pending} payment transaction(s) are waiting for payment.`,
                time:"Requires attention",
                action:"payments"
            })
        }

        /* FAILED PAYMENTS */
        const failed=Number(statistics.failed||0)

        const latestFailed=recent.find(payment =>
            String(payment.status||"").toLowerCase()==="failed"
        )

        if(latestFailed){
            result.push({
                id:`failed-${latestFailed.id}`,
                type:"failed",
                icon:"bi bi-x-circle-fill",
                title:"Payment Failed",
                message:`${getStudentName(latestFailed.student)} has a failed payment transaction.`,
                time:formatDate(latestFailed.updated_at||latestFailed.created_at),
                action:"payment",
                paymentId:latestFailed.id
            })
        }else if(failed>0){
            result.push({
                id:"failed-summary",
                type:"failed",
                icon:"bi bi-x-circle-fill",
                title:"Failed Payments",
                message:`${failed} payment transaction(s) failed.`,
                time:"Requires attention",
                action:"payments"
            })
        }

        /* LATEST PAID */
        const latestPaid=recent.find(payment =>
            String(payment.status||"").toLowerCase()==="paid"
        )

        if(latestPaid){
            result.push({
                id:`paid-${latestPaid.id}`,
                type:"paid",
                icon:"bi bi-check-circle-fill",
                title:"Payment Received",
                message:`${getStudentName(latestPaid.student)} paid ₱${formatAmount(latestPaid.amount)}.`,
                time:formatDate(latestPaid.created_at),
                action:"payment",
                paymentId:latestPaid.id
            })
        }

        /* RECENT PAYMENT UPDATE */
        if(recent.length){
            const latest=recent[0]
            const status=String(latest.status||"").toLowerCase()

            if(status && status!=="paid" && status!=="failed"){
                result.push({
                    id:`recent-${latest.id}`,
                    type:getPaymentType(latest.status),
                    icon:getPaymentIcon(latest.status),
                    title:"Recent Payment Update",
                    message:`${getStudentName(latest.student)} payment status is ${latest.status}.`,
                    time:formatDate(latest.updated_at||latest.created_at),
                    action:"payment",
                    paymentId:latest.id
                })
            }
        }

        /* TODAY'S COLLECTION */
        const todayCollection=Number(statistics.today_collection||0)

        if(todayCollection>0){
            result.push({
                id:"today-collection",
                type:"paid",
                icon:"bi bi-cash-stack",
                title:"Today's Collection",
                message:`Today's collection is ₱${formatAmount(todayCollection)}.`,
                time:"Today",
                action:"payments"
            })
        }

        notifications.value=result
    }catch(error){
        console.error("Failed to load cashier notifications:",error)
        notifications.value=[]
    }finally{
        notificationLoading.value=false
    }
}

/* STUDENT NAME */
function getStudentName(student){
    if(!student) return "Unknown Student"

    return `${student.first_name||""} ${student.last_name||""}`.trim()||"Unknown Student"
}

/* AMOUNT */
function formatAmount(amount){
    return Number(amount||0).toLocaleString("en-PH",{
        minimumFractionDigits:2,
        maximumFractionDigits:2
    })
}

/* PAYMENT TYPE */
function getPaymentType(status){
    const value=String(status||"").toLowerCase()

    if(value==="paid") return "paid"
    if(value==="failed") return "failed"

    return "pending"
}

/* PAYMENT ICON */
function getPaymentIcon(status){
    const value=String(status||"").toLowerCase()

    if(value==="paid") return "bi bi-check-circle-fill"
    if(value==="failed") return "bi bi-x-circle-fill"

    return "bi bi-clock-fill"
}

/* DATE */
function formatDate(date){
    if(!date) return "Recently"

    const parsed=new Date(date)

    if(Number.isNaN(parsed.getTime())) return "Recently"

    return parsed.toLocaleDateString("en-PH",{
        month:"short",
        day:"numeric",
        year:"numeric"
    })
}

/* CLICK NOTIFICATION */
function handleNotification(notification){
    showNotifications.value=false

    if(notification.action==="payments"){
        router.push("/cashier/payments")
        return
    }

    if(notification.action==="payment" && notification.paymentId){
        router.push(`/cashier/payments/${notification.paymentId}`)
    }
}

/* MARK READ */
function markAllAsRead(){
    notifications.value=[]
    showNotifications.value=false
}

/* VIEW PAYMENTS */
function goToPayments(){
    showNotifications.value=false
    router.push("/cashier/payments")
}

/* OUTSIDE CLICK */
function handleOutsideClick(event){
    if(!event.target.closest(".notification-wrapper")){
        showNotifications.value=false
    }
}

/* LOGOUT */
async function logout(){
    if(!confirm("Are you sure you want to logout?")) return

    try{
        await api.post("/logout")
    }catch(error){
        console.error("Logout error:",error)
    }finally{
        localStorage.removeItem("token")
        localStorage.removeItem("user")
        sidebarOpen.value=false
        router.replace("/login")
    }
}

/* LIFECYCLE */
onMounted(()=>{
    document.addEventListener("click",handleOutsideClick)

    loadNotifications()

    notificationInterval=setInterval(()=>{
        loadNotifications()
    },30000)
})

onUnmounted(()=>{
    document.removeEventListener("click",handleOutsideClick)

    if(notificationInterval){
        clearInterval(notificationInterval)
    }
})
</script>

<style scoped>
.cashier-container{
    display:flex;width:100%;min-height:100vh;overflow:hidden;background:#f5f7fb;
}
.main-content{
    flex:1;min-width:0;display:flex;flex-direction:column;
}
.topbar{
    height:80px;flex-shrink:0;background:#fff;display:flex;align-items:center;justify-content:space-between;
    padding:0 25px;border-bottom:1px solid #e5e7eb;box-shadow:0 2px 10px rgba(0,0,0,.05);
}
.page-info{
    display:flex;align-items:center;gap:12px;
}
.page-info h5{
    margin:0;font-size:18px;font-weight:800;color:#064E2A;
}
.page-info small{
    color:#6b7280;font-size:12px;
}
.menu-toggle{
    display:none;border:none;background:#f3f4f6;width:40px;height:40px;border-radius:10px;
    font-size:22px;color:#064E2A;
}
.top-actions{
    display:flex;align-items:center;gap:15px;
}
.notification-wrapper{
    position:relative;
}
.icon-btn{
    position:relative;width:40px;height:40px;border:none;border-radius:10px;
    background:#f3f4f6;color:#064E2A;font-size:19px;cursor:pointer;
}
.icon-btn:hover{
    background:#e5f5ec;
}
.notification-dot{
    position:absolute;top:7px;right:7px;width:7px;height:7px;background:#dc2626;border-radius:50%;
}
.notification-count{
    position:absolute;top:-7px;right:-7px;min-width:19px;height:19px;padding:0 5px;
    border-radius:20px;background:#dc2626;color:#fff;font-size:10px;font-weight:700;
    display:flex;align-items:center;justify-content:center;
}
.notification-dropdown{
    position:absolute;top:50px;right:0;width:360px;background:#fff;border-radius:15px;
    box-shadow:0 15px 40px rgba(0,0,0,.15);border:1px solid #e5e7eb;overflow:hidden;z-index:9999;
}
.notification-header{
    display:flex;align-items:center;justify-content:space-between;padding:15px 17px;border-bottom:1px solid #e5e7eb;
}
.notification-header strong{
    display:block;color:#064E2A;font-size:15px;
}
.notification-header small{
    display:block;margin-top:2px;color:#9ca3af;font-size:11px;
}
.refresh-notification{
    width:32px;height:32px;border:none;border-radius:8px;background:#f3f4f6;color:#064E2A;cursor:pointer;
}
.refresh-notification:hover{
    background:#e5f5ec;
}
.notification-list{
    max-height:350px;overflow-y:auto;
}
.notification-item{
    display:flex;gap:12px;padding:12px 17px;border-bottom:1px solid #f1f5f9;
    cursor:pointer;transition:.2s;
}
.notification-item:hover{
    background:#f8fafc;
}
.notification-icon{
    width:38px;height:38px;min-width:38px;border-radius:10px;display:flex;
    align-items:center;justify-content:center;font-size:16px;
}
.notification-icon.pending{
    background:#fef3c7;color:#b45309;
}
.notification-icon.paid{
    background:#dcfce7;color:#15803d;
}
.notification-icon.failed{
    background:#fee2e2;color:#dc2626;
}
.notification-content{
    min-width:0;
}
.notification-content strong{
    display:block;color:#374151;font-size:13px;
}
.notification-content p{
    margin:3px 0;color:#6b7280;font-size:12px;line-height:1.4;
}
.notification-content small{
    color:#9ca3af;font-size:10px;
}
.notification-loading{
    height:150px;display:flex;align-items:center;justify-content:center;gap:8px;color:#6b7280;font-size:12px;
}
.small-spinner{
    width:18px;height:18px;border:2px solid #d1fae5;border-top-color:#166534;
    border-radius:50%;animation:spin .7s linear infinite;
}
.notification-empty{
    min-height:170px;display:flex;flex-direction:column;align-items:center;justify-content:center;
    color:#9ca3af;padding:20px;
}
.notification-empty i{
    font-size:30px;margin-bottom:8px;
}
.notification-empty strong{
    color:#374151;font-size:13px;
}
.notification-empty span{
    margin-top:3px;font-size:11px;
}
.notification-footer{
    display:flex;gap:6px;padding:10px;border-top:1px solid #e5e7eb;
}
.notification-footer button{
    flex:1;border:none;background:#ecfdf5;color:#166534;padding:9px 6px;
    border-radius:8px;font-size:11px;font-weight:600;cursor:pointer;
}
.notification-footer button:hover{
    background:#d1fae5;
}
.spin{
    animation:spin 1s linear infinite;
}
@keyframes spin{
    from{transform:rotate(0deg)}
    to{transform:rotate(360deg)}
}
.profile{
    display:flex;align-items:center;gap:10px;padding-left:15px;border-left:1px solid #ddd;
}
.avatar{
    width:40px;height:40px;border-radius:50%;background:#064E2A;color:#fff;
    display:flex;align-items:center;justify-content:center;font-size:18px;
}
.user-info{
    display:flex;flex-direction:column;
}
.user-info strong{
    font-size:13px;
}
.user-info small{
    font-size:11px;color:#6b7280;
}
.logout-btn{
    width:36px;height:36px;border:none;border-radius:9px;background:#fee2e2;color:#dc2626;cursor:pointer;
}
.logout-btn:hover{
    background:#dc2626;color:#fff;
}
.page-content{
    flex:1;padding:25px;width:100%;overflow-x:auto;
}
.overlay{
    display:none;
}

@media(max-width:992px){
    .menu-toggle{
        display:flex;align-items:center;justify-content:center;
    }
    :deep(.sidebar){
        position:fixed;top:0;left:-270px;width:270px;height:100vh;
        z-index:1000;transition:.3s;
    }
    :deep(.sidebar.open){
        left:0;
    }
    .overlay{
        display:block;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:999;
    }
    .user-info{
        display:none;
    }
    .profile{
        border-left:none;padding-left:0;
    }
}

@media(max-width:576px){
    .topbar{
        height:70px;padding:0 15px;
    }
    .page-info h5{
        font-size:16px;
    }
    .page-info small{
        display:none;
    }
    .notification-dropdown{
        position:fixed;top:70px;left:15px;right:15px;width:auto;
    }
    .page-content{
        padding:15px;
    }
    .logout-btn{
        width:34px;height:34px;
    }
}
</style>
