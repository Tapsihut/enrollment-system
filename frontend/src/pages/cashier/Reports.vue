<template>
<div class="reports-page">

    <!-- HEADER -->
    <div class="page-header">

        <div>
            <h2>Reports</h2>
            <p>
                View payment and collection reports.
            </p>
        </div>

        <button
            class="refresh-btn"
            @click="loadReports"
            :disabled="loading"
        >
            <i class="bi bi-arrow-clockwise"></i>
            Refresh
        </button>

    </div>


    <!-- FILTER -->
    <div class="filter-card">

        <div class="filter-title">
            <i class="bi bi-funnel"></i>

            <div>
                <strong>Report Period</strong>
                <span>Select the date range for this report.</span>
            </div>
        </div>


        <div class="date-filters">

            <div>
                <label>From</label>

                <input
                    v-model="fromDate"
                    type="date"
                >
            </div>


            <div>
                <label>To</label>

                <input
                    v-model="toDate"
                    type="date"
                >
            </div>


            <button
                class="generate-btn"
                @click="loadReports"
                :disabled="loading"
            >
                <i class="bi bi-search"></i>
                Generate
            </button>

        </div>

    </div>


    <!-- ERROR -->
    <div
        v-if="errorMessage"
        class="alert alert-danger"
    >
        <i class="bi bi-exclamation-triangle me-2"></i>

        {{ errorMessage }}

        <button
            class="btn btn-sm btn-outline-danger ms-2"
            @click="loadReports"
        >
            Retry
        </button>
    </div>


    <!-- LOADING -->
    <div
        v-if="loading"
        class="loading-state"
    >

        <div class="spinner"></div>

        <p>
            Generating report...
        </p>

    </div>


    <template v-else>

        <!-- SUMMARY -->
        <div class="summary-grid">

            <!-- COLLECTION -->
            <div class="summary-card">

                <div class="summary-icon collection">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div>
                    <span>Total Collection</span>

                    <h3>
                        ₱{{ formatAmount(summary.total_collection) }}
                    </h3>
                </div>

            </div>


            <!-- PAID -->
            <div class="summary-card">

                <div class="summary-icon paid">
                    <i class="bi bi-check-circle"></i>
                </div>

                <div>
                    <span>Paid Transactions</span>

                    <h3>
                        {{ summary.paid }}
                    </h3>
                </div>

            </div>


            <!-- PENDING -->
            <div class="summary-card">

                <div class="summary-icon pending">
                    <i class="bi bi-clock"></i>
                </div>

                <div>
                    <span>Pending</span>

                    <h3>
                        {{ summary.pending }}
                    </h3>
                </div>

            </div>


            <!-- FAILED -->
            <div class="summary-card">

                <div class="summary-icon failed">
                    <i class="bi bi-x-circle"></i>
                </div>

                <div>
                    <span>Failed</span>

                    <h3>
                        {{ summary.failed }}
                    </h3>
                </div>

            </div>

        </div>


        <!-- REPORT GRID -->
        <div class="report-grid">


            <!-- PAYMENT METHODS -->
            <div class="report-card">

                <div class="card-header">

                    <div>
                        <h4>Payment Methods</h4>

                        <p>
                            Collection by payment method.
                        </p>
                    </div>

                    <i class="bi bi-wallet2"></i>

                </div>


                <div
                    v-if="paymentMethods.length"
                    class="method-list"
                >

                    <div
                        v-for="method in paymentMethods"
                        :key="method.method"
                        class="method-row"
                    >

                        <div class="method-name">

                            <div class="method-icon">
                                <i
                                    :class="
                                        getPaymentIcon(
                                            method.method
                                        )
                                    "
                                ></i>
                            </div>

                            <div>

                                <strong>
                                    {{ method.method }}
                                </strong>

                                <small>
                                    {{ method.transactions }}
                                    transaction(s)
                                </small>

                            </div>

                        </div>


                        <strong class="method-total">
                            ₱{{ formatAmount(method.total) }}
                        </strong>

                    </div>

                </div>


                <div
                    v-else
                    class="no-data"
                >
                    No payment method data.
                </div>

            </div>


            <!-- COURSE COLLECTION -->
            <div class="report-card">

                <div class="card-header">

                    <div>
                        <h4>Collection by Course</h4>

                        <p>
                            Payment collection per course.
                        </p>
                    </div>

                    <i class="bi bi-mortarboard"></i>

                </div>


                <div
                    v-if="courseCollection.length"
                    class="course-list"
                >

                    <div
                        v-for="course in courseCollection"
                        :key="course.course"
                        class="course-row"
                    >

                        <div>

                            <strong>
                                {{ course.course }}
                            </strong>

                            <small>
                                {{ course.transactions }}
                                transaction(s)
                            </small>

                        </div>


                        <strong>
                            ₱{{ formatAmount(course.total) }}
                        </strong>

                    </div>

                </div>


                <div
                    v-else
                    class="no-data"
                >
                    No course collection data.
                </div>

            </div>

        </div>


        <!-- RECENT PAYMENTS -->
        <div class="report-card recent-card">

            <div class="card-header">

                <div>
                    <h4>Recent Transactions</h4>

                    <p>
                        Latest payment transactions
                        within the selected period.
                    </p>
                </div>

                <i class="bi bi-receipt"></i>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>Student</th>

                            <th>Course</th>

                            <th>Year</th>

                            <th>Amount</th>

                            <th>Method</th>

                            <th>Status</th>

                            <th>Date</th>

                        </tr>

                    </thead>


                    <tbody>

                        <tr
                            v-for="payment in recentPayments"
                            :key="payment.id"
                        >

                            <!-- STUDENT -->
                            <td>

                                <div class="student">

                                    <div class="avatar">
                                        {{
                                            getInitials(
                                                getStudentName(payment)
                                            )
                                        }}
                                    </div>

                                    <div>

                                        <strong>
                                            {{
                                                getStudentName(
                                                    payment
                                                )
                                            }}
                                        </strong>

                                        <small>
                                            {{
                                                payment.enrollment?.student?.student_number
                                                || "N/A"
                                            }}
                                        </small>

                                    </div>

                                </div>

                            </td>


                            <!-- COURSE -->
                            <td>
                                {{
                                    payment.enrollment?.course?.name
                                    || payment.enrollment?.course?.course_name
                                    || "N/A"
                                }}
                            </td>


                            <!-- YEAR -->
                            <td>
                                {{
                                    getSchoolYear(payment)
                                }}
                            </td>


                            <!-- AMOUNT -->
                            <td>

                                <strong class="amount">
                                    ₱{{ formatAmount(payment.amount) }}
                                </strong>

                            </td>


                            <!-- METHOD -->
                            <td>
                                {{
                                    payment.payment_method
                                    || "GCash"
                                }}
                            </td>


                            <!-- STATUS -->
                            <td>

                                <span
                                    class="status"
                                    :class="
                                        getStatusClass(
                                            payment.status
                                        )
                                    "
                                >

                                    {{
                                        payment.status
                                    }}

                                </span>

                            </td>


                            <!-- DATE -->
                            <td>
                                {{
                                    formatDate(
                                        payment.created_at
                                    )
                                }}
                            </td>

                        </tr>


                        <tr
                            v-if="recentPayments.length === 0"
                        >

                            <td
                                colspan="7"
                                class="empty"
                            >

                                <i class="bi bi-receipt"></i>

                                <strong>
                                    No transactions found
                                </strong>

                                <span>
                                    There are no payments
                                    for the selected period.
                                </span>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>

        </div>

    </template>

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
| STATE
|--------------------------------------------------------------------------
*/

