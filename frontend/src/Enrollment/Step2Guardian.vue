<template>
    <div class="guardian-page">

        <div class="page-header">
            <h3>Step 2 - Guardian Information</h3>
            <p>
                Please provide the information of your parents and legal guardian.
            </p>
        </div>

        <!-- ================= FATHER ================= -->

        <div class="section-card">

            <div class="section-title">
                <i class="bi bi-person-fill"></i>
                Father's Information
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label>Father's Full Name</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.father_name"
                        placeholder="Enter father's full name"
                    >

                </div>

                <div class="col-md-3 mb-3">

                    <label>Occupation</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.father_occupation"
                        placeholder="Occupation"
                    >

                </div>

                <div class="col-md-3 mb-3">

                    <label>Contact Number</label>

                    <input
                        class="form-control"
                        type="tel"
                        maxlength="11"
                        placeholder="09XXXXXXXXX"
                        v-model="model.father_contact"
                    >

                </div>

            </div>

        </div>

        <!-- ================= MOTHER ================= -->

        <div class="section-card">

            <div class="section-title">
                <i class="bi bi-person-heart"></i>
                Mother's Information
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label>Mother's Full Name</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.mother_name"
                        placeholder="Enter mother's full name"
                    >

                </div>

                <div class="col-md-3 mb-3">

                    <label>Occupation</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.mother_occupation"
                        placeholder="Occupation"
                    >

                </div>

                <div class="col-md-3 mb-3">

                    <label>Contact Number</label>

                    <input
                        class="form-control"
                        type="tel"
                        maxlength="11"
                        placeholder="09XXXXXXXXX"
                        v-model="model.mother_contact"
                    >

                </div>

            </div>

        </div>

        <!-- ================= GUARDIAN ================= -->

        <div class="section-card">

            <div class="section-title">
                <i class="bi bi-people-fill"></i>
                Guardian Information
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label>Guardian Name</label>

                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter guardian name"
                        v-model="model.guardian_name"
                    >

                </div>

                <div class="col-md-3 mb-3">

                    <label>Relationship</label>

                    <select
                        class="form-select"
                        v-model="model.guardian_relationship"
                    >

                        <option value="">Select Relationship</option>
                        <option>Father</option>
                        <option>Mother</option>
                        <option>Brother</option>
                        <option>Sister</option>
                        <option>Grandfather</option>
                        <option>Grandmother</option>
                        <option>Uncle</option>
                        <option>Aunt</option>
                        <option>Legal Guardian</option>
                        <option>Others</option>

                    </select>

                </div>

                <div class="col-md-3 mb-3">

                    <label>Contact Number</label>

                    <input
                        class="form-control"
                        type="tel"
                        maxlength="11"
                        placeholder="09XXXXXXXXX"
                        v-model="model.guardian_contact"
                    >

                </div>

                <div class="col-12">

                    <label>Guardian Address</label>

                    <textarea
                        rows="3"
                        class="form-control"
                        placeholder="Complete address"
                        v-model="model.guardian_address"
                    ></textarea>

                </div>

            </div>

        </div>

    </div>
</template>

<script setup>

const model = defineModel()

</script>

<style scoped>

.guardian-page{

    padding:10px;

}

.page-header{

    margin-bottom:30px;

}

.page-header h3{

    font-weight:800;

    color:#064E2A;

    margin-bottom:8px;

}

.page-header p{

    color:#6B7280;

}

.section-card{

    background:white;

    border-radius:22px;

    padding:30px;

    margin-bottom:30px;

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

    font-size:18px;

    font-weight:700;

    margin-bottom:25px;

    display:flex;

    align-items:center;

    gap:10px;

}

.section-title i{

    font-size:20px;

}

label{

    font-weight:600;

    margin-bottom:8px;

    color:#374151;

    display:block;

}

.form-control,
.form-select{

    height:52px;

    border-radius:12px;

    border:1px solid #D1D5DB;

    transition:.25s;

}

textarea.form-control{

    height:120px;

    resize:none;

}

.form-control:focus,
.form-select:focus{

    border-color:#0B6B3A;

    box-shadow:0 0 0 .2rem rgba(11,107,58,.15);

}

@media(max-width:768px){

.section-card{

    padding:20px;

}

.page-header h3{

    font-size:24px;

}

}

</style>