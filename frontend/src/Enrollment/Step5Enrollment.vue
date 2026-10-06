<template>

    <div class="enrollment-page">

        <!-- PAGE HEADER -->
        <div class="page-header">

            <h3>
                Step 5 - Enrollment Details
            </h3>

            <p>
                Select your enrollment information for the current academic term.
            </p>

        </div>


        <!-- NOTICE -->
        <div class="notice">

            <i class="bi bi-info-circle-fill"></i>

            <div>
                Please select the correct
                <strong>course, year level, schedule preference,</strong>
                and <strong>academic term</strong>.
            </div>

        </div>


        <!-- ERROR -->
        <div
            v-if="loadError"
            class="alert alert-danger"
        >

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            {{ loadError }}

        </div>


        <!-- MAIN CARD -->
        <div class="section-card">

            <!-- SECTION TITLE -->
            <div class="section-title">

                <i class="bi bi-journal-check"></i>

                <span>
                    Enrollment Information
                </span>

            </div>


            <!-- LOADING -->
            <div
                v-if="loading"
                class="loading-state"
            >

                <div
                    class="spinner-border spinner-border-sm"
                    role="status"
                ></div>

                <span>
                    Loading enrollment options...
                </span>

            </div>


            <!-- FORM -->
            <div
                v-else
                class="row g-2"
            >

                <!-- SCHOOL YEAR -->
                <div class="col-md-6">

                    <label>
                        School Year
                    </label>

                    <select
                        class="form-select"
                        v-model="model.school_year_id"
                        :disabled="schoolYears.length === 0"
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

                    <small
                        v-if="schoolYears.length === 0"
                        class="field-warning"
                    >
                        No active school year is currently available.
                    </small>

                </div>


                <!-- SEMESTER -->
                <div class="col-md-6">

                    <label>
                        Semester
                    </label>

                    <select
                        class="form-select"
                        v-model="model.semester_id"
                        :disabled="semesters.length === 0"
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

                    <small
                        v-if="semesters.length === 0"
                        class="field-warning"
                    >
                        No active semester is currently available.
                    </small>

                </div>


                <!-- COURSE -->
                <div class="col-md-6">

                    <label>
                        Course
                    </label>

                    <select
                        class="form-select"
                        v-model="model.course_id"
                        :disabled="courses.length === 0"
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
                            <template v-if="course.code">
                                ({{ course.code }})
                            </template>
                        </option>

                    </select>

                    <small
                        v-if="courses.length === 0"
                        class="field-warning"
                    >
                        No courses are currently available.
                    </small>

                </div>


                <!-- YEAR LEVEL -->
                <div class="col-md-6">

                    <label>
                        Year Level
                    </label>

                    <select
                        class="form-select"
                        v-model="model.year_level"
                        :disabled="
                            String(model.student_type).toLowerCase()
                            === 'freshmen'
                        "
                    >

                        <option value="">
                            Select Year Level
                        </option>

                        <option value="1">
                            1st Year
                        </option>

                        <option value="2">
                            2nd Year
                        </option>

                        <option value="3">
                            3rd Year
                        </option>

                        <option value="4">
                            4th Year
                        </option>

                    </select>

                    <small
                        v-if="
                            String(model.student_type).toLowerCase()
                            === 'freshmen'
                        "
                        class="field-muted"
                    >
                        Freshmen students are automatically assigned to 1st Year.
                    </small>

                </div>


                <!-- SCHEDULE -->
                <div
                    v-if="showClassSchedule"
                    class="col-12 schedule-section"
                >

                    <label>
                        Schedule Preference
                    </label>


                    <div class="schedule-options">

                        <!-- DAY -->
                        <label
                            class="schedule-card"
                            :class="{
                                active:
                                    model.schedule_preference ===
                                    'Day Only'
                            }"
                        >

                            <input
                                type="radio"
                                name="schedule_preference"
                                value="Day Only"
                                v-model="model.schedule_preference"
                            >

                            <div class="schedule-icon">

                                <i class="bi bi-sun-fill"></i>

                            </div>

                            <div class="schedule-content">

                                <strong>
                                    Day Only
                                </strong>

                                <span>
                                    Available for daytime classes
                                </span>

                            </div>

                            <i
                                v-if="
                                    model.schedule_preference ===
                                    'Day Only'
                                "
                                class="bi bi-check-circle-fill selected-icon"
                            ></i>

                        </label>


                        <!-- NIGHT -->
                        <label
                            class="schedule-card"
                            :class="{
                                active:
                                    model.schedule_preference ===
                                    'Night Only'
                            }"
                        >

                            <input
                                type="radio"
                                name="schedule_preference"
                                value="Night Only"
                                v-model="model.schedule_preference"
                            >

                            <div class="schedule-icon">

                                <i class="bi bi-moon-stars-fill"></i>

                            </div>

                            <div class="schedule-content">

                                <strong>
                                    Night Only
                                </strong>

                                <span>
                                    Available for evening classes
                                </span>

                            </div>

                            <i
                                v-if="
                                    model.schedule_preference ===
                                    'Night Only'
                                "
                                class="bi bi-check-circle-fill selected-icon"
                            ></i>

                        </label>


                        <!-- FLEXIBLE -->
                        <label
                            class="schedule-card"
                            :class="{
                                active:
                                    model.schedule_preference ===
                                    'Flexible (Day & Night)'
                            }"
                        >

                            <input
                                type="radio"
                                name="schedule_preference"
                                value="Flexible (Day & Night)"
                                v-model="model.schedule_preference"
                            >

                            <div class="schedule-icon">

                                <i class="bi bi-clock-fill"></i>

                            </div>

                            <div class="schedule-content">

                                <strong>
                                    Flexible (Day & Night)
                                </strong>

                                <span>
                                    Available for both daytime and evening classes
                                </span>

                            </div>

                            <i
                                v-if="
                                    model.schedule_preference ===
                                    'Flexible (Day & Night)'
                                "
                                class="bi bi-check-circle-fill selected-icon"
                            ></i>

                        </label>

                    </div>


                    <small class="schedule-help">
                        Select the schedule that best matches your availability.
                    </small>

                </div>


                <!-- REMARKS -->
                <div class="col-12 remarks-section">

                    <label>

                        Remarks

                        <span class="optional">
                            Optional
                        </span>

                    </label>

                    <textarea
                        rows="3"
                        class="form-control"
                        placeholder="Additional requests or information..."
                        v-model="model.remarks"
                    ></textarea>

                </div>

            </div>

        </div>

    </div>

