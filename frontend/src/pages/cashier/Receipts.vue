<template>
<div class="receipts-page">

    <!-- HEADER -->
    <div class="page-header">

        <div>
            <h2>Receipts</h2>

            <p>
                View and manage student payment receipts.
            </p>
        </div>

        <button
            class="refresh-btn"
            @click="loadReceipts(currentPage)"
            :disabled="loading"
        >
            <i class="bi bi-arrow-clockwise"></i>
            Refresh
        </button>

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
            @click="loadReceipts(currentPage)"
        >
            Retry
        </button>

    </div>


    <!-- SUMMARY -->
    <div class="summary-grid">

        <div class="summary-card">

            <div class="summary-icon total">
                <i class="bi bi-receipt"></i>
            </div>

            <div>
                <span>Total Receipts</span>

                <h3>
                    {{ totalReceipts }}
                </h3>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon today">
                <i class="bi bi-calendar-check"></i>
            </div>

            <div>
                <span>Today's Receipts</span>

                <h3>
                    {{ todayReceipts }}
                </h3>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon collection">
                <i class="bi bi-cash-stack"></i>
            </div>

            <div>
                <span>Total Collection</span>

                <h3>
                    ₱{{ formatAmount(totalCollection) }}
                </h3>
            </div>

        </div>


        <div class="summary-card">

            <div class="summary-icon month">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>
                <span>This Month</span>

                <h3>
                    ₱{{ formatAmount(monthCollection) }}
                </h3>
            </div>

        </div>

    </div>


    <!-- RECEIPTS CARD -->
    <div class="receipt-card">

        <!-- TOOLBAR -->
        <div class="toolbar">

            <div>

                <h4>
                    Payment Receipts
                </h4>

                <p>
                    Official receipts for completed payments.
                </p>

            </div>


            <div class="filters">

                <!-- SEARCH -->
                <div class="search-box">

                    <i class="bi bi-search"></i>

                    <input
                        v-model="search"
                        @keyup.enter="searchReceipts"
                        type="text"
                        placeholder="Search student or reference..."
                    >

                </div>


                <!-- DATE -->
                <input
                    v-model="dateFilter"
                    @change="searchReceipts"
                    type="date"
                    class="date-filter"
                >

            </div>

        </div>


        <!-- LOADING -->
        <div
            v-if="loading"
            class="loading-state"
        >

            <div class="spinner"></div>

            <p>
                Loading receipts...
            </p>

        </div>


        <!-- TABLE -->
        <div
            v-else
            class="table-wrapper"
        >

            <table>

                <thead>

                    <tr>

                        <th>
                            Student
                        </th>

                        <th>
                            Course
                        </th>

                        <th>
                            School Year
                        </th>

                        <th>
                            Receipt / Reference
                        </th>

                        <th>
                            Amount
                        </th>

                        <th>
                            Method
                        </th>

                        <th>
                            Date
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <tr
                        v-for="receipt in receipts"
                        :key="receipt.id"
                    >

                        <!-- STUDENT -->
                        <td>

                            <div class="student">

                                <div class="avatar">

                                    {{ getInitials(
                                        receipt.student_name
                                    ) }}

                                </div>


                                <div>

                                    <strong>
                                        {{
                                            receipt.student_name
                                            || "Unknown Student"
                                        }}
                                    </strong>

                                    <small>
                                        {{
                                            receipt.student_number
                                            || "N/A"
                                        }}
                                    </small>

                                </div>

                            </div>

                        </td>


                        <!-- COURSE -->
                        <td>

                            <strong>
                                {{
                                    receipt.course
                                    || "N/A"
                                }}
                            </strong>

                        </td>


                        <!-- YEAR -->
                        <td>

                            <strong>
                                {{
                                    receipt.year
                                    || "N/A"
                                }}
                            </strong>

                            <small
                                v-if="receipt.semester"
                                class="semester"
                            >
                                {{ receipt.semester }}
                            </small>

                        </td>


                        <!-- REFERENCE -->
                        <td>

                            <span class="reference">

                                {{
                                    receipt.payment_reference
                                    || "N/A"
                                }}

                            </span>

                        </td>


                        <!-- AMOUNT -->
                        <td>

                            <strong class="amount">

                                ₱{{
                                    formatAmount(
                                        receipt.amount
                                    )
                                }}

                            </strong>

                        </td>


                        <!-- METHOD -->
                        <td>

                            <span class="method">

                                <i
                                    :class="
                                        getPaymentIcon(
                                            receipt.payment_method
                                        )
                                    "
                                ></i>

                                {{
                                    receipt.payment_method
                                    || "GCash"
                                }}

                            </span>

                        </td>


                        <!-- DATE -->
                        <td>

                            {{
                                formatDate(
                                    receipt.created_at
                                )
                            }}

                        </td>


                        <!-- ACTION -->
                        <td>

                            <button
                                class="view-btn"
                                @click="viewReceipt(receipt)"
                            >

                                <i class="bi bi-eye"></i>

                                View

                            </button>

                        </td>

                    </tr>


                    <!-- EMPTY -->
                    <tr
                        v-if="receipts.length === 0"
                    >

                        <td
                            colspan="8"
                            class="empty"
                        >

                            <i class="bi bi-receipt"></i>

                            <strong>
                                No receipts found
                            </strong>

                            <span>
                                No payment receipts match
                                your search.
                            </span>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>


        <!-- PAGINATION -->
        <div
            v-if="lastPage > 1"
            class="pagination"
        >

            <button
                @click="loadReceipts(currentPage - 1)"
                :disabled="currentPage === 1"
            >
                <i class="bi bi-chevron-left"></i>
                Previous
            </button>


            <span>
                Page {{ currentPage }}
                of {{ lastPage }}
            </span>


            <button
                @click="loadReceipts(currentPage + 1)"
                :disabled="currentPage === lastPage"
            >
                Next
                <i class="bi bi-chevron-right"></i>
            </button>

        </div>

    </div>


    <!-- RECEIPT MODAL -->
    <div
        v-if="selectedReceipt"
        class="modal-overlay"
        @click.self="closeModal"
    >

        <div class="receipt-modal">

            <!-- RECEIPT -->
            <div class="print-area">

                <div class="receipt-header">

                    <div class="school-logo">
                        SFXC
                    </div>

                    <h2>
                        ST. FRANCIS XAVIER COLLEGE
                    </h2>

                    <p>
                        Official Payment Receipt
                    </p>

                </div>


                <div class="receipt-number">

                    <span>
                        Receipt / Reference
                    </span>

                    <strong>
                        {{
                            selectedReceipt.payment_reference
                            || "N/A"
                        }}
                    </strong>

                </div>


                <!-- STUDENT -->
                <div class="receipt-section">

                    <h4>
                        Student Information
                    </h4>

                    <div class="receipt-grid">

                        <div>
                            <span>
                                Student Name
                            </span>

                            <strong>
                                {{
                                    selectedReceipt.student_name
                                    || "N/A"
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Student Number
                            </span>

                            <strong>
                                {{
                                    selectedReceipt.student_number
                                    || "N/A"
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Course
                            </span>

                            <strong>
                                {{
                                    selectedReceipt.course
                                    || "N/A"
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                School Year
                            </span>

                            <strong>
                                {{
                                    selectedReceipt.year
                                    || "N/A"
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Semester
                            </span>

                            <strong>
                                {{
                                    selectedReceipt.semester
                                    || "N/A"
                                }}
                            </strong>
                        </div>

                    </div>

                </div>


                <!-- PAYMENT -->
                <div class="receipt-section">

                    <h4>
                        Payment Information
                    </h4>

                    <div class="receipt-grid">

                        <div>
                            <span>
                                Amount Paid
                            </span>

                            <strong class="receipt-amount">
                                ₱{{
                                    formatAmount(
                                        selectedReceipt.amount
                                    )
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Payment Method
                            </span>

                            <strong>
                                {{
                                    selectedReceipt.payment_method
                                    || "GCash"
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Payment Date
                            </span>

                            <strong>
                                {{
                                    formatDate(
                                        selectedReceipt.created_at
                                    )
                                }}
                            </strong>
                        </div>


                        <div>
                            <span>
                                Enrollment ID
                            </span>

                            <strong>
                                #{{ selectedReceipt.enrollment_id }}
                            </strong>
                        </div>

                    </div>

                </div>


                <div class="paid-stamp">

                    <i class="bi bi-check-circle-fill"></i>

                    PAID

                </div>


                <div class="receipt-footer">

                    This receipt serves as proof of payment
                    for the enrollment fee.

                </div>

            </div>


            <!-- MODAL ACTIONS -->
            <div class="modal-actions">

                <button
                    class="print-btn"
                    @click="printReceipt"
                >

                    <i class="bi bi-printer"></i>

                    Print Receipt

                </button>


                <button
                    class="close-btn"
                    @click="closeModal"
                >

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
    onMounted
} from "vue"

import api from "@/services/api"


/*
|--------------------------------------------------------------------------
| STATE
|--------------------------------------------------------------------------
*/

const receipts = ref([])

const loading = ref(false)

const errorMessage = ref("")

const search = ref("")

const dateFilter = ref("")

const selectedReceipt = ref(null)


/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

const currentPage = ref(1)

const lastPage = ref(1)

const totalReceipts = ref(0)


/*
|--------------------------------------------------------------------------
| SUMMARY
|--------------------------------------------------------------------------
*/

const todayReceipts = ref(0)

const totalCollection = ref(0)

const monthCollection = ref(0)


/*
|--------------------------------------------------------------------------
| LOAD RECEIPTS
|--------------------------------------------------------------------------
*/

async function loadReceipts(page = 1) {

    if (loading.value) return

    loading.value = true

    errorMessage.value = ""

    try {

        const response = await api.get(
            "/cashier/receipts",
            {
                params: {

                    page: page,

                    per_page: 10,

                    search:
                        search.value || undefined,

                    date:
                        dateFilter.value || undefined

                }
            }
        )


        receipts.value =
            response.data?.data || []


        currentPage.value =
            response.data?.current_page || 1


        lastPage.value =
            response.data?.last_page || 1


        totalReceipts.value =
            response.data?.total || 0


    } catch (error) {

        console.error(
            "Failed to load receipts:",
            error
        )

        errorMessage.value =
            error.response?.data?.message ||
            "Unable to load receipts."

        receipts.value = []

    } finally {

        loading.value = false

    }

}


/*
|--------------------------------------------------------------------------
| SEARCH
|--------------------------------------------------------------------------
*/

function searchReceipts() {

    loadReceipts(1)

}


/*
|--------------------------------------------------------------------------
| VIEW
|--------------------------------------------------------------------------
*/

function viewReceipt(receipt) {

    selectedReceipt.value =
        receipt

}


/*
|--------------------------------------------------------------------------
| CLOSE
|--------------------------------------------------------------------------
*/

function closeModal() {

    selectedReceipt.value =
        null

}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

function printReceipt() {

    window.print()

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
| AMOUNT
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
| DATE
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
| PAYMENT ICON
|--------------------------------------------------------------------------
*/

function getPaymentIcon(method) {

    const value =
        String(method || "")
            .toLowerCase()

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
| LOAD
|--------------------------------------------------------------------------
*/

onMounted(
    () => loadReceipts()
)

</script>


<style scoped>

.receipts-page {
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

.summary-icon.today {
    background: #dcfce7;
    color: #15803d;
}

.summary-icon.collection {
    background: #dbeafe;
    color: #2563eb;
}

.summary-icon.month {
    background: #fef3c7;
    color: #b45309;
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


/* CARD */

.receipt-card {
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
    width: 210px;
    border: 0;
    outline: 0;
    font-size: 13px;
}

.date-filter {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 7px 10px;
    outline: 0;
}


/* TABLE */

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 1000px;
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

.semester {
    display: block;
    margin-top: 3px;
    color: #9ca3af;
    font-size: 10px;
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


/* PAGINATION */

.pagination {
    padding: 15px 18px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.pagination button {
    border: 1px solid #d1d5db;
    background: white;
    color: #374151;
    padding: 7px 12px;
    border-radius: 7px;
    cursor: pointer;
}

.pagination button:hover:not(:disabled) {
    background: #ecfdf5;
    color: #166534;
}

.pagination button:disabled {
    opacity: .5;
    cursor: not-allowed;
}

.pagination span {
    color: #6b7280;
    font-size: 13px;
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

.receipt-modal {
    width: 100%;
    max-width: 650px;
    max-height: 90vh;
    overflow-y: auto;
    background: white;
    border-radius: 15px;
}


/* RECEIPT */

.print-area {
    padding: 30px;
}

.receipt-header {
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 2px dashed #d1d5db;
}

.school-logo {
    width: 55px;
    height: 55px;
    margin: 0 auto 8px;
    border-radius: 50%;
    background: #064e2a;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}

.receipt-header h2 {
    margin: 0;
    font-size: 18px;
    color: #064e2a;
}

.receipt-header p {
    margin: 4px 0 0;
    color: #6b7280;
    font-size: 12px;
}

.receipt-number {
    display: flex;
    justify-content: space-between;
    gap: 10px;
    padding: 15px 0;
    border-bottom: 1px solid #e5e7eb;
}

.receipt-number span {
    color: #6b7280;
    font-size: 12px;
}

.receipt-number strong {
    font-family: monospace;
    font-size: 12px;
}


/* DETAILS */

.receipt-section {
    padding: 18px 0;
    border-bottom: 1px solid #e5e7eb;
}

.receipt-section h4 {
    margin: 0 0 14px;
    font-size: 14px;
    color: #374151;
}

.receipt-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 15px;
}

.receipt-grid span {
    display: block;
    color: #9ca3af;
    font-size: 11px;
    margin-bottom: 4px;
}

.receipt-grid strong {
    font-size: 13px;
    color: #374151;
}

.receipt-amount {
    color: #166534 !important;
    font-size: 18px !important;
}


/* PAID */

.paid-stamp {
    margin: 20px auto;
    width: fit-content;
    padding: 7px 16px;
    border: 2px solid #166534;
    border-radius: 8px;
    color: #166534;
    font-weight: 800;
    transform: rotate(-3deg);
}

.paid-stamp i {
    margin-right: 5px;
}

.receipt-footer {
    text-align: center;
    color: #9ca3af;
    font-size: 11px;
    padding-top: 10px;
}


/* MODAL ACTIONS */

.modal-actions {
    padding: 15px 20px;
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    border-top: 1px solid #e5e7eb;
}

.print-btn {
    border: 0;
    background: #064e2a;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
}

.close-btn {
    border: 0;
    background: #f3f4f6;
    color: #374151;
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
}


/* MOBILE */

@media (max-width: 1100px) {

    .summary-grid {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 768px) {

    .page-header {
        align-items: flex-start;
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

    .date-filter {
        width: 100%;
    }

    .details-grid,
    .receipt-grid {
        grid-template-columns: 1fr;
    }

}

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

}

@media print {

    body * {
        visibility: hidden;
    }

    .print-area,
    .print-area * {
        visibility: visible;
    }

    .print-area {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    .modal-actions {
        display: none !important;
    }

}

</style>