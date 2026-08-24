<template>
<div class="dashboard">

    <!-- WELCOME HEADER -->
    <div class="welcome-card mb-4">
        <div>
            <h2>Welcome, Registrar 👋</h2>

            <p>
                Manage student enrollment applications and academic records.
            </p>

            <div class="mt-3">
                <small>
                    <i class="bi bi-calendar-event"></i>
                    {{ currentDate }}
                    &nbsp; | &nbsp;
                    <i class="bi bi-clock"></i>
                    {{ currentTime }}
                </small>

                <br>

                <small>
                    <i class="bi bi-mortarboard"></i>
                    School Year: 2026 - 2027
                    &nbsp; | &nbsp;
                    1st Semester
                </small>
            </div>
        </div>

        <div class="school-badge">
            SFXC
        </div>
    </div>


    <!-- OVERVIEW -->
    <h4 class="section-title">
        Registrar Overview
    </h4>


    <div class="row g-4">

        <!-- PENDING -->
        <div class="col-xl-3 col-md-6">
            <div
                class="dashboard-card clickable"
                @click="router.push('/registrar/applications')"
            >
                <div class="icon gold">
                    <i class="bi bi-clock-history"></i>
                </div>

                <div>
                    <h6>Pending Applications</h6>

                    <h4>
                        {{ dashboard.statistics.pending }}
                    </h4>

                    <span class="badge warning">
                        For Review
                    </span>
                </div>
            </div>
        </div>


        <!-- APPROVED -->
        <div class="col-xl-3 col-md-6">
            <div
                class="dashboard-card clickable"
                @click="router.push('/registrar/applications')"
            >
                <div class="icon success">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div>
                    <h6>Approved</h6>

                    <h4>
                        {{ dashboard.statistics.approved }}
                    </h4>

                    <span class="badge success-badge">
                        Approved
                    </span>
                </div>
            </div>
        </div>


        <!-- TOTAL -->
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="icon green">
                    <i class="bi bi-people"></i>
                </div>

                <div>
                    <h6>Total Applications</h6>

                    <h4>
                        {{ dashboard.statistics.total }}
                    </h4>

                    <span class="badge info">
                        Applications
                    </span>
                </div>
            </div>
        </div>


        <!-- REJECTED -->
        <div class="col-xl-3 col-md-6">
            <div class="dashboard-card">
                <div class="icon danger-icon">
                    <i class="bi bi-x-circle"></i>
                </div>

                <div>
                    <h6>Rejected</h6>

                    <h4>
                        {{ dashboard.statistics.rejected }}
                    </h4>

                    <span class="badge danger">
                        Rejected
                    </span>
                </div>
            </div>
        </div>

    </div>


    <!-- QUICK ACTIONS -->
    <div class="quick-actions mt-4">

        <button
            class="btn btn-success"
            @click="router.push('/registrar/applications')"
        >
            <i class="bi bi-person-lines-fill"></i>
            Enrollment Applications
        </button>

        <button
            class="btn btn-outline-success"
            @click="router.push('/registrar/students')"
        >
            <i class="bi bi-people"></i>
            Students
        </button>

        <button
            class="btn btn-outline-success"
            @click="router.push('/registrar/curriculum')"
        >
            <i class="bi bi-book"></i>
            Curriculum
        </button>

        <button
            class="btn btn-outline-success"
            @click="router.push('/registrar/reports')"
        >
            <i class="bi bi-bar-chart"></i>
            Reports
        </button>

    </div>


    <!-- RECENT APPLICATIONS -->
    <div class="process-card mt-5">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div class="card-title mb-0">
                Recent Enrollment Applications
            </div>

            <div class="d-flex gap-2">

                <button
                    class="btn btn-outline-success btn-sm"
                    @click="loadDashboard"
                    :disabled="loading"
                >
                    <i
                        class="bi"
                        :class="
                            loading
                            ? 'bi-arrow-repeat spin'
                            : 'bi-arrow-clockwise'
                        "
                    ></i>

                    Refresh
                </button>

                <router-link
                    to="/registrar/applications"
                    class="btn btn-success btn-sm"
                >
                    <i class="bi bi-list"></i>
                    View All
                </router-link>

            </div>

        </div>


        <div class="text-muted mb-3">
            <small>
                Last Updated: {{ lastUpdated }}
            </small>
        </div>


        <!-- TABLE -->
        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Student Number</th>
                        <th>Course</th>
                        <th>School Year</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th width="100">Action</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- LOADING -->
                    <tr v-if="loading">

                        <td colspan="8" class="text-center">

                            <div class="spinner-border text-success"></div>

                            <div class="mt-2 text-muted">
                                Loading applications...
                            </div>

                        </td>

                    </tr>


                    <!-- DATA -->
                    <tr
                        v-for="application in dashboard.recent"
                        :key="application.id"
                        v-else
                    >

                        <!-- STUDENT -->
                        <td>

                            <div class="student-cell">

                                <div class="student-avatar">
                                    {{ getInitials(application) }}
                                </div>

                                <div>
                                    <strong>
                                        {{ getStudentName(application) }}
                                    </strong>
                                </div>

                            </div>

                        </td>


                        <!-- STUDENT NUMBER -->
                        <td>
                            {{
                                application.student?.student_number ||
                                application.student_number ||
                                "N/A"
                            }}
                        </td>


                        <!-- COURSE -->
                        <td>
                            {{
                                application.course?.code ||
                                application.course?.name ||
                                application.course_name ||
                                "N/A"
                            }}
                        </td>


                        <!-- SCHOOL YEAR -->
                        <td>
                            {{
                                application.school_year?.name ||
                                application.schoolYear?.name ||
                                application.school_year ||
                                "N/A"
                            }}
                        </td>


                        <!-- SEMESTER -->
                        <td>
                            {{
                                application.semester?.name ||
                                application.semester_name ||
                                "N/A"
                            }}
                        </td>


                        <!-- STATUS -->
                        <td>

                            <span
                                class="badge"
                                :class="
                                    getStatusClass(
                                        application.status
                                    )
                                "
                            >
                                {{ application.status || "Pending" }}
                            </span>

                        </td>


                        <!-- DATE -->
                        <td>
                            {{ formatDate(application.created_at) }}
                        </td>


                        <!-- ACTION -->
                        <td>

                            <router-link
                                :to="
                                    `/registrar/applications/${application.id}`
                                "
                                class="btn btn-success btn-sm"
                            >
                                <i class="bi bi-eye"></i>
                                View
                            </router-link>

                        </td>

                    </tr>


                    <!-- EMPTY -->
                    <tr
                        v-if="
                            !loading &&
                            dashboard.recent.length === 0
                        "
                    >

                        <td
                            colspan="8"
                            class="text-center text-muted empty-state"
                        >

                            <i class="bi bi-person-lines-fill"></i>

                            <p>
                                No enrollment applications found.
                            </p>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>
