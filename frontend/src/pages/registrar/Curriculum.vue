<template>

<div class="page">

    <!-- PAGE ALERT -->

    <transition name="alert">

        <div
            v-if="alertMessage"
            class="page-alert"
            :class="`alert-${alertType}`"
        >

            <div class="alert-icon">

                <i
                    class="bi"
                    :class="
                        alertType === 'success'
                            ? 'bi-check-circle-fill'
                            : 'bi-exclamation-circle-fill'
                    "
                ></i>

            </div>

            <div class="alert-content">

                <strong>
                    {{ alertType === "success" ? "Success" : "Error" }}
                </strong>

                <span>
                    {{ alertMessage }}
                </span>

            </div>

            <button
                class="alert-close"
                @click="clearAlert"
            >

                <i class="bi bi-x"></i>

            </button>

        </div>

    </transition>


    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h2>
                Curriculum Management
            </h2>

            <p>
                Manage curricula and subjects under each course.
            </p>

        </div>

        <button
            class="btn-primary"
            @click="showCurriculumModal = true"
        >

            <i class="bi bi-plus-circle"></i>

            Add Curriculum

        </button>

    </div>


    <!-- ACTIVE ACADEMIC PERIOD -->

    <div class="card active-period-card">

        <div class="card-body">

            <div class="active-period-header">

                <div>

                    <div class="section-title">

                        <i class="bi bi-calendar2-week-fill"></i>

                        Academic Period

                    </div>

                    <p class="description">

                        Select the academic year and semester that
                        should currently be active for enrollment.

                    </p>

                </div>


                <div
                    v-if="activePeriod"
                    class="active-period-badge"
                >

                    <span class="active-dot"></span>

                    Active

                </div>

            </div>


            <div class="row g-4 mt-1">

                <!-- ACADEMIC YEAR -->

                <div class="col-md-5">

                    <label>
                        Academic Year
                    </label>

                    <select
                        class="form-select"
                        v-model="selectedAcademicYear"
                        :disabled="
                            loadingAcademicPeriod ||
                            savingAcademicPeriod
                        "
                    >

                        <option value="">
                            Select Academic Year
                        </option>

                        <option
                            v-for="schoolYear in schoolYears"
                            :key="schoolYear.id"
                            :value="schoolYear.id"
                        >

                            {{ getSchoolYearName(schoolYear) }}

                        </option>

                    </select>

                </div>


                <!-- SEMESTER -->

                <div class="col-md-4">

                    <label>
                        Semester
                    </label>

                    <select
                        class="form-select"
                        v-model="selectedSemester"
                        :disabled="
                            loadingAcademicPeriod ||
                            savingAcademicPeriod
                        "
                    >

                        <option value="">
                            Select Semester
                        </option>

                        <option
                            v-for="semester in semesters"
                            :key="semester.id"
                            :value="semester.id"
                        >

                            {{ getSemesterName(semester) }}

                        </option>

                    </select>

                </div>


                <!-- BUTTON -->

                <div class="col-md-3 period-button-wrapper">

                    <button
                        class="btn-primary btn-active-period"
                        @click="setActivePeriod"
                        :disabled="
                            !selectedAcademicYear ||
                            !selectedSemester ||
                            savingAcademicPeriod
                        "
                    >

                        <i
                            class="bi"
                            :class="
                                savingAcademicPeriod
                                    ? 'bi-hourglass-split'
                                    : 'bi-check-circle'
                            "
                        ></i>

                        {{
                            savingAcademicPeriod
                                ? "Saving..."
                                : "Set as Active"
                        }}

                    </button>

                </div>

            </div>


            <!-- CURRENT ACTIVE PERIOD -->

            <div
                v-if="activePeriod"
                class="current-period"
            >

                <div class="current-period-icon">

                    <i class="bi bi-calendar-check"></i>

                </div>


                <div class="current-period-info">

                    <span class="current-period-label">
                        Current Active Period
                    </span>

                    <strong>

                        {{ activeAcademicYearName }}

                        <span class="separator">
                            •
                        </span>

                        {{ activeSemesterName }}

                    </strong>

                </div>

            </div>


            <!-- NO ACTIVE PERIOD -->

            <div
                v-else-if="!loadingAcademicPeriod"
                class="no-active-period"
            >

                <i class="bi bi-exclamation-circle"></i>

                <div>

                    <strong>
                        No active academic period
                    </strong>

                    <span>
                        Select an academic year and semester above.
                    </span>

                </div>

            </div>


            <!-- LOADING -->

            <div
                v-if="loadingAcademicPeriod"
                class="period-loading"
            >

                <div class="spinner"></div>

                Loading academic period...

            </div>

        </div>

    </div>


    <!-- COURSE + CURRICULUM -->

    <div class="card">

        <div class="card-body">

            <div class="section-title">

                <i class="bi bi-mortarboard-fill"></i>

                Select Curriculum

            </div>


            <div class="row g-4">

                <!-- COURSE -->

                <div class="col-md-6">

                    <label>
                        Course
                    </label>

                    <select
                        class="form-select"
                        v-model="selectedCourse"
                        @change="loadCurricula"
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


                <!-- CURRICULUM -->

                <div class="col-md-6">

                    <label>
                        Year Curriculum
                    </label>

                    <select
                        class="form-select"
                        v-model="selectedCurriculum"
                        @change="loadSubjects"
                        :disabled="!selectedCourse"
                    >

                        <option value="">

                            {{
                                selectedCourse
                                    ? "Select Curriculum"
                                    : "Select Course First"
                            }}

                        </option>

                        <option
                            v-for="curriculum in curricula"
                            :key="curriculum.id"
                            :value="curriculum.id"
                        >

                            {{ curriculum.name }}

                            {{
                                Number(curriculum.active) === 0
                                    ? "(Inactive)"
                                    : ""
                            }}

                        </option>

                    </select>

                </div>

            </div>

        </div>

    </div>


    <!-- CURRICULUM INFORMATION -->

    <div
        v-if="selectedCurriculumData"
        class="card"
    >

        <div class="card-body">

            <div class="curriculum-header">

                <div>

                    <div class="section-title">

                        <i class="bi bi-journal-bookmark-fill"></i>

                        {{ selectedCurriculumData.name }}

                    </div>

                    <p class="description">

                        Course:

                        <strong>
                            {{ selectedCurriculumData.course?.name || "-" }}
                        </strong>

                        &nbsp; • &nbsp;

                        Effective Year:

                        <strong>
                            {{ selectedCurriculumData.effective_year || "-" }}
                        </strong>

                    </p>

                </div>


                <span
                    class="status"
                    :class="
                        Number(selectedCurriculumData.active) === 1
                            ? 'active'
                            : 'inactive'
                    "
                >

                    {{
                        Number(selectedCurriculumData.active) === 1
                            ? "Active"
                            : "Inactive"
                    }}

                </span>

            </div>

        </div>

    </div>


    <!-- SUBJECT MANAGEMENT -->

    <div
        v-if="selectedCurriculum"
        class="card"
    >

        <div class="card-body">

            <div class="subjects-header">

                <div>

                    <div class="section-title">

                        <i class="bi bi-journal-check"></i>

                        Curriculum Subjects

                    </div>

                    <p class="description">

                        Manage subjects, year levels, semesters,
                        and units for this curriculum.

                    </p>

                </div>


                <button
                    class="btn-primary"
                    @click="openSubjectModal"
                >

                    <i class="bi bi-plus-lg"></i>

                    Add Subject

                </button>

            </div>


            <!-- LOADING -->

            <div
                v-if="loadingSubjects"
                class="loading"
            >

                <div class="spinner"></div>

                Loading subjects...

            </div>


            <!-- EMPTY -->

            <div
                v-else-if="subjects.length === 0"
                class="empty"
            >

                <i class="bi bi-journal-x"></i>

                <h5>
                    No subjects assigned
                </h5>

                <p>
                    Add subjects to this curriculum.
                </p>

            </div>


            <!-- TABLE -->

            <div
                v-else
                class="table-responsive"
            >

                <table class="table">

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Code</th>

                            <th>Subject</th>

                            <th>Year Level</th>

                            <th>Semester</th>

                            <th>Units</th>

                            <th>Action</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="(
                                item,
                                index
                            ) in subjects"
                            :key="item.id"
                        >

                            <td>
                                {{ index + 1 }}
                            </td>

                            <td>

                                <span class="subject-code">

                                    {{
                                        item.subject?.code || "-"
                                    }}

                                </span>

                            </td>

                            <td>

                                <strong>

                                    {{
                                        item.subject?.title || "-"
                                    }}

                                </strong>

                            </td>

                            <td>
                                {{ formatYear(item.year_level) }}
                            </td>

                            <td>
                                {{ formatSemester(item.semester) }}
                            </td>

                            <td>

                                <span class="units">

                                    {{
                                        item.subject?.units || 0
                                    }}

                                </span>

                            </td>

                            <td>

                                <button
                                    class="btn-edit"
                                    @click="editSubject(item)"
                                >

                                    <i class="bi bi-pencil"></i>

                                </button>


                                <button
                                    class="btn-delete"
                                    @click="deleteSubject(item.id)"
                                >

                                    <i class="bi bi-trash"></i>

                                </button>

                            </td>

                        </tr>

                    </tbody>


                    <tfoot>

                        <tr>

                            <td
                                colspan="5"
                                class="text-end"
                            >

                                <strong>
                                    Total Units
                                </strong>

                            </td>

                            <td>

                                <strong>
                                    {{ totalUnits }}
                                </strong>

                            </td>

                            <td></td>

                        </tr>

                    </tfoot>

                </table>

            </div>

        </div>

    </div>


    <!-- NO COURSE -->

    <div
        v-if="!selectedCourse"
        class="empty-main"
    >

        <i class="bi bi-mortarboard"></i>

        <h4>
            Select a Course
        </h4>

        <p>
            Choose a course above to manage its curricula.
        </p>

    </div>


    <!-- ADD CURRICULUM MODAL -->

    <div
        v-if="showCurriculumModal"
        class="modal-overlay"
        @click.self="closeCurriculumModal"
    >

        <div class="modal-box">

            <div class="modal-header">

                <h5>
                    Add Curriculum
                </h5>

                <button
                    class="close"
                    @click="closeCurriculumModal"
                >
                    ×
                </button>

            </div>


            <div class="modal-body">

                <label>
                    Course
                </label>

                <select
                    class="form-select mb-3"
                    v-model="curriculumForm.course_id"
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


                <label>
                    Curriculum Name
                </label>

                <input
                    class="form-control mb-3"
                    v-model="curriculumForm.name"
                    placeholder="Example: BSIT 2026 Curriculum"
                >


                <label>
                    Effective Year
                </label>

                <input
                    class="form-control"
                    v-model="curriculumForm.effective_year"
                    placeholder="Example: 2026"
                >

            </div>


            <div class="modal-footer">

                <button
                    class="btn-secondary"
                    @click="closeCurriculumModal"
                >

                    Cancel

                </button>


                <button
                    class="btn-primary"
                    @click="createCurriculum"
                    :disabled="saving"
                >

                    {{
                        saving
                            ? "Saving..."
                            : "Save Curriculum"
                    }}

                </button>

            </div>

        </div>

    </div>


    <!-- SUBJECT MODAL -->

    <div
        v-if="showSubjectModal"
        class="modal-overlay"
        @click.self="closeSubjectModal"
    >

        <div class="modal-box">

            <div class="modal-header">

                <h5>

                    {{
                        editingSubject
                            ? "Edit Subject"
                            : "Add Subject"
                    }}

                </h5>

                <button
                    class="close"
                    @click="closeSubjectModal"
                >
                    ×
                </button>

            </div>


            <div class="modal-body">

                <label>
                    Subject
                </label>

                <select
                    class="form-select mb-3"
                    v-model="subjectForm.subject_id"
                    :disabled="editingSubject"
                >

                    <option value="">
                        Select Subject
                    </option>

                    <option
                        v-for="subject in allSubjects"
                        :key="subject.id"
                        :value="subject.id"
                    >

                        {{ subject.code }}
                        -
                        {{ subject.title }}

                    </option>

                </select>


                <label>
                    Year Level
                </label>

                <select
                    class="form-select mb-3"
                    v-model="subjectForm.year_level"
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


                <label>
                    Semester
                </label>

                <select
                    class="form-select mb-3"
                    v-model="subjectForm.semester"
                >

                    <option value="">
                        Select Semester
                    </option>

                    <option value="1">
                        1st Semester
                    </option>

                    <option value="2">
                        2nd Semester
                    </option>

                    <option value="Summer">
                        Summer
                    </option>

                </select>


                <div v-if="editingSubject">

                    <label>
                        Units
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        :value="subjectForm.units"
                        disabled
                    >

                    <small class="text-muted">
                        Units are taken from the Subject table.
                    </small>

                </div>

                <div v-else>

                    <label>
                        Units
                    </label>

                    <input
                        type="number"
                        class="form-control"
                        :value="selectedSubjectUnits"
                        disabled
                    >

                    <small class="text-muted">
                        Units are automatically taken from the selected subject.
                    </small>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    class="btn-secondary"
                    @click="closeSubjectModal"
                >

                    Cancel

                </button>


                <button
                    class="btn-primary"
                    @click="saveSubject"
                    :disabled="saving"
                >

                    {{
                        saving
                            ? "Saving..."
                            : editingSubject
                                ? "Update Subject"
                                : "Add Subject"
                    }}

                </button>

            </div>

        </div>

    </div>

