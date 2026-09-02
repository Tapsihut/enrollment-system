<template>

<div class="enrollment-page">

    <!-- ================= PAGE HEADER ================= -->

    <div class="page-header">

        <h3>
            Step 5 - Enrollment Details
        </h3>

        <p>
            Select your enrollment information for the current academic term.
        </p>

    </div>


    <!-- ================= NOTICE ================= -->

    <div class="notice">

        <i class="bi bi-info-circle-fill"></i>

        <div>

            Please select the correct
            <strong>course, year level,</strong>
            and <strong>academic term</strong>.

        </div>

    </div>


    <!-- ================= MAIN CARD ================= -->

    <div class="section-card">

        <!-- SECTION TITLE -->

        <div class="section-title">

            <i class="bi bi-journal-check"></i>

            Enrollment Information

        </div>


        <div class="row g-4">


            <!-- ================= SCHOOL YEAR ================= -->

            <div class="col-md-6">

                <label>
                    School Year
                </label>

                <select
                    class="form-select"
                    v-model="model.school_year_id"
                >

                    <option value="">
                        Select School Year
                    </option>

                    <option
                        v-for="year in schoolYears"
                        :key="year.id"
                        :value="year.id"
                    >

                        {{ year.school_year }}

                    </option>

                </select>

            </div>


            <!-- ================= SEMESTER ================= -->

            <div class="col-md-6">

                <label>
                    Semester
                </label>

                <select
                    class="form-select"
                    v-model="model.semester_id"
                >

                    <option value="">
                        Select Semester
                    </option>

                    <option
                        v-for="semester in semesters"
                        :key="semester.id"
                        :value="semester.id"
                    >

                        {{ semester.name }}

                    </option>

                </select>

            </div>


            <!-- ================= COURSE ================= -->

            <div class="col-md-6">

                <label>
                    Course
                </label>

                <select
                    class="form-select"
                    v-model="model.course_id"
                >

                    <option value="">
                        Select Course
                    </option>

                    <option
                        v-for="course in courses"
                        :key="course.id"
                        :value="course.id"
                    >

                        {{ course.name }}

                    </option>

                </select>

            </div>


            <!-- ================= YEAR CURRICULUM ================= -->

            <div class="col-md-6">

                <label>
                    Year Curriculum
                </label>

                <div class="curriculum-display">

                    <i class="bi bi-journal-bookmark-fill"></i>

                    <span v-if="selectedCurriculum">

                        {{ selectedCurriculum.name }}

                    </span>

                    <span
                        v-else
                        class="placeholder"
                    >

                        Select a course first

                    </span>

                </div>

            </div>


            <!-- ================= YEAR LEVEL ================= -->
            <div class="col-md-6">
                <label>Year Level</label>
                <select class="form-select" v-model="model.year_level"
                    :disabled="model.student_type === 'Freshmen'">
                    <option value="">Select Year Level</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
                <small v-if="model.student_type === 'Freshmen'" class="text-muted">
                    New students are automatically assigned to 1st Year.
                </small>
            </div>

            <!-- ================= REMARKS ================= -->

            <div class="col-12">

                <label>

                    Remarks

                    <span class="optional">
                        Optional
                    </span>

                </label>

                <textarea
                    rows="4"
                    class="form-control"
                    placeholder="Additional requests or information..."
                    v-model="model.remarks"
                ></textarea>

            </div>


        </div>

    </div>

</div>

