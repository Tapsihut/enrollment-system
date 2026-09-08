<template>
<div class="reports-page">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <div class="page-title">
                <div class="title-icon">
                    <i class="bi bi-bar-chart-line"></i>
                </div>
                <div>
                    <h2>Registrar Reports</h2>
                    <p>Monitor enrollment, student, course, and payment records.</p>
                </div>
            </div>
        </div>

        <button
            class="btn btn-outline-success refresh-btn"
            @click="loadReports"
            :disabled="loading"
        >
            <i class="bi bi-arrow-clockwise me-2"></i>
            {{ loading ? 'Refreshing...' : 'Refresh' }}
        </button>
    </div>


    <!-- LOADING -->
    <div v-if="loading" class="loading-state">
        <div class="spinner-border text-success"></div>
        <div>
            <strong>Loading reports...</strong>
            <small>Please wait while the latest records are retrieved.</small>
        </div>
    </div>


    <div v-else>

        <!-- SUMMARY -->
        <div class="section-label">
            <span>Overview</span>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="bi bi-file-earmark-person"></i>
                    </div>
                    <div class="stat-info">
                        <small>Total Applications</small>
                        <h3>{{ statistics.total }}</h3>
                        <span>All enrollment applications</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon pending-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <div class="stat-info">
                        <small>Pending</small>
                        <h3>{{ statistics.pending }}</h3>
                        <span>Awaiting review</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon enrolled-icon">
                        <i class="bi bi-person-check"></i>
                    </div>
                    <div class="stat-info">
                        <small>Enrolled</small>
                        <h3>{{ statistics.enrolled }}</h3>
                        <span>Completed enrollment</span>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="stat-icon paid-icon">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-info">
                        <small>Paid</small>
                        <h3>{{ payments.paid }}</h3>
                        <span>Payments completed</span>
                    </div>
                </div>
            </div>

        </div>


        <!-- REPORT CENTER -->
        <div class="section-label">
            <span>Report Center</span>
        </div>

        <div class="report-center mb-4">

            <!-- STUDENTS -->
            <div class="report-item">

                <div class="report-item-icon">
                    <i class="bi bi-person-vcard"></i>
                </div>

                <div class="report-item-content">

                    <div class="report-item-header">
                        <div>
                            <h5>Student Masterlist</h5>
                            <p>Complete list of registered students.</p>
                        </div>

                        <div class="report-count">
                            <strong>{{ studentCount }}</strong>
                            <span>Students</span>
                        </div>
                    </div>

                    <div class="report-actions">

                        <button
                            class="btn btn-success"
                            @click="activeReport='students'"
                        >
                            <i class="bi bi-eye me-2"></i>
                            View Report
                        </button>

                        <button
                            class="btn btn-outline-success"
                            @click="exportStudentsToExcel"
                            :disabled="exportingStudents"
                        >
                            <i class="bi bi-file-earmark-excel me-2"></i>
                            {{ exportingStudents ? 'Exporting...' : 'Export Excel' }}
                        </button>

                    </div>

                </div>

            </div>


            <!-- ENROLLMENT -->
            <div class="report-item">

                <div class="report-item-icon">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                </div>

                <div class="report-item-content">

                    <div class="report-item-header">
                        <div>
                            <h5>Enrollment Report</h5>
                            <p>Enrollment applications and their status.</p>
                        </div>

                        <div class="report-count">
                            <strong>{{ statistics.total }}</strong>
                            <span>Applications</span>
                        </div>
                    </div>

                    <div class="report-actions">

                        <button
                            class="btn btn-success"
                            @click="activeReport='enrollment'"
                        >
                            <i class="bi bi-eye me-2"></i>
                            View Report
                        </button>

                        <button
                            class="btn btn-outline-success"
                            @click="generateEnrollmentReport"
                        >
                            <i class="bi bi-printer me-2"></i>
                            Print
                        </button>

                    </div>

                </div>

            </div>


            <!-- COURSE -->
            <div class="report-item">

                <div class="report-item-icon">
                    <i class="bi bi-mortarboard"></i>
                </div>

                <div class="report-item-content">

                    <div class="report-item-header">
                        <div>
                            <h5>Course Report</h5>
                            <p>Enrollment distribution by course.</p>
                        </div>

                        <div class="report-count">
                            <strong>{{ courses.length }}</strong>
                            <span>Courses</span>
                        </div>
                    </div>

                    <div class="report-actions">

                        <button
                            class="btn btn-success"
                            @click="activeReport='courses'"
                        >
                            <i class="bi bi-eye me-2"></i>
                            View Report
                        </button>

                        <button
                            class="btn btn-outline-success"
                            @click="generateCourseReport"
                        >
                            <i class="bi bi-printer me-2"></i>
                            Print
                        </button>

                    </div>

                </div>

            </div>


            <!-- ASSESSMENT -->
            <div class="report-item">

                <div class="report-item-icon">
                    <i class="bi bi-credit-card"></i>
                </div>

                <div class="report-item-content">

                    <div class="report-item-header">
                        <div>
                            <h5>Assessment Report</h5>
                            <p>Payment and assessment records.</p>
                        </div>

                        <div class="report-count">
                            <strong>{{ payments.paid }}</strong>
                            <span>Paid</span>
                        </div>
                    </div>

                    <div class="report-actions">

                        <button
                            class="btn btn-success"
                            @click="activeReport='assessment'"
                        >
                            <i class="bi bi-eye me-2"></i>
                            View Report
                        </button>

                        <button
                            class="btn btn-outline-success"
                            @click="generateAssessmentReport"
                        >
                            <i class="bi bi-printer me-2"></i>
                            Print
                        </button>

                    </div>

                </div>

            </div>

        </div>


        <!-- REPORT VIEWER -->
        <div class="report-viewer">

            <!-- VIEWER HEADER -->
            <div class="viewer-header">

                <div>
                    <div class="viewer-title">

                        <div class="viewer-icon">
                            <i
                                class="bi"
                                :class="{
                                    'bi-person-vcard': activeReport==='students',
                                    'bi-file-earmark-bar-graph': activeReport==='enrollment',
                                    'bi-mortarboard': activeReport==='courses',
                                    'bi-credit-card': activeReport==='assessment'
                                }"
                            ></i>
                        </div>

                        <div>
                            <h4>{{ activeReportTitle }}</h4>
                            <p>{{ activeReportDescription }}</p>
                        </div>

                    </div>
                </div>


                <div class="viewer-actions">

                    <button
                        v-if="activeReport==='students'"
                        class="btn btn-success"
                        @click="exportStudentsToExcel"
                        :disabled="exportingStudents"
                    >
                        <i class="bi bi-file-earmark-excel me-2"></i>
                        {{ exportingStudents ? 'Exporting...' : 'Export Excel' }}
                    </button>

                    <button
                        v-if="activeReport!=='students'"
                        class="btn btn-outline-success"
                        @click="printActiveReport"
                    >
                        <i class="bi bi-printer me-2"></i>
                        Print Report
                    </button>

                </div>

            </div>


            <!-- TABS -->
            <div class="report-tabs">

                <button
                    :class="{active:activeReport==='students'}"
                    @click="activeReport='students'"
                >
                    <i class="bi bi-people me-2"></i>
                    Students
                </button>

                <button
                    :class="{active:activeReport==='enrollment'}"
                    @click="activeReport='enrollment'"
                >
                    <i class="bi bi-file-earmark-text me-2"></i>
                    Enrollment
                </button>

                <button
                    :class="{active:activeReport==='courses'}"
                    @click="activeReport='courses'"
                >
                    <i class="bi bi-mortarboard me-2"></i>
                    Courses
                </button>

                <button
                    :class="{active:activeReport==='assessment'}"
                    @click="activeReport='assessment'"
                >
                    <i class="bi bi-credit-card me-2"></i>
                    Assessment
                </button>

            </div>


            <!-- STUDENTS -->
            <div v-if="activeReport==='students'">

                <div class="table-toolbar">

                    <div>
                        <strong>Student Records</strong>
                        <span>{{ studentCount }} total records</span>
                    </div>

                    <button
                        class="btn btn-sm btn-outline-success"
                        @click="loadStudents"
                    >
                        <i class="bi bi-arrow-clockwise me-1"></i>
                        Refresh
                    </button>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student No.</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Student Type</th>
                                <th>Birth Date</th>
                                <th>Nationality</th>
                                <th>Email</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="(student,index) in students"
                                :key="student.id"
                            >

                                <td>{{ index+1 }}</td>

                                <td>
                                    <strong>
                                        {{ student.student_number || 'N/A' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ fullName(student) }}
                                </td>

                                <td>
                                    {{ student.gender || 'N/A' }}
                                </td>

                                <td>
                                    <span class="soft-badge">
                                        {{ student.student_type || 'Regular' }}
                                    </span>
                                </td>

                                <td>
                                    {{ formatDateOnly(student.birth_date) }}
                                </td>

                                <td>
                                    {{ student.nationality || 'N/A' }}
                                </td>

                                <td>
                                    {{ student.email || 'N/A' }}
                                </td>

                            </tr>

                            <tr v-if="students.length===0">
                                <td colspan="8" class="empty-state">
                                    <i class="bi bi-people"></i>
                                    <strong>No student records found</strong>
                                    <span>There are currently no student records available.</span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- ENROLLMENT -->
            <div v-if="activeReport==='enrollment'">

                <div class="table-toolbar">

                    <div>
                        <strong>Enrollment Applications</strong>
                        <span>{{ statistics.total }} total applications</span>
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

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

                            <tr
                                v-for="enrollment in recent"
                                :key="enrollment.id"
                            >

                                <td>
                                    {{ formatDate(enrollment.created_at) }}
                                </td>

                                <td>
                                    <strong>
                                        {{ enrollment.student?.first_name }}
                                        {{ enrollment.student?.last_name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ enrollment.course?.code || enrollment.course?.name || 'N/A' }}
                                </td>

                                <td>
                                    {{ enrollment.year_level || 'N/A' }}
                                </td>

                                <td>
                                    <span
                                        class="badge"
                                        :class="statusClass(enrollment.status)"
                                    >
                                        {{ enrollment.status }}
                                    </span>
                                </td>

                            </tr>

                            <tr v-if="recent.length===0">
                                <td colspan="5" class="empty-state">
                                    <i class="bi bi-file-earmark-text"></i>
                                    <strong>No enrollment records found</strong>
                                    <span>No enrollment records are available.</span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- COURSES -->
            <div v-if="activeReport==='courses'">

                <div class="table-toolbar">

                    <div>
                        <strong>Course Enrollment Summary</strong>
                        <span>{{ courses.length }} courses</span>
                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Course</th>
                                <th>Department</th>
                                <th class="text-center">Applications</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr
                                v-for="(course,index) in courses"
                                :key="course.id"
                            >

                                <td>{{ index+1 }}</td>

                                <td>
                                    <strong>{{ course.code }}</strong>
                                    <small>{{ course.name }}</small>
                                </td>

                                <td>
                                    {{ course.department || 'N/A' }}
                                </td>

                                <td class="text-center">
                                    <span class="count-badge">
                                        {{ course.enrollments_count || 0 }}
                                    </span>
                                </td>

                            </tr>

                            <tr v-if="courses.length===0">
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-mortarboard"></i>
                                    <strong>No course data found</strong>
                                    <span>No course enrollment data is available.</span>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- ASSESSMENT -->
            <div v-if="activeReport==='assessment'">

                <div class="payment-overview">

                    <div class="payment-box paid">
                        <i class="bi bi-check-circle"></i>
                        <div>
                            <strong>{{ payments.paid }}</strong>
                            <span>Paid</span>
                        </div>
                    </div>

                    <div class="payment-box pending">
                        <i class="bi bi-clock"></i>
                        <div>
                            <strong>{{ payments.unpaid }}</strong>
                            <span>Pending</span>
                        </div>
                    </div>

                    <div class="payment-box failed">
                        <i class="bi bi-x-circle"></i>
                        <div>
                            <strong>{{ payments.failed }}</strong>
                            <span>Failed</span>
                        </div>
                    </div>

                </div>

                <div class="assessment-note">
                    <i class="bi bi-info-circle"></i>
                    <span>
                        Use <strong>Print Report</strong> above to generate the complete assessment and payment report.
                    </span>
                </div>

            </div>

        </div>

    </div>

