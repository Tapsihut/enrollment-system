<template>
    <div class="receipt-page">

        <div
            class="receipt-card"
            ref="receiptArea"
        >

            <!-- HEADER -->
            <div class="receipt-header">

                <div class="logo">
                    🏫
                </div>

                <h2>
                    St. Francis Xavier College
                </h2>

                <p>
                    Official Enrollment Payment Receipt
                </p>

            </div>

            <!-- LOADING -->
            <div
                v-if="loading"
                class="loading"
            >
                <div class="spinner"></div>

                <p>
                    Generating receipt...
                </p>
            </div>

            <!-- RECEIPT -->
            <div
                v-else
                class="receipt-body"
            >

                <!-- SUCCESS -->
                <div class="success">

                    <div class="success-icon">
                        ✓
                    </div>

                    <h3>
                        Payment Successful
                    </h3>

                    <p class="success-text">
                        Your enrollment payment has been successfully recorded.
                    </p>

                    <div class="notice">

                        <strong>
                            Notice:
                        </strong>

                        This is not an official receipt (O.R.).
                        The official receipt will be issued after you submit
                        and complete all required original documents and your
                        enrollment requirements have been verified.

                    </div>

                </div>

                <!-- PAYMENT DETAILS -->
                <div class="receipt-info">

                    <div class="receipt-row">
                        <span>Student</span>

                        <strong>
                            {{ receipt.student || '—' }}
                        </strong>
                    </div>

                    <div class="receipt-row">
                        <span>Reference Number</span>

                        <strong class="reference">
                            {{ receipt.reference || '—' }}
                        </strong>
                    </div>

                    <div class="receipt-row">
                        <span>Payment Method</span>

                        <strong>
                            {{ receipt.payment_method || 'GCash' }}
                        </strong>
                    </div>

                    <div class="receipt-row">
                        <span>Amount Paid</span>

                        <strong class="amount">
                            ₱{{ formatAmount(receipt.amount) }}
                        </strong>
                    </div>

                    <div class="receipt-row status-row">
                        <span>Status</span>

                        <span class="badge">
                            {{ receipt.status || 'Paid' }}
                        </span>
                    </div>

                </div>

                <!-- PRINT -->
                <button
                    type="button"
                    class="print-btn"
                    @click="printReceipt"
                >
                    <i class="bi bi-printer"></i>
                    Print Receipt
                </button>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    onMounted,
    ref
} from "vue";

import api from "@/services/api";

const receiptArea = ref(null);

const receipt = ref({});

const loading = ref(true);


/*
|--------------------------------------------------------------------------
| LOAD RECEIPT
|--------------------------------------------------------------------------
*/

async function loadReceipt() {

    try {

        const response = await api.get(
            "/student/receipt"
        );

        receipt.value =
            response.data;

    } catch (error) {

        console.log(
            "RECEIPT ERROR:",
            error.response || error
        );

    } finally {

        loading.value = false;

    }

}


/*
|--------------------------------------------------------------------------
| FORMAT AMOUNT
|--------------------------------------------------------------------------
*/

function formatAmount(value) {

    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return "0.00";
    }

    return Number(value).toLocaleString(
        "en-US",
        {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }
    );

}


/*
|--------------------------------------------------------------------------
| PRINT
|--------------------------------------------------------------------------
*/

function printReceipt() {

    window.print();

}


onMounted(() => {

    loadReceipt();

});

</script>


<style scoped>

/* =========================================================
   PAGE
========================================================= */

.receipt-page {

    min-height: 100vh;

    display: flex;

    justify-content: center;

    align-items: center;

    padding: 20px;

    background:
        linear-gradient(
            135deg,
            #198754,
            #064d2b
        );

}


/* =========================================================
   CARD
========================================================= */

.receipt-card {

    width: 100%;

    max-width: 450px;

    background: #fff;

    border-radius: 18px;

    overflow: hidden;

    box-shadow:
        0 15px 35px rgba(0, 0, 0, .22);

}


/* =========================================================
   HEADER
========================================================= */

.receipt-header {

    background: #198754;

    color: #fff;

    text-align: center;

    padding: 24px 18px 22px;

}

.logo {

    width: 52px;

    height: 52px;

    margin: 0 auto 8px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: rgba(255,255,255,.14);

    border-radius: 50%;

    font-size: 27px;

}

.receipt-header h2 {

    margin: 0;

    font-size: 21px;

    font-weight: 800;

    line-height: 1.25;

}

.receipt-header p {

    margin: 5px 0 0;

    font-size: 12px;

    opacity: .9;

}


/* =========================================================
   BODY
========================================================= */

.receipt-body {

    padding: 22px;

}


/* =========================================================
   SUCCESS
========================================================= */

.success {

    text-align: center;

    margin-bottom: 18px;

}

.success-icon {

    width: 58px;

    height: 58px;

    margin: 0 auto;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #198754;

    color: #fff;

    font-size: 32px;

    font-weight: 700;

}

.success h3 {

    margin: 10px 0 3px;

    color: #198754;

    font-size: 20px;

    font-weight: 800;

}

.success-text {

    margin: 0;

    color: #777;

    font-size: 12px;

}


/* =========================================================
   NOTICE
========================================================= */

