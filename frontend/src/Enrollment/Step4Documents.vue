<template>
    <div class="documents-page">

        <div class="page-header">
            <h3>Step 4 - Upload Requirements</h3>
            <p>
                Upload clear scanned copies of your required documents.
                If you cannot provide a required document yet, you may submit a promissory undertaking.
            </p>
        </div>

        <div v-if="isContinuing" class="continuing-notice">
            <div class="continuing-icon">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <h5>Document Upload Not Required</h5>
                <p>
                    Since you are <strong>Continuing Student</strong>,
                    your existing records will be used for this enrollment.
                </p>
            </div>
        </div>

        <template v-else>

            <div class="notice">
                <i class="bi bi-shield-check"></i>
                <div>
                    <strong>Secure Document Upload</strong>
                    <div>Accepted: <strong>PDF, JPG, JPEG, PNG</strong></div>
                    <div>Maximum file size: <strong>5 MB</strong></div>
                    <div>ID Picture: <strong>3 MB</strong></div>
                    <div>
                        If you cannot provide a required document,
                        you may request a <strong>Promissory Undertaking</strong>.
                    </div>
                </div>
            </div>

            <div class="row g-2">

                <!-- PSA -->
                <div class="col-md-6">
                    <div class="upload-card">
                        <i class="bi bi-file-earmark-text-fill upload-icon"></i>
                        <h5>PSA Birth Certificate</h5>
                        <p>Required for enrollment verification.</p>

                        <input
                            :ref="el => fileInputs.psa_birth_certificate = el"
                            type="file"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                            :disabled="promissory.psa_birth_certificate || uploading"
                            @change="upload($event,'psa_birth_certificate')"
                        >

                        <div v-if="model.psa_birth_certificate" class="file-success">
                            <div class="file-info">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>{{ displayFileName(model.psa_birth_certificate) }}</strong>
                                    <small>{{ formatFileSize(model.psa_birth_certificate.size) }}</small>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="remove-file"
                                @click="removeFile('psa_birth_certificate')"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="promissory-option">
                            <label class="promissory-check">
                                <input
                                    type="checkbox"
                                    v-model="promissory.psa_birth_certificate"
                                    :disabled="uploading"
                                    @change="togglePromissory('psa_birth_certificate')"
                                >
                                <span>I cannot provide this document right now.</span>
                            </label>

                            <div v-if="promissory.psa_birth_certificate" class="promissory-box">
                                <label>Reason for Promissory Undertaking</label>
                                <textarea
                                    v-model="promissoryReasons.psa_birth_certificate"
                                    class="form-control"
                                    rows="2"
                                    maxlength="500"
                                    placeholder="Please explain why you cannot provide this document yet."
                                    @input="syncPromissoryReason('psa_birth_certificate')"
                                ></textarea>
                                <small>{{ promissoryReasons.psa_birth_certificate.length }}/500</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GOOD MORAL -->
                <div class="col-md-6">
                    <div class="upload-card">
                        <i class="bi bi-award-fill upload-icon"></i>
                        <h5>Good Moral Certificate</h5>
                        <p>Issued by your previous school.</p>

                        <input
                            :ref="el => fileInputs.good_moral = el"
                            type="file"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                            :disabled="promissory.good_moral || uploading"
                            @change="upload($event,'good_moral')"
                        >

                        <div v-if="model.good_moral" class="file-success">
                            <div class="file-info">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>{{ displayFileName(model.good_moral) }}</strong>
                                    <small>{{ formatFileSize(model.good_moral.size) }}</small>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="remove-file"
                                @click="removeFile('good_moral')"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="promissory-option">
                            <label class="promissory-check">
                                <input
                                    type="checkbox"
                                    v-model="promissory.good_moral"
                                    :disabled="uploading"
                                    @change="togglePromissory('good_moral')"
                                >
                                <span>I cannot provide this document right now.</span>
                            </label>

                            <div v-if="promissory.good_moral" class="promissory-box">
                                <label>Reason for Promissory Undertaking</label>
                                <textarea
                                    v-model="promissoryReasons.good_moral"
                                    class="form-control"
                                    rows="2"
                                    maxlength="500"
                                    placeholder="Please explain why you cannot provide this document yet."
                                    @input="syncPromissoryReason('good_moral')"
                                ></textarea>
                                <small>{{ promissoryReasons.good_moral.length }}/500</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACADEMIC DOCUMENT -->
                <div class="col-md-6">
                    <div class="upload-card">
                        <i class="bi bi-folder-fill upload-icon"></i>
                        <h5>{{ academicDocument }}</h5>
                        <p>{{ academicDescription }}</p>

                        <input
                            :ref="el => fileInputs.academic_document = el"
                            type="file"
                            class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png"
                            :disabled="promissory.academic_document || uploading"
                            @change="upload($event,'academic_document')"
                        >

                        <div v-if="model.academic_document" class="file-success">
                            <div class="file-info">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>{{ displayFileName(model.academic_document) }}</strong>
                                    <small>{{ formatFileSize(model.academic_document.size) }}</small>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="remove-file"
                                @click="removeFile('academic_document')"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="promissory-option">
                            <label class="promissory-check">
                                <input
                                    type="checkbox"
                                    v-model="promissory.academic_document"
                                    :disabled="uploading"
                                    @change="togglePromissory('academic_document')"
                                >
                                <span>I cannot provide this document right now.</span>
                            </label>

                            <div v-if="promissory.academic_document" class="promissory-box">
                                <label>Reason for Promissory Undertaking</label>
                                <textarea
                                    v-model="promissoryReasons.academic_document"
                                    class="form-control"
                                    rows="2"
                                    maxlength="500"
                                    placeholder="Please explain why you cannot provide this document yet."
                                    @input="syncPromissoryReason('academic_document')"
                                ></textarea>
                                <small>{{ promissoryReasons.academic_document.length }}/500</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ID -->
                <div class="col-md-6">
                    <div class="upload-card">
                        <i class="bi bi-person-badge-fill upload-icon"></i>
                        <h5>2x2 ID Picture</h5>
                        <p>Recent photo with white background preferred.</p>

                        <input
                            :ref="el => fileInputs.id_picture = el"
                            type="file"
                            class="form-control"
                            accept=".jpg,.jpeg,.png,image/jpeg,image/png"
                            :disabled="promissory.id_picture || uploading"
                            @change="upload($event,'id_picture')"
                        >

                        <div v-if="model.id_picture" class="file-success">
                            <div class="file-info">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <strong>{{ displayFileName(model.id_picture) }}</strong>
                                    <small>{{ formatFileSize(model.id_picture.size) }}</small>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="remove-file"
                                @click="removeFile('id_picture')"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="promissory-option">
                            <label class="promissory-check">
                                <input
                                    type="checkbox"
                                    v-model="promissory.id_picture"
                                    :disabled="uploading"
                                    @change="togglePromissory('id_picture')"
                                >
                                <span>I cannot provide this document right now.</span>
                            </label>

                            <div v-if="promissory.id_picture" class="promissory-box">
                                <label>Reason for Promissory Undertaking</label>
                                <textarea
                                    v-model="promissoryReasons.id_picture"
                                    class="form-control"
                                    rows="2"
                                    maxlength="500"
                                    placeholder="Please explain why you cannot provide this document yet."
                                    @input="syncPromissoryReason('id_picture')"
                                ></textarea>
                                <small>{{ promissoryReasons.id_picture.length }}/500</small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div v-if="promissoryDocuments.length" class="promissory-summary">
                <div class="summary-icon">
                    <i class="bi bi-file-earmark-check-fill"></i>
                </div>

                <div>
                    <h5>Promissory Undertaking Requested</h5>
                    <p>
                        You are requesting permission to submit the following
                        document(s) at a later time:
                    </p>

                    <ul>
                        <li
                            v-for="document in promissoryDocuments"
                            :key="document"
                        >
                            {{ documentLabel(document) }}
                        </li>
                    </ul>

                    <small>Your request will be reviewed by the college.</small>
                </div>
            </div>

        </template>
    </div>