</div>
</template>


<script setup>
import { ref,computed,onMounted } from "vue"
import api from "@/services/api"
import * as XLSX from "xlsx"

const loading=ref(false)
const exportingStudents=ref(false)
const students=ref([])
const courses=ref([])
const recent=ref([])
const activeReport=ref("students")

const statistics=ref({
    total:0,
    pending:0,
    approved:0,
    enrolled:0,
    rejected:0
})

const payments=ref({
    paid:0,
    unpaid:0,
    failed:0
})

const studentCount=computed(()=>students.value.length)

const activeReportTitle=computed(()=>{
    if(activeReport.value==="students")return "Student Masterlist"
    if(activeReport.value==="enrollment")return "Enrollment Report"
    if(activeReport.value==="courses")return "Course Report"
    return "Assessment Report"
})

const activeReportDescription=computed(()=>{
    if(activeReport.value==="students")
        return "Complete list of registered student records."
    if(activeReport.value==="enrollment")
        return "Enrollment applications and current status."
    if(activeReport.value==="courses")
        return "Enrollment distribution across academic programs."
    return "Payment and assessment information."
})

async function loadReports(){
    loading.value=true

    try{
        const response=await api.get("/registrar/reports")
        const data=response.data

        statistics.value=data.statistics||statistics.value
        payments.value=data.payments||payments.value
        courses.value=data.courses||[]
        recent.value=data.recent||[]

        if(Array.isArray(data.students))
            students.value=data.students
        else
            await loadStudents()

    }catch(error){
        console.error("Failed to load reports:",error)
    }finally{
        loading.value=false
    }
}

