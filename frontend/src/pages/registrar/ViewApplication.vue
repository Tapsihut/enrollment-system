<template>

<div class="application-page">


<!-- SUCCESS MESSAGE -->

<div
v-if="alert.show"
class="alert-box"
:class="alert.type"
>

<i
v-if="alert.type==='success'"
class="bi bi-check-circle-fill">
</i>


<i
v-if="alert.type==='error'"
class="bi bi-x-circle-fill">
</i>


{{ alert.message }}

</div>


<!-- HEADER -->

<div class="page-header">


<div>

<h2>

Enrollment Application

</h2>


<p>

Review student's submitted enrollment details.

</p>


</div>



<router-link

to="/registrar/applications"

class="btn btn-outline-success"

>

<i class="bi bi-arrow-left"></i>

Back

</router-link>


</div>




<!-- LOADING -->

<div

v-if="loading"

class="loading"

>

<div class="spinner-border text-success"></div>

<p>

Loading application...

</p>


</div>





<div v-else>



<!-- APPLICATION HEADER CARD -->


<div class="review-card top-card">


<div>


<h4>

Application #{{ enrollment.id }}

</h4>


<p>

Submitted:

{{ formatDate(enrollment.created_at) }}

</p>


</div>



<span

class="status"

:class="enrollment.status"

>

{{ enrollment.status }}

</span>


</div>







<!-- STUDENT INFORMATION -->


<div class="review-card">


<div class="section-title">

<i class="bi bi-person-circle"></i>

Student Information

</div>



<div class="row">



<div class="col-md-4 info">

<label>

First Name

</label>

<p>

{{ enrollment.student?.first_name }}

</p>

</div>



<div class="col-md-4 info">

<label>

Middle Name

</label>

<p>

{{ enrollment.student?.middle_name }}

</p>

</div>




<div class="col-md-4 info">

<label>

Last Name

</label>

<p>

{{ enrollment.student?.last_name }}

</p>

</div>
<div class="col-md-4 info">
<label>Birth Date</label>
<p>{{ formatDate(enrollment.student?.birth_date) }}</p>
</div>



<div class="col-md-4 info">

<label>

Student Type

</label>

<p>

{{ enrollment.student?.student_type }}

</p>

</div>



<div class="col-md-4 info">

<label>

Gender

</label>

<p>

{{ enrollment.student?.gender }}

</p>

</div>




<div class="col-md-4 info">

<label>

Civil Status

</label>

<p>

{{ enrollment.student?.civil_status }}

</p>

</div>



<div class="col-md-6 info">

<label>

Email

</label>

<p>

{{ enrollment.student?.email }}

</p>

</div>




<div class="col-md-6 info">

<label>

Contact Number

</label>

<p>

{{ enrollment.student?.contact_number }}

</p>

</div>



<div class="col-md-12 info">

<label>

Address

</label>

<p>

{{ enrollment.student?.address }}

</p>

</div>


</div>


</div>





<!-- GUARDIAN -->


<div class="review-card">


<div class="section-title">

<i class="bi bi-people-fill"></i>

Guardian Information

</div>



<div class="row">



<div class="col-md-4 info">

<label>

Guardian Name

</label>


<p>

{{ enrollment.guardian?.guardian_name }}

</p>


</div>



<div class="col-md-4 info">

<label>

Relationship

</label>


<p>

{{ enrollment.guardian?.relationship }}

</p>


</div>



<div class="col-md-4 info">

<label>

Contact Number

</label>


<p>

{{ enrollment.guardian?.guardian_contact }}

</p>


</div>



</div>


</div>
<!-- ENROLLMENT INFORMATION -->


<div class="review-card">


<div class="section-title">

<i class="bi bi-mortarboard-fill"></i>

Enrollment Information

</div>



<div class="row">



<div class="col-md-6 info">

<label>

Course

</label>


<p>

{{ enrollment.course?.name }}

</p>


</div>




<div class="col-md-6 info">

<label>

Curriculum

</label>


<p>

{{ enrollment.curriculum?.name }}

</p>


</div>




<div class="col-md-4 info">

<label>

School Year

</label>


<p>

{{ enrollment.schoolYear?.school_year }}

</p>


</div>



<div class="col-md-4 info">

<label>