</template>

<script setup>
import { computed, reactive, ref, onMounted } from "vue"

const model = defineModel()

const uploading = ref(false)

const fileInputs = reactive({
    psa_birth_certificate:null,
    good_moral:null,
    academic_document:null,
    id_picture:null
})

const fileRules = {
    psa_birth_certificate:{
        maxSize:5*1024*1024,
        extensions:["pdf","jpg","jpeg","png"],
        mimeTypes:["application/pdf","image/jpeg","image/png"]
    },
    good_moral:{
        maxSize:5*1024*1024,
        extensions:["pdf","jpg","jpeg","png"],
        mimeTypes:["application/pdf","image/jpeg","image/png"]
    },
    academic_document:{
        maxSize:5*1024*1024,
        extensions:["pdf","jpg","jpeg","png"],
        mimeTypes:["application/pdf","image/jpeg","image/png"]
    },
    id_picture:{
        maxSize:3*1024*1024,
        extensions:["jpg","jpeg","png"],
        mimeTypes:["image/jpeg","image/png"]
    }
}

const studentType = computed(() =>
    String(model.value.student_type||"").trim().toLowerCase()
)

const isContinuing = computed(() =>
    studentType.value==="continuing"
)

const isTransferee = computed(() =>
    ["transferee","transfer"].includes(studentType.value)
)

