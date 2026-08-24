<template>

<div class="page">

    <!-- HEADER -->
    <div class="header">

        <div>
            <h3>Students</h3>

            <p>
                List of officially enrolled students
            </p>
        </div>

        <button class="btn-primary">
            <i class="bi bi-person-plus"></i>
            Add Student
        </button>

    </div>


    <!-- CARD -->
    <div class="card">

        <!-- TOOLBAR -->
        <div class="toolbar">

            <!-- SEARCH -->
            <div class="search-box">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    placeholder="Search student..."
                    v-model="search"
                >

            </div>


            <!-- COURSE -->
            <select
                v-model="selectedCourse"
                @change="loadStudents(1)"
            >

                <option value="">
                    All Courses
                </option>

                <option
                    v-for="course in courses"
                    :key="course.id"
                    :value="course.name"
                >
                    {{ course.name }}
                </option>

            </select>


            <!-- PER PAGE -->
            <select
                v-model="perPage"
                @change="loadStudents(1)"
            >

                <option :value="10">
                    10 per page
                </option>

                <option :value="25">
                    25 per page
                </option>

                <option :value="50">
                    50 per page
                </option>

            </select>

        </div>


        <!-- LOADING -->
        <div
            v-if="loading"
            class="loading"
        >

            <div class="spinner"></div>

            <span>
                Loading students...
            </span>

        </div>


        <!-- EMPTY -->
        <div
            v-else-if="students.length === 0"
            class="empty"
        >

            <i class="bi bi-people"></i>

            <h5>
                No students found
            </h5>

            <p>
                No officially enrolled students match your search.
            </p>

        </div>


        <!-- TABLE -->
        <div
            v-else
            class="table-container"
        >

            <table>

                <thead>

                    <tr>

                        <th>
                            Student ID
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Course
                        </th>

                        <th>
                            Year
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="enrollment in students"
                        :key="enrollment.id"
                    >

                        <!-- STUDENT ID -->
                        <td>

                            {{ enrollment.student?.id || "-" }}

                        </td>


                        <!-- NAME -->
                        <td>

                            <div class="student-name">

                                <div class="avatar">

                                    {{ getInitials(enrollment.student) }}

                                </div>

                                <strong>
                                    {{ getStudentName(enrollment.student) }}
                                </strong>

                            </div>

                        </td>


                        <!-- COURSE -->
                        <td>

                            {{ enrollment.course?.name || "-" }}

                        </td>


                        <!-- YEAR -->
                        <td>

                            {{ formatYear(enrollment.year_level) }}

                        </td>


                        <!-- STATUS -->
                        <td>

                            <span class="status">

                                <i class="bi bi-check-circle-fill"></i>

                                Enrolled

                            </span>

                        </td>


                        <!-- ACTION -->
                        <td>

                            <button
                                class="view"
                                @click="viewStudent(enrollment.id)"
                            >

                                <i class="bi bi-eye"></i>

                                View

                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- FOOTER -->
        <div
            v-if="!loading && pagination.total > 0"
            class="table-footer"
        >

            <div class="results">

                Showing

                <strong>
                    {{ pagination.from }}
                </strong>

                to

                <strong>
                    {{ pagination.to }}
                </strong>

                of

                <strong>
                    {{ pagination.total }}
                </strong>

                students

            </div>


            <!-- PAGINATION -->
            <div class="pagination">

                <button
                    :disabled="pagination.current_page === 1"
                    @click="changePage(pagination.current_page - 1)"
                >

                    <i class="bi bi-chevron-left"></i>

                </button>


                <button
                    v-for="page in visiblePages"
                    :key="page"
                    :class="{
                        active:
                            page === pagination.current_page
                    }"
                    @click="changePage(page)"
                >

                    {{ page }}

                </button>


                <button
                    :disabled="
                        pagination.current_page ===
                        pagination.last_page
                    "
                    @click="changePage(
                        pagination.current_page + 1
                    )"
                >

                    <i class="bi bi-chevron-right"></i>

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
    onMounted,
    watch
} from "vue"

