<template>

<div class="review-page">

    <!-- LOADING -->

    <div
        v-if="loading"
        class="loading-state"
    >

        <div
            class="spinner-border text-success"
            role="status"
        ></div>

        <div class="loading-text">
            Loading student information...
        </div>

    </div>


    <!-- CONTENT -->

    <template v-else>


        <!-- =================================================
             PERSONAL INFORMATION
        ================================================== -->

        <div class="review-card">

            <div class="section-title">
                <i class="bi bi-person-circle"></i>
                <span>Personal Information</span>
            </div>


            <div class="row g-2">

                <div class="col-6 col-md-4">
                    <strong>First Name</strong>
                    <p>{{ profile.student.first_name || "-" }}</p>
                </div>

                <div class="col-6 col-md-4">
                    <strong>Middle Name</strong>
                    <p>{{ profile.student.middle_name || "-" }}</p>
                </div>

                <div class="col-12 col-md-4">
                    <strong>Last Name</strong>
                    <p>{{ profile.student.last_name || "-" }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Birth Date</strong>
                    <p>{{ formatDate(profile.student.birth_date) }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Gender</strong>
                    <p>{{ profile.student.gender || "-" }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Civil Status</strong>
                    <p>{{ profile.student.civil_status || "-" }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Student Type</strong>
                    <p>{{ profile.student.student_type || "-" }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <strong>Address</strong>
                    <p>{{ profile.student.address || "-" }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Contact Number</strong>
                    <p>{{ profile.student.contact_number || "-" }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Nationality</strong>
                    <p>{{ profile.student.nationality || "-" }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <strong>Email Address</strong>
                    <p>{{ profile.student.email || "-" }}</p>
                </div>

                <div class="col-12">
                    <strong>Religion</strong>
                    <p>{{ profile.student.religion || "-" }}</p>
                </div>

            </div>

        </div>



        <!-- =================================================
             GUARDIAN INFORMATION
        ================================================== -->

        <div class="review-card">

            <div class="section-title">
                <i class="bi bi-people-fill"></i>
                <span>Guardian Information</span>
            </div>


            <div class="row g-2">

                <div class="col-12 col-md-6">
                    <strong>Guardian</strong>
                    <p>{{ model.guardian_name || "-" }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <strong>Relationship</strong>
                    <p>{{ model.guardian_relationship || "-" }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <strong>Guardian Contact</strong>
                    <p>{{ model.guardian_contact || "-" }}</p>
                </div>

            </div>

        </div>



        <!-- =================================================
             COURSE INFORMATION
        ================================================== -->

        <div
            v-if="model.student_type !== 'Continuing'"
            class="review-card"
        >

            <div class="section-title">
                <i class="bi bi-mortarboard-fill"></i>
                <span>Course Information</span>
            </div>


            <div class="row g-2">

                <div class="col-12 col-md-6">
                    <strong>Course</strong>
                    <p>{{ selectedCourseName }}</p>
                </div>

                <div class="col-12 col-md-6">
                    <strong>Academic Year</strong>
                    <p>{{ selectedSchoolYearName }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Year Level</strong>
                    <p>{{ formatYearLevel(model.year_level) }}</p>
                </div>

                <div class="col-6 col-md-6">
                    <strong>Semester</strong>
                    <p>{{ selectedSemesterName }}</p>
                </div>

                <div
                    v-if="model.schedule_preference"
                    class="col-12 col-md-6"
                >

                    <strong>Schedule Preference</strong>

                    <p>
                        <span class="schedule-badge">

                            <i :class="scheduleIcon"></i>

                            {{ model.schedule_preference }}

                        </span>
                    </p>

                </div>

            </div>

        </div>



        <!-- =================================================
             ACADEMIC INFORMATION
        ================================================== -->

        <div
            v-if="model.student_type !== 'Continuing'"
            class="review-card"
        >

            <div class="section-title">
                <i class="bi bi-book-fill"></i>
                <span>Academic Information</span>
            </div>


            <div class="row g-2">

                <div class="col-12 col-md-6">
                    <strong>Last School Attended</strong>
                    <p>{{ model.last_school || "-" }}</p>
                </div>

                <div class="col-6 col-md-3">
                    <strong>Strand</strong>
                    <p>{{ model.strand || "-" }}</p>
                </div>

                <div class="col-6 col-md-3">
                    <strong>Graduation Year</strong>
                    <p>{{ model.graduation_year || "-" }}</p>
                </div>


                <!-- TRANSFEREE -->

                <div
                    v-if="model.student_type === 'Transferee'"
                    class="col-12 col-md-6"
                >
                    <strong>Previous Course</strong>
                    <p>{{ model.previous_course || "-" }}</p>
                </div>

                <div
                    v-if="model.student_type === 'Transferee'"
                    class="col-12 col-md-6"
                >
                    <strong>Units Earned</strong>
                    <p>{{ model.units_earned || "-" }}</p>
                </div>


                <!-- RETURNEE -->

                <div
                    v-if="model.student_type === 'Returnee'"
                    class="col-12 col-md-6"
                >
                    <strong>Last School Year</strong>
                    <p>{{ model.last_school_year || "-" }}</p>
                </div>

                <div
                    v-if="model.student_type === 'Returnee'"
                    class="col-12 col-md-6"
                >
                    <strong>Last Semester</strong>
                    <p>{{ model.last_semester || "-" }}</p>
                </div>

            </div>

        </div>



        <!-- =================================================
             DOCUMENTS
        ================================================== -->

        <div
            v-if="model.student_type !== 'Continuing'"
            class="review-card"
        >

            <div class="section-title">
                <i class="bi bi-folder-check"></i>
                <span>Enrollment Documents</span>
            </div>


            <div class="document-notice">

                <i class="bi bi-info-circle-fill"></i>

                <span>
                    Required documents may either be uploaded
                    or submitted through a Promissory Undertaking.
                </span>

            </div>


            <div class="document-list">


                <!-- PSA -->

                <div class="document-row">

                    <div class="document-info">

                        <strong>
                            PSA Birth Certificate
                        </strong>

                        <small>

                            <template
                                v-if="model.psa_birth_certificate"
                            >
                                {{ model.psa_birth_certificate.name }}
                            </template>

                            <template
                                v-else-if="model.psa_birth_certificate_promissory"
                            >

                                Promissory Undertaking

                                <template
                                    v-if="
                                        model.psa_birth_certificate_promissory_reason
                                    "
                                >
                                    —
                                    {{ model.psa_birth_certificate_promissory_reason }}
                                </template>

                            </template>

                            <template v-else>
                                Not provided
                            </template>

                        </small>

                    </div>


                    <span
                        class="badge"
                        :class="
                            documentStatusClass(
                                model.psa_birth_certificate,
                                model.psa_birth_certificate_promissory
                            )
                        "
                    >
                        {{
                            documentStatus(
                                model.psa_birth_certificate,
                                model.psa_birth_certificate_promissory
                            )
                        }}
                    </span>

                </div>



                <!-- GOOD MORAL -->

                <div class="document-row">

                    <div class="document-info">

                        <strong>
                            Good Moral Certificate
                        </strong>

                        <small>

                            <template
                                v-if="model.good_moral"
                            >
                                {{ model.good_moral.name }}
                            </template>

                            <template
                                v-else-if="model.good_moral_promissory"
                            >

                                Promissory Undertaking

                                <template
                                    v-if="
                                        model.good_moral_promissory_reason
                                    "
                                >
                                    —
                                    {{ model.good_moral_promissory_reason }}
                                </template>

                            </template>

                            <template v-else>
                                Not provided
                            </template>

                        </small>

                    </div>


                    <span
                        class="badge"
                        :class="
                            documentStatusClass(
                                model.good_moral,
                                model.good_moral_promissory
                            )
                        "
                    >
                        {{
                            documentStatus(
                                model.good_moral,
                                model.good_moral_promissory
                            )
                        }}
                    </span>

                </div>



                <!-- ACADEMIC DOCUMENT -->

                <div class="document-row">

                    <div class="document-info">

                        <strong>
                            Academic Document
                        </strong>

                        <small>

                            <template
                                v-if="model.academic_document"
                            >
                                {{ model.academic_document.name }}
                            </template>

                            <template
                                v-else-if="model.academic_document_promissory"
                            >

                                Promissory Undertaking

                                <template
                                    v-if="
                                        model.academic_document_promissory_reason
                                    "
                                >
                                    —
                                    {{ model.academic_document_promissory_reason }}
                                </template>

                            </template>

                            <template v-else>
                                Not provided
                            </template>

                        </small>

                    </div>


                    <span
                        class="badge"
                        :class="
                            documentStatusClass(
                                model.academic_document,
                                model.academic_document_promissory
                            )
                        "
                    >
                        {{
                            documentStatus(
                                model.academic_document,
                                model.academic_document_promissory
                            )
                        }}
                    </span>

                </div>



                <!-- 2X2 PICTURE -->

                <div class="document-row">

                    <div class="document-info">

                        <strong>
                            2x2 Picture
                        </strong>

                        <small>

                            <template
                                v-if="model.id_picture"
                            >
                                {{ model.id_picture.name }}
                            </template>

                            <template
                                v-else-if="model.id_picture_promissory"
                            >

                                Promissory Undertaking

                                <template
                                    v-if="
                                        model.id_picture_promissory_reason
                                    "
                                >
                                    —
                                    {{ model.id_picture_promissory_reason }}
                                </template>

                            </template>

                            <template v-else>
                                Not provided
                            </template>

                        </small>

                    </div>


                    <span
                        class="badge"
                        :class="
                            documentStatusClass(
                                model.id_picture,
                                model.id_picture_promissory
                            )
                        "
                    >
                        {{
                            documentStatus(
                                model.id_picture,
                                model.id_picture_promissory
                            )
                        }}
                    </span>

                </div>

            </div>

        </div>



        <!-- =================================================
             REMARKS
        ================================================== -->

        <div
            v-if="model.remarks"
            class="review-card"
        >

            <div class="section-title">
                <i class="bi bi-chat-left-text-fill"></i>
                <span>Remarks</span>
            </div>

            <div class="remarks-box">
                {{ model.remarks }}
            </div>

        </div>



        <!-- =================================================
             DATA PRIVACY & CERTIFICATION
        ================================================== -->

        <div class="review-card">

            <div class="section-title">
                <i class="bi bi-shield-check"></i>
                <span>Data Privacy & Certification</span>
            </div>


            <div class="declaration">

                <label
                    class="declaration-item"
                    for="dataPrivacy"
                >

                    <input
                        id="dataPrivacy"
                        type="checkbox"
                        class="form-check-input"
                        v-model="model.data_privacy_consent"
                    >

                    <span>
                        I agree to the collection, processing,
                        and storage of my personal information
                        for enrollment purposes in accordance
                        with the Data Privacy Act.
                    </span>

                </label>


                <label
                    class="declaration-item"
                    for="informationCertified"
                >

                    <input
                        id="informationCertified"
                        type="checkbox"
                        class="form-check-input"
                        v-model="model.information_certified"
                    >

                    <span>
                        I certify that all information and
                        documents I provided are true,
                        complete, and correct.
                    </span>

                </label>

            </div>

        </div>


    </template>

</div>

</template>


<script setup>

import {
    ref,
    onMounted,
    computed
} from "vue"

import api from "@/services/api"


/* =========================================================
   FORM MODEL
========================================================= */

const model = defineModel()


/* =========================================================
   LOADING
========================================================= */

const loading = ref(true)


/* =========================================================
   PROFILE
========================================================= */

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
        nationality: "",
        religion: "",
        email: ""

    }

})


/* =========================================================
   ENROLLMENT OPTIONS
========================================================= */

const schoolYears = ref([])
const semesters = ref([])
const courses = ref([])
const curricula = ref([])


/* =========================================================
   FORMAT DATE
========================================================= */

function formatDate(date) {

    if (!date) {
        return "-"
    }

    const parsedDate = new Date(date)

    if (Number.isNaN(parsedDate.getTime())) {
        return "-"
    }

    return parsedDate.toLocaleDateString(
        "en-PH",
        {
            year: "numeric",
            month: "long",
            day: "numeric"
        }
    )

}


/* =========================================================
   FORMAT YEAR LEVEL
========================================================= */

function formatYearLevel(year) {

    if (!year) {
        return "-"
    }

    const levels = {
        "1": "1st Year",
        "2": "2nd Year",
        "3": "3rd Year",
        "4": "4th Year"
    }

    return levels[String(year)] ||
        `${year} Year`

}


/* =========================================================
   LOAD PROFILE
========================================================= */

async function loadProfile() {

    try {

        const response =
            await api.get("/student/profile")

        profile.value =
            response.data

    } catch (error) {

        console.error(
            "Failed to load profile:",
            error
        )

    }

}


/* =========================================================
   LOAD ENROLLMENT OPTIONS
========================================================= */

async function loadEnrollmentOptions() {

    try {

        const { data } =
            await api.get(
                "/enrollment/options"
            )

        schoolYears.value =
            data.school_years || []

        semesters.value =
            data.semesters || []

        courses.value =
            data.courses || []

        curricula.value =
            data.curricula || []

    } catch (error) {

        console.error(
            "Failed to load enrollment options:",
            error
        )

    }

}


/* =========================================================
   SELECTED COURSE
========================================================= */

const selectedCourse =
    computed(() => {

        return courses.value.find(
            course =>
                String(course.id) ===
                String(model.value.course_id)
        ) || null

    })


/* =========================================================
   SELECTED SEMESTER
========================================================= */

const selectedSemester =
    computed(() => {

        return semesters.value.find(
            semester =>
                String(semester.id) ===
                String(model.value.semester_id)
        ) || null

    })


/* =========================================================
   SELECTED SCHOOL YEAR
========================================================= */

const selectedSchoolYear =
    computed(() => {

        return schoolYears.value.find(
            year =>
                String(year.id) ===
                String(model.value.school_year_id)
        ) || null

    })


/* =========================================================
   SELECTED CURRICULUM
========================================================= */

const selectedCurriculum =
    computed(() => {

        return curricula.value.find(
            curriculum =>
                String(curriculum.id) ===
                String(model.value.curriculum_id)
        ) || null

    })


/* =========================================================
   COURSE NAME
========================================================= */

const selectedCourseName =
    computed(() => {

        if (!selectedCourse.value) {
            return "-"
        }

        return (
            selectedCourse.value.name ||
            selectedCourse.value.course_name ||
            selectedCourse.value.code ||
            "-"
        )

    })


/* =========================================================
   SCHOOL YEAR NAME
========================================================= */

const selectedSchoolYearName =
    computed(() => {

        if (!selectedSchoolYear.value) {
            return "-"
        }

        return (
            selectedSchoolYear.value.school_year ||
            selectedSchoolYear.value.name ||
            selectedSchoolYear.value.year ||
            "-"
        )

    })


/* =========================================================
   SEMESTER NAME
========================================================= */

const selectedSemesterName =
    computed(() => {

        if (!selectedSemester.value) {
            return "-"
        }

        return (
            selectedSemester.value.name ||
            selectedSemester.value.semester_name ||
            "-"
        )

    })


/* =========================================================
   SCHEDULE ICON
========================================================= */

const scheduleIcon =
    computed(() => {

        switch (
            model.value.schedule_preference
        ) {

            case "Day Only":
                return "bi bi-sun-fill"

            case "Night Only":
                return "bi bi-moon-stars-fill"

            case "Flexible (Day & Night)":
                return "bi bi-clock-fill"

            default:
                return "bi bi-calendar-check"

        }

    })


/* =========================================================
   DOCUMENT STATUS
========================================================= */

function documentStatus(
    file,
    promissory
) {

    if (file) {
        return "Uploaded"
    }

    if (promissory) {
        return "Promissory"
    }

    return "Not Provided"

}


/* =========================================================
   DOCUMENT STATUS CLASS
========================================================= */

function documentStatusClass(
    file,
    promissory
) {

    if (file) {
        return "bg-success"
    }

    if (promissory) {
        return "bg-warning text-dark"
    }

    return "bg-secondary"

}


/* =========================================================
   LOAD DATA
========================================================= */

onMounted(
    async () => {

        await Promise.all([
            loadProfile(),
            loadEnrollmentOptions()
        ])

        loading.value = false

    }
)

</script>


<style scoped>

/* =========================================================
   PAGE
========================================================= */

.review-page {
    padding: 6px 8px;
}


/* =========================================================
   LOADING
========================================================= */

.loading-state {
    min-height: 180px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.loading-text {
    font-size: 14px;
    color: #374151;
}


/* =========================================================
   REVIEW CARD
========================================================= */

.review-card {
    background: #fff;
    border-radius: 14px;
    padding: 14px 16px;
    margin-bottom: 12px;
    border: 1px solid #E5E7EB;
    box-shadow: 0 4px 14px rgba(0, 0, 0, .04);
}


/* =========================================================
   SECTION TITLE
========================================================= */

.section-title {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(
        135deg,
        #064E2A,
        #0B6B3A
    );
    color: #fff;
    padding: 9px 12px;
    border-radius: 9px;
    margin-bottom: 12px;
    font-size: 14px;
    font-weight: 700;
}

.section-title i {
    font-size: 16px;
}


/* =========================================================
   LABEL
========================================================= */

strong {
    display: block;
    margin-bottom: 2px;
    color: #064E2A;
    font-size: 12px;
    font-weight: 700;
}


/* =========================================================
   VALUE
========================================================= */

p {
    margin: 0;
    color: #374151;
    font-size: 13px;
    line-height: 1.35;
    overflow-wrap: anywhere;
}


/* =========================================================
   DOCUMENT NOTICE
========================================================= */

.document-notice {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin-bottom: 8px;
    padding: 9px 10px;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 9px;
    color: #065F46;
    font-size: 12px;
    line-height: 1.4;
}

.document-notice i {
    flex-shrink: 0;
    margin-top: 1px;
}


/* =========================================================
   DOCUMENT LIST
========================================================= */

.document-list {
    border-top: 1px solid #E5E7EB;
}


/* =========================================================
   DOCUMENT ROW
========================================================= */

.document-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    padding: 9px 2px;
    border-bottom: 1px solid #E5E7EB;
}

.document-row:last-child {
    border-bottom: 0;
}


/* =========================================================
   DOCUMENT INFO
========================================================= */

.document-info {
    min-width: 0;
    flex: 1;
}

.document-info strong {
    margin-bottom: 1px;
    font-size: 12px;
}

.document-info small {
    display: block;
    color: #6B7280;
    font-size: 11px;
    line-height: 1.35;
    overflow-wrap: anywhere;
}


/* =========================================================
   BADGE
========================================================= */

.badge {
    flex-shrink: 0;
    padding: 5px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
}


/* =========================================================
   SCHEDULE BADGE
========================================================= */

.schedule-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 9px;
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 20px;
    color: #065F46;
    font-size: 11px;
    font-weight: 600;
}


/* =========================================================
   REMARKS
========================================================= */

.remarks-box {
    padding: 10px 12px;
    background: #F9FAFB;
    border: 1px solid #E5E7EB;
    border-radius: 9px;
    color: #374151;
    font-size: 13px;
    line-height: 1.5;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}


/* =========================================================
   DECLARATION
========================================================= */

.declaration {
    background: #ECFDF5;
    border: 1px solid #A7F3D0;
    border-radius: 10px;
    padding: 11px;
}


/* =========================================================
   DECLARATION ITEM
========================================================= */

.declaration-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    margin: 0;
    color: #374151;
    font-size: 12px;
    line-height: 1.45;
    cursor: pointer;
}

.declaration-item + .declaration-item {
    margin-top: 10px;
}


/* =========================================================
   CHECKBOX
========================================================= */

.form-check-input {
    flex: 0 0 auto;
    width: 16px;
    height: 16px;
    margin: 1px 0 0;
    cursor: pointer;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {

    .review-page {
        padding: 3px 2px;
    }

    .review-card {
        padding: 10px;
        margin-bottom: 9px;
        border-radius: 11px;
    }

    .section-title {
        padding: 8px 10px;
        margin-bottom: 9px;
        border-radius: 8px;
        font-size: 13px;
    }

    .section-title i {
        font-size: 14px;
    }

    strong {
        font-size: 11px;
    }

    p {
        font-size: 12px;
    }

    .document-notice {
        padding: 8px;
        font-size: 11px;
        margin-bottom: 6px;
    }

    .document-row {
        align-items: flex-start;
        padding: 8px 1px;
        gap: 7px;
    }

    .document-info strong {
        font-size: 11px;
    }

    .document-info small {
        font-size: 10px;
    }

    .badge {
        font-size: 9px;
        padding: 4px 7px;
    }

    .schedule-badge {
        font-size: 10px;
        padding: 4px 8px;
    }

    .remarks-box {
        padding: 9px;
        font-size: 12px;
    }

    .declaration {
        padding: 9px;
    }

    .declaration-item {
        gap: 7px;
        font-size: 11px;
        line-height: 1.4;
    }

    .form-check-input {
        width: 15px;
        height: 15px;
    }

}

</style>
