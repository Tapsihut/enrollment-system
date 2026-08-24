<template>


<div
    v-if="model.student_type !== 'Continuing'"
    class="documents-page"
>

    <!-- your existing Step 4 code -->

</div>

<div class="review-page">

    <div
        v-if="loading"
        class="text-center py-5"
    >

        <div
            class="spinner-border text-success mb-3"
            role="status">
        </div>

        <h5>Loading student information...</h5>

    </div>


<!-- ================= PERSONAL ================= -->

<div class="review-card">

    <div class="section-title">

        <i class="bi bi-person-circle"></i>

        Personal Information

    </div>

    <div class="row">

        <div class="col-md-4">
            <strong>First Name</strong>
            <p>{{ profile.student.first_name }}</p>
        </div>

        <div class="col-md-4">
            <strong>Middle Name</strong>
            <p>{{ profile.student.middle_name }}</p>
        </div>

        <div class="col-md-4">
            <strong>Last Name</strong>
            <p>{{ profile.student.last_name }}</p>
        </div>

<div class="col-md-6">
    <strong>Birth Date</strong>
    <p>{{ formatDate(profile.student.birth_date) }}</p>
</div>

        <div class="col-md-6">
            <strong>Gender</strong>
            <p>{{ profile.student.gender }}</p>
        </div>

        <div class="col-md-6">
            <strong>Civil Status</strong>
            <p>{{ profile.student.civil_status }}</p>
        </div>

        <div class="col-md-6">
            <strong>Student Type</strong>
            <p>{{ profile.student.student_type }}</p>
        </div>

        <div class="col-md-6">
            <strong>Address</strong>
            <p>{{ profile.student.address }}</p>
        </div>

        <div class="col-md-6">
            <strong>Contact Number</strong>
            <p>{{ profile.student.contact_number }}</p>
        </div>

        <div class="col-md-6">
            <strong>Nationality</strong>
            <p>{{ profile.student.nationality }}</p>
        </div>

        <div class="col-md-12">
            <strong>Email Address</strong>
            <p>{{ profile.student.email }}</p>
        </div>


        <div class="col-md-12">
            <strong>Religion</strong>
            <p>{{ profile.student.religion }}</p>
        </div>
    </div>
</div>

    <!-- ================= GUARDIAN ================= -->

    <div class="review-card">

        <div class="section-title">

            <i class="bi bi-people-fill"></i>

            Guardian Information

        </div>

        <div class="row">

            <div class="col-md-6">

                <strong>Guardian</strong>

                <p>{{ model.guardian_name || "-" }}</p>

            </div>

            <div class="col-md-6">

                <strong>Relationship</strong>

                <p>{{ model.guardian_relationship || "-" }}</p>

            </div>

            <div class="col-md-6">

                <strong>Guardian Contact</strong>

                <p>{{ model.guardian_contact || "-" }}</p>

            </div>

        </div>

    </div>
<!-- ================= COURSE INFORMATION ================= -->

<div
    v-if="model.student_type !== 'Continuing'"
    class="review-card"
>

    <div class="section-title">

        <i class="bi bi-mortarboard-fill"></i>

        Course Information

    </div>

    <div class="row">

        <!-- COURSE -->
        <div class="col-md-6 mb-3">

            <strong>Course</strong>

            <p>
                {{ selectedCourse?.name || selectedCourse?.course_name || selectedCourse?.code || "-" }}
            </p>

        </div>


        <!-- ACADEMIC YEAR -->
        <div class="col-md-6 mb-3">

            <strong>Academic Year</strong>

            <p>
                {{ selectedSchoolYear?.name || selectedSchoolYear?.school_year || selectedSchoolYear?.year || "-" }}
            </p>

        </div>


        <!-- YEAR LEVEL -->
        <div class="col-md-6 mb-3">

            <strong>Year Level</strong>

            <p>
                {{ model.year_level || "-" }}
            </p>

        </div>


        <!-- CURRICULUM -->
        <div class="col-md-6 mb-3">

            <strong>Year Curriculum</strong>

            <p>
                {{ selectedCurriculum?.name || selectedCurriculum?.curriculum_name || selectedCurriculum?.code || "-" }}
            </p>

        </div>


        <!-- SEMESTER -->
        <div class="col-md-6 mb-3">

            <strong>Semester</strong>

            <p>
                {{ selectedSemester?.name || selectedSemester?.semester_name || "-" }}
            </p>

        </div>

    </div>