const loading = ref(false)

const errorMessage = ref("")

const fromDate = ref("")

const toDate = ref("")


const summary = ref({

    total_payments: 0,

    paid: 0,

    pending: 0,

    failed: 0,

    total_collection: 0

})


const paymentMethods = ref([])

const courseCollection = ref([])

const recentPayments = ref([])


/*
|--------------------------------------------------------------------------
| LOAD REPORTS
|--------------------------------------------------------------------------
*/

async function loadReports() {

    if (loading.value) return

    loading.value = true

    errorMessage.value = ""


    try {

        const params = {}


        if (fromDate.value) {

            params.from =
                fromDate.value

        }


        if (toDate.value) {

            params.to =
                toDate.value

        }


        const response =
            await api.get(
                "/cashier/reports",
                {
                    params
                }
            )


        const data =
            response.data || {}


        summary.value =
            data.summary || summary.value


        paymentMethods.value =
            Array.isArray(
                data.payment_methods
            )
                ? data.payment_methods
                : []


        courseCollection.value =
            Array.isArray(
                data.course_collection
            )
                ? data.course_collection
                : []


        recentPayments.value =
            Array.isArray(
                data.recent_payments
            )
                ? data.recent_payments
                : []


    } catch (error) {

        console.error(
            "Failed to load reports:",
            error
        )


        errorMessage.value =
            error.response?.data?.message ||
            "Unable to load report data."

    } finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| STUDENT NAME
|--------------------------------------------------------------------------
*/

function getStudentName(payment) {

    const student =
        payment?.enrollment?.student


    if (!student) {

        return "Unknown Student"

    }


    const first =
        student.first_name || ""


    const middle =
        student.middle_name || ""


    const last =
        student.last_name || ""


    return [
        first,
        middle,
        last
    ]
        .filter(Boolean)
        .join(" ")
        .trim()
        || "Unknown Student"

}


/*
|--------------------------------------------------------------------------
| SCHOOL YEAR
|--------------------------------------------------------------------------
*/

function getSchoolYear(payment) {

    const schoolYear =
        payment?.enrollment?.school_year


    if (!schoolYear) {

        return "N/A"

    }


    return (
        schoolYear.name
        ||
        schoolYear.school_year
        ||
        schoolYear.year
        ||
        "N/A"
    )

}


/*
|--------------------------------------------------------------------------
| FORMAT AMOUNT
|--------------------------------------------------------------------------
*/

function formatAmount(amount) {

    return Number(
        amount || 0
    ).toLocaleString(
        "en-PH",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    )

}


/*
|--------------------------------------------------------------------------
| FORMAT DATE
|--------------------------------------------------------------------------
*/

function formatDate(date) {

    if (!date) return "—"


    const parsed =
        new Date(date)


    if (
        Number.isNaN(
            parsed.getTime()
        )
    ) {

        return "—"

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


/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

function getInitials(name) {

    if (!name) return "ST"


    const parts =
        name.trim().split(/\s+/)


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


/*
|--------------------------------------------------------------------------
| PAYMENT ICON
|--------------------------------------------------------------------------
*/

function getPaymentIcon(method) {

    const value =
        String(method || "")
            .toLowerCase()


    if (value.includes("gcash")) {

        return "bi bi-phone"

    }


    if (value.includes("cash")) {

        return "bi bi-cash"

    }


    if (value.includes("card")) {

        return "bi bi-credit-card"

    }


    return "bi bi-wallet2"

}


/*
|--------------------------------------------------------------------------
| STATUS
|--------------------------------------------------------------------------
*/

function getStatusClass(status) {

    switch (
        String(status || "")
            .toLowerCase()
    ) {

        case "paid":
            return "status-paid"

        case "pending":
            return "status-pending"

        case "failed":
            return "status-failed"

        default:
            return "status-default"

    }

}


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    loadReports
)

</script>


<style scoped>

.reports-page {
    width: 100%;
    min-width: 0;
}


/* HEADER */

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.page-header h2 {
    margin: 0;
    font-size: 25px;
    font-weight: 800;
    color: #064e2a;
}

.page-header p {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 14px;
}

.refresh-btn {
    border: 0;
    background: #064e2a;
    color: white;
    padding: 9px 14px;
    border-radius: 9px;
    cursor: pointer;
}

.refresh-btn:hover {
    background: #0b6b3a;
}


/* FILTER */

.filter-card {
    background: white;
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 20px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.filter-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.filter-title > i {
    width: 42px;
    height: 42px;
    border-radius: 10px;
    background: #ecfdf5;
    color: #166534;
    display: flex;
    align-items: center;
    justify-content: center;
}

.filter-title strong,
.filter-title span {
    display: block;
}

.filter-title strong {
    font-size: 14px;
}

.filter-title span {
    margin-top: 3px;
    color: #9ca3af;
    font-size: 12px;
}

.date-filters {
    display: flex;
    align-items: flex-end;
    gap: 10px;
}

.date-filters label {
    display: block;
    margin-bottom: 5px;
    font-size: 11px;
    color: #6b7280;
}

.date-filters input {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 8px 10px;
    outline: none;
}

.generate-btn {
    border: 0;
    background: #064e2a;
    color: white;
    padding: 9px 14px;
    border-radius: 8px;
    cursor: pointer;
}


/* SUMMARY */

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4,1fr);
    gap: 15px;
    margin-bottom: 20px;
}

.summary-card {
    background: white;
    border-radius: 14px;
    padding: 18px;
    display: flex;
    align-items: center;
    gap: 13px;
    box-shadow: 0 3px 12px rgba(0,0,0,.05);
}

.summary-icon {
    width: 45px;
    height: 45px;
    min-width: 45px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
}

.summary-icon.collection {
    background: #e8f0ff;
    color: #2563eb;
}

.summary-icon.paid {
    background: #dcfce7;
    color: #15803d;
}

.summary-icon.pending {
    background: #fef3c7;
    color: #b45309;
}

.summary-icon.failed {
    background: #fee2e2;
    color: #dc2626;
}

.summary-card span {
    display: block;
    color: #6b7280;
    font-size: 12px;
}

.summary-card h3 {
    margin: 2px 0 0;
    font-size: 20px;
    color: #111827;
}


/* REPORT GRID */

.report-grid {
    display: grid;
    grid-template-columns: repeat(2,1fr);
    gap: 20px;
    margin-bottom: 20px;
}

.report-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,.05);
    overflow: hidden;
}