</div>

</template>


<script setup>

import {
    ref,
    computed,
    onMounted
} from "vue"

import api from "@/services/api"


/*
|--------------------------------------------------------------------------
| ALERT
|--------------------------------------------------------------------------
*/

const alertMessage = ref("")

const alertType = ref("success")

let alertTimer = null


function showAlert(
    message,
    type = "success"
) {

    alertMessage.value = message

    alertType.value = type


    if (alertTimer) {

        clearTimeout(alertTimer)

    }


    alertTimer = setTimeout(() => {

        clearAlert()

    }, 4000)

}


function clearAlert() {

    alertMessage.value = ""

}


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

const courses = ref([])

const curricula = ref([])

const subjects = ref([])

const allSubjects = ref([])


/*
|--------------------------------------------------------------------------
| ACADEMIC PERIOD
|--------------------------------------------------------------------------
*/

const schoolYears = ref([])

const semesters = ref([])

const selectedAcademicYear = ref("")

const selectedSemester = ref("")

const activePeriod = ref(null)

const loadingAcademicPeriod = ref(false)

const savingAcademicPeriod = ref(false)


/*
|--------------------------------------------------------------------------
| CURRICULUM
|--------------------------------------------------------------------------
*/

const selectedCourse = ref("")

const selectedCurriculum = ref("")