Semester

</label>


<p>

{{ enrollment.semester?.name }}

</p>


</div>




<div class="col-md-4 info">

<label>

Year Level

</label>


<p>

{{ enrollment.year_level }}

</p>


</div>



</div>


</div>



<!-- ACADEMIC BACKGROUND -->

<div class="review-card">


    <div class="section-title">

        <i class="bi bi-building"></i>

        Academic Background

    </div>




    <div
        v-if="enrollment.academicBackground"
        class="row"
    >



        <div class="col-md-6 info">

            <label>
                Last School Attended
            </label>

            <p>
                {{ enrollment.academicBackground.last_school }}
            </p>

        </div>





        <div class="col-md-6 info">

            <label>
                Year Graduated
            </label>

            <p>
                {{ enrollment.academicBackground.graduation_year }}
            </p>

        </div>





        <div class="col-md-12 info">

            <label>
                School Address
            </label>

            <p>
                {{ enrollment.academicBackground.school_address }}
            </p>

        </div>



    </div>




    <div v-else>

        <p class="text-muted">

            No academic background record.

        </p>

    </div>



</div>

<!-- DOCUMENTS -->

<div class="review-card">


    <div class="section-title">

        <i class="bi bi-folder-fill"></i>

        Submitted Documents

    </div>

<div
v-if="enrollment.documents && enrollment.documents.length"
class="documents"
>


<a

v-for="doc in enrollment.documents"

:key="doc.id"

:href="getFileUrl(doc.file_path)"

target="_blank"

class="document-btn"

>


<i class="bi bi-file-earmark-text"></i>


<div>

<strong>
{{ doc.document_type }}
</strong>

<br>

<small>
{{ doc.file_name }}
</small>

</div>


</a>


</div>




    <p

        v-else

        class="text-muted"

    >

        No documents uploaded.

    </p>



</div>

<!-- ACTIONS -->


<div class="action-area">



<button

class="btn btn-danger"

@click="rejectApplication"

>

<i class="bi bi-x-circle"></i>

Reject

</button>





<button

class="btn btn-success"

@click="approveApplication"

>

<i class="bi bi-check-circle"></i>

Approve

</button>



</div>



</div>


</div>


</template>

<script setup>

import { ref, onMounted } from "vue"
import { useRoute, useRouter } from "vue-router"

import api from "@/services/api"


const route = useRoute()

const router = useRouter()



const loading = ref(true)


const enrollment = ref({})
const alert = ref({

    show:false,

    type:"",

    message:""

})
function showAlert(type,message){

    alert.value = {

        show:true,

        type,

        message

    }


    window.scrollTo({

        top:0,

        behavior:"smooth"

    })


    setTimeout(()=>{

        alert.value.show=false

    },4000)

}
async function loadApplication(){

    
    loading.value = true


    try{

        const {data} = await api.get(

            `/registrar/enrollment/${route.params.id}`

        )
        console.log("ENROLLMENT DATA:", data)

        enrollment.value = data


    }

    catch(error){

        console.log(error)

        alert("Unable to load application.")

    }

    finally{

        loading.value = false

    }

}





async function approveApplication(){


    if(!confirm("Approve this enrollment application?"))

        return



    try{


        await api.put(

            `/registrar/enrollment/${route.params.id}/approve`

        )


        showAlert(
            "success",
            "Enrollment approved successfully."
        )


        setTimeout(()=>{

            router.push("/registrar/applications")

        },1500)


    }

    catch(error){

        console.log(error)

        showAlert(
            "danger",
            "Approval failed."
        )

    }


}
async function rejectApplication(){


    if(!confirm("Reject this enrollment application?"))

        return



    try{


        await api.put(

            `/registrar/enrollment/${route.params.id}/reject`

        )


        showAlert(
            "error",
            "Enrollment rejected successfully."
        )


        setTimeout(()=>{

            router.push("/registrar/applications")

        },1500)


    }

    catch(error){

        console.log(error)

        showAlert(
            "danger",
            "Rejection failed."
        )

    }


}

function getFileUrl(path){

    if(!path)

        return "#"


    return "http://127.0.0.1:8000/storage/" + path

}

function scrollToTop() {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

}


function formatDate(date){


    if(!date)

        return "-"



    return new Date(date).toLocaleString()

}




