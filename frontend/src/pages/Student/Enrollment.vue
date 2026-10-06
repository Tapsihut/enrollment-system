<script setup>
import { onMounted,ref,computed } from 'vue'
import { useRouter } from 'vue-router'
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

const alert=ref({show:false,type:"",message:""})

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
    school_id:'',
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

    psa_birth_certificate_promissory:false,
    psa_birth_certificate_promissory_reason:'',
    good_moral_promissory:false,
    good_moral_promissory_reason:'',
    academic_document_promissory:false,
    academic_document_promissory_reason:'',
    id_picture_promissory:false,
    id_picture_promissory_reason:'',

    school_year_id:'',
    semester_id:'',
    course_id:'',
    curriculum_id:'',
    year_level:'',
    schedule_preference:'',
    remarks:'',

    data_privacy_consent:false,
    information_certified:false
})

const studentType=computed(()=>String(form.value.student_type||'').trim().toLowerCase())
const isContinuing=computed(()=>studentType.value==="continuing")
const isFreshman=computed(()=>["freshman","freshmen","new"].includes(studentType.value))
const isTransferee=computed(()=>["transferee","transfer"].includes(studentType.value))
const isReturning=computed(()=>["returning","returnee"].includes(studentType.value))
const requiresAcademic=computed(()=>!isContinuing.value)
const requiresDocuments=computed(()=>!isContinuing.value)

const visibleSteps=computed(()=>{
    const result=[
        {number:1,label:"Personal"},
        {number:2,label:"Guardian"}
    ]
    if(requiresAcademic.value) result.push({number:3,label:"Academic"})
    if(requiresDocuments.value) result.push({number:4,label:"Documents"})
    result.push({number:5,label:"Enrollment"},{number:6,label:"Review"})
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
    const index=visibleSteps.value.findIndex(item=>item.number===step.value)
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
            form.value.student_type=student.student_type??''
        }
    }catch(error){
        console.log("PROFILE ERROR:",error.response)
    }
}

async function checkExistingEnrollment(){
    checkingEnrollment.value=true
    try{
        const response=await api.get('/student/enrollment/check-current')
        console.log("ENROLLMENT CHECK:",response.data)

        if(response.data.allowed===false){
            enrollmentBlocked.value=true
            enrollmentBlockMessage.value=response.data.message||"You already have an active enrollment."
        }else{
            enrollmentBlocked.value=false
            enrollmentBlockMessage.value=''
        }
    }catch(error){
        console.log("ENROLLMENT CHECK ERROR:",error.response?.status,error.response?.data)
        enrollmentBlocked.value=false
        enrollmentBlockMessage.value=''
        showAlert("error","Unable to check your enrollment status. Please try again.")
    }finally{
        checkingEnrollment.value=false
    }
}

onMounted(()=>{
    loadStudentProfile()
    checkExistingEnrollment()
})

function showAlert(type,message){
    alert.value={show:true,type,message}
    setTimeout(()=>{alert.value.show=false},4000)
}

function scrollToTop(){
    window.scrollTo({top:0,behavior:"smooth"})
}

function getNextStep(current){
    const steps=visibleSteps.value.map(item=>item.number)
    const index=steps.indexOf(current)
    return steps[index+1]??null
}

function getPreviousStep(current){
    const steps=visibleSteps.value.map(item=>item.number)
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

    if(step.value===5){
        if(!form.value.school_year_id)errors.push("School Year")
        if(!form.value.semester_id)errors.push("Semester")
        if(!form.value.course_id)errors.push("Course")
        if(!form.value.curriculum_id)errors.push("Curriculum")
        if(!form.value.year_level)errors.push("Year Level")
    }

    return errors
}

