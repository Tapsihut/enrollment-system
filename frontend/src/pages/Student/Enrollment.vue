<script setup>
import {onMounted,ref,computed} from 'vue'
import {useRouter} from 'vue-router'
import api from "@/services/api"

import Step1Personal from '@/Enrollment/Step1Personal.vue'
import Step2Guardian from '@/Enrollment/Step2Guardian.vue'
import Step3Academic from '@/Enrollment/Step3Academic.vue'
import Step4Documents from '@/Enrollment/Step4Documents.vue'
import Step5Enrollment from '@/Enrollment/Step5Enrollment.vue'
import Step6Review from '@/Enrollment/Step6Review.vue'

const step=ref(1)
const loading=ref(false)
const checkingEnrollment=ref(true)
const enrollmentBlocked=ref(false)
const enrollmentBlockMessage=ref('')
const router=useRouter()

const alert=ref({
    show:false,
    type:"",
    message:""
})

const form=ref({
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

    last_school:'',
    school_address:'',
    strand:'',
    graduation_year:'',
    gwa:'',
    previous_course:'',
    units_earned:'',
    last_school_year:'',
    last_semester:'',

    psa_birth_certificate:null,
    good_moral:null,
    academic_document:null,
    id_picture:null,

    school_year_id:'',
    semester_id:'',
    course_id:'',
    curriculum_id:'',
    year_level:'',
    remarks:'',

    data_privacy_consent:false,
    information_certified:false
})

const studentType=computed(()=>{
    return String(form.value.student_type||'').trim().toLowerCase()
})

const isContinuing=computed(()=>{
    return studentType.value==="continuing"
})

const isFreshman=computed(()=>{
    return ["freshman","freshmen","new"].includes(studentType.value)
})

const isTransferee=computed(()=>{
    return ["transferee","transfer"].includes(studentType.value)
})

const isReturning=computed(()=>{
    return ["returning","returnee"].includes(studentType.value)
})

const requiresAcademic=computed(()=>{
    return !isContinuing.value
})

const requiresDocuments=computed(()=>{
    return !isContinuing.value
})

const visibleSteps=computed(()=>{
    const result=[
        {number:1,label:"Personal"},
        {number:2,label:"Guardian"}
    ]

    if(requiresAcademic.value){
        result.push({number:3,label:"Academic"})
    }

    if(requiresDocuments.value){
        result.push({number:4,label:"Documents"})
    }

    result.push(
        {number:5,label:"Enrollment"},
        {number:6,label:"Review"}
    )

    return result
})

const currentComponent=computed(()=>{
    switch(step.value){
        case 1:return Step1Personal
        case 2:return Step2Guardian
        case 3:return Step3Academic
        case 4:return Step4Documents
        case 5:return Step5Enrollment
        default:return Step6Review
    }
})

const progress=computed(()=>{
    const index=visibleSteps.value.findIndex(
        item=>item.number===step.value
    )

    if(index<0)return 0

    return((index+1)/visibleSteps.value.length)*100
})

async function loadStudentProfile(){
    try{
        const response=await api.get('/student/profile')
        const student=response.data.student

        if(student){
            form.value.first_name=student.first_name??''
            form.value.middle_name=student.middle_name??''
            form.value.last_name=student.last_name??''
            form.value.birth_date=student.birth_date??''
            form.value.gender=student.gender??''
            form.value.civil_status=student.civil_status??''
            form.value.nationality=student.nationality??''
            form.value.religion=student.religion??''
            form.value.address=student.address??''
            form.value.contact_number=student.contact_number??''
            form.value.email=student.email??''
        }
    }catch(error){
        console.log("PROFILE ERROR:",error.response)
    }
}