const selectedCurriculumData = ref(null)

const loadingSubjects = ref(false)

const saving = ref(false)

const showCurriculumModal = ref(false)

const showSubjectModal = ref(false)

const editingSubject = ref(null)


/*
|--------------------------------------------------------------------------
| FORMS
|--------------------------------------------------------------------------
*/

const curriculumForm = ref({

    course_id: "",

    name: "",

    effective_year: ""

})


const subjectForm = ref({

    subject_id: "",

    year_level: "",

    semester: "",

    units: ""

})


/*
|--------------------------------------------------------------------------
| ACTIVE ACADEMIC YEAR NAME
|--------------------------------------------------------------------------
*/

const activeAcademicYearName = computed(() => {

    if (!activePeriod.value) {

        return "-"

    }


    const schoolYear =
        schoolYears.value.find(
            item =>
                String(item.id) ===
                String(
                    activePeriod.value.school_year_id
                )
        )


    if (schoolYear) {

        return getSchoolYearName(schoolYear)

    }


    return (
        activePeriod.value.school_year?.year ||
        activePeriod.value.school_year?.name ||
        activePeriod.value.academic_year ||
        "-"
    )

})


/*
|--------------------------------------------------------------------------
| ACTIVE SEMESTER NAME
|--------------------------------------------------------------------------
*/

