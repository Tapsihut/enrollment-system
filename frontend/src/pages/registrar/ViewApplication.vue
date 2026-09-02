<template>

<div class="application-page">


<!-- SUCCESS / ERROR MESSAGE -->

<div
v-if="alert.show"
class="alert-box"
:class="alert.type"
>

<i
v-if="alert.type === 'success'"
class="bi bi-check-circle-fill">
</i>

<i
v-if="alert.type === 'error'"
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

<label>
Birth Date
</label>

<p>
{{ formatDate(enrollment.student?.birth_date) }}
</p>

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


<!-- EXISTING REJECTION INFORMATION -->

<div
v-if="
    enrollment.status === 'Rejected' &&
    enrollment.rejection_reason
"
class="review-card rejection-history"
>

<div class="section-title rejection-title">

<i class="bi bi-exclamation-triangle-fill"></i>

Rejection Information

</div>


<div class="rejection-message">

<strong>
Reason for Rejection
</strong>

<p>
{{ enrollment.rejection_reason }}
</p>


<small v-if="enrollment.rejected_at">

Rejected on:
{{ formatDate(enrollment.rejected_at) }}

</small>

</div>

</div>


<!-- ACTIONS -->

<div
v-if="enrollment.status === 'Pending'"
class="action-area"
>

<button
class="btn btn-danger"
@click="openRejectModal"
:disabled="processing"
>

<i class="bi bi-x-circle"></i>

Reject

</button>


<button
class="btn btn-success"
@click="approveApplication"
:disabled="processing"
>

<i class="bi bi-check-circle"></i>

Approve

</button>

</div>


</div>


<!-- ================================================= -->
<!-- REJECTION MODAL -->
<!-- ================================================= -->

<div
v-if="showRejectModal"
class="modal-overlay"
@click.self="closeRejectModal"
>


<div class="reject-modal">


<!-- MODAL HEADER -->

<div class="modal-header">

<div>

<h3>
Reject Enrollment Application
</h3>

<p>
Please provide a reason for rejecting this application.
</p>

</div>


<button
class="modal-close"
@click="closeRejectModal"
:disabled="processing"
>

<i class="bi bi-x-lg"></i>

</button>

</div>


<!-- STUDENT -->

<div class="modal-student">

<div class="student-icon">

<i class="bi bi-person-circle"></i>

</div>


<div>

<strong>

{{ enrollment.student?.first_name }}
{{ enrollment.student?.middle_name }}
{{ enrollment.student?.last_name }}

</strong>

<small>

Application #{{ enrollment.id }}

</small>

</div>

</div>


<!-- REJECTION REASON -->

<div class="form-group">

<label>

Reason for Rejection

<span>*</span>

</label>


<textarea
v-model="rejectionReason"
rows="6"
maxlength="5000"
placeholder="Please explain what the student needs to correct, submit, or comply with..."
:disabled="processing"
></textarea>


<div class="textarea-footer">

<small>

{{ rejectionReason.length }} / 5000

</small>

</div>

</div>


<!-- VALIDATION ERROR -->

<div
v-if="rejectError"
class="reject-error"
>

<i class="bi bi-exclamation-circle"></i>

{{ rejectError }}

</div>


<!-- MODAL ACTIONS -->

<div class="modal-actions">

<button
class="btn btn-secondary"
@click="closeRejectModal"
:disabled="processing"
>

Cancel

</button>


<button
class="btn btn-danger"
@click="rejectApplication"
:disabled="
    processing ||
    rejectionReason.trim().length < 5
"
>

<i
v-if="processing"
class="bi bi-hourglass-split"
></i>

<i
v-else
class="bi bi-x-circle"
></i>


<span v-if="processing">
Rejecting...
</span>

<span v-else>
Reject Application
</span>

</button>

</div>

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


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

const loading = ref(true)

const processing = ref(false)

const enrollment = ref({})


const alert = ref({

    show: false,

    type: "",

    message: ""

})


/*
|--------------------------------------------------------------------------
| REJECTION MODAL
|--------------------------------------------------------------------------
*/

const showRejectModal = ref(false)

const rejectionReason = ref("")

const rejectError = ref("")


/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/

function showAlert(type, message) {

    alert.value = {

        show: true,

        type,

        message

    }


    window.scrollTo({

        top: 0,

        behavior: "smooth"

    })


    setTimeout(() => {

        alert.value.show = false

    }, 4000)

}


