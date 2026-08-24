<template>

<div class="reports-page">


    <!-- HEADER -->

    <div class="page-header mb-4">

        <div>

            <h2 class="fw-bold mb-1">

                Registrar Reports

            </h2>

            <p class="text-muted mb-0">

                View enrollment, course, and payment reports.

            </p>

        </div>


        <button
            class="btn btn-outline-success"
            @click="loadReports"
            :disabled="loading"
        >

            <i class="bi bi-arrow-clockwise me-2"></i>

            {{ loading ? 'Loading...' : 'Refresh' }}

        </button>

    </div>



    <!-- LOADING -->

    <div
        v-if="loading"
        class="text-center py-5"
    >

        <div
            class="spinner-border text-success"
        ></div>

        <p class="text-muted mt-3">

            Loading reports...

        </p>

    </div>



    <div v-else>


        <!-- SUMMARY CARDS -->

        <div class="row g-4 mb-4">


            <!-- TOTAL -->

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-people"></i>

                    </div>

                    <div>

                        <small>
                            Total Applications
                        </small>

                        <h3>
                            {{ statistics.total }}
                        </h3>

                    </div>

                </div>

            </div>



            <!-- PENDING -->

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-hourglass-split"></i>

                    </div>

                    <div>

                        <small>
                            Pending
                        </small>

                        <h3>
                            {{ statistics.pending }}
                        </h3>

                    </div>

                </div>

            </div>



            <!-- ENROLLED -->

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-person-check"></i>

                    </div>

                    <div>

                        <small>
                            Enrolled
                        </small>

                        <h3>
                            {{ statistics.enrolled }}
                        </h3>

                    </div>

                </div>

            </div>



            <!-- PAID -->

            <div class="col-md-6 col-xl-3">

                <div class="stat-card">

                    <div class="stat-icon">

                        <i class="bi bi-wallet2"></i>

                    </div>

                    <div>

                        <small>
                            Paid
                        </small>

                        <h3>
                            {{ payments.paid }}
                        </h3>

                    </div>

                </div>

            </div>


        </div>




        <!-- REPORT CARDS -->

        <div class="row g-4">


            <!-- ENROLLMENT REPORT -->

            <div class="col-md-4">

                <div class="report-card">

                    <div class="report-icon">

                        <i class="bi bi-file-earmark-bar-graph"></i>

                    </div>

                    <h5>

                        Enrollment Report

                    </h5>

                    <p>

                        View all enrollment applications
                        and their current status.

                    </p>

                    <div class="report-number">

                        {{ statistics.total }}

                        <span>
                            Applications
                        </span>

                    </div>

                    <button
                        class="btn btn-success w-100"
                        @click="generateEnrollmentReport"
                    >

                        <i class="bi bi-printer me-2"></i>

                        Generate Report

                    </button>

                </div>

            </div>




            <!-- COURSE REPORT -->

            <div class="col-md-4">

                <div class="report-card">

                    <div class="report-icon">

                        <i class="bi bi-mortarboard"></i>

                    </div>

                    <h5>

                        Course Report

                    </h5>

                    <p>

                        View enrollment numbers
                        for every course.

                    </p>

                    <div class="report-number">

                        {{ courses.length }}

                        <span>
                            Courses
                        </span>

                    </div>

                    <button
                        class="btn btn-success w-100"
                        @click="generateCourseReport"
                    >

                        <i class="bi bi-printer me-2"></i>

                        Generate Report

                    </button>

                </div>

            </div>




            <!-- ASSESSMENT REPORT -->

            <div class="col-md-4">

                <div class="report-card">

                    <div class="report-icon">

                        <i class="bi bi-credit-card"></i>

                    </div>

                    <h5>

                        Assessment Report

                    </h5>

                    <p>

                        View enrollment payment
                        and assessment status.

                    </p>

                    <div class="report-number">

                        {{ payments.paid }}

                        <span>
                            Paid
                        </span>

                    </div>

                    <button
                        class="btn btn-success w-100"
                        @click="generateAssessmentReport"
                    >

                        <i class="bi bi-printer me-2"></i>

                        Generate Report

                    </button>

                </div>

            </div>


        </div>




        <!-- COURSE SUMMARY -->

        <div class="card report-table-card mt-5">

            <div class="card-header">

                <div>

                    <h5 class="mb-1">

                        Course Enrollment Summary

                    </h5>

                    <small class="text-muted">

                        Number of enrollment applications per course

                    </small>

                </div>

            </div>


            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Course
                            </th>

                            <th>
                                Department
                            </th>

                            <th class="text-center">
                                Applications
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="(course, index) in courses"
                            :key="course.id"
                        >

                            <td>
                                {{ index + 1 }}
                            </td>

                            <td>

                                <strong>
                                    {{ course.code }}
                                </strong>

                                <br>

                                <small class="text-muted">
                                    {{ course.name }}
                                </small>

                            </td>

                            <td>
                                {{ course.department }}
                            </td>

                            <td class="text-center">

                                <span class="badge bg-success">

                                    {{ course.enrollments_count }}

                                </span>

                            </td>

                        </tr>


                        <tr v-if="courses.length === 0">

                            <td
                                colspan="4"
                                class="text-center text-muted py-4"
                            >

                                No course data available.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>




        <!-- RECENT ENROLLMENTS -->

        <div class="card report-table-card mt-4">

            <div class="card-header">

                <div>

                    <h5 class="mb-1">

                        Recent Enrollment Records

                    </h5>

                    <small class="text-muted">

                        Latest enrollment applications

                    </small>

                </div>

            </div>


            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead>

                        <tr>

                            <th>
                                Date
                            </th>

                            <th>
                                Student
                            </th>

                            <th>
                                Course
                            </th>

                            <th>
                                Year Level
                            </th>

                            <th>
                                Status
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="enrollment in recent"
                            :key="enrollment.id"
                        >

                            <td>

                                {{ formatDate(
                                    enrollment.created_at
                                ) }}

                            </td>


                            <td>

                                <strong>

                                    {{
                                        enrollment.student?.first_name
                                    }}

                                    {{
                                        enrollment.student?.last_name
                                    }}

                                </strong>

                            </td>


                            <td>

                                {{
                                    enrollment.course?.code
                                    || enrollment.course?.name
                                    || 'N/A'
                                }}

                            </td>


                            <td>

                                {{ enrollment.year_level }}

                            </td>


                            <td>

                                <span
                                    class="badge"
                                    :class="
                                        statusClass(
                                            enrollment.status
                                        )
                                    "
                                >

                                    {{ enrollment.status }}

                                </span>

                            </td>

                        </tr>


                        <tr v-if="recent.length === 0">

                            <td
                                colspan="5"
                                class="text-center text-muted py-4"
                            >

                                No enrollment records found.

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>




        <!-- PAYMENT SUMMARY -->

        <div class="card report-table-card mt-4 mb-5">

            <div class="card-header">

                <h5 class="mb-0">

                    Payment Summary

                </h5>

            </div>


            <div class="card-body">

                <div class="row text-center">


                    <div class="col-md-4">

                        <div class="payment-stat">

                            <i class="bi bi-check-circle"></i>

                            <h3>
                                {{ payments.paid }}
                            </h3>

                            <span>
                                Paid
                            </span>

                        </div>

                    </div>



                    <div class="col-md-4">

                        <div class="payment-stat">

                            <i class="bi bi-clock"></i>

                            <h3>
                                {{ payments.unpaid }}
                            </h3>

                            <span>
                                Pending
                            </span>

                        </div>

                    </div>



                    <div class="col-md-4">

                        <div class="payment-stat">

                            <i class="bi bi-x-circle"></i>

                            <h3>
                                {{ payments.failed }}
                            </h3>

                            <span>
                                Failed
                            </span>

                        </div>

                    </div>


                </div>

            </div>

        </div>


    </div>

