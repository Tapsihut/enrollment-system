<template>
<div class="documents-page">

    <div class="page-header">
        <h3>Step 4 - Upload Requirements</h3>
        <p>Upload clear scanned copies of your required documents.</p>
    </div>

    <!-- CONTINUING -->
    <div v-if="isContinuing" class="continuing-notice">
        <div class="continuing-icon">
            <i class="bi bi-check-circle-fill"></i>
        </div>
        <div>
            <h5>Document Upload Not Required</h5>
            <p>
                Since you are a <strong>Continuing Student</strong>,
                your existing records will be used for this enrollment.
            </p>
        </div>
    </div>

    <!-- DOCUMENTS -->
    <template v-else>

        <div class="notice">
            <i class="bi bi-info-circle-fill"></i>
            <div>
                Accepted file types:
                <strong>PDF, JPG, JPEG, PNG</strong>
                <br>
                Maximum file size:
                <strong>5 MB</strong>
            </div>
        </div>

        <div class="row g-4">

            <!-- PSA -->
            <div class="col-md-6">
                <div class="upload-card">
                    <i class="bi bi-file-earmark-text-fill upload-icon"></i>
                    <h5>PSA Birth Certificate</h5>
                    <p>Required for enrollment verification.</p>

                    <input
                        type="file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                        @change="upload($event,'psa_birth_certificate')"
                    >

                    <div v-if="model.psa_birth_certificate" class="success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{model.psa_birth_certificate.name}}
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
                        type="file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                        @change="upload($event,'good_moral')"
                    >

                    <div v-if="model.good_moral" class="success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{model.good_moral.name}}
                    </div>
                </div>
            </div>

            <!-- ACADEMIC DOCUMENT -->
            <div class="col-md-6">
                <div class="upload-card">
                    <i class="bi bi-folder-fill upload-icon"></i>

                    <h5>{{academicDocument}}</h5>

                    <p>{{academicDescription}}</p>

                    <input
                        type="file"
                        class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png"
                        @change="upload($event,'academic_document')"
                    >

                    <div v-if="model.academic_document" class="success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{model.academic_document.name}}
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
                        type="file"
                        class="form-control"
                        accept=".jpg,.jpeg,.png"
                        @change="upload($event,'id_picture')"
                    >

                    <div v-if="model.id_picture" class="success">
                        <i class="bi bi-check-circle-fill"></i>
                        {{model.id_picture.name}}
                    </div>
                </div>
            </div>

        </div>

    </template>

</div>
</template>

<script setup>
import {computed} from "vue"

const model=defineModel()

const studentType=computed(()=>{
    return String(model.value.student_type||'').trim().toLowerCase()
})

const isContinuing=computed(()=>{
    return studentType.value==="continuing"
})

const isTransferee=computed(()=>{
    return ["transferee","transfer"].includes(studentType.value)
})

const isReturnee=computed(()=>{
    return ["returnee","returning"].includes(studentType.value)
})

const academicDocument=computed(()=>{

    if(isTransferee.value)
        return "Transcript of Records (TOR)"

    if(isReturnee.value)
        return "Previous Registration Form"

    return "Form 138 / Report Card"
})

const academicDescription=computed(()=>{

    if(isTransferee.value)
        return "Submit your official Transcript of Records from your previous school."

    if(isReturnee.value)
        return "Submit your previous registration or enrollment record."

    return "Submit your Form 138 or Senior High School Report Card."
})

function upload(event,field){

    const file=event.target.files[0]

    if(!file)return

    const allowed=[
        "application/pdf",
        "image/jpeg",
        "image/png"
    ]

    const maxSize=5*1024*1024

    if(!allowed.includes(file.type)){

        alert("Invalid file type. Please upload PDF, JPG, JPEG, or PNG.")
        event.target.value=""
        return
    }

    if(file.size>maxSize){

        alert("File size must not exceed 5 MB.")
        event.target.value=""
        return
    }

    model.value[field]=file
}
</script>

<style scoped>
.documents-page{padding:10px}
.page-header{margin-bottom:25px}
.page-header h3{font-weight:800;color:#064E2A}
.page-header p{color:#6B7280}

.notice{background:#ECFDF5;border:1px solid #A7F3D0;color:#065F46;border-radius:15px;padding:18px;display:flex;gap:15px;margin-bottom:30px}
.notice i{font-size:30px}

.continuing-notice{display:flex;align-items:center;gap:18px;padding:25px;margin-bottom:25px;background:#ECFDF5;border:1px solid #A7F3D0;border-radius:20px;box-shadow:0 8px 20px rgba(0,0,0,.05)}
.continuing-icon{width:55px;height:55px;display:flex;align-items:center;justify-content:center;flex-shrink:0;border-radius:50%;background:#D1FAE5;color:#047857}
.continuing-icon i{font-size:28px}
.continuing-notice h5{margin:0 0 5px;color:#065F46;font-weight:700}
.continuing-notice p{margin:0;color:#047857;font-size:14px}

.upload-card{background:white;border:1px solid #E5E7EB;border-radius:20px;padding:25px;text-align:center;transition:.3s;box-shadow:0 8px 20px rgba(0,0,0,.05)}
.upload-card:hover{transform:translateY(-5px);border-color:#0B6B3A}
.upload-icon{font-size:45px;color:#0B6B3A;margin-bottom:15px}
.upload-card h5{font-weight:700}
.upload-card p{color:#6B7280;min-height:40px}
.form-control{border-radius:12px}
.success{margin-top:15px;background:#DCFCE7;color:#166534;border-radius:10px;padding:10px;font-weight:600;font-size:14px;word-break:break-all}
.success i{margin-right:8px}

@media(max-width:768px){
    .continuing-notice{align-items:flex-start;padding:20px}
}
</style>