<template>
<div class="success-page">
<canvas ref="fireworksCanvas" class="fireworks-canvas"></canvas>

<div v-for="confetti in confettiPieces" :key="confetti.id" class="confetti" :style="{left:confetti.left+'%',animationDelay:confetti.delay+'s',animationDuration:confetti.duration+'s',transform:`rotate(${confetti.rotate}deg)`}">
{{confetti.symbol}}
</div>

<div class="success-card">

<div v-if="loading" class="loading">
<div class="spinner"></div>
<h3>Checking Payment...</h3>
<p>Please wait while we verify your payment.</p>
</div>

<div v-else-if="paymentStatus==='Paid'||enrollmentStatus==='Paid'||enrollmentStatus==='Processing'||enrollmentStatus==='Completed'" class="success-content">

<div class="celebration-icon">✓</div>
<div class="welcome-badge">⭐</div>

<h1>Congratulations! 🎉</h1>
<h2>Welcome to St. Francis Xavier College!</h2>

<div class="knights-message">
<span>🛡️</span>
<div>
<strong>Welcome, Xavier Knight!</strong>
<p>You are now part of the <strong>Xavier Knights</strong> community.</p>
</div>
</div>

<p class="subtitle">Your enrollment payment has been successfully received and recorded.</p>

<div class="payment-details">
<div class="detail-item">
<span>Payment Status</span>
<strong class="paid">{{paymentStatus||'Paid'}}</strong>
</div>

<div class="detail-item">
<span>Amount Paid</span>
<strong>₱{{formatAmount(amount)}}</strong>
</div>

<div class="detail-item">
<span>Payment Method</span>
<strong>{{paymentMethod||'GCash'}}</strong>
</div>

<div v-if="paymentReference" class="detail-item">
<span>Payment Reference</span>
<strong>{{paymentReference}}</strong>
</div>

<div class="detail-item">
<span>Enrollment Status</span>
<strong :class="enrollmentStatus==='Completed'?'completed':'processing'">{{enrollmentStatus||'Paid'}}</strong>
</div>
</div>

<div class="next-step-box">
<div class="next-icon">📋</div>
<div>
<h3>What's Next?</h3>
<p>Your enrollment is now being processed by <strong>St. Francis Xavier College</strong>.</p>
<div class="processing-time">
<span>⏱️</span>
<strong>Estimated processing time: 1–2 working days</strong>
</div>
<p>Once your enrollment has been processed, your <strong>Study Load</strong> and <strong>Payment Receipt</strong> will be sent to your registered email address.</p>
<p class="small-note">Please check your inbox and spam/junk folder for updates from the college.</p>
</div>
</div>

<button class="confirm-btn" @click="goToReceipt">View Payment Details</button>
<p class="note">No further action is required at this time.</p>

<div class="school-footer">
<span>🛡️</span>
<span>Once a Xavier Knight, always a Xavier Knight.</span>
</div>

</div>

<div v-else>
<div class="pending-icon">⏳</div>

<h1>Payment Processing</h1>

<p class="subtitle">We're checking your payment status. Please wait a moment and try again if needed.</p>

<div class="payment-details">
<div class="detail-item">
<span>Payment Status</span>
<strong class="pending">{{paymentStatus||'Pending'}}</strong>
</div>

<div class="detail-item">
<span>Amount</span>
<strong>₱{{formatAmount(amount)}}</strong>
</div>

<div class="detail-item">
<span>Payment Method</span>
<strong>{{paymentMethod||'GCash'}}</strong>
</div>

<div v-if="enrollmentStatus" class="detail-item">
<span>Enrollment Status</span>
<strong>{{enrollmentStatus}}</strong>
</div>
</div>

<button class="confirm-btn" @click="checkPayment">Check Payment Status</button>

<p class="note">If you have already completed the payment, please allow a moment for the payment status to update.</p>
</div>

</div>
</div>
</template>

<script setup>
import {ref,onMounted,onBeforeUnmount} from "vue";
import axios from "@/services/api";

const loading=ref(true);
const paymentStatus=ref("");
const enrollmentStatus=ref("");
const amount=ref(1500);
const paymentMethod=ref("GCash");
const paymentReference=ref("");
const fireworksCanvas=ref(null);
const confettiPieces=ref([]);

let paymentInterval=null;
let paymentChecking=false;

let canvas=null;
let ctx=null;
let animationFrame=null;
let fireworks=[];
let particles=[];
let celebrationStarted=false;
let celebrationTimeout=null;

const random=(min,max)=>Math.random()*(max-min)+min;

