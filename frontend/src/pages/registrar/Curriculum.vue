<template>

<div class="page">

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

                            <th>
                                #
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Subject
                            </th>

                            <th>
                                Year Level
                            </th>

                            <th>
                                Semester
                            </th>

                            <th>
                                Units
                            </th>

                            <th>
                                Action
                            </th>

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


                            <!-- CODE -->

                            <td>

                                <span class="subject-code">

                                    {{
                                        item.subject?.code || "-"
                                    }}

                                </span>

                            </td>


                            <!-- SUBJECT TITLE -->

                            <td>

                                <strong>

                                    {{
                                        item.subject?.title || "-"
                                    }}

                                </strong>

                            </td>


                            <!-- YEAR -->

                            <td>

                                {{ formatYear(item.year_level) }}

                            </td>


                            <!-- SEMESTER -->

                            <td>

                                {{ formatSemester(item.semester) }}

                            </td>


                            <!-- UNITS -->

                            <td>

                                <span class="units">

                                    {{
                                        item.subject?.units || 0
                                    }}

                                </span>

                            </td>


                            <!-- ACTION -->

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


                    <!-- TOTAL -->

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

                <!-- SUBJECT -->

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


                <!-- YEAR -->

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


                <!-- SEMESTER -->

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


                <!-- UNITS -->

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
| DATA
|--------------------------------------------------------------------------
*/

const courses = ref([])

const curricula = ref([])

const subjects = ref([])

const allSubjects = ref([])


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
| SELECTED SUBJECT UNITS
|--------------------------------------------------------------------------
*/

const selectedSubjectUnits = computed(() => {

    const selected = allSubjects.value.find(
        subject =>
            String(subject.id) ===
            String(subjectForm.value.subject_id)
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
                Number(item.subject?.units || 0)

        },

        0

    )

})


/*
|--------------------------------------------------------------------------
| LOAD COURSES
|--------------------------------------------------------------------------
*/

async function loadCourses() {

    try {

        const response = await api.get(
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

        const response = await api.get(
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

        /*
         * Load curriculum information
         */

        const curriculumResponse =
            await api.get(
                `/registrar/curriculum/${selectedCurriculum.value}`
            )


        selectedCurriculumData.value =
            curriculumResponse.data.curriculum ||
            curriculumResponse.data


        /*
         * Load assigned subjects
         */

        const subjectsResponse =
            await api.get(
                `/registrar/curriculum/${selectedCurriculum.value}/subjects`
            )


        subjects.value =
            Array.isArray(subjectsResponse.data)
                ? subjectsResponse.data
                : subjectsResponse.data.data || []


        console.log(
            "Assigned curriculum subjects:",
            subjects.value
        )

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


        console.log(
            "All subjects:",
            allSubjects.value
        )

    }

    catch (error) {

        console.error(
            "Failed to load all subjects:",
            error
        )

        allSubjects.value = []

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

        alert(
            "Please complete the curriculum information."
        )

        return

    }


    saving.value = true


    try {

        await api.post(
            "/registrar/curriculum",
            curriculumForm.value
        )


        alert(
            "Curriculum created successfully."
        )


        const courseId =
            curriculumForm.value.course_id


        closeCurriculumModal()


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

        console.error(
            "Response:",
            error.response?.data
        )

        alert(
            error.response?.data?.message ||
            "Failed to create curriculum."
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

        alert(
            "Please select a curriculum first."
        )

        return

    }


    if (
        !subjectForm.value.subject_id &&
        !editingSubject.value
    ) {

        alert(
            "Please select a subject."
        )

        return

    }


    if (!subjectForm.value.year_level) {

        alert(
            "Please select a year level."
        )

        return

    }


    if (!subjectForm.value.semester) {

        alert(
            "Please select a semester."
        )

        return

    }


    saving.value = true


    try {

        /*
         * ADD SUBJECT
         */

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


        /*
         * EDIT SUBJECT
         *
         * This requires a PUT route in Laravel.
         */

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


        alert(

            editingSubject.value

                ? "Subject updated successfully."

                : "Subject added successfully."

        )


        closeSubjectModal()


        await loadSubjects()

    }

    catch (error) {

        console.error(
            "Save subject error:",
            error
        )

        console.error(
            "Response:",
            error.response?.data
        )

        alert(
            error.response?.data?.message ||
            "Failed to save subject."
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


        alert(
            "Subject removed successfully."
        )


        await loadSubjects()

    }

    catch (error) {

        console.error(
            "Delete subject error:",
            error
        )

        console.error(
            "Response:",
            error.response?.data
        )

        alert(
            error.response?.data?.message ||
            "Failed to remove subject."
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

    await loadCourses()

    await loadAllSubjects()

})

</script>


<style scoped>

.page {

    padding: 10px;

}


/* HEADER */

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


/* CARD */

.card {

    background: white;

    border: none;

    border-radius: 15px;

    margin-bottom: 20px;

    box-shadow: 0 5px 20px rgba(0,0,0,.06);

}

.card-body {

    padding: 25px;

}


/* SECTION */

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


/* FORM */

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


/* BUTTON */

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


/* CURRICULUM HEADER */

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


/* STATUS */

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


/* TABLE */

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


/* SUBJECT CODE */

.subject-code {

    background: #ECFDF5;

    color: #065F46;

    padding: 6px 10px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: 700;

}


/* UNITS */

.units {

    background: #f3f4f6;

    padding: 6px 10px;

    border-radius: 7px;

    font-weight: 700;

}


/* ACTION BUTTONS */

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


/* EMPTY */

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
        0 5px 20px
        rgba(0,0,0,.05);

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


/* LOADING */

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


/* MODAL */

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
        0 20px 50px
        rgba(0,0,0,.2);

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


/* MOBILE */

@media (max-width: 768px) {

    .page-header,
    .curriculum-header,
    .subjects-header {

        flex-direction: column;

    }

    .page-header .btn-primary,
    .subjects-header .btn-primary {

        width: 100%;

    }

    .table {

        min-width: 800px;

    }

}

</style>