</template>


<script setup>

import {
    ref,
    onMounted,
    onUnmounted
} from "vue"

import { useRouter } from "vue-router"

import api from "@/services/api"


const router = useRouter()

const loading = ref(false)

const dashboard = ref({
    statistics: {
        pending: 0,
        approved: 0,
        total: 0,
        rejected: 0
    },
    recent: [],
    updated_at: ""
})

const currentDate = ref("")
const currentTime = ref("")
const lastUpdated = ref("")

let clockTimer = null
let refreshTimer = null


function updateClock() {

    const now = new Date()

    currentDate.value =
        now.toLocaleDateString(
            "en-US",
            {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric"
            }
        )

    currentTime.value =
        now.toLocaleTimeString("en-US")
}


function updateLastUpdated() {

    lastUpdated.value =
        new Date().toLocaleTimeString("en-US")
}


async function loadDashboard() {

    if (loading.value) return

    loading.value = true

    try {

        const { data } =
            await api.get(
                "/registrar/dashboard"
            )

        dashboard.value = {

            statistics: {

                pending:
                    data.statistics?.pending ||
                    data.pending ||
                    0,

                approved:
                    data.statistics?.approved ||
                    data.approved ||
                    0,

                total:
                    data.statistics?.total ||
                    data.total ||
                    0,

                rejected:
                    data.statistics?.rejected ||
                    data.rejected ||
                    0

            },

            recent:
                data.recent ||
                data.recent_applications ||
                data.applications ||
                [],

            updated_at:
                data.updated_at ||
                ""

        }

        updateLastUpdated()

    }

    catch(error) {

        console.error(
            "Failed to load registrar dashboard:",
            error
        )

    }

    finally {

        loading.value = false

    }
}


function getStudentName(application) {

    const student =
        application.student

    if (student) {

        const name =
            `${student.first_name || ""} ${student.last_name || ""}`
                .trim()

        if (name) return name

    }

    if (application.student_name) {
        return application.student_name
    }

    return "Unknown Student"
}


function getInitials(application) {

    const name =
        getStudentName(application)

    if (!name || name === "Unknown Student") {
        return "?"
    }

    const parts =
        name.split(/\s+/)

    if (parts.length === 1) {

        return parts[0]
            .substring(0, 2)
            .toUpperCase()

    }

    return (
        parts[0][0] +
        parts[parts.length - 1][0]
    ).toUpperCase()
}


function formatDate(date) {

    if (!date) return "N/A"

    const parsed =
        new Date(date)

    if (Number.isNaN(parsed.getTime())) {
        return "N/A"
    }

    return parsed.toLocaleDateString(
        "en-PH",
        {
            year: "numeric",
            month: "short",
            day: "numeric"
        }
    )
}