/*
|--------------------------------------------------------------------------
| LOAD APPLICATION
|--------------------------------------------------------------------------
*/

async function loadApplication() {

    loading.value = true


    try {

        const { data } = await api.get(

            `/registrar/enrollment/${route.params.id}`

        )


        console.log(
            "ENROLLMENT DATA:",
            data
        )


        enrollment.value = data

    }

    catch (error) {

        console.log(error)


        showAlert(
            "error",
            "Unable to load application."
        )

    }

    finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| APPROVE APPLICATION
|--------------------------------------------------------------------------
*/

async function approveApplication() {

    if (
        !confirm(
            "Approve this enrollment application?"
        )
    ) {

        return

    }


    processing.value = true


    try {

        await api.put(

            `/registrar/enrollment/${route.params.id}/approve`

        )


        showAlert(

            "success",

            "Enrollment approved successfully."

        )


        setTimeout(() => {

            router.push(
                "/registrar/applications"
            )

        }, 1500)

    }

    catch (error) {

        console.log(error)


        showAlert(

            "error",

            error.response?.data?.message ||
            "Approval failed."

        )

    }

    finally {

        processing.value = false

    }

}


/*
|--------------------------------------------------------------------------
| OPEN REJECT MODAL
|--------------------------------------------------------------------------
*/

function openRejectModal() {

    rejectionReason.value = ""

    rejectError.value = ""

    showRejectModal.value = true

}


/*
|--------------------------------------------------------------------------
| CLOSE REJECT MODAL
|--------------------------------------------------------------------------
*/

function closeRejectModal() {

    if (processing.value) {

        return

    }


    showRejectModal.value = false

    rejectionReason.value = ""

    rejectError.value = ""

}


/*
|--------------------------------------------------------------------------
| REJECT APPLICATION
|--------------------------------------------------------------------------
*/

async function rejectApplication() {

    const reason =
        rejectionReason.value.trim()


    /*
    |--------------------------------------------------------------------------
    | VALIDATE REASON
    |--------------------------------------------------------------------------
    */

    if (!reason) {

        rejectError.value =
            "Please provide a reason for rejecting this application."

        return

    }


    if (reason.length < 5) {

        rejectError.value =
            "The rejection reason must be at least 5 characters."

        return

    }


    processing.value = true

    rejectError.value = ""


    try {

        const response = await api.put(

            `/registrar/enrollment/${route.params.id}/reject`,

            {

                rejection_reason: reason

            }

        )


        console.log(
            "REJECTION RESPONSE:",
            response.data
        )


        /*
        |--------------------------------------------------------------------------
        | UPDATE LOCAL ENROLLMENT
        |--------------------------------------------------------------------------
        */

        if (response.data.enrollment) {

            enrollment.value =
                response.data.enrollment

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE MODAL
        |--------------------------------------------------------------------------
        */

        showRejectModal.value = false


        /*
        |--------------------------------------------------------------------------
        | SUCCESS MESSAGE
        |--------------------------------------------------------------------------
        */

        showAlert(

            "success",

            "Enrollment rejected successfully."

        )


        /*
        |--------------------------------------------------------------------------
        | RETURN TO APPLICATION LIST
        |--------------------------------------------------------------------------
        */

        setTimeout(() => {

            router.push(
                "/registrar/applications"
            )

        }, 1500)

    }

    catch (error) {

        console.log(
            "REJECTION ERROR:",
            error
        )


        /*
        |--------------------------------------------------------------------------
        | VALIDATION ERROR FROM LARAVEL
        |--------------------------------------------------------------------------
        */

        if (
            error.response?.data?.errors?.rejection_reason
        ) {

            rejectError.value =
                error.response.data.errors.rejection_reason[0]

        }

        else {

            rejectError.value =
                error.response?.data?.message ||
                "Rejection failed."

        }

    }

    finally {

        processing.value = false

    }

}


/*
|--------------------------------------------------------------------------
| FILE URL
|--------------------------------------------------------------------------
*/

function getFileUrl(path) {

    if (!path) {

        return "#"

    }


    return "http://127.0.0.1:8000/storage/" + path

}


/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

function formatDate(date) {

    if (!date) {

        return "-"

    }


    return new Date(date).toLocaleString()

}


/*
|--------------------------------------------------------------------------
| ON MOUNT
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadApplication()

})

</script>


<style scoped>


/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/

.alert-box {

    padding: 15px 20px;

    border-radius: 12px;

    margin-bottom: 20px;

    font-weight: 700;

    display: flex;

    align-items: center;

    gap: 10px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

    animation:
        slideDown .3s ease;

}


.alert-box.success {

    background: #dcfce7;

    color: #166534;

    border-left: 5px solid #0B6B3A;

}


.alert-box.error {

    background: #fee2e2;

    color: #991b1b;

    border-left: 5px solid #dc2626;

}


.alert-box i {

    font-size: 20px;

}


@keyframes slideDown {

    from {

        opacity: 0;

        transform:
            translateY(-20px);

    }

    to {

        opacity: 1;

        transform:
            translateY(0);

    }

}


/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

.application-page {

    padding: 20px;

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.page-header {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

    margin-bottom: 25px;

    flex-wrap: wrap;

}


.page-header h2 {

    color: #064E2A;

    font-weight: 800;

}


.page-header p {

    color: #6B7280;

}


/*
|--------------------------------------------------------------------------
| LOADING
|--------------------------------------------------------------------------
*/

.loading {

    text-align: center;

    padding: 80px;

}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.review-card {

    background: white;

    border-radius: 20px;

    padding: 25px;

    margin-bottom: 25px;

    box-shadow:
        0 10px 30px rgba(0,0,0,.08);

}


/*
|--------------------------------------------------------------------------
| TOP CARD
|--------------------------------------------------------------------------
*/

.top-card {

    display: flex;

    justify-content:
        space-between;

    align-items: center;

}


.top-card h4 {

    color: #064E2A;

    font-weight: 800;

}


.top-card p {

    color: #6B7280;

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

.status {

    padding: 10px 20px;

    border-radius: 50px;

    font-weight: 700;

    font-size: 14px;

}


.status.Pending {

    background: #fef3c7;

    color: #92400e;

}


.status.Approved {

    background: #dcfce7;

    color: #166534;

}


.status.Rejected {

    background: #fee2e2;

    color: #991b1b;

}


/*
|--------------------------------------------------------------------------
| SECTION TITLE
|--------------------------------------------------------------------------
*/

.section-title {

    background:
        linear-gradient(
            135deg,
            #064E2A,
            #0B6B3A
        );

    color: white;

    padding: 14px 18px;

    border-radius: 12px;

    margin-bottom: 25px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| INFO
|--------------------------------------------------------------------------
*/

.info {

    margin-bottom: 20px;

}


.info label {

    display: block;

    color: #064E2A;

    font-size: 13px;

    font-weight: 700;

}


.info p {

    margin: 5px 0 0;

    color: #374151;

    font-size: 15px;

}


/*
|--------------------------------------------------------------------------
| DOCUMENTS
|--------------------------------------------------------------------------
*/

.documents {

    display: flex;

    gap: 15px;

    flex-wrap: wrap;

}


.document-btn {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 12px 18px;

    border-radius: 12px;

    background: #f0fdf4;

    color: #064E2A;

    text-decoration: none;

    font-weight: 600;

    border:
        1px solid #bbf7d0;

    transition: .3s;

}


.document-btn:hover {

    background: #0B6B3A;

    color: white;

}


/*
|--------------------------------------------------------------------------
| ACTIONS
|--------------------------------------------------------------------------
*/

.action-area {

    display: flex;

    justify-content: flex-end;

    gap: 15px;

    margin-bottom: 30px;

}


.action-area button {

    padding: 12px 25px;

    border-radius: 12px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| REJECTION INFORMATION
|--------------------------------------------------------------------------
*/

.rejection-history {

    border:
        1px solid #fecaca;

}


.rejection-title {

    background:
        linear-gradient(
            135deg,
            #991b1b,
            #dc2626
        );

}


.rejection-message {

    background: #fff5f5;

    border-left:
        5px solid #dc2626;

    padding: 18px;

    border-radius: 10px;

}


.rejection-message strong {

    color: #991b1b;

}


.rejection-message p {

    margin:
        8px 0 10px;

    color: #374151;

    line-height: 1.6;

}


.rejection-message small {

    color: #6B7280;

}


/*
|--------------------------------------------------------------------------
| MODAL OVERLAY
|--------------------------------------------------------------------------
*/

.modal-overlay {

    position: fixed;

    inset: 0;

    background:
        rgba(0, 0, 0, .55);

    display: flex;

    align-items: center;

    justify-content: center;

    z-index: 9999;

    padding: 20px;

}


/*
|--------------------------------------------------------------------------
| REJECT MODAL
|--------------------------------------------------------------------------
*/

.reject-modal {

    width: 100%;

    max-width: 600px;

    background: white;

    border-radius: 18px;

    box-shadow:
        0 20px 60px rgba(0,0,0,.25);

    overflow: hidden;

    animation:
        modalShow .25s ease;

}


@keyframes modalShow {

    from {

        opacity: 0;

        transform:
            translateY(-20px)
            scale(.97);

    }

    to {

        opacity: 1;

        transform:
            translateY(0)
            scale(1);

    }

}


/*
|--------------------------------------------------------------------------
| MODAL HEADER
|--------------------------------------------------------------------------
*/

.modal-header {

    padding: 22px;

    display: flex;

    justify-content:
        space-between;

    align-items:
        flex-start;

    border-bottom:
        1px solid #eee;

}


.modal-header h3 {

    margin: 0;

    color: #991b1b;

    font-weight: 800;

}


.modal-header p {

    margin:
        5px 0 0;

    color: #6B7280;

}


.modal-close {

    border: none;

    background: transparent;

    font-size: 20px;

    color: #6B7280;

    cursor: pointer;

}


.modal-close:hover {

    color: #dc2626;

}


/*
|--------------------------------------------------------------------------
| STUDENT PREVIEW
|--------------------------------------------------------------------------
*/

.modal-student {

    margin:
        20px 22px;

    padding: 15px;

    background: #f9fafb;

    border:
        1px solid #e5e7eb;

    border-radius: 10px;

    display: flex;

    align-items: center;

    gap: 12px;

}


.student-icon {

    font-size: 38px;

    color: #0B6B3A;

}


.modal-student strong {

    display: block;

    color: #374151;

}


.modal-student small {

    display: block;

    margin-top: 3px;

    color: #6B7280;

}


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

.form-group {

    padding:
        0 22px;

}


.form-group label {

    display: block;

    font-weight: 700;

    margin-bottom: 8px;

    color: #374151;

}


.form-group label span {

    color: #dc2626;

}


.form-group textarea {

    width: 100%;

    min-height: 140px;

    resize: vertical;

    padding: 13px;

    border:
        1px solid #d1d5db;

    border-radius: 10px;

    font-family: inherit;

    font-size: 14px;

    outline: none;

    transition: .2s;

}


.form-group textarea:focus {

    border-color: #0B6B3A;

    box-shadow:
        0 0 0 3px
        rgba(11,107,58,.1);

}


.form-group textarea:disabled {

    background: #f3f4f6;

}


.textarea-footer {

    display: flex;

    justify-content: flex-end;

    color: #9CA3AF;

    margin-top: 5px;

}


/*
|--------------------------------------------------------------------------
| ERROR
|--------------------------------------------------------------------------
*/

.reject-error {

    margin:
        15px 22px 0;

    padding: 12px 15px;

    border-radius: 8px;

    background: #fee2e2;

    color: #991b1b;

    font-size: 14px;

    display: flex;

    align-items: center;

    gap: 8px;

}


/*
|--------------------------------------------------------------------------
| MODAL ACTIONS
|--------------------------------------------------------------------------
*/

.modal-actions {

    display: flex;

    justify-content: flex-end;

    gap: 10px;

    padding: 22px;

}


.modal-actions button {

    padding:
        11px 20px;

    border-radius: 9px;

    font-weight: 700;

}


.modal-actions button:disabled {

    opacity: .6;

    cursor: not-allowed;

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media(max-width:768px) {

    .application-page {

        padding: 10px;

    }


    .top-card {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;

    }


    .action-area {

        flex-direction: column;

    }


    .action-area button {

        width: 100%;

    }


    .document-btn {

        width: 100%;

        justify-content: center;

    }


    .reject-modal {

        max-height: 90vh;

        overflow-y: auto;

    }


    .modal-actions {

        flex-direction: column-reverse;

    }


    .modal-actions button {

        width: 100%;

    }

}

</style>