async function checkExistingEnrollment(){
    checkingEnrollment.value=true

    try{
        const response=await api.get(
            '/student/enrollment/check-current'
        )

        console.log(
            "ENROLLMENT CHECK:",
            response.data
        )

        if(response.data.allowed===false){
            enrollmentBlocked.value=true
            enrollmentBlockMessage.value=
                response.data.message||
                "You already have an active enrollment."
        }else{
            enrollmentBlocked.value=false
            enrollmentBlockMessage.value=''
        }

        }catch(error){

            console.log(
                "ENROLLMENT CHECK ERROR:",
                error.response?.status,
                error.response?.data
            )

            enrollmentBlocked.value=false

            enrollmentBlockMessage.value=''

            showAlert(
                "error",
                "Unable to check your enrollment status. Please try again."
            )

        }finally{
        checkingEnrollment.value=false
    }
}

onMounted(()=>{
    loadStudentProfile()
    checkExistingEnrollment()
})

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

function scrollToTop(){
    window.scrollTo({
        top:0,
        behavior:"smooth"
    })
}

function getNextStep(current){
    const steps=visibleSteps.value.map(
        item=>item.number
    )

    const index=steps.indexOf(current)

    return steps[index+1]??null
}

function getPreviousStep(current){
    const steps=visibleSteps.value.map(
        item=>item.number
    )

    const index=steps.indexOf(current)

    return steps[index-1]??null
}

function validateStep(){
    const errors=[]

    if(step.value===1){
        if(!form.value.student_type)errors.push("Student Type")
        if(!form.value.first_name)errors.push("First Name")
        if(!form.value.last_name)errors.push("Last Name")
        if(!form.value.birth_date)errors.push("Birth Date")
        if(!form.value.gender)errors.push("Gender")
        if(!form.value.civil_status)errors.push("Civil Status")
        if(!form.value.nationality)errors.push("Nationality")
        if(!form.value.address)errors.push("Address")
        if(!form.value.contact_number)errors.push("Contact Number")
        if(!form.value.email)errors.push("Email")
    }

    if(step.value===2){
        if(!form.value.guardian_name)errors.push("Guardian Name")
        if(!form.value.guardian_relationship)errors.push("Guardian Relationship")
        if(!form.value.guardian_contact)errors.push("Guardian Contact")
        if(!form.value.guardian_address)errors.push("Guardian Address")
    }

    if(step.value===3&&requiresAcademic.value){

        if(!form.value.last_school)
            errors.push("Last School")

        if(!form.value.school_address)
            errors.push("School Address")

        if(isFreshman.value){

            if(!form.value.strand)
                errors.push("Strand")

            if(!form.value.graduation_year)
                errors.push("Graduation Year")

            if(!form.value.gwa)
                errors.push("GWA")

        }else if(isTransferee.value){

            if(!form.value.previous_course)
                errors.push("Previous Course")

            if(!form.value.units_earned)
                errors.push("Units Earned")

            if(!form.value.last_school_year)
                errors.push("Last School Year")

            if(!form.value.last_semester)
                errors.push("Last Semester")

        }else if(isReturning.value){

            if(!form.value.previous_course)
                errors.push("Previous Course")

            if(!form.value.last_school_year)
                errors.push("Last School Year")

            if(!form.value.last_semester)
                errors.push("Last Semester")

        }else{

            if(!form.value.graduation_year)
                errors.push("Graduation Year")

            if(!form.value.gwa)
                errors.push("GWA")
        }
    }

    if(step.value===4&&requiresDocuments.value){

        if(!form.value.psa_birth_certificate)
            errors.push("PSA Birth Certificate")

        if(!form.value.good_moral)
            errors.push("Good Moral Certificate")

        if(!form.value.academic_document)
            errors.push("Academic Document")

        if(!form.value.id_picture)
            errors.push("2x2 ID Picture")
    }

    if(step.value===5){

        if(!form.value.school_year_id)
            errors.push("School Year")

        if(!form.value.semester_id)
            errors.push("Semester")

        if(!form.value.course_id)
            errors.push("Course")

        if(!form.value.curriculum_id)
            errors.push("Curriculum")

        if(!form.value.year_level)
            errors.push("Year Level")
    }

    return errors
}

