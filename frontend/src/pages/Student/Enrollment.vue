<script setup>

import { onMounted, ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import api from "@/services/api"

import Step1Personal from '@/Enrollment/Step1Personal.vue'
import Step2Guardian from '@/Enrollment/Step2Guardian.vue'
import Step3Academic from '@/Enrollment/Step3Academic.vue'
import Step4Documents from '@/Enrollment/Step4Documents.vue'
import Step5Enrollment from '@/Enrollment/Step5Enrollment.vue'
import Step6Review from '@/Enrollment/Step6Review.vue'


const step = ref(1)

const loading = ref(false)
const router = useRouter()


const alert = ref({

    show:false,
    type:"",
    message:""

})


const steps = [
    'Personal',
    'Guardian',
    'Academic',
    'Documents',
    'Enrollment',
    'Review'
]



// MAIN FORM DATA

const form = ref({

    // Step 1
    student_type:'',

    first_name:'',
    middle_name:'',
    last_name:'',
    birth_date:'',
    gender:'',
    civil_status:'',
    nationality:'',
    religion:'',
    address:'',
    contact_number:'',
    email:'',


    // Step 2
    guardian_name:'',
    guardian_relationship:'',
    guardian_contact:'',
    guardian_address:'',

    father_name:'',
    father_occupation:'',
    father_contact:'',

    mother_name:'',
    mother_occupation:'',
    mother_contact:'',


    // Step 3 Academic

    last_school:'',
    school_address:'',
    strand:'',
    graduation_year:'',
    gwa:'',

    previous_course:'',
    units_earned:'',

    last_school_year:'',
    last_semester:'',


    // Step 4 Documents

    psa_birth_certificate:null,
    good_moral:null,
    academic_document:null,
    id_picture:null,


// Step 5 Enrollment

school_year_id:'',
semester_id:'',
course_id:'',
curriculum_id:'',
year_level:'',
remarks:'',


// Step 6 Certification

data_privacy_consent:false,
information_certified:false


});



// LOAD LOGGED-IN STUDENT PROFILE

async function loadStudentProfile(){

    try{


        const response = await api.get('/student/profile')


        console.log(
            "PROFILE RESPONSE:",
            response.data
        )
        const student = response.data.student
        if(student){
            form.value.first_name = student.first_name ?? ''
            form.value.middle_name = student.middle_name ?? ''
            form.value.last_name = student.last_name ?? ''
            form.value.birth_date = student.birth_date ?? ''
            form.value.gender = student.gender ?? ''
            form.value.civil_status = student.civil_status ?? ''
            form.value.nationality = student.nationality ?? ''
            form.value.religion = student.religion ?? ''
            form.value.address = student.address ?? ''
            form.value.contact_number = student.contact_number ?? ''
            form.value.email = student.email ?? ''
        }


    }
    catch(error){

        console.log(
            "PROFILE ERROR:",
            error.response
        )

    }

}



// LOAD PROFILE WHEN PAGE OPENS

onMounted(()=>{

    loadStudentProfile()

})




// ALERT

function showAlert(type,message){

    alert.value={

        show:true,

        type,

        message

    }


    setTimeout(()=>{

        alert.value.show=false

    },4000)


}
    function scrollToTop() {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

}




// COMPONENT SWITCH

const currentComponent = computed(()=>{


    switch(step.value){


        case 1:
            return Step1Personal


        case 2:
            return Step2Guardian


        case 3:
            return Step3Academic


        case 4:
            return Step4Documents


        case 5:
            return Step5Enrollment


        default:
            return Step6Review

    }


})




// PROGRESS

const progress = computed(()=>{

    return (step.value / 6) * 100

})




// NAVIGATION

function next(){

    if(step.value < 6){

        step.value++

    }

}



function previous(){

    if(step.value > 1){

        step.value--

    }

}





// SUBMIT
async function submitEnrollment(){


    if(!form.value.data_privacy_consent){

        showAlert(
            "error",
            "Please agree to the Data Privacy Agreement before submitting."
        )

        return

    }



    if(!form.value.information_certified){

        showAlert(
            "error",
            "Please certify that all information submitted is true and correct."
        )

        return

    }



    console.log(
        "SUBMIT DATA:",
        form.value
    )



    loading.value=true



    try{


        const formData = new FormData()



        Object.keys(form.value).forEach(key=>{


            const value = form.value[key]



            if(value !== null && value !== ''){


                formData.append(
                    key,
                    value
                )


            }


        })




        const response = await api.post(

            "/student/enrollment",

            formData,

            {

                headers:{

                    "Content-Type":"multipart/form-data"

                }

            }

        )




        console.log(
            "SUCCESS:",
            response.data
        )




        showAlert(

            "success",

            response.data.message

        )
            scrollToTop()

    }
    catch(error){


        console.log(
            "ERROR:",
            error.response
        )



        showAlert(

            "error",

            error.response?.data?.message ??

            "Enrollment failed."

        )


        scrollToTop()
        if(error.response?.data?.redirect){


            setTimeout(()=>{


                router.push(
                    error.response.data.redirect
                )


            },2000)


        }


    }
    finally{


        loading.value=false


    }


}

</script>

<template>


<div class="enrollment-page">


<transition name="slide">


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

class="bi bi-exclamation-circle-fill">

</i>



{{alert.message}}



</div>


</transition>










<div class="enrollment-card">







<div class="header">


<div>


<h3>

Enrollment Application

</h3>


<p>

Complete all required information

</p>


</div>


</div>










<!-- PROGRESS -->


<div class="progress-container">


<div

class="progress-bar"

:style="{width:progress+'%'}"

>


</div>


</div>









<!-- STEPS -->



<div class="steps">



<div

v-for="(item,index) in steps"

:key="index"

class="step-item"

>



<div

class="circle"

:class="{

active:step===index+1,

completed:step>index+1

}"