async function loadStudents(){
    try{
        const response=await api.get("/registrar/reports/students")

        students.value=
            response.data.students||
            response.data.data||
            []

    }catch(error){
        console.error("Failed to load students:",error)
        students.value=[]
    }
}

function fullName(student){
    const parts=[
        student.last_name,
        student.first_name,
        student.middle_name
    ].filter(Boolean)

    if(!parts.length)return "N/A"

    return `${student.last_name||""}, ${student.first_name||""}${student.middle_name?" "+student.middle_name:""}`
}

function formatDate(date){
    if(!date)return "N/A"

    return new Date(date).toLocaleDateString(
        "en-US",
        {
            year:"numeric",
            month:"long",
            day:"numeric"
        }
    )
}

function formatDateOnly(date){
    if(!date)return "N/A"

    return new Date(date).toLocaleDateString(
        "en-US",
        {
            year:"numeric",
            month:"2-digit",
            day:"2-digit"
        }
    )
}

function statusClass(status){
    switch(status){
        case "Enrolled":
            return "bg-success"
        case "Approved":
            return "bg-primary"
        case "Pending":
            return "bg-warning text-dark"
        case "Rejected":
            return "bg-danger"
        case "Paid":
            return "bg-success"
        default:
            return "bg-secondary"
    }
}