function next(){

    if(checkingEnrollment.value){
        return
    }

    if(enrollmentBlocked.value){

        showAlert(
            "error",
            enrollmentBlockMessage.value||
            "You already have an active enrollment."
        )

        scrollToTop()
        return
    }

    if(loading.value){
        return
    }

    const errors=validateStep()

    if(errors.length){

        showAlert(
            "error",
            "Please complete the following required fields: "+
            errors.join(", ")
        )

        scrollToTop()
        return
    }

    const nextStep=getNextStep(step.value)

    if(nextStep){

        step.value=nextStep
        scrollToTop()
    }
}

function previous(){

    if(
        loading.value||
        checkingEnrollment.value
    ){
        return
    }

    const previousStep=getPreviousStep(step.value)

    if(previousStep){

        step.value=previousStep
        scrollToTop()
    }
}

async function submitEnrollment(){

    if(loading.value)return

    if(!form.value.data_privacy_consent){

        showAlert(
            "error",
            "Please agree to the Data Privacy Agreement before submitting."
        )

        scrollToTop()
        return
    }

    if(!form.value.information_certified){

        showAlert(
            "error",
            "Please certify that all information submitted is true and correct."
        )

        scrollToTop()
        return
    }

    loading.value=true

    try{

        /*
        | Final duplicate check
        */

        const check=await api.get(
            '/student/enrollment/check-current'
        )

        if(check.data.allowed===false){

            enrollmentBlocked.value=true

            enrollmentBlockMessage.value=
                check.data.message||
                "You already have an active enrollment."

            showAlert(
                "error",
                enrollmentBlockMessage.value
            )

            step.value=1
            scrollToTop()
            return
        }

        const formData=new FormData()

        Object.keys(form.value).forEach(key=>{

            const value=form.value[key]

            if(
                value!==null&&
                value!==''
            ){

                formData.append(
                    key,
                    value
                )
            }
        })

        const response=await api.post(
            "/student/enrollment",
            formData,
            {
                headers:{
                    "Content-Type":
                        "multipart/form-data"
                }
            }
        )

        showAlert(
            "success",
            response.data.message||
            "Enrollment submitted successfully."
        )

        scrollToTop()

        if(response.data.redirect){

            setTimeout(()=>{
                router.push(
                    response.data.redirect
                )
            },2000)
        }

    }catch(error){

        console.log(
            "SUBMIT ERROR:",
            error.response
        )

        showAlert(
            "error",
            error.response?.data?.message||
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

    }finally{

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
                class="bi bi-check-circle-fill"
            ></i>

            <i
                v-else
                class="bi bi-exclamation-circle-fill"
            ></i>

            {{alert.message}}
        </div>
    </transition>

    <div class="enrollment-card">

        <div class="header">
            <h3>Enrollment Application</h3>
            <p>Complete all required information</p>
        </div>

        <div
            v-if="checkingEnrollment"
            class="checking-box"
        >
            <span class="spinner-border spinner-border-sm"></span>
            Checking your enrollment status...
        </div>

        <div
            v-if="enrollmentBlocked&&!checkingEnrollment"
            class="enrollment-blocked"
        >
            <i class="bi bi-exclamation-triangle-fill"></i>

            <div>
                <h5>Enrollment Already Exists</h5>
                <p>{{enrollmentBlockMessage}}</p>
            </div>
        </div>

        <div class="progress-container">
            <div
                class="progress-bar"
                :style="{width:progress+'%'}"
            ></div>
        </div>

        <div class="steps">

            <div
                v-for="(item,index) in visibleSteps"
                :key="item.number"
                class="step-item"
            >

                <div
                    class="circle"
                    :class="{
                        active:step===item.number,
                        completed:step>item.number
                    }"
                >

                    <i
                        v-if="step>item.number"
                        class="bi bi-check"
                    ></i>

                    <span v-else>
                        {{index+1}}
                    </span>

                </div>

                <p
                    :class="{
                        activeText:
                            step===item.number
                    }"
                >
                    {{item.label}}
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
                :disabled="
                    !getPreviousStep(step)||
                    loading||
                    checkingEnrollment
                "
            >
                <i class="bi bi-arrow-left"></i>
                Previous
            </button>

            <button
                v-if="getNextStep(step)"
                class="btn-next"
                @click="next"
                :disabled="
                    loading||
                    checkingEnrollment||
                    enrollmentBlocked
                "
            >

                <span
                    v-if="checkingEnrollment"
                    class="spinner-border spinner-border-sm me-2"
                ></span>

                <span v-if="checkingEnrollment">
                    Checking...
                </span>

                <span v-else>
                    Next
                    <i class="bi bi-arrow-right"></i>
                </span>

            </button>

            <button
                v-else
                class="btn-submit"
                @click="submitEnrollment"
                :disabled="
                    loading||
                    enrollmentBlocked
                "
            >

                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm me-2"
                ></span>

                <i
                    v-else
                    class="bi bi-send"
                ></i>

                {{loading?'Submitting...':'Submit Enrollment'}}

            </button>

        </div>

    </div>
</div>
</template>

<style scoped>
.enrollment-page{padding:10px}
.enrollment-card{background:white;border-radius:25px;padding:35px;box-shadow:0 10px 35px rgba(0,0,0,.08)}
.header{border-bottom:1px solid #eee;padding-bottom:20px;margin-bottom:30px}
.header h3{font-weight:800;color:#064E2A}
.header p{color:#6b7280;margin:5px 0 0}
.progress-container{height:10px;background:#e5e7eb;border-radius:20px;overflow:hidden}
.progress-bar{height:100%;background:linear-gradient(90deg,#064E2A,#0B6B3A);transition:.4s}
.steps{display:flex;justify-content:space-between;margin:35px 0}
.step-item{text-align:center;flex:1}
.circle{width:45px;height:45px;border-radius:50%;margin:auto;display:flex;justify-content:center;align-items:center;background:#f3f4f6;border:2px solid #ddd;font-weight:700}
.circle.active,.circle.completed{background:#0B6B3A;color:white;border:none}
.step-item p{margin-top:10px;font-size:13px;color:#777}
.activeText{font-weight:700;color:#0B6B3A!important}
.form-area{background:#fafafa;border-radius:20px;padding:25px}
.buttons{display:flex;justify-content:space-between;margin-top:30px}
button{padding:12px 25px;border-radius:12px;border:none;font-weight:700}
.btn-prev{background:#e5e7eb}
.btn-next,.btn-submit{background:#0B6B3A;color:white}
.btn-next:hover,.btn-submit:hover{background:#064E2A}
button:disabled{opacity:.6;cursor:not-allowed}
.alert-box{display:flex;align-items:center;gap:12px;padding:15px 20px;border-radius:15px;margin-bottom:25px;font-weight:600}
.alert-box.success{background:#dcfce7;color:#166534;border-left:5px solid #0B6B3A}
.alert-box.error{background:#fee2e2;color:#991b1b;border-left:5px solid #dc2626}
.checking-box{display:flex;align-items:center;gap:10px;padding:15px 18px;margin-bottom:20px;border-radius:12px;background:#f0fdf4;color:#166534;font-weight:600}
.enrollment-blocked{display:flex;align-items:flex-start;gap:15px;padding:18px 20px;margin-bottom:25px;border-radius:15px;background:#FEF2F2;border:1px solid #FECACA;color:#991B1B}
.enrollment-blocked>i{font-size:25px}
.enrollment-blocked h5{margin:0 0 5px;font-weight:700}
.enrollment-blocked p{margin:0;font-size:14px}
.slide-enter-active,.slide-leave-active{transition:.3s}
.slide-enter-from,.slide-leave-to{opacity:0;transform:translateY(-15px)}
@media(max-width:768px){
    .steps{overflow-x:auto}
    .step-item{min-width:90px}
    .buttons{flex-direction:column;gap:15px}
}
</style>