</template>


<script setup>

import {
    ref,
    onMounted,
    computed,
    watch
} from "vue"

import api from "@/services/api"


/* =========================================================
   FORM MODEL
========================================================= */

const model = defineModel()


/* =========================================================
   DATA
========================================================= */

const schoolYears = ref([])

const semesters = ref([])

const courses = ref([])

const curricula = ref([])

const loading = ref(false)

const loadError = ref("")


/* =========================================================
   LOAD ENROLLMENT OPTIONS
========================================================= */

async function loadEnrollmentData() {

    loading.value = true

    loadError.value = ""

    try {

        const { data } =
            await api.get("/enrollment/options")


        console.log(
            "Enrollment Options:",
            data
        )


        /* SCHOOL YEARS */

        schoolYears.value =
            (data.school_years || []).filter(
                year =>
                    year.is_active == 1 ||
                    year.is_active === true
            )


        /* SEMESTERS */

        semesters.value =
            (data.semesters || []).filter(
                semester =>
                    semester.is_active == 1 ||
                    semester.is_active === true
            )


        /* COURSES */

        courses.value =
            data.courses || []


        /* CURRICULA */

        curricula.value =
            data.curricula || []


        /* ACTIVE SCHOOL YEAR */

        const activeYear =
            schoolYears.value.find(
                year =>
                    year.is_active == 1 ||
                    year.is_active === true
            )


        /* ACTIVE SEMESTER */

        const activeSemester =
            semesters.value.find(
                semester =>
                    semester.is_active == 1 ||
                    semester.is_active === true
            )


        /* AUTO SELECT SCHOOL YEAR */

        if (
            activeYear &&
            !model.value.school_year_id
        ) {

            model.value.school_year_id =
                activeYear.id

        }


        /* AUTO SELECT SEMESTER */

        if (
            activeSemester &&
            !model.value.semester_id
        ) {

            model.value.semester_id =
                activeSemester.id

        }


        /* FRESHMEN */

        if (
            String(model.value.student_type)
                .toLowerCase() === "freshmen"
        ) {

            model.value.year_level = "1"

        }


        /* NO DATA */

        if (
            schoolYears.value.length === 0 &&
            semesters.value.length === 0 &&
            courses.value.length === 0
        ) {

            loadError.value =
                "No enrollment options are currently available. Please contact the registrar."

        }

    } catch (error) {

        console.error(
            "Failed to load enrollment options:",
            error
        )


        if (
            error?.response?.data?.message
        ) {

            loadError.value =
                error.response.data.message

        } else {

            loadError.value =
                "Unable to load enrollment options. Please check your connection and try again."

        }

    } finally {

        loading.value = false

    }

}