const activeSemesterName = computed(() => {

    if (!activePeriod.value) {

        return "-"

    }


    const semester =
        semesters.value.find(
            item =>
                String(item.id) ===
                String(
                    activePeriod.value.semester_id
                )
        )


    if (semester) {

        return getSemesterName(semester)

    }


    return (
        activePeriod.value.semester?.name ||
        activePeriod.value.semester_name ||
        "-"
    )

})


/*
|--------------------------------------------------------------------------
| SELECTED SUBJECT UNITS
|--------------------------------------------------------------------------
*/

const selectedSubjectUnits = computed(() => {

    const selected =
        allSubjects.value.find(
            subject =>
                String(subject.id) ===
                String(
                    subjectForm.value.subject_id
                )
        )


    return selected?.units || 0

})


/*
|--------------------------------------------------------------------------
| TOTAL UNITS
|--------------------------------------------------------------------------
*/

const totalUnits = computed(() => {

    return subjects.value.reduce(

        (total, item) => {

            return total +
                Number(
                    item.subject?.units || 0
                )

        },

        0

    )

})


/*
|--------------------------------------------------------------------------
| SCHOOL YEAR NAME
|--------------------------------------------------------------------------
*/

function getSchoolYearName(schoolYear) {

    return (
        schoolYear.year ||
        schoolYear.name ||
        schoolYear.school_year ||
        schoolYear.label ||
        "-"
    )

}


/*
|--------------------------------------------------------------------------
| SEMESTER NAME
|--------------------------------------------------------------------------
*/

function getSemesterName(semester) {

    return (
        semester.name ||
        semester.semester_name ||
        semester.semester ||
        semester.label ||
        "-"
    )

}


/*
|--------------------------------------------------------------------------
| LOAD SCHOOL YEARS
|--------------------------------------------------------------------------
*/

async function loadSchoolYears() {

    try {

        const response =
            await api.get(
                "/registrar/academic-years"
            )


        schoolYears.value =
            Array.isArray(response.data)
                ? response.data
                : response.data.data || []

    }

    catch (error) {

        console.error(
            "Failed to load academic years:",
            error
        )

        schoolYears.value = []

        showAlert(
            "Failed to load academic years.",
            "error"
        )

    }

}


/*
|--------------------------------------------------------------------------
| LOAD SEMESTERS
|--------------------------------------------------------------------------
*/

async function loadSemesters() {

    try {

        const response =
            await api.get(
                "/registrar/semesters"
            )


        semesters.value =
            Array.isArray(response.data)
                ? response.data
                : response.data.data || []

    }

    catch (error) {

        console.error(
            "Failed to load semesters:",
            error
        )

        semesters.value = []

        showAlert(
            "Failed to load semesters.",
            "error"
        )

    }

}


/*
|--------------------------------------------------------------------------
| LOAD ACTIVE PERIOD
|--------------------------------------------------------------------------
*/

async function loadActivePeriod() {

    loadingAcademicPeriod.value = true


    try {

        const response =
            await api.get(
                "/registrar/active-period"
            )


        activePeriod.value =
            response.data.active_period ||
            response.data.data ||
            response.data ||
            null


        if (activePeriod.value) {

            selectedAcademicYear.value =
                String(
                    activePeriod.value.school_year_id ||
                    ""
                )


            selectedSemester.value =
                String(
                    activePeriod.value.semester_id ||
                    ""
                )

        }

    }

    catch (error) {

        if (
            error.response?.status !== 404
        ) {

            console.error(
                "Failed to load active academic period:",
                error
            )

        }

        activePeriod.value = null

    }

    finally {

        loadingAcademicPeriod.value = false

    }

}


/*
|--------------------------------------------------------------------------
| SET ACTIVE ACADEMIC PERIOD
|--------------------------------------------------------------------------
*/

