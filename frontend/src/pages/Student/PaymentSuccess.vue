<template>
<div class="success-page">
    <div class="success-card">
        <div v-if="loading" class="loading">
            <div class="spinner"></div>
            <h3>Checking Payment...</h3>
            <p>Please wait while we verify your payment.</p>
        </div>

        <div v-else-if="paymentStatus==='Paid'||enrollmentStatus==='Enrolled'">
            <div class="icon-wrapper">✓</div>
            <h1>Payment Successful!</h1>
            <p class="subtitle">Your enrollment payment has been recorded successfully.</p>

            <div class="payment-details">
                <div class="detail-item">
                    <span>Payment Status</span>
                    <strong class="paid">{{ paymentStatus }}</strong>
                </div>
                <div class="detail-item">
                    <span>Amount Paid</span>
                    <strong>₱{{ formatAmount(amount) }}</strong>
                </div>
                <div class="detail-item">
                    <span>Payment Method</span>
                    <strong>{{ paymentMethod }}</strong>
                </div>
                <div class="detail-item" v-if="paymentReference">
                    <span>Payment Reference</span>
                    <strong>{{ paymentReference }}</strong>
                </div>
                <div class="detail-item">
                    <span>Enrollment Status</span>
                    <strong class="paid">{{ enrollmentStatus }}</strong>
                </div>
            </div>

            <button class="confirm-btn" @click="goToReceipt">
                View Receipt
            </button>

            <p class="note">Your payment has been successfully verified.</p>
        </div>

        <div v-else>
            <div class="pending-icon">⏳</div>
            <h1>Payment Processing</h1>
            <p class="subtitle">Your payment is still being verified by PayMongo.</p>

            <div class="payment-details">
                <div class="detail-item">
                    <span>Payment Status</span>
                    <strong class="pending">{{ paymentStatus||'Pending' }}</strong>
                </div>
                <div class="detail-item">
                    <span>Amount</span>
                    <strong>₱{{ formatAmount(amount) }}</strong>
                </div>
                <div class="detail-item">
                    <span>Payment Method</span>
                    <strong>{{ paymentMethod||'GCash' }}</strong>
                </div>
            </div>

            <button class="confirm-btn" @click="checkPayment">
                Check Payment Status
            </button>

            <p class="note">Please wait while PayMongo confirms your payment.</p>
        </div>
    </div>
</div>
</template>

<script setup>
import { ref,onMounted } from "vue";
import axios from "@/services/api";

const loading=ref(true);
const paymentStatus=ref("");
const enrollmentStatus=ref("");
const amount=ref(0);
const paymentMethod=ref("");
const paymentReference=ref("");

const checkPayment=async()=>{
    try{
        loading.value=true;
        const response=await axios.get("/student/payment/info");
        console.log("Payment Status:",response.data);

        paymentStatus.value=response.data.payment_status||response.data.status||"";
        enrollmentStatus.value=response.data.enrollment_status||response.data.status||"";
        amount.value=response.data.amount||0;
        paymentMethod.value=response.data.payment_method||"GCash";
        paymentReference.value=response.data.payment_reference||"";
    }catch(error){
        console.log("Payment Check Error:",error);
    }finally{
        loading.value=false;
    }
};

const formatAmount=(value)=>{
    return Number(value||0).toLocaleString("en-PH",{
        minimumFractionDigits:2,
        maximumFractionDigits:2
    });
};

const goToReceipt=()=>{
    window.location.href="/student/receipt";
};

onMounted(()=>{
    checkPayment();
});
</script>

<style scoped>
.success-page{
    min-height:90vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#198754,#0f5132);
    padding:30px;
}
.success-card{
    background:white;
    width:450px;
    border-radius:25px;
    padding:40px;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.2);
}
.icon-wrapper,.pending-icon{
    width:90px;
    height:90px;
    border-radius:50%;
    color:white;
    font-size:55px;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 20px;
}
.icon-wrapper{background:#198754;}
.pending-icon{background:#ffc107;font-size:45px;}
h1{color:#198754;font-weight:700;margin-bottom:10px;}
.subtitle{color:#6c757d;margin-bottom:30px;}
.payment-details{
    background:#f8f9fa;
    border-radius:15px;
    padding:20px;
    margin-bottom:25px;
}
.detail-item{
    display:flex;
    justify-content:space-between;
    gap:15px;
    padding:12px 0;
    border-bottom:1px solid #ddd;
    text-align:left;
}
.detail-item:last-child{border-bottom:none;}
.detail-item span{color:#6c757d;}
.detail-item strong{
    text-align:right;
    word-break:break-word;
}
.paid{color:#198754;}
.pending{color:#d39e00;}
.confirm-btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#198754;
    color:white;
    font-size:18px;
    font-weight:600;
    cursor:pointer;
    transition:.3s;
}
.confirm-btn:hover{
    background:#146c43;
    transform:translateY(-2px);
}
.note{
    margin-top:20px;
    font-size:14px;
    color:#6c757d;
}
.loading{
    padding:30px 0;
    text-align:center;
}
.loading h3{color:#198754;margin:15px 0 8px;}
.loading p{color:#6c757d;}
.spinner{
    width:45px;
    height:45px;
    border:5px solid #ddd;
    border-top-color:#198754;
    border-radius:50%;
    animation:spin 1s linear infinite;
    margin:auto;
}
@keyframes spin{
    to{transform:rotate(360deg);}
}
@media(max-width:500px){
    .success-page{padding:15px;}
    .success-card{
        width:100%;
        padding:25px 20px;
    }
    .detail-item{
        font-size:14px;
    }
}
</style>