</div>

<!-- ================= ACADEMIC ================= -->

<div
    v-if="model.student_type !== 'Continuing'"
    class="review-card"
>

    <div class="section-title">

        <i class="bi bi-mortarboard-fill"></i>

        Academic Information

    </div>


    <div class="row">


        <!-- Previous School -->

        <div class="col-md-6">

            <strong>Last School Attended</strong>

            <p>
                {{ model.last_school || "-" }}
            </p>

        </div>



        <!-- Strand -->

        <div class="col-md-3">

            <strong>Strand</strong>

            <p>
                {{ model.strand || "-" }}
            </p>

        </div>



        <!-- Graduation Year -->

        <div class="col-md-3">

            <strong>Graduation Year</strong>

            <p>
                {{ model.graduation_year || "-" }}
            </p>

        </div>



        <!-- Previous Course (Transferee) -->

        <div 
        v-if="model.student_type === 'Transferee'"
        class="col-md-6"
        >

            <strong>Previous Course</strong>

            <p>
                {{ model.previous_course || "-" }}
            </p>

        </div>



        <!-- Units Earned (Transferee) -->

        <div 
        v-if="model.student_type === 'Transferee'"
        class="col-md-6"
        >

            <strong>Units Earned</strong>

            <p>
                {{ model.units_earned || "-" }}
            </p>

        </div>



        <!-- Returnee -->

        <div 
        v-if="model.student_type === 'Returnee'"
        class="col-md-6"
        >

            <strong>Last School Year</strong>

            <p>
                {{ model.last_school_year || "-" }}
            </p>

        </div>



        <div 
        v-if="model.student_type === 'Returnee'"
        class="col-md-6"
        >

            <strong>Last Semester</strong>

            <p>
                {{ model.last_semester || "-" }}
            </p>

        </div>


    </div>


</div>

<!-- ================= DOCUMENTS ================= -->

<div
    v-if="model.student_type !== 'Continuing'"
    class="review-card"
>
    <div class="section-title">

        <i class="bi bi-folder-check"></i>

        Uploaded Documents

    </div>


    <ul class="list-group list-group-flush">


        <li class="list-group-item d-flex justify-content-between align-items-center">

            <div>

                <strong>PSA Birth Certificate</strong>

                <br>

                <small class="text-muted">

                    {{ 
                        model.psa_birth_certificate?.name 
                        ?? 
                        "No file selected" 
                    }}

                </small>

            </div>


            <span
                class="badge"
                :class="model.psa_birth_certificate ? 'bg-success' : 'bg-danger'"
            >

                {{ model.psa_birth_certificate ? "Uploaded" : "Missing" }}

            </span>

        </li>



        <li class="list-group-item d-flex justify-content-between align-items-center">

            <div>

                <strong>Good Moral Certificate</strong>

                <br>

                <small class="text-muted">

                    {{
                        model.good_moral?.name
                        ??
                        "No file selected"
                    }}

                </small>

            </div>


            <span
                class="badge"
                :class="model.good_moral ? 'bg-success' : 'bg-danger'"
            >

                {{ model.good_moral ? "Uploaded" : "Missing" }}

            </span>

        </li>




        <li class="list-group-item d-flex justify-content-between align-items-center">

            <div>

                <strong>Academic Document</strong>

                <br>

                <small class="text-muted">

                    {{
                        model.academic_document?.name
                        ??
                        "No file selected"
                    }}

                </small>

            </div>


            <span
                class="badge"
                :class="model.academic_document ? 'bg-success' : 'bg-danger'"
            >

                {{ model.academic_document ? "Uploaded" : "Missing" }}

            </span>

        </li>




        <li class="list-group-item d-flex justify-content-between align-items-center">

            <div>

                <strong>2x2 Picture</strong>

                <br>

                <small class="text-muted">

                    {{
                        model.id_picture?.name
                        ??
                        "No file selected"
                    }}

                </small>

            </div>


            <span
                class="badge"
                :class="model.id_picture ? 'bg-success' : 'bg-danger'"
            >

                {{ model.id_picture ? "Uploaded" : "Missing" }}

            </span>

        </li>



    </ul>