</div>

</template>


<script setup>

import {
    ref,
    onMounted
} from "vue"

import api from "@/services/api"



/*
|--------------------------------------------------------------------------
| Data
|--------------------------------------------------------------------------
*/

const loading = ref(false)


const statistics = ref({

    total: 0,

    pending: 0,

    approved: 0,

    enrolled: 0,

    rejected: 0

})


const payments = ref({

    paid: 0,

    unpaid: 0,

    failed: 0

})


const courses = ref([])

const recent = ref([])



/*
|--------------------------------------------------------------------------
| Load Reports
|--------------------------------------------------------------------------
*/

async function loadReports()
{

    loading.value = true


    try {

        const response = await api.get(
            "/registrar/reports"
        )


        const data = response.data


        statistics.value =
            data.statistics || statistics.value


        payments.value =
            data.payments || payments.value


        courses.value =
            data.courses || []


        recent.value =
            data.recent || []


    }
    catch(error)
    {

        console.error(
            "Failed to load reports:",
            error
        )

    }
    finally
    {

        loading.value = false

    }

}



/*
|--------------------------------------------------------------------------
| Date Formatting
|--------------------------------------------------------------------------
*/

function formatDate(date)
{

    if(!date)
        return "N/A"


    return new Date(date)
        .toLocaleDateString(
            "en-US",
            {
                year: "numeric",
                month: "long",
                day: "numeric"
            }
        )

}