class Firework{
constructor(){
this.x=random(canvas.width*.15,canvas.width*.85);
this.y=canvas.height+10;
this.targetX=random(canvas.width*.15,canvas.width*.85);
this.targetY=random(canvas.height*.15,canvas.height*.55);
this.speed=random(5,8);
this.angle=Math.atan2(this.targetY-this.y,this.targetX-this.x);
this.velocityX=Math.cos(this.angle)*this.speed;
this.velocityY=Math.sin(this.angle)*this.speed;
this.exploded=false;
this.trail=[];
}
update(){
this.trail.push({x:this.x,y:this.y});
if(this.trail.length>6)this.trail.shift();
this.x+=this.velocityX;
this.y+=this.velocityY;
const distance=Math.sqrt(Math.pow(this.targetX-this.x,2)+Math.pow(this.targetY-this.y,2));
if(distance<15){
this.explode();
this.exploded=true;
}
}
draw(){
ctx.beginPath();
ctx.moveTo(this.trail[0]?.x||this.x,this.trail[0]?.y||this.y);
ctx.lineTo(this.x,this.y);
ctx.strokeStyle="rgba(255,215,0,.9)";
ctx.lineWidth=2;
ctx.stroke();
}
explode(){
const particleCount=55;
for(let i=0;i<particleCount;i++){
const angle=Math.PI*2/particleCount*i;
const speed=random(2,7);
particles.push(new Particle(this.x,this.y,Math.cos(angle)*speed,Math.sin(angle)*speed));
}
}
}

class Particle{
constructor(x,y,vx,vy){
this.x=x;
this.y=y;
this.vx=vx;
this.vy=vy;
this.alpha=1;
this.gravity=.06;
this.friction=.985;
this.size=random(1,3);
const colors=["#198754","#0f5132","#ffc107","#ffdf70","#ffffff"];
this.color=colors[Math.floor(Math.random()*colors.length)];
}
update(){
this.vx*=this.friction;
this.vy*=this.friction;
this.vy+=this.gravity;
this.x+=this.vx;
this.y+=this.vy;
this.alpha-=.012;
}
draw(){
ctx.save();
ctx.globalAlpha=this.alpha;
ctx.fillStyle=this.color;
ctx.beginPath();
ctx.arc(this.x,this.y,this.size,0,Math.PI*2);
ctx.fill();
ctx.restore();
}
}

const animateFireworks=()=>{
if(!ctx||!canvas)return;

ctx.fillStyle="rgba(15,81,50,.18)";
ctx.fillRect(0,0,canvas.width,canvas.height);

if(Math.random()<.045&&fireworks.length<4)fireworks.push(new Firework());

fireworks.forEach(f=>{
f.update();
f.draw();
});

fireworks=fireworks.filter(f=>!f.exploded);

particles.forEach(p=>{
p.update();
p.draw();
});

particles=particles.filter(p=>p.alpha>0);

animationFrame=requestAnimationFrame(animateFireworks);
};

const resizeCanvas=()=>{
if(!canvas)return;
canvas.width=window.innerWidth;
canvas.height=window.innerHeight;
};

const startCelebration=()=>{
if(celebrationStarted)return;

celebrationStarted=true;
canvas=fireworksCanvas.value;

if(!canvas)return;

ctx=canvas.getContext("2d");
resizeCanvas();

window.addEventListener("resize",resizeCanvas);

for(let i=0;i<3;i++){
setTimeout(()=>{
if(canvas)fireworks.push(new Firework());
},i*500);
}

createConfetti();
animateFireworks();

celebrationTimeout=setTimeout(()=>{
stopCelebration();
},7000);
};

const stopCelebration=()=>{
if(animationFrame){
cancelAnimationFrame(animationFrame);
animationFrame=null;
}

window.removeEventListener("resize",resizeCanvas);

if(ctx&&canvas){
ctx.clearRect(0,0,canvas.width,canvas.height);
}

fireworks=[];
particles=[];
};

const createConfetti=()=>{
const pieces=[];
const symbols=["✦","✧","•","★","⭐"];

for(let i=0;i<35;i++){
pieces.push({
id:i,
left:random(5,95),
delay:random(0,2),
duration:random(3,6),
rotate:random(0,360),
symbol:symbols[Math.floor(Math.random()*symbols.length)]
});
}

confettiPieces.value=pieces;
};

const normalizeStatus=value=>{
return String(value??"").trim().toLowerCase();
};