const isReturnee = computed(() =>
    ["returnee","returning"].includes(studentType.value)
)

const academicDocument = computed(() => {
    if(isTransferee.value)return "Transcript of Records (TOR)"
    if(isReturnee.value)return "Previous Registration Form"
    return "Form 138 / Report Card"
})

const academicDescription = computed(() => {
    if(isTransferee.value)
        return "Submit your official Transcript of Records from your previous school."
    if(isReturnee.value)
        return "Submit your previous registration or enrollment record."
    return "Submit your Form 138 or Senior High School Report Card."
})

const promissory = reactive({
    psa_birth_certificate:false,
    good_moral:false,
    academic_document:false,
    id_picture:false
})

const promissoryReasons = reactive({
    psa_birth_certificate:"",
    good_moral:"",
    academic_document:"",
    id_picture:""
})

/*
|--------------------------------------------------------------------------
| LOAD EXISTING VALUES FROM PARENT MODEL
|--------------------------------------------------------------------------
*/

function loadPromissoryValues(){
    const fields=[
        "psa_birth_certificate",
        "good_moral",
        "academic_document",
        "id_picture"
    ]

    fields.forEach(field=>{
        const promissoryField=`${field}_promissory`
        const reasonField=`${field}_promissory_reason`

        promissory[field]=Boolean(
            model.value[promissoryField]
        )

        promissoryReasons[field]=
            model.value[reasonField]||""
    })
}

onMounted(()=>{
    loadPromissoryValues()
})

const promissoryDocuments = computed(() =>
    Object.keys(promissory).filter(field=>promissory[field])
)

function documentLabel(field){
    const labels={
        psa_birth_certificate:"PSA Birth Certificate",
        good_moral:"Good Moral Certificate",
        academic_document:academicDocument.value,
        id_picture:"2x2 ID Picture"
    }

    return labels[field]||field
}

function displayFileName(file){
    if(!file)return ""
    const name=String(file.name||"Selected file")
    return name.length>70?name.substring(0,67)+"...":name
}

function formatFileSize(bytes){
    if(!bytes||bytes<=0)return "0 KB"
    if(bytes<1024*1024)return `${Math.ceil(bytes/1024)} KB`
    return `${(bytes/(1024*1024)).toFixed(2)} MB`
}

function getExtension(filename){
    const name=String(filename||"").trim().toLowerCase()
    const parts=name.split(".")
    if(parts.length<2)return ""
    return parts.pop()
}

function validateFile(file,field){
    if(!file){
        return {
            valid:false,
            message:"No file was selected."
        }
    }

    const rules=fileRules[field]

    if(!rules){
        return {
            valid:false,
            message:"This document type is not allowed."
        }
    }

    if(file.size<=0){
        return {
            valid:false,
            message:"The selected file is empty."
        }
    }

    if(file.size>rules.maxSize){
        return {
            valid:false,
            message:field==="id_picture"
                ?"ID picture must not exceed 3 MB."
                :"File size must not exceed 5 MB."
        }
    }

    const extension=getExtension(file.name)

    if(!rules.extensions.includes(extension)){
        return {
            valid:false,
            message:"Invalid file extension. Please upload an allowed document type."
        }
    }

    if(
        file.type &&
        !rules.mimeTypes.includes(file.type.toLowerCase())
    ){
        return {
            valid:false,
            message:"The selected file type is not allowed. Please choose a valid PDF, JPG, JPEG, or PNG file."
        }
    }

    return {valid:true}
}

/*
|--------------------------------------------------------------------------
| UPLOAD
|--------------------------------------------------------------------------
*/