function next(){
    if(checkingEnrollment.value)return

    if(enrollmentBlocked.value){
        showAlert("error",enrollmentBlockMessage.value||"You already have an active enrollment.")
        scrollToTop()
        return
    }

    if(loading.value)return

    const errors=validateStep()

    if(errors.length){
        showAlert("error","Please complete the following required fields: "+errors.join(", "))
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
    if(loading.value||checkingEnrollment.value)return

    const previousStep=getPreviousStep(step.value)

    if(previousStep){
        step.value=previousStep
        scrollToTop()
    }
}

async function submitEnrollment(){
    if(loading.value)return

    if(!form.value.data_privacy_consent){
        showAlert("error","Please agree to the Data Privacy Agreement before submitting.")
        scrollToTop()
        return
    }

    if(!form.value.information_certified){
        showAlert("error","Please certify that all information submitted is true and correct.")
        scrollToTop()
        return
    }

    loading.value=true

    try{
        const check=await api.get('/student/enrollment/check-current')

        if(check.data.allowed===false){
            enrollmentBlocked.value=true
            enrollmentBlockMessage.value=check.data.message||"You already have an active enrollment."
            showAlert("error",enrollmentBlockMessage.value)
            step.value=1
            scrollToTop()
            return
        }

        const formData=new FormData()

        Object.entries(form.value).forEach(([key,value])=>{
            if(value instanceof File){
                formData.append(key,value,value.name)
                return
            }

            if(typeof value==="boolean"){
                formData.append(key,value?"1":"0")
                return
            }

            if(value!==null&&value!==undefined&&value!==''){
                formData.append(key,String(value))
            }
        })

        // Explicitly append promissory fields.
        // This guarantees Laravel receives them even when no file is selected.
        const promissoryFields=[
            'psa_birth_certificate_promissory',
            'psa_birth_certificate_promissory_reason',
            'good_moral_promissory',
            'good_moral_promissory_reason',
            'academic_document_promissory',
            'academic_document_promissory_reason',
            'id_picture_promissory',
            'id_picture_promissory_reason'
        ]

        promissoryFields.forEach(key=>{
            if(key.endsWith('_promissory')){
                formData.set(key,form.value[key]?'1':'0')
            }else{
                formData.set(key,form.value[key]||'')
            }
        })

        console.log("PROMISSORY DATA:",{
            psa:form.value.psa_birth_certificate_promissory,
            psa_reason:form.value.psa_birth_certificate_promissory_reason,
            good_moral:form.value.good_moral_promissory,
            good_moral_reason:form.value.good_moral_promissory_reason,
            academic:form.value.academic_document_promissory,
            academic_reason:form.value.academic_document_promissory_reason,
            id_picture:form.value.id_picture_promissory,
            id_picture_reason:form.value.id_picture_promissory_reason
        })

        console.log("ENROLLMENT FORM DATA:")

        for(const [key,value] of formData.entries()){
            console.log(
                key,
                value instanceof File?`FILE: ${value.name}`:value
            )
        }

        const response=await api.post(
            "/student/enrollment",
            formData,
            {
                headers:{
                    "Content-Type":"multipart/form-data"
                }
            }
        )

        showAlert(
            "success",
            response.data.message||"Enrollment submitted successfully."
        )

        scrollToTop()

        if(response.data.redirect){
            setTimeout(()=>{
                router.push(response.data.redirect)
            },2000)
        }

    }catch(error){
        console.log("SUBMIT ERROR:",error.response)

        showAlert(
            "error",
            error.response?.data?.message||
            "Enrollment failed. Please try again."
        )

        scrollToTop()

        if(error.response?.data?.redirect){
            setTimeout(()=>{
                router.push(error.response.data.redirect)
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
            <div v-if="alert.show" class="alert-box" :class="alert.type">
                <i v-if="alert.type==='success'" class="bi bi-check-circle-fill"></i>
                <i v-else class="bi bi-exclamation-circle-fill"></i>
                <span>{{alert.message}}</span>
            </div>
        </transition>

        <div class="enrollment-card">
            <div class="header">
                <h3>Enrollment Application</h3>
                <p>Complete all required information</p>
            </div>

            <div v-if="checkingEnrollment" class="checking-box">
                <span class="spinner-border spinner-border-sm"></span>
                <span>Checking your enrollment status...</span>
            </div>

            <div v-if="enrollmentBlocked&&!checkingEnrollment" class="enrollment-blocked">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div class="blocked-content">
                    <h5>Enrollment Already Exists</h5>
                    <p>{{enrollmentBlockMessage}}</p>
                </div>
            </div>

            <div class="progress-container">
                <div class="progress-bar" :style="{width:progress+'%'}"></div>
            </div>

            <div class="steps">
                <div v-for="(item,index) in visibleSteps" :key="item.number" class="step-item">
                    <div class="circle" :class="{active:step===item.number,completed:step>item.number}">
                        <i v-if="step>item.number" class="bi bi-check"></i>
                        <span v-else>{{index+1}}</span>
                    </div>
                    <p :class="{activeText:step===item.number}">{{item.label}}</p>
                </div>
            </div>

            <div class="form-area">
                <component :is="currentComponent" v-model="form"/>
            </div>

            <div class="buttons">
                <button type="button" class="btn-prev" @click="previous" :disabled="!getPreviousStep(step)||loading||checkingEnrollment">
                    <i class="bi bi-arrow-left"></i>
                    <span>Previous</span>
                </button>

                <button v-if="getNextStep(step)" type="button" class="btn-next" @click="next" :disabled="loading||checkingEnrollment||enrollmentBlocked">
                    <span v-if="checkingEnrollment" class="spinner-border spinner-border-sm"></span>
                    <span v-if="checkingEnrollment">Checking...</span>
                    <span v-else>Next <i class="bi bi-arrow-right"></i></span>
                </button>

                <button v-else type="button" class="btn-submit" @click="submitEnrollment" :disabled="loading||enrollmentBlocked">
                    <span v-if="loading" class="spinner-border spinner-border-sm"></span>
                    <i v-else class="bi bi-send"></i>
                    <span>{{loading?'Submitting...':'Submit Enrollment'}}</span>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.enrollment-page{padding:6px 8px;width:100%;max-width:100%}
.enrollment-card{width:100%;background:#fff;border-radius:16px;padding:16px;box-shadow:0 6px 24px rgba(0,0,0,.06);overflow:hidden}
.header{border-bottom:1px solid #eee;padding-bottom:11px;margin-bottom:14px}
.header h3{font-size:20px;line-height:1.2;font-weight:800;color:#064E2A;margin:0}
.header p{color:#6b7280;font-size:12px;margin:3px 0 0}
.progress-container{height:6px;background:#e5e7eb;border-radius:20px;overflow:hidden}
.progress-bar{height:100%;background:linear-gradient(90deg,#064E2A,#0B6B3A);transition:width .4s ease}
.steps{display:flex;align-items:flex-start;justify-content:space-between;gap:4px;margin:15px 0;width:100%}
.step-item{text-align:center;flex:1;min-width:0}
.circle{width:34px;height:34px;border-radius:50%;margin:auto;display:flex;justify-content:center;align-items:center;background:#f3f4f6;border:1px solid #d9dde2;color:#6b7280;font-size:12px;font-weight:700}
.circle.active,.circle.completed{background:#0B6B3A;color:#fff;border-color:#0B6B3A}
.step-item p{margin:5px 0 0;font-size:10px;line-height:1.2;color:#777;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.activeText{font-weight:700;color:#0B6B3A!important}
.form-area{background:#fafafa;border-radius:12px;padding:12px;width:100%;min-width:0}
.buttons{display:grid;grid-template-columns:1fr 1fr;align-items:stretch;gap:8px;width:100%;margin-top:14px}
.buttons button{width:100%;min-width:0;min-height:40px;padding:8px 14px;border-radius:9px;border:none;font-size:13px;font-weight:700;display:inline-flex;align-items:center;justify-content:center;gap:7px;transition:.2s ease}
.btn-prev{background:#e5e7eb;color:#374151}
.btn-prev:hover:not(:disabled){background:#d1d5db}
.btn-next,.btn-submit{background:#0B6B3A;color:#fff}
.btn-next:hover:not(:disabled),.btn-submit:hover:not(:disabled){background:#064E2A}
.btn-submit{grid-column:2}
button:disabled{opacity:.6;cursor:not-allowed}
button .spinner-border{width:14px;height:14px;border-width:2px}
.alert-box{display:flex;align-items:flex-start;gap:8px;padding:10px 12px;border-radius:9px;margin-bottom:10px;font-size:12px;line-height:1.4;font-weight:600}
.alert-box i{flex-shrink:0;margin-top:1px}
.alert-box.success{background:#dcfce7;color:#166534;border-left:3px solid #0B6B3A}
.alert-box.error{background:#fee2e2;color:#991b1b;border-left:3px solid #dc2626}
.checking-box{display:flex;align-items:center;gap:7px;padding:9px 11px;margin-bottom:10px;border-radius:8px;background:#f0fdf4;color:#166534;font-size:12px;font-weight:600}
.checking-box .spinner-border{width:13px;height:13px}
.enrollment-blocked{display:flex;align-items:flex-start;gap:9px;padding:11px 12px;margin-bottom:12px;border-radius:9px;background:#FEF2F2;border:1px solid #FECACA;color:#991B1B}
.enrollment-blocked>i{flex-shrink:0;font-size:18px;margin-top:1px}
.blocked-content{min-width:0}
.enrollment-blocked h5{margin:0 0 2px;font-size:13px;font-weight:700}
.enrollment-blocked p{margin:0;font-size:11px;line-height:1.4;word-break:break-word}
.slide-enter-active,.slide-leave-active{transition:.25s ease}
.slide-enter-from,.slide-leave-to{opacity:0;transform:translateY(-8px)}

@media(max-width:768px){
    .enrollment-page{padding:4px 5px}
    .enrollment-card{padding:12px;border-radius:13px;box-shadow:0 4px 18px rgba(0,0,0,.05)}
    .header{padding-bottom:9px;margin-bottom:11px}
    .header h3{font-size:18px}
    .header p{font-size:11px}
    .steps{margin:12px 0;gap:2px}
    .circle{width:31px;height:31px;font-size:11px}
    .step-item p{font-size:9px;margin-top:4px}
    .form-area{padding:9px;border-radius:10px}
    .buttons{margin-top:11px;gap:7px}
    .buttons button{min-height:39px;padding:8px 12px;font-size:12px}
}

@media(max-width:576px){
    .enrollment-page{padding:2px}
    .enrollment-card{padding:8px;border-radius:11px}
    .header{padding-bottom:8px;margin-bottom:9px}
    .header h3{font-size:17px}
    .header p{font-size:10px;margin-top:2px}
    .progress-container{height:5px}
    .steps{margin:10px 0;gap:1px}
    .circle{width:28px;height:28px;font-size:10px}
    .step-item p{font-size:8px;margin-top:3px}
    .form-area{padding:7px;border-radius:9px}
    .buttons{display:grid;grid-template-columns:1fr 1fr;gap:6px;margin-top:9px;width:100%}
    .buttons button{width:100%;min-height:38px;padding:7px 8px;font-size:11px;white-space:nowrap}
    .btn-prev{grid-column:1}
    .btn-next,.btn-submit{grid-column:2}
    .alert-box{padding:8px 9px;margin-bottom:8px;font-size:11px;border-radius:8px}
    .checking-box{padding:8px 9px;margin-bottom:8px;font-size:11px}
    .enrollment-blocked{padding:9px 10px;margin-bottom:9px;gap:7px}
    .enrollment-blocked>i{font-size:16px}
    .enrollment-blocked h5{font-size:12px}
    .enrollment-blocked p{font-size:10px}
}

@media(max-width:380px){
    .enrollment-card{padding:6px}
    .header h3{font-size:16px}
    .circle{width:25px;height:25px;font-size:9px}
    .step-item p{font-size:7px}
    .form-area{padding:5px}
    .buttons{gap:5px}
    .buttons button{font-size:10px;padding-left:5px;padding-right:5px}
}
</style>
