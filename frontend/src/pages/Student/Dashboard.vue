<script setup>

import { ref,onMounted } from "vue";
import axios from "axios";


const dashboard = ref({

    enrollment_status: '',
    subjects: 0,
    enrollment_fee: 0,
    payment_status: '',

    progress: 0,

    steps:{
        profile:false,
        enrollment:false,
        approval:false,
        payment:false,
        completed:false
    }

});


const loadDashboard = async()=>{

    try{

        const response = await axios.get(
            "http://127.0.0.1:8000/api/student/dashboard",
            {
                headers:{
                    Authorization:
                    `Bearer ${localStorage.getItem("token")}`
                }
            }
        );


        console.log("Dashboard Data:", response.data);

dashboard.value = response.data;


    }
    catch(error){

        console.log(error);

    }

}



onMounted(()=>{

    loadDashboard();

});


</script>
<template>

<div class="dashboard">


<!-- HEADER -->

<div class="welcome-card mb-4">


<div>

<h2>

Welcome, Student 👋

</h2>


<p>

Manage your enrollment, payments, and academic information.

</p>


</div>



<div class="school-badge">

SFXC

</div>


</div>





<h4 class="section-title">

Student Overview

</h4>




<div class="row g-4">



<!-- ENROLLMENT -->

<div class="col-md-3">


<div class="dashboard-card">


<div class="icon green">

<i class="bi bi-journal-text"></i>

</div>



<div>

<h6>
Enrollment Status
</h6>


<h4>
{{ dashboard.enrollment_status }}
</h4>


<span class="badge pending">

{{ dashboard.enrollment_status }}

</span>


</div>


</div>


</div>






<!-- SUBJECTS -->

<div class="col-md-3">


<div class="dashboard-card">


<div class="icon blue">

<i class="bi bi-book"></i>

</div>



<div>

<h6>
Subjects
</h6>

<h4>
{{ dashboard.subjects }}
</h4>


<span class="badge info">

Assigned

</span>


</div>


</div>


</div>







<!-- FEES -->

<div class="col-md-3">


<div class="dashboard-card">


<div class="icon gold">

<i class="bi bi-wallet"></i>

</div>



<div>

<h6>
Enrollment Fee
</h6>

<h4>
₱{{ Number(dashboard.enrollment_fee).toLocaleString('en-US', {
    minimumFractionDigits: 2
}) }}
</h4>

<span class="badge warning">

Balance

</span>


</div>


</div>


</div>








<!-- PAYMENT -->

<div class="col-md-3">


<div class="dashboard-card">


<div class="icon success">

<i class="bi bi-check-circle"></i>

</div>



<div>

<h6>
Payment
</h6>

<h4>
{{ dashboard.payment_status }}
</h4>


<span 
class="badge"
:class="dashboard.payment_status === 'Paid'
? 'success'
: 'danger'"
>

{{ dashboard.payment_status }}

</span>


</div>


</div>


</div>




</div>








<!-- ENROLLMENT TRACKER -->

<div class="process-card mt-5">


<div class="card-title">

Enrollment Progress

</div>



<div class="progress-wrapper">


<div class="progress">


<div

class="progress-bar"

:style="{
width: dashboard.progress + '%'
}"

>

{{dashboard.progress}}%

</div>

</div>


</div>






<div class="steps">


<div 
class="step"
:class="{active: dashboard.steps.profile}"
>

<div>1</div>

<span>
Complete Profile
</span>

</div>



<div 
class="step"
:class="{active: dashboard.steps.enrollment}"
>
<div>2</div>
<span>
Submit Enrollment
</span>
</div>
<div 
class="step"
:class="{active: dashboard.steps.approval}"
>
<div>3</div>
<span>
Registrar Approval
</span>
</div>
<div 
class="step"
:class="{active: dashboard.steps.payment}"
>

<div>4</div>

<span>
Payment
</span>
</div>
</div>


</div>



</div>


</template>






<style scoped>

.badge.success{

background:#d1fae5;

color:#065f46;

}


.dashboard{

padding:10px;

}




.welcome-card{

background:

linear-gradient(

135deg,

#064E2A,

#0B6B3A

);

color:white;

padding:35px;

border-radius:25px;

display:flex;

justify-content:space-between;

align-items:center;

box-shadow:0 15px 40px rgba(0,0,0,.15);

}



.welcome-card h2{

font-weight:800;

}



.welcome-card p{

opacity:.8;

}




.school-badge{

height:90px;

width:90px;

border-radius:50%;

background:#ffffff;

color:#0B6B3A;

display:flex;

justify-content:center;

align-items:center;

font-size:30px;

font-weight:900;

border:5px solid #9cffc8;

}





.section-title{

font-weight:700;

margin-bottom:20px;

}




.dashboard-card{

background:white;

border-radius:20px;

padding:25px;

display:flex;

gap:20px;

align-items:center;

box-shadow:

0 10px 30px rgba(0,0,0,.08);

transition:.3s;

}



.dashboard-card:hover{

transform:translateY(-5px);

}




.icon{

height:60px;

width:60px;

border-radius:15px;

display:flex;

align-items:center;

justify-content:center;

font-size:30px;

}




.icon.green{

background:#dcfce7;

color:#0B6B3A;

}


.icon.blue{

background:#dbeafe;

color:#2563eb;

}


.icon.gold{

background:#fef3c7;

color:#d97706;

}


.icon.success{

background:#d1fae5;

color:#059669;

}





.dashboard-card h6{

color:#6b7280;

margin:0;

}



.dashboard-card h4{

font-weight:800;

margin:5px 0;

}





.badge{

padding:6px 12px;

border-radius:20px;

font-size:12px;

}



.pending{

background:#fef3c7;

color:#92400e;

}


.info{

background:#dbeafe;

color:#1e40af;

}


.warning{

background:#fef3c7;

color:#92400e;

}


.danger{

background:#fee2e2;

color:#991b1b;

}





.process-card{

background:white;

border-radius:25px;

padding:35px;

box-shadow:

0 10px 30px rgba(0,0,0,.08);

}



.card-title{

font-size:22px;

font-weight:700;

margin-bottom:25px;

}




.progress{

height:18px;

border-radius:20px;

background:#e5e7eb;

}



.progress-bar{

background:

linear-gradient(

90deg,

#0B6B3A,

#34d399

);

border-radius:20px;

}





.steps{

display:flex;

justify-content:space-between;

margin-top:35px;

}



.step{

text-align:center;

color:#9ca3af;

font-size:14px;

}



.step div{

height:45px;

width:45px;

border-radius:50%;

background:#e5e7eb;

display:flex;

align-items:center;

justify-content:center;

margin:auto;

font-weight:bold;

margin-bottom:10px;

}




.step.active div{

background:#0B6B3A;

color:white;

}



.step.active{

color:#0B6B3A;

font-weight:600;

}





@media(max-width:768px){


.steps{

flex-direction:column;

gap:25px;

}


.welcome-card{

flex-direction:column;

gap:20px;

align-items:flex-start;

}


}

</style>