function upload(event,field){
    const input=event.target

    const file=
        input.files&&input.files.length
            ?input.files[0]
            :null

    const validation=validateFile(file,field)

    if(!validation.valid){
        alert(validation.message)
        input.value=""
        return
    }

    /*
     * Uploading a file cancels the promissory request.
     */
    promissory[field]=false
    promissoryReasons[field]=""

    model.value[field]=file

    model.value[`${field}_promissory`]=false
    model.value[`${field}_promissory_reason`]=""
}

/*
|--------------------------------------------------------------------------
| REMOVE FILE
|--------------------------------------------------------------------------
*/

function removeFile(field){
    model.value[field]=null

    if(fileInputs[field]){
        fileInputs[field].value=""
    }
}

/*
|--------------------------------------------------------------------------
| TOGGLE PROMISSORY
|--------------------------------------------------------------------------
*/

function togglePromissory(field){

    /*
     * v-model has already changed promissory[field].
     * Now copy that value into the parent form.
     */
    model.value[`${field}_promissory`] =
        !!promissory[field]

    if(promissory[field]){

        /*
         * Promissory selected:
         * remove any uploaded file.
         */
        model.value[field]=null

        if(fileInputs[field]){
            fileInputs[field].value=""
        }

        model.value[`${field}_promissory_reason`] =
            promissoryReasons[field]||""

    }else{

        /*
         * Promissory cancelled.
         */
        promissoryReasons[field]=""

        model.value[`${field}_promissory_reason`]=""
    }
}

/*
|--------------------------------------------------------------------------
| PROMISSORY REASON
|--------------------------------------------------------------------------
*/

function syncPromissoryReason(field){
    model.value[`${field}_promissory_reason`] =
        promissoryReasons[field]||""
}
</script>