async function setActivePeriod() {

    if (!selectedAcademicYear.value) {

        showAlert(
            "Please select an academic year.",
            "error"
        )

        return

    }


    if (!selectedSemester.value) {

        showAlert(
            "Please select a semester.",
            "error"
        )

        return

    }


    if (
        !confirm(
            "Set this academic year and semester as the active enrollment period?"
        )
    ) {

        return

    }


    savingAcademicPeriod.value = true


    try {

        const response =
            await api.post(

                "/registrar/active-period",

                {

                    school_year_id:
                        selectedAcademicYear.value,

                    semester_id:
                        selectedSemester.value

                }

            )


        activePeriod.value =
            response.data.active_period ||
            response.data.data ||
            response.data


        if (activePeriod.value) {

            selectedAcademicYear.value =
                String(
                    activePeriod.value.school_year_id ||
                    selectedAcademicYear.value
                )


            selectedSemester.value =
                String(
                    activePeriod.value.semester_id ||
                    selectedSemester.value
                )

        }


        showAlert(
            "Academic period has been set as active successfully.",
            "success"
        )

    }

    catch (error) {

        console.error(
            "Set active academic period error:",
            error
        )

        console.error(
            "Response:",
            error.response?.data
        )


        showAlert(

            error.response?.data?.message ||
            "Failed to set the active academic period.",

            "error"

        )

    }

    finally {

        savingAcademicPeriod.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LOAD COURSES
|--------------------------------------------------------------------------
*/

async function loadCourses() {

    try {

        const response =
            await api.get(
                "/registrar/curriculum/courses"
            )


        courses.value =
            Array.isArray(response.data)
                ? response.data
                : response.data.data || []

    }

    catch (error) {

        console.error(
            "Failed to load courses:",
            error
        )

        showAlert(
            "Failed to load courses.",
            "error"
        )

    }

}


/*
|--------------------------------------------------------------------------
| LOAD CURRICULA
|--------------------------------------------------------------------------
*/

async function loadCurricula() {

    selectedCurriculum.value = ""

    selectedCurriculumData.value = null

    subjects.value = []


    if (!selectedCourse.value) {

        curricula.value = []

        return

    }


    try {

        const response =
            await api.get(

                "/registrar/curriculum",

                {

                    params: {

                        course_id:
                            selectedCourse.value

                    }

                }

            )


        curricula.value =
            Array.isArray(response.data)
                ? response.data
                : response.data.data || []

    }

    catch (error) {

        console.error(
            "Failed to load curricula:",
            error
        )

        curricula.value = []

        showAlert(
            "Failed to load curricula.",
            "error"
        )

    }

}


/*
|--------------------------------------------------------------------------
| LOAD SUBJECTS
|--------------------------------------------------------------------------
*/

async function loadSubjects() {

    if (!selectedCurriculum.value) {

        selectedCurriculumData.value = null

        subjects.value = []

        return

    }


    loadingSubjects.value = true


    try {

        const curriculumResponse =
            await api.get(
                `/registrar/curriculum/${selectedCurriculum.value}`
            )


        selectedCurriculumData.value =
            curriculumResponse.data.curriculum ||
            curriculumResponse.data


        const subjectsResponse =
            await api.get(
                `/registrar/curriculum/${selectedCurriculum.value}/subjects`
            )


        subjects.value =
            Array.isArray(subjectsResponse.data)
                ? subjectsResponse.data
                : subjectsResponse.data.data || []

    }

    catch (error) {

        console.error(
            "Failed to load subjects:",
            error
        )

        console.error(
            "Response:",
            error.response?.data
        )

        subjects.value = []

        showAlert(
            "Failed to load curriculum subjects.",
            "error"
        )

    }

    finally {

        loadingSubjects.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LOAD ALL SUBJECTS
|--------------------------------------------------------------------------
*/

async function loadAllSubjects() {

    try {

        const response =
            await api.get(
                "/registrar/curriculum/subjects/all"
            )


        allSubjects.value =
            Array.isArray(response.data)
                ? response.data
                : response.data.data || []

    }

    catch (error) {

        console.error(
            "Failed to load all subjects:",
            error
        )

        allSubjects.value = []

        showAlert(
            "Failed to load subjects.",
            "error"
        )

    }

}


/*
|--------------------------------------------------------------------------
| CREATE CURRICULUM
|--------------------------------------------------------------------------
*/

async function createCurriculum() {

    if (
        !curriculumForm.value.course_id ||
        !curriculumForm.value.name ||
        !curriculumForm.value.effective_year
    ) {

        showAlert(
            "Please complete the curriculum information.",
            "error"
        )

        return

    }


    saving.value = true


    try {

        await api.post(

            "/registrar/curriculum",

            curriculumForm.value

        )


        const courseId =
            curriculumForm.value.course_id


        closeCurriculumModal()


        showAlert(
            "Curriculum created successfully.",
            "success"
        )


        if (
            String(courseId) ===
            String(selectedCourse.value)
        ) {

            await loadCurricula()

        }

    }

    catch (error) {

        console.error(
            "Create curriculum error:",
            error
        )

        showAlert(

            error.response?.data?.message ||
            "Failed to create curriculum.",

            "error"

        )

    }

    finally {

        saving.value = false

    }

}


/*
|--------------------------------------------------------------------------
| OPEN SUBJECT MODAL
|--------------------------------------------------------------------------
*/

function openSubjectModal() {

    editingSubject.value = null


    subjectForm.value = {

        subject_id: "",

        year_level: "",

        semester: "",

        units: ""

    }


    showSubjectModal.value = true

}


/*
|--------------------------------------------------------------------------
| EDIT SUBJECT
|--------------------------------------------------------------------------
*/

function editSubject(item) {

    editingSubject.value = item


    subjectForm.value = {

        subject_id:
            item.subject_id,

        year_level:
            String(item.year_level),

        semester:
            String(item.semester),

        units:
            item.subject?.units || ""

    }


    showSubjectModal.value = true

}


/*
|--------------------------------------------------------------------------
| SAVE SUBJECT
|--------------------------------------------------------------------------
*/

async function saveSubject() {

    if (!selectedCurriculum.value) {

        showAlert(
            "Please select a curriculum first.",
            "error"
        )

        return

    }


    if (
        !subjectForm.value.subject_id &&
        !editingSubject.value
    ) {

        showAlert(
            "Please select a subject.",
            "error"
        )

        return

    }


    if (!subjectForm.value.year_level) {

        showAlert(
            "Please select a year level.",
            "error"
        )

        return

    }


    if (!subjectForm.value.semester) {

        showAlert(
            "Please select a semester.",
            "error"
        )

        return

    }


    saving.value = true


    try {

        if (!editingSubject.value) {

            await api.post(

                `/registrar/curriculum/${selectedCurriculum.value}/subjects`,

                {

                    subject_id:
                        subjectForm.value.subject_id,

                    year_level:
                        subjectForm.value.year_level,

                    semester:
                        subjectForm.value.semester

                }

            )

        }

        else {

            await api.put(

                `/registrar/curriculum-subject/${editingSubject.value.id}`,

                {

                    year_level:
                        subjectForm.value.year_level,

                    semester:
                        subjectForm.value.semester

                }

            )

        }


        const message =
            editingSubject.value
                ? "Subject updated successfully."
                : "Subject added successfully."


        closeSubjectModal()


        showAlert(
            message,
            "success"
        )


        await loadSubjects()

    }

    catch (error) {

        console.error(
            "Save subject error:",
            error
        )

        showAlert(

            error.response?.data?.message ||
            "Failed to save subject.",

            "error"

        )

    }

    finally {

        saving.value = false

    }

}


/*
|--------------------------------------------------------------------------
| DELETE SUBJECT
|--------------------------------------------------------------------------
*/

async function deleteSubject(id) {

    if (
        !confirm(
            "Are you sure you want to remove this subject from the curriculum?"
        )
    ) {

        return

    }


    try {

        await api.delete(

            `/registrar/curriculum-subject/${id}`

        )


        showAlert(
            "Subject removed successfully.",
            "success"
        )


        await loadSubjects()

    }

    catch (error) {

        console.error(
            "Delete subject error:",
            error
        )

        showAlert(

            error.response?.data?.message ||
            "Failed to remove subject.",

            "error"

        )

    }

}


/*
|--------------------------------------------------------------------------
| CLOSE CURRICULUM MODAL
|--------------------------------------------------------------------------
*/

function closeCurriculumModal() {

    showCurriculumModal.value = false


    curriculumForm.value = {

        course_id:
            selectedCourse.value || "",

        name: "",

        effective_year: ""

    }

}


/*
|--------------------------------------------------------------------------
| CLOSE SUBJECT MODAL
|--------------------------------------------------------------------------
*/

function closeSubjectModal() {

    showSubjectModal.value = false

    editingSubject.value = null


    subjectForm.value = {

        subject_id: "",

        year_level: "",

        semester: "",

        units: ""

    }

}


/*
|--------------------------------------------------------------------------
| FORMAT YEAR
|--------------------------------------------------------------------------
*/

function formatYear(year) {

    const years = {

        1: "1st Year",

        2: "2nd Year",

        3: "3rd Year",

        4: "4th Year"

    }


    return years[year] || year || "-"

}


/*
|--------------------------------------------------------------------------
| FORMAT SEMESTER
|--------------------------------------------------------------------------
*/

function formatSemester(semester) {

    if (
        String(semester) === "1"
    ) {

        return "1st Semester"

    }


    if (
        String(semester) === "2"
    ) {

        return "2nd Semester"

    }


    if (
        String(semester).toLowerCase() ===
        "summer"
    ) {

        return "Summer"

    }


    return semester || "-"

}


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await Promise.all([

        loadSchoolYears(),

        loadSemesters()

    ])


    await loadActivePeriod()


    await loadCourses()

    await loadAllSubjects()

})

</script>


<style scoped>

/*
|--------------------------------------------------------------------------
| PAGE
|--------------------------------------------------------------------------
*/

.page {

    padding: 10px;

}


/*
|--------------------------------------------------------------------------
| PAGE ALERT
|--------------------------------------------------------------------------
*/

.page-alert {

    position: fixed;

    top: 25px;

    right: 25px;

    min-width: 320px;

    max-width: 450px;

    padding: 15px 18px;

    border-radius: 12px;

    display: flex;

    align-items: center;

    gap: 12px;

    z-index: 20000;

    box-shadow:
        0 10px 30px rgba(0,0,0,.15);

}

.alert-success {

    background: #ECFDF5;

    border: 1px solid #A7F3D0;

    color: #065F46;

}

.alert-error {

    background: #FEF2F2;

    border: 1px solid #FECACA;

    color: #991B1B;

}

.alert-icon {

    font-size: 22px;

}

.alert-content {

    display: flex;

    flex-direction: column;

    flex: 1;

    gap: 2px;

}

.alert-content strong {

    font-size: 14px;

}

.alert-content span {

    font-size: 13px;

}

.alert-close {

    border: none;

    background: transparent;

    color: inherit;

    font-size: 20px;

    cursor: pointer;

}


/*
|--------------------------------------------------------------------------
| ALERT ANIMATION
|--------------------------------------------------------------------------
*/

.alert-enter-active,
.alert-leave-active {

    transition:
        all .3s ease;

}

.alert-enter-from,
.alert-leave-to {

    opacity: 0;

    transform:
        translateX(30px);

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

.page-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

}

.page-header h2 {

    color: #064E2A;

    font-weight: 800;

    margin-bottom: 5px;

}

.page-header p {

    color: #6b7280;

    margin: 0;

}


/*
|--------------------------------------------------------------------------
| CARD
|--------------------------------------------------------------------------
*/

.card {

    background: white;

    border: none;

    border-radius: 15px;

    margin-bottom: 20px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.06);

}

.card-body {

    padding: 25px;

}


/*
|--------------------------------------------------------------------------
| ACTIVE PERIOD
|--------------------------------------------------------------------------
*/

.active-period-card {

    border-left:
        5px solid #064E2A;

}

.active-period-header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 20px;

}