</div>

    <!-- ================= DECLARATION ================= -->

<!-- ================= DATA PRIVACY & CERTIFICATION ================= -->

<div class="review-card">

    <div class="section-title">

        <i class="bi bi-shield-check"></i>

        Data Privacy & Certification

    </div>



    <div class="declaration">


        <div>


            <div class="mb-3">

                <input
                    type="checkbox"
                    class="form-check-input me-2"
                    v-model="model.data_privacy_consent"
                >

                <label>

                    I agree to the collection, processing, and storage
                    of my personal information for enrollment purposes
                    in accordance with the Data Privacy Act.

                </label>

            </div>



            <div>

                <input
                    type="checkbox"
                    class="form-check-input me-2"
                    v-model="model.information_certified"
                >


                <label>

                    I certify that all information and documents I provided
                    are true, complete, and correct.

                </label>


            </div>



        </div>


    </div>


</div>

</div>

</template>

<script setup>

import { ref, onMounted, computed } from "vue"
import api from "@/services/api"


const model = defineModel()

const loading = ref(true)
const schoolYears = ref([])
const semesters = ref([])
const courses = ref([])
const curricula = ref([])

const profile = ref({
    student: {
        first_name: "",
        middle_name: "",
        last_name: "",
        birth_date: "",
        gender: "",
        civil_status: "",
        student_type: "",
        address: "",
        contact_number: "",
        email: ""
    }
})

function formatDate(date){

    if(!date) return "-"

    return new Date(date).toLocaleDateString("en-PH",{

        year:"numeric",
        month:"long",
        day:"numeric"

    })

}
async function loadProfile() {

    loading.value = true

    try {

        const response = await api.get("/student/profile")

        console.log("API Response:", response)

        console.log("Student Data:", response.data)

        profile.value = response.data

        console.log("Profile Ref:", profile.value)

    }

    catch(error){

        console.error("ERROR:", error)

    }

    finally{

        loading.value = false

    }

}


onMounted(() => {

    loadProfile()
    loadEnrollmentOptions()

})
async function loadEnrollmentOptions() {

    try {

        const { data } = await api.get("/enrollment/options")

        schoolYears.value = data.school_years
        semesters.value = data.semesters
        courses.value = data.courses
        curricula.value = data.curricula

    }

    catch (error) {

        console.error(error)

    }

}
const selectedCourse = computed(() => {
    return courses.value.find(c => c.id == model.value.course_id)
})

const selectedSemester = computed(() => {
    return semesters.value.find(s => s.id == model.value.semester_id)
})

const selectedSchoolYear = computed(() => {
    return schoolYears.value.find(y => y.id == model.value.school_year_id)
})

const selectedCurriculum = computed(() => {
    return curricula.value.find(c => c.id == model.value.curriculum_id)
})

</script>
<style scoped>

.review-page{

    padding:10px;

}

.page-header{

    margin-bottom:30px;

}

.page-header h3{

    font-weight:800;

    color:#064E2A;

}

.page-header p{

    color:#6B7280;

}

.review-card{

    background:white;

    border-radius:20px;

    padding:25px;

    margin-bottom:25px;

    box-shadow:0 10px 30px rgba(0,0,0,.06);

    border:1px solid #E5E7EB;

}

.section-title{

    background:linear-gradient(
        135deg,
        #064E2A,
        #0B6B3A
    );

    color:white;

    padding:12px 18px;

    border-radius:12px;

    margin-bottom:20px;

    display:flex;

    align-items:center;

    gap:10px;

    font-weight:700;

}

strong{

    color:#064E2A;

}

p{

    margin-bottom:20px;

    color:#374151;

}

.declaration{

    background:#ECFDF5;

    border:1px solid #A7F3D0;

    border-radius:15px;

    padding:20px;

    display:flex;

    align-items:flex-start;

    font-size:15px;

}

.form-check-input{

    margin-top:5px;

}

.badge{

    padding:8px 14px;

    border-radius:20px;

}

</style>