/*
|--------------------------------------------------------------------------
| Status Badge
|--------------------------------------------------------------------------
*/

function statusClass(status)
{

    switch(status)
    {

        case "Enrolled":
            return "bg-success"

        case "Approved":
            return "bg-primary"

        case "Pending":
            return "bg-warning text-dark"

        case "Rejected":
            return "bg-danger"

        default:
            return "bg-secondary"

    }

}



/*
|--------------------------------------------------------------------------
| Generate Enrollment Report
|--------------------------------------------------------------------------
*/

async function generateEnrollmentReport()
{

    try {

        const response = await api.get(
            "/registrar/reports/enrollment"
        )


        const data = response.data


        printReport(

            data.report,

            `
                <p>
                    <strong>Total Applications:</strong>
                    ${data.total}
                </p>

                ${buildEnrollmentTable(data.data)}
            `

        )

    }
    catch(error)
    {

        console.error(
            "Enrollment report error:",
            error
        )

    }

}



/*
|--------------------------------------------------------------------------
| Generate Course Report
|--------------------------------------------------------------------------
*/

async function generateCourseReport()
{

    try {

        const response = await api.get(
            "/registrar/reports/course"
        )


        const data = response.data


        let rows = ""


        data.data.forEach(
            (course, index) => {

                rows += `

                    <tr>

                        <td>
                            ${index + 1}
                        </td>

                        <td>
                            ${course.code}
                        </td>

                        <td>
                            ${course.name}
                        </td>

                        <td>
                            ${course.department}
                        </td>

                        <td>
                            ${course.enrollments_count}
                        </td>

                    </tr>

                `

            }
        )


        printReport(

            data.report,

            `

                <table>

                    <thead>

                        <tr>

                            <th>#</th>

                            <th>Code</th>

                            <th>Course</th>

                            <th>Department</th>

                            <th>Applications</th>

                        </tr>

                    </thead>

                    <tbody>

                        ${rows}

                    </tbody>

                </table>

            `

        )

    }
    catch(error)
    {

        console.error(
            "Course report error:",
            error
        )

    }

}



/*
|--------------------------------------------------------------------------
| Generate Assessment Report
|--------------------------------------------------------------------------
*/

async function generateAssessmentReport()
{

    try {

        const response = await api.get(
            "/registrar/reports/assessment"
        )


        const data = response.data


        let rows = ""


        data.data.forEach(
            payment => {

                const student =
                    payment.enrollment?.student


                const course =
                    payment.enrollment?.course


                rows += `

                    <tr>

                        <td>
                            ${formatDate(
                                payment.created_at
                            )}
                        </td>

                        <td>
                            ${student?.first_name || ""}
                            ${student?.last_name || ""}
                        </td>

                        <td>
                            ${course?.code || "N/A"}
                        </td>

                        <td>
                            ₱${Number(
                                payment.amount || 0
                            ).toLocaleString(
                                "en-PH",
                                {
                                    minimumFractionDigits: 2
                                }
                            )}
                        </td>

                        <td>
                            ${payment.status}
                        </td>

                    </tr>

                `

            }
        )


        printReport(

            data.report,

            `

                <p>
                    <strong>Paid:</strong>
                    ${data.paid}
                </p>

                <p>
                    <strong>Pending:</strong>
                    ${data.pending}
                </p>

                <p>
                    <strong>Failed:</strong>
                    ${data.failed}
                </p>

                <table>

                    <thead>

                        <tr>

                            <th>Date</th>

                            <th>Student</th>

                            <th>Course</th>

                            <th>Amount</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        ${rows}

                    </tbody>

                </table>

            `

        )

    }
    catch(error)
    {

        console.error(
            "Assessment report error:",
            error
        )

    }

}



/*
|--------------------------------------------------------------------------
| Enrollment Table
|--------------------------------------------------------------------------
*/

function buildEnrollmentTable(
    enrollments
)
{

    let rows = ""


    enrollments.forEach(
        enrollment => {

            const student =
                enrollment.student


            const course =
                enrollment.course


            rows += `

                <tr>

                    <td>
                        ${formatDate(
                            enrollment.created_at
                        )}
                    </td>

                    <td>
                        ${student?.first_name || ""}
                        ${student?.last_name || ""}
                    </td>

                    <td>
                        ${course?.code || "N/A"}
                    </td>

                    <td>
                        ${enrollment.year_level || "N/A"}
                    </td>

                    <td>
                        ${enrollment.status}
                    </td>

                </tr>

            `

        }
    )


    return `

        <table>

            <thead>

                <tr>

                    <th>Date</th>

                    <th>Student</th>

                    <th>Course</th>

                    <th>Year Level</th>

                    <th>Status</th>

                </tr>

            </thead>

            <tbody>

                ${rows}

            </tbody>

        </table>

    `

}