.notice {

    margin-top: 14px;

    padding: 11px 12px;

    background: #fff3cd;

    border-left: 4px solid #ffc107;

    border-radius: 8px;

    color: #664d03;

    font-size: 11.5px;

    line-height: 1.45;

    text-align: left;

}

.notice strong {

    font-weight: 800;

}


/* =========================================================
   RECEIPT INFORMATION
========================================================= */

.receipt-info {

    background: #f8f9fa;

    border: 1px solid #e9ecef;

    padding: 12px 14px;

    border-radius: 12px;

}

.receipt-row {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 12px;

    min-height: 42px;

    padding: 8px 0;

    border-bottom: 1px solid #dee2e6;

}

.receipt-row:last-child {

    border-bottom: none;

}

.receipt-row > span:first-child {

    flex: 0 0 auto;

    color: #777;

    font-size: 12px;

}

.receipt-row strong {

    min-width: 0;

    text-align: right;

    color: #333;

    font-size: 12.5px;

    overflow-wrap: anywhere;

}

.reference {

    font-size: 11px !important;

}

.amount {

    color: #198754 !important;

    font-size: 18px !important;

    font-weight: 800;

}

.badge {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 5px 12px;

    border-radius: 20px;

    background: #198754;

    color: #fff !important;

    font-size: 11px !important;

    font-weight: 700;

}


/* =========================================================
   PRINT BUTTON
========================================================= */

.print-btn {

    width: 100%;

    min-height: 42px;

    margin-top: 16px;

    border: none;

    border-radius: 9px;

    background: #198754;

    color: #fff;

    padding: 9px 14px;

    font-size: 13px;

    font-weight: 700;

    display: flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    cursor: pointer;

    transition: .2s ease;

}

.print-btn:hover {

    background: #146c43;

}

.print-btn:active {

    transform: scale(.98);

}


/* =========================================================
   LOADING
========================================================= */

.loading {

    text-align: center;

    padding: 45px 20px;

}

.loading p {

    margin: 12px 0 0;

    color: #777;

    font-size: 13px;

}

.spinner {

    width: 40px;

    height: 40px;

    margin: auto;

    border: 4px solid #ddd;

    border-top-color: #198754;

    border-radius: 50%;

    animation: spin 1s linear infinite;

}

@keyframes spin {

    from {
        transform: rotate(0deg);
    }

    to {
        transform: rotate(360deg);
    }

}


/* =========================================================
   TABLET
========================================================= */

@media (max-width: 600px) {

    .receipt-page {

        min-height: 100vh;

        padding: 10px;

        align-items: flex-start;

    }

    .receipt-card {

        margin-top: 8px;

        border-radius: 14px;

    }

    .receipt-header {

        padding: 20px 14px 18px;

    }

    .logo {

        width: 46px;

        height: 46px;

        font-size: 24px;

        margin-bottom: 7px;

    }

    .receipt-header h2 {

        font-size: 18px;

    }

    .receipt-header p {

        font-size: 11px;

    }

    .receipt-body {

        padding: 16px;

    }

    .success-icon {

        width: 52px;

        height: 52px;

        font-size: 28px;

    }

    .success h3 {

        font-size: 18px;

    }

    .notice {

        font-size: 11px;

        padding: 10px;

    }

    .receipt-info {

        padding: 10px 12px;

        border-radius: 10px;

    }

    .receipt-row {

        min-height: 40px;

        padding: 7px 0;

        gap: 8px;

    }

    .receipt-row > span:first-child {

        font-size: 11.5px;

    }

    .receipt-row strong {

        font-size: 11.5px;

    }

    .amount {

        font-size: 17px !important;

    }

    .print-btn {

        min-height: 40px;

        margin-top: 13px;

        font-size: 12.5px;

    }

}


/* =========================================================
   SMALL PHONES
========================================================= */

@media (max-width: 380px) {

    .receipt-page {

        padding: 6px;

    }

    .receipt-card {

        margin-top: 4px;

        border-radius: 12px;

    }

    .receipt-header {

        padding: 17px 10px;

    }

    .receipt-header h2 {

        font-size: 16px;

    }

    .receipt-header p {

        font-size: 10px;

    }

    .receipt-body {

        padding: 12px;

    }

    .success {

        margin-bottom: 14px;

    }

    .success-icon {

        width: 46px;

        height: 46px;

        font-size: 25px;

    }

    .success h3 {

        font-size: 16px;

    }

    .success-text {

        font-size: 11px;

    }

    .notice {

        margin-top: 10px;

        font-size: 10.5px;

    }

    .receipt-row {

        display: grid;

        grid-template-columns: 1fr auto;

    }

    .receipt-row strong {

        max-width: 190px;

    }

}


/* =========================================================
   PRINT
========================================================= */

@media print {

    .receipt-page {

        min-height: auto;

        padding: 0;

        background: #fff;

    }

    .receipt-card {

        max-width: 450px;

        margin: 0 auto;

        box-shadow: none;

        border-radius: 0;

    }

    .print-btn {

        display: none !important;

    }

    .receipt-header {

        print-color-adjust: exact;

        -webkit-print-color-adjust: exact;

    }

    .badge {

        print-color-adjust: exact;

        -webkit-print-color-adjust: exact;

    }

}

</style>