>


<i

v-if="step>index+1"

class="bi bi-check"

></i>


<span v-else>

{{index+1}}

</span>


</div>



<p

:class="{activeText:step===index+1}"

>

{{item}}

</p>



</div>



</div>









<div class="form-area">


<component

:is="currentComponent"

v-model="form"

/>


</div>










<div class="buttons">





<button

class="btn-prev"

@click="previous"

:disabled="step===1"

>


<i class="bi bi-arrow-left"></i>

Previous


</button>








<button

v-if="step<6"

class="btn-next"

@click="next"

>


Next

<i class="bi bi-arrow-right"></i>


</button>







<button

v-else

class="btn-submit"

@click="submitEnrollment"

:disabled="loading"

>


<i class="bi bi-send"></i>


{{loading?'Submitting...':'Submit Enrollment'}}



</button>





</div>






</div>





</div>


</template>









<style scoped>


.enrollment-page{

padding:10px;

}





.page-title{

font-weight:800;

color:#064E2A;

margin-bottom:5px;

}



.subtitle{

color:#6b7280;

margin-bottom:25px;

}








.enrollment-card{


background:white;

border-radius:25px;


padding:35px;


box-shadow:

0 10px 35px rgba(0,0,0,.08);


}







.header{


border-bottom:1px solid #eee;

padding-bottom:20px;

margin-bottom:30px;

}



.header h3{

font-weight:800;

color:#064E2A;

}



.header p{

color:#6b7280;

}







.progress-container{


height:10px;


background:#e5e7eb;


border-radius:20px;


overflow:hidden;


}



.progress-bar{


height:100%;


background:

linear-gradient(

90deg,

#064E2A,

#0B6B3A

);


transition:.4s;


}









.steps{


display:flex;


justify-content:space-between;


margin:35px 0;


}





.step-item{

text-align:center;

flex:1;

}



.circle{


width:45px;

height:45px;


border-radius:50%;


margin:auto;


display:flex;


justify-content:center;


align-items:center;


background:#f3f4f6;


border:2px solid #ddd;


font-weight:700;


}





.circle.active,


.circle.completed{


background:#0B6B3A;


color:white;


border:none;


}





.step-item p{


margin-top:10px;


font-size:13px;


color:#777;


}



.activeText{


font-weight:700;


color:#0B6B3A !important;


}





.form-area{


background:#fafafa;


border-radius:20px;


padding:25px;


}








.buttons{


display:flex;


justify-content:space-between;


margin-top:30px;


}





button{


padding:12px 25px;


border-radius:12px;


border:none;


font-weight:700;


}





.btn-prev{


background:#e5e7eb;


}





.btn-next,


.btn-submit{


background:#0B6B3A;


color:white;


}



.btn-next:hover,

.btn-submit:hover{


background:#064E2A;


}









.alert-box{


display:flex;


align-items:center;


gap:12px;


padding:15px 20px;


border-radius:15px;


margin-bottom:25px;


font-weight:600;


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




.slide-enter-active,
.slide-leave-active{

transition:.3s;

}



.slide-enter-from,
.slide-leave-to{

opacity:0;

transform:translateY(-15px);

}








@media(max-width:768px){


.steps{


overflow-x:auto;


}



.step-item{


min-width:90px;


}



.buttons{


flex-direction:column;


gap:15px;


}


}



</style>