/* =========================================================
   SELECTED CURRICULUM
========================================================= */

const selectedCurriculum =
    computed(() => {

        if (!model.value.course_id) {

            return null

        }


        return curricula.value.find(
            curriculum =>
                String(curriculum.course_id) ===
                String(model.value.course_id)
        )

    })


/* =========================================================
   AUTOMATIC CURRICULUM
========================================================= */

watch(
    selectedCurriculum,

    curriculum => {

        model.value.curriculum_id =
            curriculum
                ? curriculum.id
                : ""

    },

    {
        immediate: true
    }
)


/* =========================================================
   STUDENT TYPE
========================================================= */

watch(
    () => model.value.student_type,

    type => {

        if (
            String(type).toLowerCase() ===
            "freshmen"
        ) {

            model.value.year_level = "1"

        }

    },

    {
        immediate: true
    }
)


/* =========================================================
   SCHEDULE COURSES
========================================================= */

const scheduleCourseKeywords = [

    "entrep",
    "entrepreneurship",
    "bsba",
    "bsoa",
    "beed",
    "bsed"

]


/* =========================================================
   SELECTED COURSE
========================================================= */

const selectedCourse =
    computed(() => {

        if (!model.value.course_id) {

            return null

        }


        return courses.value.find(
            course =>
                String(course.id) ===
                String(model.value.course_id)
        )

    })


/* =========================================================
   SHOW SCHEDULE
========================================================= */

const showClassSchedule =
    computed(() => {

        if (!selectedCourse.value) {

            return false

        }


        const courseText = [

            selectedCourse.value.name || "",

            selectedCourse.value.code || ""

        ]
            .join(" ")
            .toLowerCase()


        return scheduleCourseKeywords.some(
            keyword =>
                courseText.includes(keyword)
        )

    })


/* =========================================================
   CLEAR SCHEDULE
========================================================= */

watch(
    showClassSchedule,

    visible => {

        if (!visible) {

            model.value.schedule_preference = ""

        }

    },

    {
        immediate: true
    }
)


/* =========================================================
   LOAD DATA
========================================================= */

onMounted(() => {

    loadEnrollmentData()

})

</script>


<style scoped>

/* =========================================================
   PAGE
========================================================= */

.enrollment-page{
    width:100%;
    padding:6px 8px;
}


/* =========================================================
   HEADER
========================================================= */

.page-header{
    margin-bottom:12px;
}