<style scoped>
.documents-page{padding:6px 8px;width:100%}
.page-header{margin-bottom:12px}
.page-header h3{margin:0 0 4px;font-size:20px;font-weight:800;color:#064E2A}
.page-header p{color:#6B7280;margin:0;font-size:13px;line-height:1.45}
.notice{background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;border-radius:10px;padding:10px 12px;display:flex;align-items:flex-start;gap:9px;margin-bottom:12px;font-size:12px;line-height:1.45}
.notice>i{flex-shrink:0;font-size:20px;color:#047857;margin-top:1px}
.continuing-notice{display:flex;align-items:center;gap:10px;padding:12px;margin-bottom:12px;background:#ECFDF5;border:1px solid #A7F3D0;border-radius:11px}
.continuing-icon{width:38px;height:38px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:50%;background:#D1FAE5;color:#047857}
.continuing-icon i{font-size:19px}
.continuing-notice h5{margin:0 0 2px;font-size:14px;color:#065F46;font-weight:700}
.continuing-notice p{margin:0;color:#047857;font-size:12px;line-height:1.4}
.upload-card{height:100%;background:#fff;border:1px solid #E5E7EB;border-radius:11px;padding:14px;text-align:center;transition:.2s ease;box-shadow:0 2px 8px rgba(0,0,0,.035)}
.upload-card:hover{border-color:#0B6B3A;box-shadow:0 4px 12px rgba(0,0,0,.06)}
.upload-icon{display:block;font-size:29px;color:#0B6B3A;margin-bottom:4px}
.upload-card h5{margin:0 0 3px;font-size:15px;font-weight:700;line-height:1.3}
.upload-card p{color:#6B7280;min-height:32px;margin:0 0 8px;font-size:12px;line-height:1.35}
.form-control{border-radius:8px;font-size:13px;padding:7px 9px}
input[type="file"]{padding:6px 8px}
.form-control:focus{border-color:#0B6B3A;box-shadow:0 0 0 .12rem rgba(11,107,58,.12)}
.file-success{display:flex;align-items:center;justify-content:space-between;gap:7px;margin-top:7px;padding:7px 8px;background:#DCFCE7;color:#166534;border:1px solid #BBF7D0;border-radius:8px;text-align:left}
.file-info{min-width:0;display:flex;align-items:center;gap:6px}
.file-info>i{flex-shrink:0;font-size:16px}
.file-info div{min-width:0;display:flex;flex-direction:column}
.file-info strong{font-size:11px;line-height:1.25;word-break:break-word}
.file-info small{margin-top:1px;color:#15803D;font-size:10px}
.remove-file{width:27px;height:27px;flex-shrink:0;border:0;border-radius:6px;background:#FEE2E2;color:#B91C1C;display:flex;align-items:center;justify-content:center;cursor:pointer}
.remove-file:hover{background:#FECACA}
.promissory-option{margin-top:9px;padding-top:8px;border-top:1px solid #E5E7EB;text-align:left}
.promissory-check{display:flex;align-items:flex-start;gap:6px;cursor:pointer;color:#374151;font-size:11px;line-height:1.35;font-weight:600}
.promissory-check input{width:15px;height:15px;margin:0;margin-top:1px;flex-shrink:0;accent-color:#0B6B3A;cursor:pointer}
.promissory-box{margin-top:7px;padding:8px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:8px}
.promissory-box label{display:block;margin-bottom:3px;font-size:11px;font-weight:700;color:#92400E}
.promissory-box textarea{resize:vertical;min-height:55px;font-size:12px;padding:6px 8px}
.promissory-box small{display:block;text-align:right;margin-top:2px;color:#92400E;font-size:10px}
.promissory-summary{display:flex;align-items:flex-start;gap:9px;margin-top:12px;padding:11px 12px;background:#FFFBEB;border:1px solid #FDE68A;border-radius:10px;color:#78350F}
.summary-icon{width:34px;height:34px;flex-shrink:0;border-radius:50%;background:#FEF3C7;display:flex;align-items:center;justify-content:center}
.summary-icon i{font-size:16px;color:#B45309}
.promissory-summary h5{margin:0 0 2px;font-size:14px;font-weight:700}
.promissory-summary p{margin:0 0 4px;font-size:11px;line-height:1.4}
.promissory-summary ul{margin:3px 0 4px;padding-left:16px}
.promissory-summary li{font-size:11px;margin-bottom:1px}
.promissory-summary small{color:#92400E;font-size:10px}

@media(max-width:768px){
    .documents-page{padding:4px 2px}
    .page-header{margin-bottom:9px}
    .page-header h3{font-size:18px}
    .page-header p{font-size:12px;line-height:1.4}
    .notice{padding:9px 10px;margin-bottom:9px;gap:7px;font-size:11px;line-height:1.4;border-radius:9px}
    .notice>i{font-size:18px}
    .continuing-notice{padding:10px;gap:8px;margin-bottom:9px;border-radius:9px}
    .continuing-icon{width:34px;height:34px}
    .continuing-icon i{font-size:17px}
    .continuing-notice h5{font-size:13px}
    .continuing-notice p{font-size:11px}
    .row{--bs-gutter-x:7px;--bs-gutter-y:7px}
    .upload-card{padding:11px;border-radius:9px}
    .upload-icon{font-size:25px;margin-bottom:3px}
    .upload-card h5{font-size:13px}
    .upload-card p{min-height:auto;margin-bottom:6px;font-size:11px}
    .form-control{font-size:12px;padding:6px 7px}
    input[type="file"]{font-size:11px;padding:5px}
    .file-success{margin-top:6px;padding:6px}
    .file-info{gap:5px}
    .file-info strong{font-size:10px}
    .file-info small{font-size:9px}
    .remove-file{width:25px;height:25px}
    .promissory-option{margin-top:7px;padding-top:7px}
    .promissory-check{font-size:10px}
    .promissory-check input{width:14px;height:14px}
    .promissory-box{padding:7px;margin-top:6px}
    .promissory-box label{font-size:10px}
    .promissory-box textarea{font-size:11px;min-height:50px}
    .promissory-box small{font-size:9px}
    .promissory-summary{margin-top:9px;padding:9px;gap:7px;border-radius:9px}
    .summary-icon{width:30px;height:30px}
    .summary-icon i{font-size:14px}
    .promissory-summary h5{font-size:12px}
    .promissory-summary p,.promissory-summary li{font-size:10px}
    .promissory-summary small{font-size:9px}
}

@media(max-width:380px){
    .documents-page{padding:3px 1px}
    .page-header h3{font-size:17px}
    .page-header p{font-size:11px}
    .upload-card{padding:9px}
    .upload-icon{font-size:23px}
    .upload-card h5{font-size:12px}
    .upload-card p{font-size:10px}
    input[type="file"]{font-size:10px}
    .promissory-check{font-size:9.5px}
}
</style>