async function exportStudentsToExcel(){
    exportingStudents.value=true

    try{
        await loadStudents()

        if(!students.value.length){
            alert("No student records available to export.")
            return
        }

        const excelData=students.value.map((student,index)=>({
            "#":index+1,
            "Student Number":student.student_number||"",
            "Last Name":student.last_name||"",
            "First Name":student.first_name||"",
            "Middle Name":student.middle_name||"",
            "Birth Date":student.birth_date||"",
            "Gender":student.gender||"",
            "Civil Status":student.civil_status||"",
            "Nationality":student.nationality||"",
            "Religion":student.religion||"",
            "Student Type":student.student_type||"",
            "Email":student.email||"",
            "Contact Number":student.contact_number||student.phone||"",
            "Address":student.address||"",
            "Created At":student.created_at||""
        }))

        const worksheet=XLSX.utils.json_to_sheet(excelData)

        worksheet["!cols"]=[
            {wch:6},
            {wch:18},
            {wch:20},
            {wch:20},
            {wch:20},
            {wch:15},
            {wch:12},
            {wch:15},
            {wch:18},
            {wch:18},
            {wch:18},
            {wch:30},
            {wch:18},
            {wch:35},
            {wch:22}
        ]

        const workbook=XLSX.utils.book_new()

        XLSX.utils.book_append_sheet(
            workbook,
            worksheet,
            "Students"
        )

        if(recent.value.length){

            const enrollmentData=recent.value.map(enrollment=>({
                "Enrollment ID":enrollment.id,
                "Student Number":enrollment.student?.student_number||"",
                "Student Name":fullName(enrollment.student||{}),
                "Course":enrollment.course?.code||"",
                "Course Name":enrollment.course?.name||"",
                "Year Level":enrollment.year_level||"",
                "Semester":enrollment.semester?.name||"",
                "School Year":enrollment.schoolYear?.school_year||"",
                "Status":enrollment.status||"",
                "Date":enrollment.created_at||""
            }))

            const enrollmentSheet=
                XLSX.utils.json_to_sheet(enrollmentData)

            enrollmentSheet["!cols"]=[
                {wch:15},
                {wch:18},
                {wch:30},
                {wch:15},
                {wch:35},
                {wch:15},
                {wch:20},
                {wch:18},
                {wch:15},
                {wch:22}
            ]

            XLSX.utils.book_append_sheet(
                workbook,
                enrollmentSheet,
                "Enrollments"
            )
        }

        const filename=
            `SFXC_Student_Masterlist_${
                new Date().toISOString().slice(0,10)
            }.xlsx`

        XLSX.writeFile(workbook,filename)

    }catch(error){

        console.error(
            "Excel export error:",
            error
        )

        alert(
            "Unable to export student records."
        )

    }finally{
        exportingStudents.value=false
    }
}