.page-header h3{
    margin:0 0 4px;
    color:#064E2A;
    font-size:20px;
    font-weight:800;
}

.page-header p{
    margin:0;
    color:#6B7280;
    font-size:13px;
    line-height:1.45;
}


/* =========================================================
   NOTICE
========================================================= */

.notice{
    display:flex;
    align-items:flex-start;
    gap:8px;
    margin-bottom:12px;
    padding:10px 12px;
    background:#ECFDF5;
    border:1px solid #A7F3D0;
    border-radius:10px;
    color:#065F46;
    font-size:12px;
    line-height:1.45;
}

.notice i{
    flex-shrink:0;
    margin-top:1px;
    color:#047857;
    font-size:19px;
}

.notice strong{
    font-weight:700;
}


/* =========================================================
   ERROR
========================================================= */

.alert{
    margin-bottom:12px;
    padding:9px 11px;
    border-radius:9px;
    font-size:12px;
}


/* =========================================================
   MAIN CARD
========================================================= */

.section-card{
    padding:15px;
    background:#fff;
    border:1px solid #E5E7EB;
    border-radius:12px;
    box-shadow:0 3px 10px rgba(0,0,0,.04);
}


/* =========================================================
   SECTION TITLE
========================================================= */

.section-title{
    display:flex;
    align-items:center;
    gap:7px;
    padding-bottom:10px;
    margin-bottom:12px;
    border-bottom:1px solid #E5E7EB;
    color:#064E2A;
    font-size:15px;
    font-weight:700;
}

.section-title i{
    color:#0B6B3A;
    font-size:18px;
}


/* =========================================================
   LOADING
========================================================= */

.loading-state{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    min-height:110px;
    color:#6B7280;
    font-size:12px;
}


/* =========================================================
   LABEL
========================================================= */

label{
    display:block;
    margin-bottom:5px;
    color:#374151;
    font-size:12px;
    font-weight:600;
}


/* =========================================================
   SELECT
========================================================= */

.form-select{
    min-height:42px;
    padding:7px 32px 7px 10px;
    border:1px solid #D1D5DB;
    border-radius:9px;
    font-size:13px;
    transition:.2s;
}

.form-select:hover{
    border-color:#0B6B3A;
}

.form-select:focus{
    border-color:#0B6B3A;
    box-shadow:0 0 0 2px rgba(11,107,58,.10);
}


/* =========================================================
   WARNINGS
========================================================= */

.field-warning,
.field-muted{
    display:block;
    margin-top:4px;
    font-size:10px;
    line-height:1.35;
}

.field-warning{
    color:#B45309;
}

.field-muted{
    color:#6B7280;
}


/* =========================================================
   SCHEDULE
========================================================= */

.schedule-section{
    margin-top:3px;
}

.schedule-options{
    display:grid;
    grid-template-columns:repeat(3,minmax(0,1fr));
    gap:8px;
}


/* =========================================================
   SCHEDULE CARD
========================================================= */

.schedule-card{
    position:relative;
    display:flex;
    align-items:center;
    gap:9px;
    min-height:70px;
    margin:0;
    padding:11px;
    background:#fff;
    border:1px solid #E5E7EB;
    border-radius:10px;
    cursor:pointer;
    transition:.2s;
}

.schedule-card:hover{
    border-color:#0B6B3A;
}

.schedule-card.active{
    background:#F0FDF4;
    border-color:#0B6B3A;
}


/* =========================================================
   HIDDEN RADIO
========================================================= */

.schedule-card input{
    position:absolute;
    opacity:0;
    pointer-events:none;
}


/* =========================================================
   SCHEDULE ICON
========================================================= */

.schedule-icon{
    width:36px;
    height:36px;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    border-radius:9px;
    background:#ECFDF5;
    color:#0B6B3A;
}

.schedule-icon i{
    font-size:17px;
}


/* =========================================================
   SCHEDULE CONTENT
========================================================= */