import api from "@/services/api"


/*
|--------------------------------------------------------------------------
| DATA
|--------------------------------------------------------------------------
*/

const students = ref([])

const courses = ref([])

const search = ref("")

const selectedCourse = ref("")

const perPage = ref(10)

const loading = ref(false)


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

const pagination = ref({

    current_page: 1,

    last_page: 1,

    total: 0,

    from: 0,

    to: 0

})


/*
|--------------------------------------------------------------------------
| LOAD STUDENTS
|--------------------------------------------------------------------------
*/

async function loadStudents(page = 1) {

    loading.value = true

    try {

        const params = {

            page: page,

            per_page: perPage.value

        }


        /*
        |--------------------------------------------------------------------------
        | SEARCH
        |--------------------------------------------------------------------------
        */

        if (search.value.trim()) {

            params.search =
                search.value.trim()

        }


        /*
        |--------------------------------------------------------------------------
        | COURSE
        |--------------------------------------------------------------------------
        */

        if (selectedCourse.value) {

            params.course =
                selectedCourse.value

        }


        const response = await api.get(
            "/registrar/enrollments",
            {
                params
            }
        )


        console.log(
            "Students:",
            response.data
        )


        /*
        |--------------------------------------------------------------------------
        | STUDENTS
        |--------------------------------------------------------------------------
        */

        students.value =
            response.data.data || []


        /*
        |--------------------------------------------------------------------------
        | PAGINATION
        |--------------------------------------------------------------------------
        */

        pagination.value = {

            current_page:
                response.data.current_page || 1,

            last_page:
                response.data.last_page || 1,

            total:
                response.data.total || 0,

            from:
                response.data.from || 0,

            to:
                response.data.to || 0

        }

    }

    catch (error) {

        console.error(
            "Failed to load students:",
            error
        )

        students.value = []

    }

    finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| LOAD COURSES
|--------------------------------------------------------------------------
|
| We load courses separately so the dropdown contains
| all courses, not just courses from the current page.
|
*/

async function loadCourses() {

    try {

        const response =
            await api.get("/enrollment/options")


        courses.value =
            response.data.courses || []

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
| SEARCH DEBOUNCE
|--------------------------------------------------------------------------
*/

let searchTimer = null

watch(search, () => {

    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {

        loadStudents(1)

    }, 400)

})


/*
|--------------------------------------------------------------------------
| VISIBLE PAGINATION
|--------------------------------------------------------------------------
*/

const visiblePages = computed(() => {

    const current =
        pagination.value.current_page

    const last =
        pagination.value.last_page


    const pages = []


    let start =
        Math.max(1, current - 2)

    let end =
        Math.min(last, current + 2)


    for (
        let i = start;
        i <= end;
        i++
    ) {

        pages.push(i)

    }


    return pages

})


/*
|--------------------------------------------------------------------------
| CHANGE PAGE
|--------------------------------------------------------------------------
*/

function changePage(page) {

    if (page < 1) {
        return
    }

    if (
        page >
        pagination.value.last_page
    ) {
        return
    }


    loadStudents(page)

}


/*
|--------------------------------------------------------------------------
| STUDENT NAME
|--------------------------------------------------------------------------
*/

function getStudentName(student) {

    if (!student) {

        return "-"

    }


    return [

        student.first_name,

        student.middle_name,

        student.last_name

    ]

    .filter(Boolean)

    .join(" ")

}


/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

function getInitials(student) {

    if (!student) {

        return "?"

    }


    const first =
        student.first_name?.charAt(0) || ""

    const last =
        student.last_name?.charAt(0) || ""


    return (
        first + last
    ).toUpperCase()

}


/*
|--------------------------------------------------------------------------
| YEAR LEVEL
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
| VIEW
|--------------------------------------------------------------------------
*/

function viewStudent(id) {

    console.log(
        "View enrollment:",
        id
    )

}


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadStudents()

    loadCourses()

})

</script>


<style scoped>

.page {

    padding: 10px;

}


/* HEADER */

.header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 25px;

}