.card-header {
    padding: 18px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h4 {
    margin: 0;
    font-size: 16px;
}

.card-header p {
    margin: 3px 0 0;
    color: #9ca3af;
    font-size: 11px;
}

.card-header > i {
    color: #166534;
    font-size: 20px;
}


/* METHODS */

.method-list,
.course-list {
    padding: 8px 18px;
}

.method-row,
.course-row {
    padding: 13px 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
}

.method-row:last-child,
.course-row:last-child {
    border-bottom: 0;
}

.method-name {
    display: flex;
    align-items: center;
    gap: 10px;
}

.method-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    background: #ecfdf5;
    color: #166534;
    display: flex;
    align-items: center;
    justify-content: center;
}

.method-row strong,
.course-row strong {
    display: block;
    font-size: 13px;
}

.method-row small,
.course-row small {
    display: block;
    color: #9ca3af;
    margin-top: 2px;
    font-size: 11px;
}

.method-total {
    color: #064e2a;
}

.course-row > strong {
    color: #064e2a;
}


/* RECENT */

.recent-card {
    margin-bottom: 20px;
}

.table-wrapper {
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
}

th {
    padding: 12px 15px;
    background: #f8faf9;
    color: #6b7280;
    font-size: 11px;
    text-align: left;
    white-space: nowrap;
}