async function generateEnrollmentReport(){
    try{
        const response=
            await api.get("/registrar/reports/enrollment")

        const data=response.data

        printReport(
            data.report,
            `
            <p><strong>Total Applications:</strong> ${data.total}</p>
            ${buildEnrollmentTable(data.data)}
            `
        )

    }catch(error){
        console.error(
            "Enrollment report error:",
            error
        )
    }
}

async function generateCourseReport(){
    try{
        const response=
            await api.get("/registrar/reports/course")

        const data=response.data
        let rows=""

        data.data.forEach((course,index)=>{
            rows+=`
                <tr>
                    <td>${index+1}</td>
                    <td>${course.code||""}</td>
                    <td>${course.name||""}</td>
                    <td>${course.department||""}</td>
                    <td>${course.enrollments_count||0}</td>
                </tr>
            `
        })

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
                <tbody>${rows}</tbody>
            </table>
            `
        )

    }catch(error){
        console.error(
            "Course report error:",
            error
        )
    }
}

async function generateAssessmentReport(){
    try{
        const response=
            await api.get("/registrar/reports/assessment")

        const data=response.data
        let rows=""

        data.data.forEach(payment=>{

            const student=
                payment.enrollment?.student

            const course=
                payment.enrollment?.course

            rows+=`
                <tr>
                    <td>${formatDate(payment.created_at)}</td>
                    <td>
                        ${student?.first_name||""}
                        ${student?.last_name||""}
                    </td>
                    <td>${course?.code||"N/A"}</td>
                    <td>
                        ₱${Number(
                            payment.amount||0
                        ).toLocaleString(
                            "en-PH",
                            {
                                minimumFractionDigits:2
                            }
                        )}
                    </td>
                    <td>${payment.status||""}</td>
                </tr>
            `
        })

        printReport(
            data.report,
            `
            <p><strong>Paid:</strong> ${data.paid}</p>
            <p><strong>Pending:</strong> ${data.pending}</p>
            <p><strong>Failed:</strong> ${data.failed}</p>

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
                <tbody>${rows}</tbody>
            </table>
            `
        )

    }catch(error){
        console.error(
            "Assessment report error:",
            error
        )
    }
}

function buildEnrollmentTable(enrollments){
    let rows=""

    enrollments.forEach(enrollment=>{

        const student=enrollment.student
        const course=enrollment.course

        rows+=`
            <tr>
                <td>${formatDate(enrollment.created_at)}</td>
                <td>
                    ${student?.first_name||""}
                    ${student?.last_name||""}
                </td>
                <td>${course?.code||"N/A"}</td>
                <td>${enrollment.year_level||"N/A"}</td>
                <td>${enrollment.status||""}</td>
            </tr>
        `
    })

    return`
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
            <tbody>${rows}</tbody>
        </table>
    `
}

function printActiveReport(){

    if(activeReport.value==="enrollment"){
        generateEnrollmentReport()
        return
    }

    if(activeReport.value==="courses"){
        generateCourseReport()
        return
    }

    if(activeReport.value==="assessment"){
        generateAssessmentReport()
        return
    }

    exportStudentsToExcel()
}

function printReport(title,content){

    const printWindow=
        window.open(
            "",
            "_blank",
            "width=1200,height=800"
        )

    if(!printWindow)return

    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>${title}</title>

            <style>
                body{
                    font-family:Arial,sans-serif;
                    padding:30px;
                    color:#222;
                }

                h1{
                    color:#064E2A;
                    margin:0 0 5px;
                }

                h2{
                    margin:0;
                }

                .header{
                    border-bottom:3px solid #0B6B3A;
                    padding-bottom:12px;
                    margin-bottom:20px;
                }

                table{
                    width:100%;
                    border-collapse:collapse;
                    margin-top:15px;
                }

                th,td{
                    border:1px solid #ddd;
                    padding:8px;
                    text-align:left;
                }

                th{
                    background:#064E2A;
                    color:white;
                }

                .footer{
                    margin-top:30px;
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
                window.onload=function(){
                    window.print();
                }
            <\/script>

        </body>
        </html>
    `)

    printWindow.document.close()
}

