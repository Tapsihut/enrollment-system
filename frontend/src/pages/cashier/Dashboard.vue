```vue
<template>
<div class="cashier-dashboard">

    <!-- HEADER -->
    <div class="welcome-card">
        <div class="welcome-content">
            <div class="header-icon">
                <i class="bi bi-cash-coin"></i>
            </div>

            <div>
                <h2>Cashier Dashboard</h2>
                <p>Monitor enrollment payments, collections, and receipts.</p>
            </div>
        </div>

        <div class="cashier-badge">
            <i class="bi bi-shield-check"></i>
            CASHIER
        </div>
    </div>


    <!-- ERROR -->
    <div v-if="errorMessage" class="alert alert-danger">
        <i class="bi bi-exclamation-triangle me-2"></i>
        {{ errorMessage }}

        <button
            class="btn btn-sm btn-outline-danger ms-2"
            @click="loadDashboard"
        >
            Retry
        </button>
    </div>


    <!-- STATISTICS -->
    <div class="row g-3 mb-4">

        <div
            v-for="stat in stats"
            :key="stat.label"
            class="col-xl-3 col-md-6"
        >
            <div class="stat-card" :class="stat.type">

                <div class="stat-icon">
                    <i :class="stat.icon"></i>
                </div>

                <div class="stat-info">
                    <small>{{ stat.label }}</small>

                    <h3>
                        {{ stat.money ? '₱' : '' }}{{ stat.value }}
                    </h3>

                    <span>{{ stat.description }}</span>
                </div>

            </div>
        </div>

    </div>


    <!-- SERVICES -->
    <div class="section-title">
        <h5>Cashier Services</h5>
        <p>Monitor and manage payment records.</p>
    </div>


    <div class="row g-3 mb-4">

        <div
            v-for="service in services"
            :key="service.title"
            class="col-lg-4 col-md-6"
        >
            <div class="action-card">

                <div
                    class="action-icon"
                    :class="service.type"
                >
                    <i :class="service.icon"></i>
                </div>

                <div>
                    <h5>{{ service.title }}</h5>

                    <p>{{ service.description }}</p>

                    <router-link
                        :to="service.route"
                        class="btn"
                        :class="service.button"
                    >
                        {{ service.buttonText }}
                        <i class="bi bi-arrow-right ms-1"></i>
                    </router-link>
                </div>

            </div>
        </div>

    </div>


    <!-- RECENT TRANSACTIONS -->
    <div class="transaction-card">

        <div class="transaction-header">
            <div>
                <h5>Recent Payment Transactions</h5>
                <p>Latest enrollment payment activity</p>
            </div>

            <router-link
                to="/cashier/payments"
                class="view-all"
            >
                View All
                <i class="bi bi-arrow-right"></i>
            </router-link>
        </div>


        <div class="table-responsive">

            <table class="table align-middle mb-0">

                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Student</th>
                        <th>Course</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                    <!-- LOADING -->
                    <tr v-if="loading">
                        <td colspan="7" class="loading-state">
                            <div class="spinner-border spinner-border-sm text-success"></div>
                            <span>Loading transactions...</span>
                        </td>
                    </tr>


                    <!-- DATA -->
                    <tr
                        v-for="payment in recentPayments"
                        :key="payment.id"
                        v-else
                    >

                        <td>
                            <span class="reference-number">
                                {{ payment.payment_reference || 'N/A' }}
                            </span>
                        </td>


                        <td>
                            <div class="student-cell">

                                <div class="avatar">
                                    {{ getInitials(payment.student) }}
                                </div>

                                <strong>
                                    {{ getStudentName(payment.student) }}
                                </strong>

                            </div>
                        </td>


                        <td>
                            <span class="course-code">
                                {{ getCourseCode(payment) }}
                            </span>
                        </td>


                        <td>
                            <strong class="amount">
                                ₱{{ formatAmount(payment.amount) }}
                            </strong>
                        </td>


                        <td>
                            <span class="payment-method">
                                <i :class="getPaymentIcon(payment.payment_method)"></i>
                                {{ payment.payment_method || 'N/A' }}
                            </span>
                        </td>


                        <td>
                            <span
                                class="status-badge"
                                :class="getStatusClass(payment.status)"
                            >
                                <i :class="getStatusIcon(payment.status)"></i>
                                {{ payment.status || 'Unknown' }}
                            </span>
                        </td>


                        <td>
                            <span class="transaction-date">
                                {{ formatDate(payment.created_at) }}
                            </span>
                        </td>

                    </tr>


                    <!-- EMPTY -->
                    <tr v-if="!loading && recentPayments.length === 0">
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-receipt"></i>

                            <strong>
                                No payment transactions
                            </strong>

                            <p>
                                Payment transactions will appear here.
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
import { ref, computed, onMounted } from "vue"
import api from "@/services/api"

const loading = ref(false)
const errorMessage = ref("")

const statistics = ref({
    total: 0,
    pending: 0,
    paid: 0,
    today_collection: 0
})

const recentPayments = ref([])


/* STATISTICS */

const stats = computed(() => [
    {
        label: "Total Payments",
        value: statistics.value.total,
        description: "All transactions",
        icon: "bi bi-wallet2",
        type: "total"
    },
    {
        label: "Pending Payments",
        value: statistics.value.pending,
        description: "Awaiting payment",
        icon: "bi bi-clock-history",
        type: "pending"
    },
    {
        label: "Paid",
        value: statistics.value.paid,
        description: "Completed payments",
        icon: "bi bi-check-circle",
        type: "paid"
    },
    {
        label: "Today's Collection",
        value: formatAmount(statistics.value.today_collection),
        description: "Today's paid transactions",
        icon: "bi bi-cash-stack",
        type: "collection",
        money: true
    }
])


/* SERVICES */

const services = [
    {
        title: "Payment Transactions",
        description: "View enrollment payment transactions and their current status.",
        route: "/cashier/payments",
        icon: "bi bi-credit-card",
        button: "btn-success",
        buttonText: "View Payments",
        type: ""
    },
    {
        title: "Payment Receipts",
        description: "View completed payment records and generate receipts.",
        route: "/cashier/receipts",
        icon: "bi bi-receipt",
        button: "btn-outline-success",
        buttonText: "View Receipts",
        type: "receipt"
    },
    {
        title: "Payment Reports",
        description: "Review payment collections and transaction reports.",
        route: "/cashier/reports",
        icon: "bi bi-bar-chart",
        button: "btn-outline-success",
        buttonText: "View Reports",
        type: "report"
    }
]


/* LOAD DASHBOARD */

async function loadDashboard() {

    loading.value = true
    errorMessage.value = ""

    try {

        const { data } = await api.get("/cashier/dashboard")

        statistics.value = {
            total: Number(data.statistics?.total || 0),
            pending: Number(data.statistics?.pending || 0),
            paid: Number(data.statistics?.paid || 0),
            today_collection: Number(
                data.statistics?.today_collection || 0
            )
        }

        recentPayments.value =
            Array.isArray(data.recent)
                ? data.recent
                : []

    } catch (error) {

        console.error("Cashier dashboard error:", error)

        errorMessage.value =
            error.response?.data?.message ||
            "Unable to load cashier dashboard."

    } finally {

        loading.value = false

    }
}


/* STUDENT NAME */

function getStudentName(student) {

    if (!student) return "Unknown Student"

    const first =
        student.first_name ||
        student.firstname ||
        ""

    const last =
        student.last_name ||
        student.lastname ||
        ""

    return `${first} ${last}`.trim() || "Unknown Student"
}


/* INITIALS */

function getInitials(student) {

    if (!student) return "?"

    const first =
        student.first_name ||
        student.firstname ||
        ""

    const last =
        student.last_name ||
        student.lastname ||
        ""

    return (
        `${first.charAt(0)}${last.charAt(0)}`
    ).toUpperCase() || "?"
}


/* COURSE */

function getCourseCode(payment) {

    return (
        payment?.course?.code ||
        payment?.enrollment?.course?.code ||
        payment?.enrollment?.course_code ||
        "N/A"
    )
}


/* AMOUNT */

function formatAmount(amount) {

    return Number(amount || 0).toLocaleString(
        "en-PH",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    )
}


/* DATE */

function formatDate(date) {

    if (!date) return "N/A"

    const parsed = new Date(date)

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


/* STATUS */

function getStatusClass(status) {

    const value =
        String(status || "").toLowerCase()

    return {
        paid: "status-paid",
        pending: "status-pending",
        failed: "status-failed"
    }[value] || "status-unknown"
}


function getStatusIcon(status) {

    const value =
        String(status || "").toLowerCase()

    return {
        paid: "bi bi-check-circle-fill",
        pending: "bi bi-clock-fill",
        failed: "bi bi-x-circle-fill"
    }[value] || "bi bi-question-circle-fill"
}


/* PAYMENT METHOD */

function getPaymentIcon(method) {

    const value =
        String(method || "").toLowerCase()

    if (value.includes("gcash"))
        return "bi bi-phone"

    if (value.includes("cash"))
        return "bi bi-cash"

    if (value.includes("card"))
        return "bi bi-credit-card"

    return "bi bi-wallet2"
}


onMounted(loadDashboard)
</script>


<style scoped>

/* MAIN */

.cashier-dashboard {
    width: 100%;
    box-sizing: border-box;
    padding: 20px;
    overflow-x: hidden;
}


/* HEADER */

.welcome-card {
    background: linear-gradient(135deg,#064e2a,#0b6b3a);
    color: white;
    padding: 24px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    margin-bottom: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,.1);
}

.welcome-content {
    display: flex;
    align-items: center;
    gap: 14px;
}

.header-icon {
    width: 52px;
    height: 52px;
    min-width: 52px;
    border-radius: 14px;
    background: rgba(255,255,255,.15);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.welcome-card h2 {
    margin: 0 0 4px;
    font-size: 23px;
    font-weight: 800;
}

.welcome-card p {
    margin: 0;
    font-size: 13px;
    opacity: .8;
}

.cashier-badge {
    padding: 9px 15px;
    border-radius: 20px;
    background: rgba(255,255,255,.15);
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}


/* SECTION */

.section-title {
    margin: 5px 0 12px;
}

.section-title h5 {
    margin: 0;
    font-weight: 700;
}

.section-title p {
    margin: 3px 0 0;
    color: #6b7280;
    font-size: 13px;
}


/* STATISTICS */

.stat-card {
    height: 100%;
    padding: 18px;
    background: white;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 13px;
    box-shadow: 0 4px 16px rgba(0,0,0,.05);
}

.stat-icon {
    width: 48px;
    height: 48px;
    min-width: 48px;
    border-radius: 13px;
    background: #e7f7ee;
    color: #0b6b3a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}

.stat-card.pending .stat-icon {
    background: #fff7df;
    color: #c28a00;
}

.stat-card.paid .stat-icon {
    background: #dcfce7;
    color: #15803d;
}

.stat-card.collection .stat-icon {
    background: #e0f2fe;
    color: #0369a1;
}

.stat-info {
    min-width: 0;
}

.stat-info small {
    color: #6b7280;
    font-size: 12px;
}

.stat-info h3 {
    margin: 2px 0;
    color: #064e2a;
    font-weight: 800;
    font-size: 22px;
}

.stat-info span {
    color: #9ca3af;
    font-size: 10px;
}


/* SERVICES */

.action-card {
    height: 100%;
    padding: 20px;
    background: white;
    border-radius: 15px;
    display: flex;
    gap: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,.05);
}

.action-icon {
    width: 45px;
    height: 45px;
    min-width: 45px;
    border-radius: 12px;
    background: #e7f7ee;
    color: #0b6b3a;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.action-icon.receipt {
    background: #ecfdf5;
    color: #047857;
}

.action-icon.report {
    background: #eff6ff;
    color: #2563eb;
}

.action-card h5 {
    font-size: 16px;
    margin: 0 0 6px;
}

.action-card p {
    color: #6b7280;
    font-size: 12px;
    line-height: 1.5;
    margin-bottom: 12px;
}


/* TRANSACTIONS */

.transaction-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 16px rgba(0,0,0,.05);
}

.transaction-header {
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    border-bottom: 1px solid #eee;
}

.transaction-header h5 {
    margin: 0;
    font-size: 16px;
}

.transaction-header p {
    margin: 3px 0 0;
    color: #6b7280;
    font-size: 12px;
}

.view-all {
    color: #0b6b3a;
    text-decoration: none;
    font-weight: 600;
    font-size: 13px;
    white-space: nowrap;
}


/* TABLE */

.transaction-card table {
    min-width: 900px;
}

.table th {
    background: #f8faf9;
    font-size: 11px;
    white-space: nowrap;
    padding: 11px 14px;
}

.table td {
    padding: 12px 14px;
    white-space: nowrap;
    font-size: 13px;
}

.reference-number,
.course-code {
    font-weight: 600;
    color: #374151;
}

.student-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.avatar {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 50%;
    background: #0b6b3a;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 700;
}

.amount {
    color: #064e2a;
}

.payment-method {
    color: #374151;
}

.payment-method i {
    color: #0b6b3a;
    margin-right: 4px;
}

.transaction-date {
    color: #6b7280;
}


/* STATUS */

.status-badge {
    padding: 5px 9px;
    border-radius: 15px;
    font-size: 10px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 4px;
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

.status-unknown {
    background: #f3f4f6;
    color: #6b7280;
}


/* LOADING */

.loading-state {
    text-align: center;
    padding: 30px !important;
    color: #6b7280;
}

.loading-state span {
    margin-left: 8px;
    font-size: 13px;
}


/* EMPTY */

.empty-state {
    text-align: center;
    padding: 40px !important;
    color: #9ca3af;
}

.empty-state i {
    display: block;
    font-size: 28px;
    margin-bottom: 8px;
}

.empty-state strong {
    display: block;
    color: #6b7280;
}

.empty-state p {
    margin: 4px 0 0;
    font-size: 12px;
}


/* TABLET */

@media (max-width: 992px) {

    .cashier-dashboard {
        padding: 15px;
    }

    .welcome-card {
        padding: 20px;
    }

}


/* MOBILE */

@media (max-width: 768px) {

    .welcome-card {
        align-items: flex-start;
    }

    .cashier-badge {
        display: none;
    }

    .welcome-card h2 {
        font-size: 20px;
    }

    .welcome-card p {
        font-size: 12px;
    }

    .transaction-header {
        align-items: flex-start;
    }

}


/* SMALL MOBILE */

@media (max-width: 576px) {

    .cashier-dashboard {
        padding: 10px;
    }

    .welcome-card {
        padding: 18px;
    }

    .header-icon {
        display: none;
    }

    .welcome-card h2 {
        font-size: 18px;
    }

    .transaction-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .stat-card {
        padding: 15px;
    }

    .action-card {
        padding: 16px;
    }

}

</style>