.active-period-badge {

    display: flex;

    align-items: center;

    gap: 7px;

    background: #DCFCE7;

    color: #166534;

    padding: 7px 13px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: 700;

}

.active-dot {

    width: 8px;

    height: 8px;

    background: #16A34A;

    border-radius: 50%;

}

.period-button-wrapper {

    display: flex;

    align-items: flex-end;

}

.btn-active-period {

    width: 100%;

    min-height: 45px;

}

.current-period {

    margin-top: 22px;

    padding: 15px 18px;

    background: #ECFDF5;

    border: 1px solid #BBF7D0;

    border-radius: 12px;

    display: flex;

    align-items: center;

    gap: 14px;

}

.current-period-icon {

    width: 42px;

    height: 42px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 10px;

    background: #064E2A;

    color: white;

    font-size: 19px;

}

.current-period-info {

    display: flex;

    flex-direction: column;

    gap: 3px;

}

.current-period-label {

    color: #6B7280;

    font-size: 12px;

    font-weight: 600;

}

.current-period-info strong {

    color: #064E2A;

    font-size: 16px;

}

.separator {

    margin: 0 5px;

    color: #6B7280;

}

.no-active-period {

    margin-top: 22px;

    padding: 15px 18px;

    background: #FFFBEB;

    border: 1px solid #FDE68A;

    border-radius: 12px;

    display: flex;

    align-items: center;

    gap: 12px;

    color: #92400E;

}