onMounted(()=>{
    loadReports()
})
</script>


<style scoped>

.reports-page{
    padding-bottom:30px;
}

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:24px;
}

.page-title{
    display:flex;
    align-items:center;
    gap:14px;
}

.title-icon{
    width:48px;
    height:48px;
    border-radius:12px;
    background:#E8F5EE;
    color:#0B6B3A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}

.page-header h2{
    margin:0;
    color:#064E2A;
    font-weight:800;
    font-size:24px;
}

.page-header p{
    margin:3px 0 0;
    color:#6b7280;
    font-size:14px;
}

.refresh-btn{
    border-radius:8px;
    font-weight:600;
}

.section-label{
    margin-bottom:10px;
    color:#374151;
    font-size:13px;
    font-weight:800;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.stat-card{
    background:#fff;
    border:1px solid #edf0ee;
    border-radius:12px;
    padding:17px;
    display:flex;
    align-items:center;
    gap:13px;
    box-shadow:0 2px 10px rgba(0,0,0,.035);
    height:100%;
    transition:.2s;
}

.stat-card:hover{
    transform:translateY(-2px);
    box-shadow:0 6px 18px rgba(0,0,0,.07);
}

.stat-icon{
    width:44px;
    height:44px;
    border-radius:10px;
    background:#E8F5EE;
    color:#0B6B3A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    flex-shrink:0;
}

.pending-icon{
    background:#FFF4D6;
    color:#9A6700;
}

.enrolled-icon{
    background:#E6F4EA;
    color:#137333;
}

.paid-icon{
    background:#E8F0FE;
    color:#1967D2;
}

.stat-info{
    min-width:0;
}

.stat-info small{
    display:block;
    color:#6b7280;
    font-size:12px;
    font-weight:700;
}

.stat-info h3{
    margin:1px 0;
    color:#064E2A;
    font-size:25px;
    font-weight:800;
}

.stat-info span{
    display:block;
    color:#9ca3af;
    font-size:11px;
}

.report-center{
    background:#fff;
    border:1px solid #edf0ee;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,.035);
}

.report-item{
    display:flex;
    align-items:center;
    gap:16px;
    padding:17px 19px;
    border-bottom:1px solid #edf0ee;
}

.report-item:last-child{
    border-bottom:0;
}

.report-item-icon{
    width:46px;
    height:46px;
    border-radius:10px;
    background:#E8F5EE;
    color:#0B6B3A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:21px;
    flex-shrink:0;
}

.report-item-content{
    flex:1;
    min-width:0;
}

.report-item-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:15px;
}

.report-item h5{
    margin:0 0 2px;
    font-weight:750;
    color:#1f2937;
}

.report-item p{
    margin:0;
    color:#6b7280;
    font-size:13px;
}

.report-count{
    text-align:right;
    flex-shrink:0;
}

.report-count strong{
    display:block;
    color:#064E2A;
    font-size:20px;
    font-weight:800;
}

.report-count span{
    display:block;
    color:#9ca3af;
    font-size:11px;
}

.report-actions{
    display:flex;
    gap:8px;
    margin-top:10px;
}

.report-actions .btn{
    font-size:12px;
    padding:7px 12px;
    border-radius:7px;
    font-weight:600;
}

.report-viewer{
    background:#fff;
    border:1px solid #edf0ee;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 2px 10px rgba(0,0,0,.035);
}

.viewer-header{
    padding:17px 19px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
    border-bottom:1px solid #edf0ee;
}

.viewer-title{
    display:flex;
    align-items:center;
    gap:12px;
}

.viewer-icon{
    width:40px;
    height:40px;
    border-radius:9px;
    background:#E8F5EE;
    color:#0B6B3A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:19px;
}

.viewer-title h4{
    margin:0;
    font-size:17px;
    font-weight:750;
    color:#1f2937;
}

.viewer-title p{
    margin:2px 0 0;
    color:#6b7280;
    font-size:12px;
}

.viewer-actions .btn{
    font-size:12px;
    font-weight:600;
}

.report-tabs{
    display:flex;
    border-bottom:1px solid #edf0ee;
    padding:0 14px;
    overflow-x:auto;
}

.report-tabs button{
    border:0;
    background:transparent;
    color:#6b7280;
    padding:12px 15px;
    font-size:13px;
    font-weight:650;
    border-bottom:2px solid transparent;
    white-space:nowrap;
}

.report-tabs button:hover{
    color:#0B6B3A;
}

.report-tabs button.active{
    color:#0B6B3A;
    border-bottom-color:#0B6B3A;
}

.table-toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 17px;
    border-bottom:1px solid #edf0ee;
}

