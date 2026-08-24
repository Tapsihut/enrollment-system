<template>

<div class="academic-page">

    <div class="page-header">

        <h3>Step 3 - Educational Background</h3>

        <p>
            Provide your previous educational information.
        </p>

    </div>


<!-- ================= FRESHMEN ================= -->

<div
    v-if="model.student_type == 'Freshmen'"
    class="section-card"
>

    <div class="section-title">

        <i class="bi bi-mortarboard-fill"></i>

        Senior High School Information

    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label>Last School Attended</label>

            <input
                class="form-control"
                v-model="model.last_school"
                placeholder="School Name"
            >

        </div>

        <div class="col-md-6 mb-3">

            <label>School Address</label>

            <input
                class="form-control"
                v-model="model.school_address"
            >

        </div>

        <div class="col-md-4 mb-3">

            <label>Strand</label>

            <select
                class="form-select"
                v-model="model.strand"
            >

                <option value="">Select Strand</option>
                <option>STEM</option>
                <option>ABM</option>
                <option>HUMSS</option>
                <option>GAS</option>
                <option>TVL</option>

            </select>

        </div>

        <div class="col-md-4 mb-3">

            <label>Graduation Year</label>

            <input
                type="number"
                class="form-control"
                v-model="model.graduation_year"
            >

        </div>

        <div class="col-md-4 mb-3">

            <label>General Weighted Average (GWA)</label>

            <input
                type="number"
                step="0.01"
                class="form-control"
                v-model="model.gwa"
            >

        </div>

    </div>

</div>

<!-- ================= TRANSFEREE ================= -->

<div
    v-else-if="model.student_type == 'Transferee'"
    class="section-card"
>

    <div class="section-title">

        <i class="bi bi-arrow-left-right"></i>

        Previous College Information

    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label>Previous School</label>

            <input
                class="form-control"
                v-model="model.last_school"
            >

        </div>

        <div class="col-md-6 mb-3">

            <label>School Address</label>

            <input
                class="form-control"
                v-model="model.school_address"
            >

        </div>

        <div class="col-md-4 mb-3">

            <label>Previous Course</label>

            <input
                class="form-control"
                v-model="model.previous_course"
            >

        </div>

        <div class="col-md-4 mb-3">

            <label>Units Earned</label>

            <input
                type="number"
                class="form-control"
                v-model="model.units_earned"
            >

        </div>

        <div class="col-md-4 mb-3">

            <label>General Weighted Average (GWA)</label>

            <input
                type="number"
                step="0.01"
                class="form-control"
                v-model="model.gwa"
            >

        </div>

    </div>

</div>

<!-- ================= RETURNEE ================= -->

<div
    v-else-if="model.student_type == 'Returnee'"
    class="section-card"
>

    <div class="section-title">

        <i class="bi bi-arrow-repeat"></i>

        Previous Enrollment Information

    </div>

    <div class="row">

        <div class="col-md-6 mb-3">

            <label>Last School Year</label>

            <input
                class="form-control"
                placeholder="2025-2026"
                v-model="model.last_school_year"
            >

        </div>

        <div class="col-md-6 mb-3">

            <label>Last Semester</label>

            <select
                class="form-select"
                v-model="model.last_semester"
            >

                <option value="">Select Semester</option>

                <option>First Semester</option>

                <option>Second Semester</option>

                <option>Summer</option>

            </select>

        </div>

    </div>

</div>

<!-- ================= CONTINUING ================= -->

<div
    v-else
    class="section-card"
>

    <div class="section-title">

        <i class="bi bi-book-fill"></i>

        Continuing Student

    </div>

    <div class="info-box">

        <i class="bi bi-check-circle-fill"></i>

        <div>

            <h5>No Academic Information Required</h5>

            <p>

                Your academic records already exist in the Student Information System.
                You may proceed to the next step.

            </p>

        </div>

    </div>

</div>


</div>


</template>


<script setup>

const model = defineModel()

</script>

<style scoped>

.academic-page{

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

.section-card{

    background:white;

    border-radius:22px;

    padding:30px;

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

    padding:14px 18px;

    border-radius:12px;

    margin-bottom:25px;

    font-weight:700;

    display:flex;

    align-items:center;

    gap:10px;

}

label{

    display:block;

    font-weight:600;

    margin-bottom:8px;

}

.form-control,
.form-select{

    height:52px;

    border-radius:12px;

    border:1px solid #D1D5DB;

}

.form-control:focus,
.form-select:focus{

    border-color:#0B6B3A;

    box-shadow:0 0 0 .2rem rgba(11,107,58,.15);

}

.info-box{

    display:flex;

    gap:20px;

    align-items:flex-start;

    padding:25px;

    border-radius:15px;

    background:#ECFDF5;

    border:1px solid #A7F3D0;

}

.info-box i{

    color:#0B6B3A;

    font-size:40px;

}

.info-box h5{

    margin-bottom:10px;

    color:#064E2A;

    font-weight:700;

}

.info-box p{

    margin:0;

    color:#4B5563;

}

</style>