.schedule-content{
    min-width:0;
    display:flex;
    flex-direction:column;
    gap:2px;
    padding-right:15px;
}

.schedule-content strong{
    color:#064E2A;
    font-size:12px;
    font-weight:700;
    line-height:1.25;
}

.schedule-content span{
    color:#6B7280;
    font-size:10px;
    font-weight:400;
    line-height:1.3;
}


/* =========================================================
   SELECTED ICON
========================================================= */

.selected-icon{
    position:absolute;
    top:8px;
    right:8px;
    color:#0B6B3A;
    font-size:14px;
}


/* =========================================================
   SCHEDULE HELP
========================================================= */

.schedule-help{
    display:block;
    margin-top:5px;
    color:#6B7280;
    font-size:10px;
}


/* =========================================================
   REMARKS
========================================================= */

.remarks-section{
    margin-top:3px;
}

.form-control{
    border:1px solid #D1D5DB;
    border-radius:9px;
    padding:8px 10px;
    font-size:13px;
    resize:vertical;
    transition:.2s;
}

.form-control:focus{
    border-color:#0B6B3A;
    box-shadow:0 0 0 2px rgba(11,107,58,.10);
}


/* =========================================================
   OPTIONAL
========================================================= */

.optional{
    display:inline-block;
    margin-left:4px;
    padding:2px 6px;
    border-radius:12px;
    background:#F3F4F6;
    color:#6B7280;
    font-size:9px;
    font-weight:600;
    vertical-align:middle;
}


/* =========================================================
   TABLET
========================================================= */

@media(max-width:992px){

    .schedule-options{
        grid-template-columns:repeat(2,minmax(0,1fr));
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:768px){

    .enrollment-page{
        padding:4px 2px;
    }

    .page-header{
        margin-bottom:9px;
    }

    .page-header h3{
        font-size:18px;
    }

    .page-header p{
        font-size:11px;
        line-height:1.4;
    }

    .notice{
        margin-bottom:9px;
        padding:9px 10px;
        gap:7px;
        font-size:11px;
    }

    .notice i{
        font-size:17px;
    }

    .section-card{
        padding:11px;
        border-radius:10px;
    }

    .section-title{
        padding-bottom:8px;
        margin-bottom:9px;
        font-size:14px;
    }

    .section-title i{
        font-size:17px;
    }

    .row{
        --bs-gutter-x:7px;
        --bs-gutter-y:7px;
    }

    label{
        margin-bottom:4px;
        font-size:11px;
    }

    .form-select{
        min-height:40px;
        font-size:13px;
        border-radius:8px;
    }

    .field-warning,
    .field-muted{
        font-size:9px;
    }

    .schedule-options{
        grid-template-columns:1fr;
        gap:6px;
    }

    .schedule-card{
        min-height:62px;
        padding:9px;
        gap:8px;
        border-radius:8px;
    }

    .schedule-icon{
        width:32px;
        height:32px;
        border-radius:8px;
    }

    .schedule-icon i{
        font-size:15px;
    }

    .schedule-content strong{
        font-size:11px;
    }

    .schedule-content span{
        font-size:9px;
    }

    .selected-icon{
        top:7px;
        right:7px;
        font-size:13px;
    }

    .schedule-help{
        margin-top:4px;
        font-size:9px;
    }

    .form-control{
        font-size:13px;
        padding:7px 8px;
        border-radius:8px;
    }

    .remarks-section{
        margin-top:2px;
    }

    .optional{
        padding:2px 5px;
        font-size:8px;
    }

}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media(max-width:380px){

    .page-header h3{
        font-size:17px;
    }

    .page-header p{
        font-size:10px;
    }

    .section-card{
        padding:9px;
    }

    .section-title{
        font-size:13px;
    }

    .form-select{
        min-height:38px;
        font-size:12px;
    }

    .schedule-card{
        min-height:58px;
    }

}
</style>