.table-toolbar strong{
    display:block;
    color:#374151;
    font-size:13px;
}

.table-toolbar span{
    display:block;
    color:#9ca3af;
    font-size:11px;
    margin-top:2px;
}

.table{
    vertical-align:middle;
}

.table thead th{
    background:#064E2A;
    color:#fff;
    border:0;
    font-size:12px;
    font-weight:700;
    padding:11px 12px;
    white-space:nowrap;
}

.table tbody td{
    padding:10px 12px;
    font-size:12px;
    color:#374151;
}

.table tbody tr:last-child td{
    border-bottom:0;
}

.table tbody td small{
    display:block;
    color:#9ca3af;
    margin-top:2px;
}

.soft-badge{
    background:#E8F5EE;
    color:#0B6B3A;
    padding:4px 8px;
    border-radius:20px;
    font-size:10px;
    font-weight:700;
}

.count-badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:30px;
    padding:4px 8px;
    background:#E8F5EE;
    color:#0B6B3A;
    border-radius:20px;
    font-size:11px;
    font-weight:800;
}

.empty-state{
    text-align:center;
    padding:35px!important;
}

.empty-state i{
    display:block;
    font-size:28px;
    color:#9ca3af;
    margin-bottom:7px;
}

.empty-state strong{
    display:block;
    color:#6b7280;
    font-size:13px;
}

.empty-state span{
    display:block;
    color:#9ca3af;
    font-size:11px;
    margin-top:3px;
}

.payment-overview{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:15px;
    padding:20px;
}

.payment-box{
    border:1px solid #edf0ee;
    border-radius:10px;
    padding:16px;
    display:flex;
    align-items:center;
    gap:12px;
}

.payment-box i{
    font-size:25px;
}

.payment-box strong{
    display:block;
    font-size:23px;
    font-weight:800;
}

.payment-box span{
    display:block;
    color:#6b7280;
    font-size:12px;
}

.payment-box.paid i,
.payment-box.paid strong{
    color:#137333;
}

.payment-box.pending i,
.payment-box.pending strong{
    color:#9A6700;
}

.payment-box.failed i,
.payment-box.failed strong{
    color:#B42318;
}

.assessment-note{
    margin:0 20px 20px;
    padding:12px 14px;
    background:#F6F8F7;
    border-radius:8px;
    color:#6b7280;
    font-size:12px;
    display:flex;
    align-items:center;
    gap:8px;
}

.assessment-note i{
    color:#0B6B3A;
}

.loading-state{
    background:#fff;
    border:1px solid #edf0ee;
    border-radius:12px;
    padding:45px;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:15px;
}

.loading-state strong{
    display:block;
    color:#374151;
    font-size:14px;
}

.loading-state small{
    display:block;
    color:#9ca3af;
    margin-top:2px;
}

@media(max-width:768px){

    .page-header{
        align-items:flex-start;
        flex-direction:column;
        gap:12px;
    }

    .refresh-btn{
        width:100%;
    }

    .report-item{
        align-items:flex-start;
    }

    .report-item-header{
        align-items:flex-start;
        flex-direction:column;
        gap:7px;
    }

    .report-count{
        text-align:left;
    }

    .report-actions{
        flex-wrap:wrap;
    }

    .viewer-header{
        align-items:flex-start;
        flex-direction:column;
    }

    .viewer-actions{
        width:100%;
    }

    .viewer-actions .btn{
        width:100%;
    }

    .payment-overview{
        grid-template-columns:1fr;
    }

}

@media(max-width:500px){

    .page-title{
        align-items:flex-start;
    }

    .page-header h2{
        font-size:21px;
    }

    .report-item{
        padding:15px;
    }

    .report-item-icon{
        width:40px;
        height:40px;
        font-size:18px;
    }

    .report-actions .btn{
        flex:1;
    }

}

</style>