.no-active-period i {

    font-size: 20px;

}

.no-active-period div {

    display: flex;

    flex-direction: column;

}

.no-active-period span {

    font-size: 13px;

    color: #A16207;

}

.period-loading {

    text-align: center;

    margin-top: 20px;

    color: #6B7280;

}


/*
|--------------------------------------------------------------------------
| SECTION
|--------------------------------------------------------------------------
*/

.section-title {

    display: flex;

    align-items: center;

    gap: 10px;

    color: #064E2A;

    font-size: 18px;

    font-weight: 800;

    margin-bottom: 20px;

}

.section-title i {

    font-size: 21px;

}


/*
|--------------------------------------------------------------------------
| FORM
|--------------------------------------------------------------------------
*/

label {

    display: block;

    color: #6b7280;

    font-size: 13px;

    font-weight: 700;

    margin-bottom: 7px;

}

.form-select,
.form-control {

    border-radius: 10px;

    padding: 11px 13px;

    border: 1px solid #e5e7eb;

}

.form-select:focus,
.form-control:focus {

    border-color: #0B6B3A;

    box-shadow:
        0 0 0 3px
        rgba(11,107,58,.1);

}


/*
|--------------------------------------------------------------------------
| BUTTON
|--------------------------------------------------------------------------
*/

.btn-primary {

    background: #064E2A;

    color: white;

    border: none;

    border-radius: 10px;

    padding: 11px 18px;

    font-weight: 700;

}

