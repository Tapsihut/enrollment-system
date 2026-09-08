<template>
<div class="success-page">
    <div class="success-card">
        <div class="icon-wrapper failed">✕</div>

        <h1 class="failed-title">Payment Failed</h1>

        <p class="subtitle">
            Your enrollment payment was cancelled or could not be completed.
        </p>

        <div class="payment-details">
            <div class="detail-item">
                <span>Payment Status</span>
                <strong class="failed-status">Failed</strong>
            </div>

            <div class="detail-item">
                <span>Payment Method</span>
                <strong>GCash</strong>
            </div>
        </div>

        <router-link
            to="/student/payment"
            class="confirm-btn"
        >
            Try Again
        </router-link>

        <p class="note">
            You may return to the payment page and try again.
        </p>
    </div>
</div>
</template>

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
.icon-wrapper{
    width:90px;
    height:90px;
    border-radius:50%;
    color:white;
    font-size:50px;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0 auto 20px;
}
.failed{
    background:#dc3545;
}
h1{
    font-weight:700;
    margin-bottom:10px;
}
.failed-title{
    color:#dc3545;
}
.subtitle{
    color:#6c757d;
    margin-bottom:30px;
}
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
.detail-item:last-child{
    border-bottom:none;
}
.detail-item span{
    color:#6c757d;
}
.detail-item strong{
    text-align:right;
    word-break:break-word;
}
.failed-status{
    color:#dc3545;
}
.confirm-btn{
    display:block;
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:#198754;
    color:white;
    font-size:18px;
    font-weight:600;
    text-decoration:none;
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
@media(max-width:500px){
    .success-page{
        padding:15px;
    }
    .success-card{
        width:100%;
        padding:25px 20px;
    }
    .detail-item{
        font-size:14px;
    }
}
</style>