const checkPayment=async()=>{
if(paymentChecking)return;

paymentChecking=true;

try{
if(!paymentStatus.value)loading.value=true;

const cacheBuster=Date.now();

const response=await axios.get(
`/student/payment/info?_=${cacheBuster}`,
{
headers:{
"Cache-Control":"no-cache",
"Pragma":"no-cache"
}
}
);

const data=response.data;

console.log("SUCCESS PAGE PAYMENT RESPONSE:",data);

const newPaymentStatus=
data.payment?.status??
data.payment_status??
data.status??
"Pending";

const newEnrollmentStatus=
data.enrollment?.status??
data.enrollment_status??
"";

const newAmount=
data.payment?.amount??
data.amount??
1500;

const newPaymentMethod=
data.payment?.payment_method??
data.payment_method??
"GCash";

const newPaymentReference=
data.payment?.payment_reference??
data.payment_reference??
"";

paymentStatus.value=String(newPaymentStatus).trim();
enrollmentStatus.value=String(newEnrollmentStatus).trim();
amount.value=Number(newAmount)||1500;
paymentMethod.value=newPaymentMethod||"GCash";
paymentReference.value=newPaymentReference||"";

console.log("SUCCESS PAGE FINAL STATUS:",{
paymentStatus:paymentStatus.value,
enrollmentStatus:enrollmentStatus.value,
amount:amount.value,
paymentMethod:paymentMethod.value,
paymentReference:paymentReference.value,
paymentId:data.payment?.id??null
});

const payment=normalizeStatus(paymentStatus.value);
const enrollment=normalizeStatus(enrollmentStatus.value);

const paymentSuccessful=
payment==="paid"||
enrollment==="paid"||
enrollment==="processing"||
enrollment==="completed";

if(paymentSuccessful){
console.log("SUCCESS PAGE: PAYMENT CONFIRMED AS PAID");
stopPaymentPolling();

if(!celebrationStarted){
setTimeout(()=>{
startCelebration();
},250);
}

return;
}

console.log("SUCCESS PAGE: PAYMENT STILL PENDING");
startPaymentPolling();

}catch(error){
console.error("SUCCESS PAGE PAYMENT CHECK ERROR:",error.response?.data||error);
startPaymentPolling();
}finally{
loading.value=false;
paymentChecking=false;
}
};

const startPaymentPolling=()=>{
if(paymentInterval)return;

console.log("SUCCESS PAGE: PAYMENT POLLING STARTED");

paymentInterval=setInterval(()=>{
checkPayment();
},3000);
};

const stopPaymentPolling=()=>{
if(!paymentInterval)return;

clearInterval(paymentInterval);
paymentInterval=null;

console.log("SUCCESS PAGE: PAYMENT POLLING STOPPED");
};

const handleVisibilityChange=()=>{
if(document.visibilityState==="visible")checkPayment();
};

const formatAmount=value=>{
return Number(value||0).toLocaleString("en-PH",{
minimumFractionDigits:2,
maximumFractionDigits:2
});
};

const goToReceipt=()=>{
window.location.href="/student/receipt";
};

onMounted(()=>{
console.log("SUCCESS PAGE: COMPONENT MOUNTED");

checkPayment();
startPaymentPolling();

document.addEventListener(
"visibilitychange",
handleVisibilityChange
);
});

onBeforeUnmount(()=>{
stopPaymentPolling();
stopCelebration();

document.removeEventListener(
"visibilitychange",
handleVisibilityChange
);

if(celebrationTimeout){
clearTimeout(celebrationTimeout);
celebrationTimeout=null;
}
});
</script>