.btn-primary:hover {

    background: #0B6B3A;

}

.btn-primary:disabled {

    opacity: .6;

    cursor: not-allowed;

}


/*
|--------------------------------------------------------------------------
| CURRICULUM
|--------------------------------------------------------------------------
*/

.curriculum-header,
.subjects-header {

    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 20px;

}

.description {

    color: #6b7280;

    margin: -10px 0 0;

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

.status {

    padding: 7px 13px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: 700;

}

.status.active {

    background: #DCFCE7;

    color: #166534;

}

.status.inactive {

    background: #FEE2E2;

    color: #991B1B;

}


/*
|--------------------------------------------------------------------------
| TABLE
|--------------------------------------------------------------------------
*/

.table {

    margin-bottom: 0;

}

.table th {

    background: #f8fafc;

    color: #374151;

    font-size: 13px;

    font-weight: 700;

    padding: 14px;

    border-bottom:
        1px solid #e5e7eb;

}

.table td {

    padding: 14px;

    vertical-align: middle;

    border-bottom:
        1px solid #f0f0f0;

}

.table tfoot td {

    background: #f8fafc;

}


/*
|--------------------------------------------------------------------------
| SUBJECT CODE
|--------------------------------------------------------------------------
*/

.subject-code {

    background: #ECFDF5;

    color: #065F46;

    padding: 6px 10px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| UNITS
|--------------------------------------------------------------------------
*/

.units {

    background: #f3f4f6;

    padding: 6px 10px;

    border-radius: 7px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| ACTION BUTTONS
|--------------------------------------------------------------------------
*/

.btn-edit,
.btn-delete {

    border: none;

    width: 34px;

    height: 34px;

    border-radius: 8px;

    margin-right: 5px;

}

.btn-edit {

    background: #ECFDF5;

    color: #065F46;

}

.btn-delete {

    background: #FEE2E2;

    color: #991B1B;

}


/*
|--------------------------------------------------------------------------
| EMPTY
|--------------------------------------------------------------------------
*/

.empty,
.empty-main {

    text-align: center;

    color: #6b7280;

}

.empty {

    padding: 50px 20px;

}

.empty-main {

    background: white;

    border-radius: 15px;

    padding: 70px 20px;

    box-shadow:
        0 5px 20px rgba(0,0,0,.05);

}

.empty i {

    font-size: 45px;

    color: #9ca3af;

}

.empty-main i {

    font-size: 55px;

    color: #0B6B3A;

}

.empty h5,
.empty-main h4 {

    color: #374151;

    margin-top: 15px;

}


/*
|--------------------------------------------------------------------------
| LOADING
|--------------------------------------------------------------------------
*/

.loading {

    text-align: center;

    padding: 40px;

    color: #6b7280;

}

.spinner {

    width: 20px;

    height: 20px;

    border: 3px solid #d1d5db;

    border-top-color: #0B6B3A;

    border-radius: 50%;

    animation:
        spin .8s linear infinite;

    display: inline-block;

    margin-right: 8px;

}

@keyframes spin {

    to {

        transform:
            rotate(360deg);

    }

}


/*
|--------------------------------------------------------------------------
| MODAL
|--------------------------------------------------------------------------
*/

.modal-overlay {

    position: fixed;

    inset: 0;

    background: rgba(0,0,0,.45);

    display: flex;

    justify-content: center;

    align-items: center;

    z-index: 9999;

    padding: 20px;

}

.modal-box {

    background: white;

    width: 100%;

    max-width: 500px;

    border-radius: 16px;

    box-shadow:
        0 20px 50px rgba(0,0,0,.2);

    overflow: hidden;

}

.modal-header {

    padding: 18px 22px;

    border-bottom: 1px solid #eee;

    display: flex;

    justify-content: space-between;

    align-items: center;

}

.modal-header h5 {

    margin: 0;

    font-weight: 800;

    color: #064E2A;

}

.modal-body {

    padding: 22px;

}

.modal-footer {

    padding: 18px 22px;

    border-top: 1px solid #eee;

    display: flex;

    justify-content: flex-end;

    gap: 10px;

}

.close {

    border: none;

    background: transparent;

    font-size: 28px;

    color: #6b7280;

}

.btn-secondary {

    background: #f3f4f6;

    color: #374151;

    border: none;

    border-radius: 10px;

    padding: 11px 18px;

    font-weight: 700;

}


/*
|--------------------------------------------------------------------------
| MOBILE
|--------------------------------------------------------------------------
*/

@media (max-width: 768px) {

    .page-header,
    .active-period-header,
    .curriculum-header,
    .subjects-header {

        flex-direction: column;

    }


    .page-header .btn-primary,
    .subjects-header .btn-primary {

        width: 100%;

    }


    .period-button-wrapper {

        width: 100%;

    }


    .current-period {

        align-items: flex-start;

    }


    .table {

        min-width: 800px;

    }


    .page-alert {

        top: 15px;

        left: 15px;

        right: 15px;

        min-width: auto;

        max-width: none;

    }

}

</style>