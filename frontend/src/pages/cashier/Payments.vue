<template>
<div class="payments-page">

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h2>Payments</h2>
            <p>Manage and monitor student enrollment payments.</p>
        </div>

        <button class="refresh-btn" @click="loadPayments" :disabled="loading">
            <i class="bi bi-arrow-clockwise"></i>
            Refresh
        </button>
    </div>


    <!-- ERROR -->
    <div v-if="errorMessage" class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ errorMessage }}

        <button class="btn btn-sm btn-outline-danger ms-2" @click="loadPayments">
            Retry
        </button>
    </div>


    <!-- STATISTICS -->
    <div class="summary-grid">

        <div class="summary-card">
            <div class="summary-icon total">
                <i class="bi bi-wallet2"></i>
            </div>
            <div>
                <span>Total Payments</span>
                <h3>{{ statistics.total }}</h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon paid">
                <i class="bi bi-check-circle"></i>
            </div>
            <div>
                <span>Paid</span>
                <h3>{{ statistics.paid }}</h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon pending">
                <i class="bi bi-clock"></i>
            </div>
            <div>
                <span>Pending</span>
                <h3>{{ statistics.pending }}</h3>
            </div>
        </div>

        <div class="summary-card">
            <div class="summary-icon failed">
                <i class="bi bi-x-circle"></i>
            </div>
            <div>
                <span>Failed</span>
                <h3>{{ statistics.failed }}</h3>
            </div>
        </div>

    </div>


    <!-- PAYMENT CARD -->
    <div class="payment-card">

        <!-- TOOLBAR -->
        <div class="toolbar">

            <div>
                <h4>Enrollment Payments</h4>
                <p>Review student payment transactions.</p>
            </div>

            <div class="filters">

                <!-- SEARCH -->
                <div class="search-box">
                    <i class="bi bi-search"></i>

                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search student, course, reference..."
                    >
                </div>


                <!-- STATUS -->
                <select v-model="statusFilter">
                    <option value="All">All Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                    <option value="Failed">Failed</option>
                </select>


                <!-- COURSE -->
                <select v-model="courseFilter">
                    <option value="All">All Courses</option>

                    <option
                        v-for="course in courseOptions"
                        :key="course"
                        :value="course"
                    >
                        {{ course }}
                    </option>
                </select>


                <!-- YEAR -->
                <select v-model="yearFilter">
                    <option value="All">All Years</option>

                    <option
                        v-for="year in yearOptions"
                        :key="year"
                        :value="year"
                    >
                        {{ year }}
                    </option>
                </select>

            </div>

        </div>


        <!-- ACTIVE FILTER INFO -->
        <div
            v-if="search || statusFilter !== 'All' || courseFilter !== 'All' || yearFilter !== 'All'"
            class="filter-info"
        >

            <span>
                Showing
                <strong>{{ filteredPayments.length }}</strong>
                of
                <strong>{{ payments.length }}</strong>
                payments
            </span>

            <button @click="clearFilters">
                <i class="bi bi-x-circle"></i>
                Clear Filters
            </button>

        </div>


        <!-- LOADING -->
        <div v-if="loading" class="loading-state">

            <div class="spinner"></div>

            <p>Loading payments...</p>

        </div>


        <!-- TABLE -->
        <div v-else class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>Student</th>
                        <th>Enrollment</th>
                        <th>Course</th>
                        <th>Year</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="payment in filteredPayments"
                        :key="payment.id"
                    >

                        <!-- STUDENT -->
                        <td>

                            <div class="student">

                                <div class="avatar">
                                    {{ getInitials(payment.student_name) }}
                                </div>

                                <div>

                                    <strong>
                                        {{ payment.student_name || "Unknown Student" }}
                                    </strong>

                                    <small>
                                        {{ payment.student_number || "N/A" }}
                                    </small>

                                </div>

                            </div>

                        </td>


                        <!-- ENROLLMENT -->
                        <td>

                            <span class="enrollment">
                                #{{ payment.enrollment_id }}
                            </span>

                        </td>


                        <!-- COURSE -->
                        <td>

                            <strong class="course-name">
                                {{ payment.course || "N/A" }}
                            </strong>

                        </td>


                        <!-- YEAR -->
                        <td>

                            <span class="year-badge">
                                {{ payment.year || "N/A" }}
                            </span>

                        </td>


                        <!-- AMOUNT -->
                        <td>

                            <strong class="amount">
                                ₱{{ formatAmount(payment.amount) }}
                            </strong>

                        </td>


                        <!-- METHOD -->
                        <td>

                            <span class="method">

                                <i
                                    :class="
                                        getPaymentIcon(
                                            payment.payment_method
                                        )
                                    "
                                ></i>

                                {{ payment.payment_method || "GCash" }}

                            </span>

                        </td>


                        <!-- REFERENCE -->
                        <td>

                            <span class="reference">
                                {{ payment.payment_reference || "—" }}
                            </span>

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

                                <i
                                    :class="
                                        getStatusIcon(
                                            payment.status
                                        )
                                    "
                                ></i>

                                {{ payment.status || "Unknown" }}

                            </span>

                        </td>


                        <!-- DATE -->
                        <td>

                            {{ formatDate(payment.created_at) }}

                        </td>


                        <!-- ACTION -->
                        <td>

                            <button
                                class="view-btn"
                                @click="viewPayment(payment)"
                            >

                                <i class="bi bi-eye"></i>

                                View

                            </button>

                        </td>

                    </tr>


                    <!-- EMPTY -->
                    <tr v-if="filteredPayments.length === 0">

                        <td
                            colspan="10"
                            class="empty"
                        >

                            <i class="bi bi-receipt"></i>

                            <strong>
                                No payments found
                            </strong>

                            <span>
                                No payment records match your filters.
                            </span>

                            <button
                                v-if="
                                    search ||
                                    statusFilter !== 'All' ||
                                    courseFilter !== 'All' ||
                                    yearFilter !== 'All'
                                "
                                class="clear-empty-btn"
                                @click="clearFilters"
                            >
                                Clear Filters
                            </button>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- PAYMENT MODAL -->
    <div
        v-if="selectedPayment"
        class="modal-overlay"
        @click.self="closeModal"
    >

        <div class="payment-modal">

            <!-- HEADER -->
            <div class="modal-header">

                <div>

                    <h3>
                        Payment Details
                    </h3>

                    <small>
                        Payment #{{ selectedPayment.id }}
                    </small>

                </div>

                <button @click="closeModal">
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>


            <!-- STATUS -->
            <div class="modal-status">

                <span
                    class="status"
                    :class="
                        getStatusClass(
                            selectedPayment.status
                        )
                    "
                >

                    <i
                        :class="
                            getStatusIcon(
                                selectedPayment.status
                            )
                        "
                    ></i>

                    {{ selectedPayment.status }}

                </span>

            </div>


            <!-- STUDENT -->
            <div class="details-section">

                <h4>
                    Student Information
                </h4>

                <div class="details-grid">

                    <div>

                        <span>
                            Student Name
                        </span>

                        <strong>
                            {{ selectedPayment.student_name || "N/A" }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Student Number
                        </span>

                        <strong>
                            {{ selectedPayment.student_number || "N/A" }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Course
                        </span>

                        <strong>
                            {{ selectedPayment.course || "N/A" }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Year
                        </span>

                        <strong>
                            {{ selectedPayment.year || "N/A" }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Enrollment ID
                        </span>

                        <strong>
                            #{{ selectedPayment.enrollment_id }}
                        </strong>

                    </div>

                </div>

            </div>


            <!-- PAYMENT -->
            <div class="details-section">

                <h4>
                    Payment Information
                </h4>

                <div class="details-grid">

                    <div>

                        <span>
                            Amount
                        </span>

                        <strong class="detail-amount">
                            ₱{{ formatAmount(selectedPayment.amount) }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Payment Method
                        </span>

                        <strong>
                            {{ selectedPayment.payment_method || "GCash" }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Reference Number
                        </span>

                        <strong>
                            {{ selectedPayment.payment_reference || "N/A" }}
                        </strong>

                    </div>


                    <div>

                        <span>
                            Payment Date
                        </span>

                        <strong>
                            {{ formatDate(selectedPayment.created_at) }}
                        </strong>

                    </div>

                </div>

            </div>


            <!-- FOOTER -->
            <div class="modal-footer">

                <button @click="closeModal">
                    Close
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
| STATE
|--------------------------------------------------------------------------
*/

const payments = ref([])

const loading = ref(false)

const search = ref("")

const statusFilter = ref("All")

const courseFilter = ref("All")

const yearFilter = ref("All")

const selectedPayment = ref(null)

const errorMessage = ref("")


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

const statistics = computed(() => {

    return {

        total: payments.value.length,

        paid:
            payments.value.filter(
                p => p.status === "Paid"
            ).length,

        pending:
            payments.value.filter(
                p => p.status === "Pending"
            ).length,

        failed:
            payments.value.filter(
                p => p.status === "Failed"
            ).length

    }

})


/*
|--------------------------------------------------------------------------
| COURSE OPTIONS
|--------------------------------------------------------------------------
*/

const courseOptions = computed(() => {

    const courses = payments.value
        .map(payment => payment.course)
        .filter(course => course)

    return [...new Set(courses)].sort()

})


/*
|--------------------------------------------------------------------------
| YEAR OPTIONS
|--------------------------------------------------------------------------
*/

const yearOptions = computed(() => {

    const years = payments.value
        .map(payment => payment.year)
        .filter(year => year)

    return [...new Set(years)].sort(
        (a, b) => String(a).localeCompare(String(b))
    )

})


/*
|--------------------------------------------------------------------------
| FILTER PAYMENTS
|--------------------------------------------------------------------------
*/

const filteredPayments = computed(() => {
    const keyword = search.value.toLowerCase().trim()

    return payments.value.filter(payment => {
        const text = [
            payment.student_name,
            payment.student_number,
            payment.payment_reference,
            payment.enrollment_id,
            payment.course,
            payment.year
        ]
        .filter(value => value !== null && value !== undefined)
        .join(" ")
        .toLowerCase()

        const matchesSearch =
            !keyword || text.includes(keyword)

        const matchesStatus =
            statusFilter.value === "All" ||
            payment.status === statusFilter.value

        return matchesSearch && matchesStatus
    })
})

/*
|--------------------------------------------------------------------------
| LOAD PAYMENTS
|--------------------------------------------------------------------------
*/

async function loadPayments() {

    if (loading.value) {
        return
    }


    loading.value = true

    errorMessage.value = ""


    try {

        const response =
            await api.get(
                "/cashier/payments"
            )


        /*
        IMPORTANT:
        Your existing API already works,
        so we preserve its response structure.
        */

        let data =
            response.data?.data ||
            response.data ||
            []


        /*
        Prevent:
        payments.value.filter is not a function
        */

        if (Array.isArray(data)) {

            payments.value = data

        }

        else {

            payments.value = []

            console.warn(
                "Payment API did not return an array:",
                data
            )

        }

    }

    catch (error) {

        console.error(
            "Failed to load payments:",
            error
        )


        errorMessage.value =
            error.response?.data?.message ||
            "Unable to load payment records."


        payments.value = []

    }

    finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| CLEAR FILTERS
|--------------------------------------------------------------------------
*/

function clearFilters() {

    search.value = ""

    statusFilter.value = "All"

    courseFilter.value = "All"

    yearFilter.value = "All"

}


/*
|--------------------------------------------------------------------------
| VIEW PAYMENT
|--------------------------------------------------------------------------
*/

function viewPayment(payment) {

    selectedPayment.value = payment

}


/*
|--------------------------------------------------------------------------
| CLOSE MODAL
|--------------------------------------------------------------------------
*/

function closeModal() {

    selectedPayment.value = null

}


/*
|--------------------------------------------------------------------------
| INITIALS
|--------------------------------------------------------------------------
*/

function getInitials(name) {

    if (!name) {
        return "ST"
    }


    const parts =
        name
            .trim()
            .split(/\s+/)


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

    if (!date) {
        return "—"
    }


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
| STATUS CLASS
|--------------------------------------------------------------------------
*/

function getStatusClass(status) {

    switch (
        String(status || "").toLowerCase()
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
| STATUS ICON
|--------------------------------------------------------------------------
*/

function getStatusIcon(status) {

    switch (
        String(status || "").toLowerCase()
    ) {

        case "paid":
            return "bi bi-check-circle-fill"

        case "pending":
            return "bi bi-clock-fill"

        case "failed":
            return "bi bi-x-circle-fill"

        default:
            return "bi bi-question-circle-fill"

    }

}


/*
|--------------------------------------------------------------------------
| PAYMENT ICON
|--------------------------------------------------------------------------
*/

function getPaymentIcon(method) {

    const value =
        String(
            method || ""
        ).toLowerCase()


    if (
        value.includes("gcash")
    ) {
        return "bi bi-phone"
    }


    if (
        value.includes("cash")
    ) {
        return "bi bi-cash"
    }


    if (
        value.includes("card")
    ) {
        return "bi bi-credit-card"
    }


    return "bi bi-wallet2"

}


/*
|--------------------------------------------------------------------------
| INITIAL LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    loadPayments
)

</script>


<style scoped>

.payments-page {
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

.refresh-btn:disabled {
    opacity: .6;
    cursor: not-allowed;
}


/* SUMMARY */

.summary-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
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

.summary-icon.total {
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
    font-size: 22px;
    color: #111827;
}


/* MAIN CARD */

.payment-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 3px 15px rgba(0,0,0,.05);
    overflow: hidden;
}


/* TOOLBAR */

.toolbar {
    padding: 18px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid #e5e7eb;
}

.toolbar h4 {
    margin: 0;
    font-size: 17px;
}

.toolbar p {
    margin: 3px 0 0;
    color: #6b7280;
    font-size: 12px;
}

.filters {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.search-box {
    display: flex;
    align-items: center;
    gap: 7px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 7px 10px;
}

.search-box i {
    color: #6b7280;
}

.search-box input {
    width: 230px;
    border: 0;
    outline: 0;
    font-size: 13px;
}

.filters select {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 7px 10px;
    background: white;
    outline: 0;
    font-size: 13px;
}


/* FILTER INFO */

.filter-info {
    padding: 10px 18px;
    background: #f8faf9;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #6b7280;
    font-size: 12px;
}

.filter-info strong {
    color: #064e2a;
}

.filter-info button {
    border: 0;
    background: transparent;
    color: #166534;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}


/* TABLE */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 1150px;
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
    white-space: nowrap;
    font-size: 13px;
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
    color: #9ca3af;
}


/* DATA */

.enrollment {
    color: #166534;
    font-weight: 600;
}

.course-name {
    color: #374151;
}

.year-badge {
    display: inline-block;
    background: #f0fdf4;
    color: #166534;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.amount {
    color: #064e2a;
}

.method {
    color: #374151;
}

.method i {
    margin-right: 4px;
    color: #0b6b3a;
}

.reference {
    color: #6b7280;
    font-family: monospace;
    font-size: 12px;
}


/* STATUS */

.status {
    display: inline-flex;
    align-items: center;
    gap: 5px;
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


/* VIEW */

.view-btn {
    border: 0;
    background: #ecfdf5;
    color: #166534;
    padding: 6px 10px;
    border-radius: 7px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
}

.view-btn:hover {
    background: #d1fae5;
}


/* LOADING */

.loading-state {
    min-height: 220px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #6b7280;
}

.spinner {
    width: 28px;
    height: 28px;
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


/* EMPTY */

.empty {
    padding: 55px 20px !important;
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

.clear-empty-btn {
    margin-top: 12px;
    border: 0;
    background: #064e2a;
    color: white;
    padding: 7px 12px;
    border-radius: 7px;
    font-size: 12px;
}


/* MODAL */

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,.45);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px;
    z-index: 9999;
}

.payment-modal {
    width: 100%;
    max-width: 620px;
    max-height: 90vh;
    overflow-y: auto;
    background: white;
    border-radius: 15px;
}

.modal-header {
    padding: 18px 20px;
    display: flex;
    justify-content: space-between;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    font-size: 19px;
}

.modal-header small {
    color: #6b7280;
}

.modal-header button {
    border: 0;
    background: transparent;
    font-size: 18px;
    color: #6b7280;
    cursor: pointer;
}

.modal-status {
    padding: 15px 20px;
    background: #f9fafb;
}

.details-section {
    padding: 18px 20px;
    border-bottom: 1px solid #f0f0f0;
}

.details-section h4 {
    margin: 0 0 14px;
    font-size: 14px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.details-grid span {
    display: block;
    color: #9ca3af;
    font-size: 11px;
    margin-bottom: 4px;
}

.details-grid strong {
    font-size: 13px;
    color: #374151;
}

.detail-amount {
    color: #166534 !important;
    font-size: 17px !important;
}

.modal-footer {
    padding: 15px 20px;
    display: flex;
    justify-content: flex-end;
}

.modal-footer button {
    border: 0;
    background: #f3f4f6;
    color: #374151;
    padding: 8px 14px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
}


/* TABLET */

@media (max-width: 1100px) {

    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}


/* MOBILE */

@media (max-width: 768px) {

    .page-header {
        align-items: flex-start;
    }

    .page-header h2 {
        font-size: 21px;
    }

    .page-header p {
        font-size: 12px;
    }

    .refresh-btn {
        padding: 8px 10px;
    }

    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .summary-card {
        padding: 14px;
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
    }

    .toolbar {
        flex-direction: column;
        align-items: stretch;
    }

    .filters {
        flex-direction: column;
    }

    .search-box input {
        width: 100%;
    }

    .filters select {
        width: 100%;
    }

    .filter-info {
        align-items: flex-start;
        gap: 8px;
        flex-direction: column;
    }

    .details-grid {
        grid-template-columns: 1fr;
    }

}


/* SMALL MOBILE */

@media (max-width: 480px) {

    .summary-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
    }

    .refresh-btn {
        width: 100%;
    }

    .summary-card {
        padding: 15px;
    }

}

</style>