<style scoped>
.success-page{
position:relative;
min-height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:radial-gradient(circle at top,#198754 0%,#146c43 35%,#0f5132 70%,#082c1c 100%);
padding:30px 15px;
overflow:hidden;
}

.fireworks-canvas{
position:fixed;
inset:0;
width:100%;
height:100%;
pointer-events:none;
z-index:1;
}

.success-card{
position:relative;
z-index:5;
background:rgba(255,255,255,.98);
width:470px;
max-width:100%;
border-radius:28px;
padding:35px;
text-align:center;
box-shadow:0 25px 80px rgba(0,0,0,.35);
animation:cardEntrance .8s ease;
}

@keyframes cardEntrance{
from{opacity:0;transform:translateY(30px) scale(.96)}
to{opacity:1;transform:translateY(0) scale(1)}
}

.celebration-icon{
width:90px;
height:90px;
border-radius:50%;
background:linear-gradient(135deg,#198754,#0f5132);
color:white;
font-size:55px;
display:flex;
justify-content:center;
align-items:center;
margin:0 auto 12px;
box-shadow:0 0 0 8px rgba(25,135,84,.1),0 12px 30px rgba(25,135,84,.3);
animation:successPop .7s ease;
}

@keyframes successPop{
0%{transform:scale(0)}
70%{transform:scale(1.12)}
100%{transform:scale(1)}
}

.welcome-badge{
font-size:30px;
margin-bottom:5px;
animation:badgeFloat 2s ease-in-out infinite;
}

@keyframes badgeFloat{
0%,100%{transform:translateY(0)}
50%{transform:translateY(-5px)}
}

h1{
color:#198754;
font-weight:800;
font-size:32px;
margin-bottom:8px;
}

h2{
color:#0f5132;
font-size:21px;
font-weight:700;
margin-bottom:18px;
}

.subtitle{
color:#6c757d;
margin-bottom:25px;
line-height:1.6;
}

.knights-message{
display:flex;
align-items:center;
gap:12px;
text-align:left;
background:linear-gradient(135deg,#fff8dc,#fffdf3);
border:1px solid #f1d36a;
border-radius:16px;
padding:15px;
margin-bottom:20px;
}

.knights-message>span{
font-size:32px;
}

.knights-message strong{
color:#8a6d00;
}

.knights-message p{
margin:3px 0 0;
color:#6c757d;
font-size:13px;
}

.payment-details{
background:#f8f9fa;
border-radius:16px;
padding:18px;
margin-bottom:20px;
}

.detail-item{
display:flex;
justify-content:space-between;
align-items:flex-start;
gap:15px;
padding:11px 0;
border-bottom:1px solid #ddd;
text-align:left;
}

.detail-item:last-child{
border-bottom:none;
}

.detail-item span{
color:#6c757d;
font-size:14px;
}

.detail-item strong{
text-align:right;
word-break:break-word;
font-size:14px;
}

.paid{color:#198754}
.processing{color:#0d6efd}
.completed{color:#198754}
.pending{color:#d39e00}

.next-step-box{
display:flex;
gap:12px;
align-items:flex-start;
background:#f0f7ff;
border:1px solid #cfe2ff;
border-radius:16px;
padding:17px;
margin-bottom:20px;
text-align:left;
}

.next-icon{
font-size:26px;
flex-shrink:0;
}

.next-step-box h3{
color:#0d6efd;
font-size:18px;
font-weight:700;
margin:2px 0 8px;
}

.next-step-box p{
color:#495057;
font-size:13px;
line-height:1.6;
margin-bottom:9px;
}

.next-step-box p:last-child{
margin-bottom:0;
}

.processing-time{
display:flex;
align-items:center;
gap:8px;
background:white;
border-radius:10px;
padding:10px;
margin:10px 0;
color:#0d6efd;
font-size:13px;
}

.small-note{
color:#6c757d!important;
font-size:12px!important;
}

.confirm-btn{
width:100%;
padding:14px;
border:none;
border-radius:12px;
background:linear-gradient(135deg,#198754,#146c43);
color:white;
font-size:17px;
font-weight:600;
cursor:pointer;
transition:.3s;
box-shadow:0 8px 20px rgba(25,135,84,.25);
}

.confirm-btn:hover{
background:#0f5132;
transform:translateY(-2px);
box-shadow:0 12px 25px rgba(25,135,84,.3);
}

.note{
margin-top:15px;
font-size:13px;
color:#6c757d;
line-height:1.5;
}

.school-footer{
display:flex;
justify-content:center;
align-items:center;
gap:7px;
margin-top:18px;
padding-top:15px;
border-top:1px solid #eee;
color:#6c757d;
font-size:12px;
}

.school-footer span:first-child{
font-size:16px;
}

.pending-icon{
width:90px;
height:90px;
border-radius:50%;
background:#ffc107;
color:white;
font-size:45px;
display:flex;
justify-content:center;
align-items:center;
margin:0 auto 20px;
animation:pendingPulse 1.8s ease-in-out infinite;
}

@keyframes pendingPulse{
0%,100%{transform:scale(1)}
50%{transform:scale(1.06)}
}

.loading{
padding:30px 0;
text-align:center;
}

.loading h3{
color:#198754;
margin:15px 0 8px;
}

.loading p{
color:#6c757d;
}

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
to{transform:rotate(360deg)}
}

.confetti{
position:fixed;
top:-30px;
z-index:3;
color:#ffc107;
font-size:18px;
pointer-events:none;
animation:confettiFall linear forwards;
}

@keyframes confettiFall{
0%{opacity:0;transform:translateY(-20px) rotate(0deg)}
10%{opacity:1}
100%{opacity:0;transform:translateY(110vh) rotate(720deg)}
}

@media(max-width:500px){
.success-page{
padding:20px 12px;
align-items:flex-start;
}

.success-card{
margin:20px 0;
padding:25px 18px;
border-radius:22px;
}

h1{font-size:27px}
h2{font-size:19px}
.subtitle{font-size:14px}

.celebration-icon,.pending-icon{
width:78px;
height:78px;
font-size:45px;
}

.detail-item{
font-size:13px;
}

.detail-item span,.detail-item strong{
font-size:13px;
}

.knights-message{
padding:13px;
}

.next-step-box{
padding:14px;
}
}
</style>