/*
|--------------------------------------------------------------------------
| Print Report
|--------------------------------------------------------------------------
*/

function printReport(
    title,
    content
)
{

    const printWindow =
        window.open(
            "",
            "_blank",
            "width=1200,height=800"
        )


    printWindow.document.write(`

        <!DOCTYPE html>

        <html>

        <head>

            <title>
                ${title}
            </title>

            <style>

                body {

                    font-family:
                    Arial,
                    sans-serif;

                    padding:40px;

                    color:#222;

                }

                h1 {

                    color:#064E2A;

                    margin-bottom:5px;

                }

                .header {

                    border-bottom:
                    3px solid #0B6B3A;

                    padding-bottom:15px;

                    margin-bottom:25px;

                }

                table {

                    width:100%;

                    border-collapse:
                    collapse;

                    margin-top:20px;

                }

                th,
                td {

                    border:
                    1px solid #ddd;

                    padding:10px;

                    text-align:left;

                }

                th {

                    background:#064E2A;

                    color:white;

                }

                .footer {

                    margin-top:40px;

                    color:#666;

                    font-size:12px;

                }

            </style>

        </head>


        <body>

            <div class="header">

                <h1>
                    St. Francis Xavier College
                </h1>

                <h2>
                    ${title}
                </h2>

                <p>
                    Generated:
                    ${new Date().toLocaleString()}
                </p>

            </div>


            ${content}


            <div class="footer">

                Registrar's Office<br>

                St. Francis Xavier College

            </div>


            <script>

                window.onload = function() {

                    window.print();

                }

            <\/script>

        </body>

        </html>

    `)


    printWindow.document.close()

}



/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadReports()

})

</script>



<style scoped>

.reports-page {

    padding-bottom:40px;

}


/* HEADER */

.page-header {

    display:flex;

    justify-content:
    space-between;

    align-items:center;

}



/* STAT CARDS */

.stat-card {

    background:white;

    border-radius:18px;

    padding:22px;

    display:flex;

    align-items:center;

    gap:18px;

    box-shadow:
    0 5px 20px
    rgba(0,0,0,.06);

    transition:.3s;

}


.stat-card:hover {

    transform:
    translateY(-3px);

    box-shadow:
    0 10px 25px
    rgba(0,0,0,.10);

}


.stat-card small {

    color:#6b7280;

    font-weight:600;

}


.stat-card h3 {

    margin:3px 0 0;

    color:#064E2A;

    font-weight:800;

}


.stat-icon {

    width:52px;

    height:52px;

    border-radius:15px;

    background:#E8F5EE;

    color:#0B6B3A;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:25px;

}



/* REPORT CARD */

.report-card {

    background:white;

    padding:25px;

    border-radius:18px;

    box-shadow:
    0 5px 20px
    rgba(0,0,0,.06);

    height:100%;

    transition:.3s;

}


.report-card:hover {

    transform:
    translateY(-4px);

    box-shadow:
    0 10px 30px
    rgba(0,0,0,.10);

}


.report-icon {

    width:55px;

    height:55px;

    border-radius:15px;

    background:#E8F5EE;

    color:#0B6B3A;

    display:flex;

    align-items:center;

    justify-content:center;

    font-size:26px;

    margin-bottom:18px;

}


.report-card h5 {

    font-weight:700;

}


.report-card p {

    color:#6b7280;

    min-height:48px;

}


.report-number {

    font-size:30px;

    font-weight:800;

    color:#064E2A;

    margin-bottom:18px;

}


.report-number span {

    font-size:13px;

    font-weight:500;

    color:#6b7280;

}



/* TABLE */

.report-table-card {

    border:
    none;

    border-radius:18px;

    overflow:hidden;

    box-shadow:
    0 5px 20px
    rgba(0,0,0,.06);

}


.report-table-card .card-header {

    background:white;

    padding:20px;

    border-bottom:
    1px solid #eee;

}


.table {

    vertical-align:middle;

}


.table thead th {

    background:#064E2A;

    color:white;

    border:none;

}


.table tbody td {

    padding:14px;

}



/* PAYMENT */

.payment-stat {

    padding:20px;

}


.payment-stat i {

    font-size:30px;

    color:#0B6B3A;

}


.payment-stat h3 {

    font-size:30px;

    font-weight:800;

    color:#064E2A;

    margin:8px 0 2px;

}


.payment-stat span {

    color:#6b7280;

}



/* MOBILE */

@media(max-width:768px) {

    .page-header {

        flex-direction:column;

        align-items:
        flex-start;

        gap:15px;

    }

}

</style>