td {
    padding: 13px 15px;
    border-top: 1px solid #f1f5f9;
    font-size: 13px;
    white-space: nowrap;
}

tbody tr:hover {
    background: #fafdfb;
}


/* STUDENT */

.student {
    display: flex;
    align-items: center;
    gap: 9px;
}

.avatar {
    width: 35px;
    height: 35px;
    min-width: 35px;
    border-radius: 50%;
    background: #dcfce7;
    color: #166534;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
}

.student strong {
    display: block;
}

.student small {
    display: block;
    color: #9ca3af;
}


/* DATA */

.amount {
    color: #064e2a;
}

.status {
    display: inline-flex;
    padding: 5px 9px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
}

.status-paid {
    background: #dcfce7;
    color: #166534;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-failed {
    background: #fee2e2;
    color: #991b1b;
}

.status-default {
    background: #e5e7eb;
    color: #374151;
}


/* EMPTY */

.empty {
    padding: 50px 20px !important;
    text-align: center;
    color: #9ca3af;
}

.empty i {
    display: block;
    font-size: 35px;
    margin-bottom: 8px;
}

.empty strong,
.empty span {
    display: block;
}

.empty strong {
    color: #374151;
}

.empty span {
    margin-top: 4px;
    font-size: 12px;
}


/* LOADING */

.loading-state {
    min-height: 300px;
    background: white;
    border-radius: 15px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.spinner {
    width: 30px;
    height: 30px;
    border: 3px solid #d1fae5;
    border-top-color: #166534;
    border-radius: 50%;
    animation: spin .7s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}


/* NO DATA */

.no-data {
    padding: 30px 18px;
    text-align: center;
    color: #9ca3af;
    font-size: 12px;
}


/* ALERT */

.alert {
    margin-bottom: 20px;
}


/* RESPONSIVE */

@media (max-width:1100px) {

    .summary-grid {
        grid-template-columns: repeat(2,1fr);
    }

    .filter-card {
        flex-direction: column;
        align-items: stretch;
    }

    .date-filters {
        justify-content: flex-start;
    }

}


@media (max-width:768px) {

    .page-header {
        align-items: flex-start;
    }

    .page-header h2 {
        font-size: 21px;
    }

    .refresh-btn {
        padding: 8px 10px;
    }

    .summary-grid {
        grid-template-columns: repeat(2,1fr);
    }

    .report-grid {
        grid-template-columns: 1fr;
    }

    .date-filters {
        flex-direction: column;
        align-items: stretch;
    }

    .date-filters > div,
    .date-filters input,
    .generate-btn {
        width: 100%;
    }

}


@media (max-width:480px) {

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
    }

    .refresh-btn {
        width: 100%;
    }

}

</style>