.header h3 {

    color: #064E2A;

    font-weight: 800;

    margin-bottom: 5px;

}

.header p {

    color: #6b7280;

    margin: 0;

}


/* PRIMARY BUTTON */

.btn-primary {

    background: #064E2A;

    color: white;

    border: none;

    padding: 12px 20px;

    border-radius: 10px;

    font-weight: 600;

}


/* CARD */

.card {

    background: white;

    padding: 25px;

    border-radius: 15px;

    box-shadow: 0 5px 20px #ddd;

}


/* TOOLBAR */

.toolbar {

    display: flex;

    gap: 12px;

    margin-bottom: 20px;

    align-items: center;

}


/* SEARCH */

.search-box {

    flex: 1;

    position: relative;

}

.search-box i {

    position: absolute;

    left: 14px;

    top: 50%;

    transform: translateY(-50%);

    color: #9ca3af;

}

.search-box input {

    width: 100%;

    padding-left: 40px;

}


/* INPUT */

input,
select {

    padding: 12px;

    border: 1px solid #ddd;

    border-radius: 10px;

    outline: none;

    background: white;

}

input:focus,
select:focus {

    border-color: #0B6B3A;

}


/* TABLE */

.table-container {

    width: 100%;

    overflow-x: auto;

}

table {

    width: 100%;

    border-collapse: collapse;

}

th {

    background: #f8fafc;

    color: #374151;

    font-size: 14px;

    font-weight: 700;

}

th,
td {

    padding: 15px;

    border-bottom: 1px solid #eee;

    text-align: left;

}


/* STUDENT */

.student-name {

    display: flex;

    align-items: center;

    gap: 12px;

}

.avatar {

    width: 38px;

    height: 38px;

    border-radius: 50%;

    background: #DCFCE7;

    color: #166534;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: 700;

}


/* STATUS */

.status {

    background: #dcfce7;

    color: #166534;

    padding: 6px 12px;

    border-radius: 20px;

    font-size: 13px;

    font-weight: 600;

}

.status i {

    margin-right: 4px;

}


/* VIEW */

.view {

    border: none;

    background: #0B6B3A;

    color: white;

    padding: 8px 15px;

    border-radius: 8px;

}


/* LOADING */

.loading {

    display: flex;

    justify-content: center;

    align-items: center;

    gap: 10px;

    padding: 50px;

    color: #6b7280;

}

.spinner {

    width: 22px;

    height: 22px;

    border: 3px solid #d1d5db;

    border-top-color: #0B6B3A;

    border-radius: 50%;

    animation: spin 0.8s linear infinite;

}

@keyframes spin {

    to {

        transform: rotate(360deg);

    }

}


/* EMPTY */

.empty {

    text-align: center;

    padding: 60px 20px;

    color: #6b7280;

}

.empty i {

    font-size: 50px;

    color: #9ca3af;

}

.empty h5 {

    margin-top: 15px;

    color: #374151;

}


/* FOOTER */

.table-footer {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-top: 25px;

    gap: 20px;

}

.results {

    color: #6b7280;

    font-size: 14px;

}


/* PAGINATION */

.pagination {

    display: flex;

    align-items: center;

    gap: 5px;

}

.pagination button {

    border: 1px solid #ddd;

    background: white;

    color: #374151;

    min-width: 38px;

    height: 38px;

    border-radius: 8px;

}

.pagination button:hover:not(:disabled) {

    background: #064E2A;

    color: white;

    border-color: #064E2A;

}

.pagination button.active {

    background: #064E2A;

    color: white;

    border-color: #064E2A;

}

.pagination button:disabled {

    background: #f3f4f6;

    color: #9ca3af;

    cursor: not-allowed;

}


/* MOBILE */

@media (max-width: 768px) {

    .header {

        flex-direction: column;

        align-items: flex-start;

        gap: 15px;

    }

    .toolbar {

        flex-direction: column;

        align-items: stretch;

    }

    .table-footer {

        flex-direction: column;

        align-items: flex-start;

    }

    .btn-primary {

        width: 100%;

    }

}

</style>