</template>
```vue
<script setup>
import { ref, onMounted, computed, watch } from "vue"
import api from "@/services/api"

const model = defineModel()
const schoolYears = ref([])
const semesters = ref([])
const courses = ref([])
const curricula = ref([])

async function loadEnrollmentData() {
    try {
        const { data } = await api.get("/enrollment/options")

        schoolYears.value = (data.school_years || []).filter(
            year => year.is_active == 1 || year.is_active === true
        )
        semesters.value = (data.semesters || []).filter(
            semester => semester.is_active == 1 || semester.is_active === true
        )
        courses.value = data.courses || []
        curricula.value = data.curricula || []

        const activeYear = schoolYears.value.find(
            year => year.is_active == 1 || year.is_active === true
        )
        const activeSemester = semesters.value.find(
            semester => semester.is_active == 1 || semester.is_active === true
        )

        if (activeYear) model.value.school_year_id = activeYear.id
        if (activeSemester) model.value.semester_id = activeSemester.id
        if (model.value.student_type === "new")
            model.value.year_level = "1"
    } catch (error) {
        console.error("Failed to load enrollment options:", error)
    }
}

const selectedCurriculum = computed(() => {
    if (!model.value.course_id) return null
    return curricula.value.find(
        curriculum => curriculum.course_id == model.value.course_id
    )
})

watch(selectedCurriculum, curriculum => {
    model.value.curriculum_id = curriculum ? curriculum.id : ""
}, { immediate: true })

watch(() => model.value.student_type, type => {
    model.value.year_level = type === "new" ? "1" : ""
})

onMounted(loadEnrollmentData)
</script>
<style scoped>

/* =========================================================
   PAGE
========================================================= */

.enrollment-page {

    padding: 10px;

}


/* =========================================================
   PAGE HEADER
========================================================= */

.page-header {

    margin-bottom: 25px;

}

.page-header h3 {

    margin-bottom: 5px;

    color: #064E2A;

    font-weight: 800;

}

.page-header p {

    margin: 0;

    color: #6B7280;

}


/* =========================================================
   NOTICE
========================================================= */

.notice {

    display: flex;

    align-items: flex-start;

    gap: 15px;

    margin-bottom: 25px;

    padding: 17px 18px;

    background: #ECFDF5;

    border: 1px solid #A7F3D0;

    border-radius: 15px;

    color: #065F46;

}

.notice i {

    flex-shrink: 0;

    font-size: 25px;

}

.notice strong {

    font-weight: 700;

}


/* =========================================================
   MAIN CARD
========================================================= */

.section-card {

    padding: 25px;

    background: white;

    border: 1px solid #E5E7EB;

    border-radius: 20px;

    box-shadow: 0 8px 20px rgba(0,0,0,.05);

}


/* =========================================================
   SECTION TITLE
========================================================= */

.section-title {

    display: flex;

    align-items: center;

    gap: 10px;

    padding-bottom: 18px;

    margin-bottom: 25px;

    border-bottom: 1px solid #E5E7EB;

    color: #064E2A;

    font-size: 18px;

    font-weight: 700;

}

.section-title i {

    color: #0B6B3A;

    font-size: 22px;

}


/* =========================================================
   LABEL
========================================================= */

label {

    display: block;

    margin-bottom: 8px;

    color: #374151;

    font-size: 14px;

    font-weight: 600;

}


/* =========================================================
   SELECT
========================================================= */

.form-select {

    min-height: 46px;

    border: 1px solid #D1D5DB;

    border-radius: 12px;

    font-size: 14px;

    transition: .2s;

}

.form-select:hover {

    border-color: #0B6B3A;

}

.form-select:focus {

    border-color: #0B6B3A;

    box-shadow: 0 0 0 3px rgba(11,107,58,.10);

}


/* =========================================================
   AUTOMATIC CURRICULUM
========================================================= */

.curriculum-display {

    min-height: 46px;

    display: flex;

    align-items: center;

    gap: 10px;

    padding: 10px 14px;

    background: #F0FDF4;

    border: 1px solid #BBF7D0;

    border-radius: 12px;

    color: #064E2A;

    font-size: 14px;

    font-weight: 600;

}

.curriculum-display i {

    flex-shrink: 0;

    color: #0B6B3A;

    font-size: 18px;

}

.curriculum-display .placeholder {

    color: #9CA3AF;

    font-weight: 400;

}


/* =========================================================
   TEXTAREA
========================================================= */

.form-control {

    border: 1px solid #D1D5DB;

    border-radius: 12px;

    font-size: 14px;

    resize: vertical;

    transition: .2s;

}

.form-control:focus {

    border-color: #0B6B3A;

    box-shadow: 0 0 0 3px rgba(11,107,58,.10);

}


/* =========================================================
   OPTIONAL
========================================================= */

.optional {

    display: inline-block;

    margin-left: 5px;

    padding: 3px 8px;

    border-radius: 20px;

    background: #F3F4F6;

    color: #6B7280;

    font-size: 10px;

    font-weight: 600;

    vertical-align: middle;

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {

    .enrollment-page {

        padding: 5px;

    }

    .section-card {

        padding: 20px;

        border-radius: 16px;

    }

    .section-title {

        font-size: 16px;

    }

    .notice {

        padding: 15px;

    }

    .curriculum-display {

        min-height: 46px;

    }

}

</style>