onMounted(()=>{

    loadApplication()

})



</script>





<style scoped>

.alert-box{

    padding:15px 20px;

    border-radius:12px;

    margin-bottom:20px;

    font-weight:700;

    display:flex;

    align-items:center;

    gap:10px;

    box-shadow:0 10px 25px rgba(0,0,0,.08);

    animation:slideDown .3s ease;

}



.alert-box.success{

    background:#dcfce7;

    color:#166534;

    border-left:5px solid #0B6B3A;

}



.alert-box.error{

    background:#fee2e2;

    color:#991b1b;

    border-left:5px solid #dc2626;

}



.alert-box i{

    font-size:20px;

}



@keyframes slideDown{

    from{

        opacity:0;

        transform:translateY(-20px);

    }

    to{

        opacity:1;

        transform:translateY(0);

    }

}

.success-message{

    background:#dcfce7;

    color:#166534;

    padding:15px 20px;

    border-radius:12px;

    margin-bottom:20px;

    font-weight:700;

    display:flex;

    align-items:center;

    gap:10px;

    box-shadow:0 10px 25px rgba(0,0,0,.08);

    animation:slideDown .3s ease;

}


.success-message i{

    font-size:20px;

}



@keyframes slideDown{

    from{

        opacity:0;

        transform:translateY(-20px);

    }

    to{

        opacity:1;

        transform:translateY(0);

    }

}
.application-page{

    padding:20px;

}



/* HEADER */


.page-header{

    display:flex;

    justify-content:space-between;

    align-items:center;

    margin-bottom:25px;

    flex-wrap:wrap;

}



.page-header h2{

    color:#064E2A;

    font-weight:800;

}



.page-header p{

    color:#6B7280;

}



/* LOADING */


.loading{

    text-align:center;

    padding:80px;

}



/* CARD */


.review-card{


    background:white;

    border-radius:20px;

    padding:25px;

    margin-bottom:25px;

    box-shadow:

    0 10px 30px rgba(0,0,0,.08);


}



/* TOP CARD */


.top-card{


    display:flex;

    justify-content:space-between;

    align-items:center;


}



.top-card h4{

    color:#064E2A;

    font-weight:800;

}



.top-card p{

    color:#6B7280;

}





/* STATUS */


.status{

    padding:10px 20px;

    border-radius:50px;

    font-weight:700;

    font-size:14px;

}



.status.Pending{

    background:#fef3c7;

    color:#92400e;

}



.status.Approved{

    background:#dcfce7;

    color:#166534;

}



.status.Rejected{

    background:#fee2e2;

    color:#991b1b;

}





/* SECTION TITLE */


.section-title{

    background:

    linear-gradient(

    135deg,

    #064E2A,

    #0B6B3A

    );


    color:white;


    padding:14px 18px;


    border-radius:12px;


    margin-bottom:25px;


    font-weight:700;

}





/* INFO */


.info{

    margin-bottom:20px;

}



.info label{


    display:block;


    color:#064E2A;


    font-size:13px;


    font-weight:700;


}



.info p{


    margin:5px 0 0;


    color:#374151;


    font-size:15px;


}






/* DOCUMENTS */


.documents{


    display:flex;


    gap:15px;


    flex-wrap:wrap;


}



.document-btn{


    display:flex;


    align-items:center;


    gap:8px;


    padding:12px 18px;


    border-radius:12px;


    background:#f0fdf4;


    color:#064E2A;


    text-decoration:none;


    font-weight:600;


    border:1px solid #bbf7d0;


    transition:.3s;


}



.document-btn:hover{


    background:#0B6B3A;


    color:white;


}





/* ACTIONS */


.action-area{


    display:flex;


    justify-content:flex-end;


    gap:15px;


    margin-bottom:30px;


}



.action-area button{


    padding:12px 25px;


    border-radius:12px;


    font-weight:700;


}





/* MOBILE */


@media(max-width:768px){



.application-page{

    padding:10px;

}



.top-card{

    flex-direction:column;

    align-items:flex-start;

    gap:15px;

}



.action-area{


    flex-direction:column;


}



.action-area button{


    width:100%;


}



.document-btn{


    width:100%;


    justify-content:center;


}



}


</style>