function getStatusClass(status) {

    const value =
        String(status || "").toLowerCase()

    if (
        value === "approved" ||
        value === "enrolled"
    ) {
        return "success-badge"
    }

    if (
        value === "pending" ||
        value === "for review"
    ) {
        return "pending"
    }

    if (
        value === "rejected" ||
        value === "failed"
    ) {
        return "danger"
    }

    return "info"
}


onMounted(() => {

    updateClock()

    loadDashboard()

    clockTimer =
        setInterval(
            updateClock,
            1000
        )

    refreshTimer =
        setInterval(
            loadDashboard,
            30000
        )

})


onUnmounted(() => {

    clearInterval(clockTimer)

    clearInterval(refreshTimer)

})

</script>


<style scoped>

.dashboard {
    padding: 5px;
}

.welcome-card {
    background:
        linear-gradient(
            135deg,
            #064E2A,
            #0B6B3A
        );

    color: white;
    padding: 35px;
    border-radius: 25px;

    display: flex;
    justify-content: space-between;
    align-items: center;

    box-shadow:
        0 15px 40px rgba(0,0,0,.15);
}

.welcome-card h2 {
    font-weight: 800;
    margin-bottom: 8px;
}

.welcome-card p {
    margin: 0;
    opacity: .85;
}

.welcome-card small {
    opacity: .9;
    font-size: 14px;
}

.school-badge {
    height: 90px;
    width: 90px;
    min-width: 90px;

    border-radius: 50%;

    background: white;
    color: #0B6B3A;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 28px;
    font-weight: 900;

    border: 5px solid #9cffc8;
}

.section-title {
    color: #064E2A;
    font-weight: 700;
    margin-bottom: 20px;
}

.dashboard-card {
    background: white;
    border-radius: 20px;
    padding: 25px;

    display: flex;
    gap: 20px;
    align-items: center;

    height: 100%;

    box-shadow:
        0 10px 30px rgba(0,0,0,.08);

    transition: .3s;
}

.dashboard-card:hover {
    transform: translateY(-5px);

    box-shadow:
        0 15px 35px rgba(0,0,0,.12);
}

.clickable {
    cursor: pointer;
}

.clickable:hover {
    transform: translateY(-8px);
}

.icon {
    height: 60px;
    width: 60px;
    min-width: 60px;

    border-radius: 15px;

    display: flex;
    justify-content: center;
    align-items: center;

    font-size: 30px;
}

.icon.green {
    background: #dcfce7;
    color: #0B6B3A;
}

.icon.success {
    background: #d1fae5;
    color: #059669;
}

.icon.gold {
    background: #fef3c7;
    color: #d97706;
}

.icon.danger-icon {
    background: #fee2e2;
    color: #dc2626;
}

.dashboard-card h6 {
    margin-bottom: 5px;
    color: #6b7280;
    font-weight: 600;
}

.dashboard-card h4 {
    margin: 0 0 8px;
    color: #064E2A;
    font-weight: 800;
}

.badge {
    padding: 7px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.warning,
.pending {
    background: #FEF3C7;
    color: #92400E;
}

.info {
    background: #DBEAFE;
    color: #1D4ED8;
}

.success-badge {
    background: #DCFCE7;
    color: #166534;
}

.danger {
    background: #FEE2E2;
    color: #991B1B;
}

.quick-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.quick-actions button {
    border-radius: 12px;
    padding: 12px 22px;
    font-weight: 600;
    transition: .3s;
}

.quick-actions button:hover {
    transform: translateY(-3px);
}

.process-card {
    background: white;
    border-radius: 25px;
    padding: 30px;

    box-shadow:
        0 10px 30px rgba(0,0,0,.08);
}

.card-title {
    font-size: 22px;
    font-weight: 700;
    color: #064E2A;
}

table th {
    font-weight: 700;
    color: #064E2A;
    white-space: nowrap;
}

table td {
    white-space: nowrap;
}

.student-cell {
    display: flex;
    align-items: center;
    gap: 10px;
}

.student-avatar {
    height: 38px;
    width: 38px;
    min-width: 38px;

    border-radius: 50%;

    background: #0B6B3A;
    color: white;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 13px;
    font-weight: 700;
}

.empty-state {
    padding: 50px !important;
}

.empty-state i {
    font-size: 35px;
    color: #9ca3af;
}

.empty-state p {
    margin-top: 10px;
}

.spin {
    animation:
        spin 1s linear infinite;
}

@keyframes spin {

    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }

}

@media(max-width:768px) {

    .dashboard {
        padding: 0;
    }

    .welcome-card {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
        padding: 25px;
        border-radius: 20px;
    }

    .welcome-card h2 {
        font-size: 22px;
    }

    .school-badge {
        height: 70px;
        width: 70px;
        min-width: 70px;
        font-size: 22px;
    }

    .quick-actions {
        flex-direction: column;
    }

    .quick-actions button {
        width: 100%;
    }

    .process-card {
        padding: 20px;
        border-radius: 18px;
    }

    .card-title {
        font-size: 18px;
    }

}

</style>