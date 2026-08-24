<script setup>
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import axios from "@/services/api"

const router = useRouter()
const profileComplete = ref(false)

const alert = ref({ show:false, type:"", message:"" })

function showAlert(type,message){
  alert.value={show:true,type,message}
  setTimeout(()=>alert.value.show=false,3500)
}

async function checkProfile(){
  try{
    const {data}=await axios.get("/student/profile/completion")
    profileComplete.value=!!data.complete
  }catch(e){
    profileComplete.value=false
  }
}

function goEnrollment(){
  if(!profileComplete.value){
    showAlert("error","Please complete your profile before enrolling.")
    router.push("/student/profile")
    return
  }
  router.push("/student/enrollment")
}

function logout(){
  localStorage.removeItem("token")
  router.push("/login")
}

onMounted(checkProfile)
</script>

<template>
<div class="sidebar">
  <transition name="slide">
    <div v-if="alert.show" class="sidebar-alert" :class="alert.type">
      <i class="bi" :class="alert.type==='success'?'bi-check-circle-fill':'bi-exclamation-circle-fill'"></i>
      {{ alert.message }}
    </div>
  </transition>

  <div class="sidebar-header">
    <div class="logo-circle">SFXC</div>
    <h5>Student Portal</h5>
    <p>Enrollment System</p>
  </div>

  <div class="menu">
    <router-link to="/student/dashboard" class="menu-item" active-class="active"><i class="bi bi-speedometer2"></i><span>Dashboard</span></router-link>
    <router-link to="/student/profile" class="menu-item" active-class="active"><i class="bi bi-person"></i><span>Profile</span></router-link>

    <div class="menu-item enrollment-item" @click="goEnrollment">
      <i class="bi bi-journal-text"></i>
      <span>Enrollment</span>
      <i v-if="!profileComplete" class="bi bi-lock-fill lock-icon"></i>
    </div>

    <router-link to="/student/payment" class="menu-item" active-class="active"><i class="bi bi-wallet2"></i><span>Payment</span></router-link>
    <router-link to="/student/receipt" class="menu-item" active-class="active"><i class="bi bi-receipt"></i><span>Receipt</span></router-link>
  </div>

  <div class="sidebar-footer">
    <button class="logout-btn" @click="logout"><i class="bi bi-box-arrow-right"></i> Logout</button>
  </div>
</div>
</template>

<style scoped>
.sidebar{position:relative;height:100vh;width:260px;background:linear-gradient(180deg,#064E2A,#0B6B3A);color:#fff;display:flex;flex-direction:column;padding:20px}
.sidebar-header{text-align:center;padding:20px 0 35px;border-bottom:1px solid rgba(255,255,255,.2)}
.logo-circle{width:75px;height:75px;margin:auto;border-radius:50%;background:#fff;color:#0B6B3A;display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:900;border:4px solid #9cffc8}
.sidebar-header h5{margin-top:15px;font-size:20px;font-weight:700}
.sidebar-header p{font-size:13px;opacity:.7}
.menu{margin-top:30px;flex:1}
.menu-item{display:flex;align-items:center;gap:15px;padding:14px 18px;margin-bottom:10px;border-radius:15px;color:#fff;text-decoration:none;font-weight:500;transition:.3s;cursor:pointer}
.menu-item:hover{background:rgba(255,255,255,.15);transform:translateX(5px)}
.menu-item.active{background:#fff;color:#0B6B3A;font-weight:700}
.lock-icon{margin-left:auto;color:#FFD54F}
.sidebar-footer{padding-top:20px;border-top:1px solid rgba(255,255,255,.2)}
.logout-btn{width:100%;padding:13px;border:none;border-radius:15px;background:rgba(255,255,255,.15);color:#fff;font-weight:600}
.logout-btn:hover{background:#fff;color:#0B6B3A}
.sidebar-alert{position:absolute;top:15px;left:15px;right:15px;padding:12px;border-radius:12px;display:flex;gap:10px;align-items:center;font-weight:600}
.sidebar-alert.error{background:#fee2e2;color:#991b1b}
.sidebar-alert.success{background:#dcfce7;color:#166534}
.slide-enter-active,.slide-leave-active{transition:.3s}
.slide-enter-from,.slide-leave-to{opacity:0;transform:translateY(-10px)}
</style>
