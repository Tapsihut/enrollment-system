<script setup>
import { ref, onMounted } from "vue"
import axios from "@/services/api"

const loading = ref(true)
const paymongoLoading = ref(false)
const securityBankLoading = ref(false)
const showQr = ref(false)

const profileComplete = ref(false)
const enrollmentExists = ref(false)
const canPay = ref(false)

const enrollmentId = ref(null)
const enrollmentStatus = ref("No Enrollment")
const paymentStatus = ref("Not Available")

const amount = ref(1500)
const paymentReference = ref("")
const paymentProvider = ref("")

const qrData = ref("")
const qrId = ref("")
const transactionId = ref("")

const rejectionReason = ref("")

const alert = ref({
    show: false,
    type: "",
    message: ""
})

function showAlert(type, message) {
    alert.value = {
        show: true,
        type,
        message
    }

    setTimeout(() => {
        alert.value.show = false
    }, 4000)
}

async function loadPayment() {
    loading.value = true

    try {
        const response = await axios.get("/student/payment/info")
        const data = response.data

        console.log("Payment info:", data)

        profileComplete.value = !!data.profile_complete
        enrollmentExists.value = !!data.enrollment_exists
        canPay.value = !!data.can_pay

        enrollmentId.value = data.enrollment?.id ?? null
        enrollmentStatus.value =
            data.enrollment?.status ?? "No Enrollment"

        paymentStatus.value =
            data.payment?.status ?? "Not Available"

        amount.value =
            data.payment?.amount ?? 1500

        paymentReference.value =
            data.payment?.payment_reference ?? ""

        paymentProvider.value =
            data.payment?.payment_provider ?? ""

        qrId.value =
            data.payment?.security_bank_qr_id ?? ""

        transactionId.value =
            data.payment?.security_bank_transaction_id ?? ""

        rejectionReason.value =
            data.rejection_reason ?? ""

    } catch (error) {
        console.error("Payment info error:", error)

        showAlert(
            "error",
            error.response?.data?.message ||
            "Unable to load payment information."
        )
    } finally {
        loading.value = false
    }
}

async function payWithPayMongo() {
    if (!enrollmentId.value) {
        showAlert("error", "No enrollment found.")
        return
    }

    if (!profileComplete.value) {
        showAlert("error", "Please complete your profile first.")
        return
    }

    if (!enrollmentExists.value) {
        showAlert("error", "Please complete your enrollment first.")
        return
    }

    if (enrollmentStatus.value !== "Pending") {
        showAlert(
            "error",
            "Payment is not available for this enrollment."
        )
        return
    }

    paymongoLoading.value = true

    try {
        const response = await axios.post(
            `/student/payment/create/${enrollmentId.value}`
        )

        const data = response.data

        console.log("PayMongo response:", data)

        const checkoutUrl =
            data.checkout_url ||
            data.checkoutUrl ||
            data.url

        if (!checkoutUrl) {
            console.error(
                "PayMongo checkout URL missing:",
                data
            )

            showAlert(
                "error",
                "PayMongo checkout URL was not returned."
            )

            return
        }

        paymentProvider.value = "PayMongo"

        window.location.href = checkoutUrl

    } catch (error) {
        console.error(
            "PayMongo payment error:",
            error
        )

        showAlert(
            "error",
            error.response?.data?.message ||
            error.response?.data?.error ||
            "Unable to create PayMongo checkout."
        )
    } finally {
        paymongoLoading.value = false
    }
}

async function generateSecurityBankQr() {
    if (!enrollmentId.value) {
        showAlert("error", "No enrollment found.")
        return
    }

    if (!profileComplete.value) {
        showAlert("error", "Please complete your profile first.")
        return
    }

    if (!enrollmentExists.value) {
        showAlert("error", "Please complete your enrollment first.")
        return
    }

    if (enrollmentStatus.value !== "Pending") {
        showAlert(
            "error",
            "Payment is not available for this enrollment."
        )
        return
    }

    securityBankLoading.value = true

    try {
        const response = await axios.post(
            `/student/payment/security-bank/${enrollmentId.value}`
        )

        const data = response.data

        console.log(
            "Security Bank response:",
            data
        )

        qrData.value =
            data.qr?.qr_data ||
            data.qr?.qr_url ||
            data.qr?.data ||
            data.qr_data ||
            ""

        qrId.value =
            data.qr?.qr_id ||
            data.qr_id ||
            ""

        transactionId.value =
            data.qr?.transaction_id ||
            data.transaction_id ||
            ""

        paymentReference.value =
            data.qr?.reference ||
            data.reference ||
            data.payment_reference ||
            ""

        paymentProvider.value = "Security Bank"

        showQr.value = true

        showAlert(
            "success",
            "Security Bank QR generated successfully."
        )

    } catch (error) {
        console.error(
            "Security Bank QR error:",
            error
        )

        showAlert(
            "error",
            error.response?.data?.message ||
            "Unable to generate Security Bank QR."
        )
    } finally {
        securityBankLoading.value = false
    }
}

function goToProfile() {
    window.location.href = "/student/profile"
}

function goToEnrollment() {
    window.location.href = "/student/enrollment"
}

function closeQr() {
    showQr.value = false
}

onMounted(loadPayment)
</script>

<template>
<div class="payment-page">

    <div class="payment-card">

        <!-- ================= HEADER ================= -->

        <div class="page-header">

            <div class="header-icon">
                <i class="bi bi-credit-card-2-front-fill"></i>
            </div>

            <div>
                <h3>Enrollment Payment</h3>

                <p>
                    Complete your enrollment payment securely.
                </p>
            </div>

        </div>


        <!-- ================= ALERT ================= -->

        <transition name="slide">

            <div
                v-if="alert.show"
                class="alert-box"
                :class="alert.type"
            >

                <i
                    v-if="alert.type === 'success'"
                    class="bi bi-check-circle-fill"
                ></i>

                <i
                    v-else
                    class="bi bi-exclamation-circle-fill"
                ></i>

                <span>{{ alert.message }}</span>

            </div>

        </transition>


        <!-- ================= LOADING ================= -->

        <div
            v-if="loading"
            class="loading-box"
        >

            <div class="spinner-border"></div>

            <p>
                Loading payment information...
            </p>

        </div>


        <div v-else>

            <!-- ================= PROFILE ================= -->

            <div
                v-if="!profileComplete"
                class="status-box warning"
            >

                <div class="status-icon">
                    <i class="bi bi-person-fill"></i>
                </div>

                <div class="status-content">

                    <h4>
                        Complete Your Profile
                    </h4>

                    <p>
                        Please complete your student profile
                        before making an enrollment payment.
                    </p>

                    <button
                        class="primary-btn"
                        @click="goToProfile"
                    >
                        <i class="bi bi-person-check"></i>
                        Complete Profile
                    </button>

                </div>

            </div>


            <!-- ================= NO ENROLLMENT ================= -->

            <div
                v-else-if="!enrollmentExists"
                class="status-box warning"
            >

                <div class="status-icon">
                    <i class="bi bi-file-earmark-text-fill"></i>
                </div>

                <div class="status-content">

                    <h4>
                        Complete Your Enrollment
                    </h4>

                    <p>
                        Please submit your enrollment
                        before making a payment.
                    </p>

                    <button
                        class="primary-btn"
                        @click="goToEnrollment"
                    >
                        <i class="bi bi-journal-plus"></i>
                        Go to Enrollment
                    </button>

                </div>

            </div>


            <!-- ================= ENROLLMENT EXISTS ================= -->

            <div v-else>

                <!-- ================= AMOUNT ================= -->

                <div class="amount-box">

                    <div class="amount-label">
                        <span>Enrollment Fee</span>
                        <small>Required payment</small>
                    </div>

                    <strong>
                        ₱{{ Number(amount).toLocaleString(
                            "en-PH",
                            {
                                minimumFractionDigits: 2
                            }
                        ) }}
                    </strong>

                </div>


                <!-- ================= PAYMENT OPTIONS ================= -->

                <div
                    v-if="
                        canPay &&
                        enrollmentStatus === 'Pending' &&
                        paymentStatus !== 'Paid'
                    "
                    class="payment-options"
                >

                    <div class="section-heading">

                        <div class="section-icon">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <div>
                            <h5>Choose Payment Method</h5>
                            <p>
                                Select your preferred payment option.
                            </p>
                        </div>

                    </div>


                    <!-- ================= SECURITY BANK ================= -->

                    <div class="payment-method">

                        <div class="method-icon qr">
                            <i class="bi bi-qr-code"></i>
                        </div>

                        <div class="method-content">

                            <div class="method-header">

                                <div>
                                    <h4>
                                        Security Bank QR
                                    </h4>

                                    <span class="method-badge">
                                        QR Payment
                                    </span>
                                </div>

                            </div>

                            <p>
                                Scan the QR code using a supported
                                banking or e-wallet application.
                            </p>

                            <button
                                class="pay-btn"
                                @click="generateSecurityBankQr"
                                :disabled="securityBankLoading"
                            >

                                <span
                                    v-if="securityBankLoading"
                                    class="spinner-border spinner-border-sm"
                                ></span>

                                <i
                                    v-else
                                    class="bi bi-qr-code"
                                ></i>

                                {{
                                    securityBankLoading
                                        ? "Generating QR..."
                                        : "Generate Security Bank QR"
                                }}

                            </button>

                        </div>

                    </div>


                    <!-- ================= PAYMONGO ================= -->

                    <div class="payment-method">

                        <div class="method-icon paymongo">
                            <i class="bi bi-wallet2"></i>
                        </div>

                        <div class="method-content">

                            <div class="method-header">

                                <div>
                                    <h4>
                                        PayMongo
                                    </h4>

                                    <span class="method-badge">
                                        GCash
                                    </span>
                                </div>

                            </div>

                            <p>
                                Continue with PayMongo and complete
                                your payment using GCash.
                            </p>

                            <button
                                class="secondary-btn"
                                @click="payWithPayMongo"
                                :disabled="paymongoLoading"
                            >

                                <span
                                    v-if="paymongoLoading"
                                    class="spinner-border spinner-border-sm"
                                ></span>

                                <i
                                    v-else
                                    class="bi bi-credit-card"
                                ></i>

                                {{
                                    paymongoLoading
                                        ? "Opening PayMongo..."
                                        : "Pay with PayMongo"
                                }}

                            </button>

                        </div>

                    </div>

                </div>


                <!-- ================= PAID ================= -->

                <div
                    v-else-if="
                        paymentStatus === 'Paid' ||
                        enrollmentStatus === 'Paid'
                    "
                    class="status-box success"
                >

                    <div class="status-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                    <div class="status-content">

                        <h4>
                            Payment Successful
                        </h4>

                        <p>
                            Your enrollment payment has been
                            successfully received.
                        </p>

                        <div
                            v-if="paymentReference"
                            class="reference"
                        >
                            <span>Reference</span>

                            <strong>
                                {{ paymentReference }}
                            </strong>
                        </div>

                    </div>

                </div>


                <!-- ================= PROCESSING ================= -->

                <div
                    v-else-if="
                        enrollmentStatus === 'Processing'
                    "
                    class="status-box info"
                >

                    <div class="status-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>

                    <div class="status-content">

                        <h4>
                            Enrollment Processing
                        </h4>

                        <p>
                            Your payment has been received.
                            The college is now processing
                            your enrollment.
                        </p>

                        <strong class="working-days">
                            <i class="bi bi-clock"></i>
                            Please allow 1–2 working days.
                        </strong>

                    </div>

                </div>


                <!-- ================= COMPLETED ================= -->

                <div
                    v-else-if="
                        enrollmentStatus === 'Completed'
                    "
                    class="status-box success"
                >

                    <div class="status-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>

                    <div class="status-content">

                        <h4>
                            Enrollment Completed
                        </h4>

                        <p>
                            Your enrollment has been completed.
                        </p>

                        <p>
                            Your study load and payment receipt
                            will be sent to your registered
                            email address.
                        </p>

                    </div>

                </div>


                <!-- ================= REJECTED ================= -->

                <div
                    v-else-if="
                        enrollmentStatus === 'Rejected'
                    "
                    class="status-box danger"
                >

                    <div class="status-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>

                    <div class="status-content">

                        <h4>
                            Enrollment Rejected
                        </h4>

                        <p>
                            Your enrollment has been rejected.
                        </p>

                        <div
                            v-if="rejectionReason"
                            class="rejection-reason"
                        >

                            <strong>
                                Reason
                            </strong>

                            <p>
                                {{ rejectionReason }}
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ================================================= -->
    <!-- SECURITY BANK QR MODAL -->
    <!-- ================================================= -->

    <div
        v-if="showQr"
        class="qr-overlay"
        @click.self="closeQr"
    >

        <div class="qr-modal">

            <button
                class="close-btn"
                @click="closeQr"
                aria-label="Close"
            >
                <i class="bi bi-x-lg"></i>
            </button>


            <div class="qr-header">

                <div class="qr-icon">
                    <i class="bi bi-qr-code"></i>
                </div>

                <h3>
                    Security Bank QR
                </h3>

                <p>
                    Scan this QR code to pay your
                    enrollment fee.
                </p>

            </div>


            <!-- QR -->

            <div class="qr-container">

                <img
                    v-if="
                        qrData &&
                        (
                            qrData.startsWith('http://') ||
                            qrData.startsWith('https://') ||
                            qrData.startsWith('data:image')
                        )
                    "
                    :src="qrData"
                    class="qr-image"
                    alt="Security Bank QR"
                >

                <div
                    v-else
                    class="qr-placeholder"
                >

                    <i class="bi bi-qr-code"></i>

                    <p>
                        QR data received.
                    </p>

                    <small>
                        {{ qrData }}
                    </small>

                </div>

            </div>


            <!-- PAYMENT INFORMATION -->

            <div class="qr-payment-info">

                <div>
                    <span>Amount</span>

                    <strong>
                        ₱{{ Number(amount).toLocaleString(
                            "en-PH",
                            {
                                minimumFractionDigits: 2
                            }
                        ) }}
                    </strong>
                </div>

                <div>
                    <span>Reference</span>

                    <strong>
                        {{ paymentReference || "—" }}
                    </strong>
                </div>

            </div>


            <!-- NOTICE -->

            <div class="qr-notice">

                <i class="bi bi-info-circle-fill"></i>

                <span>
                    After completing your payment,
                    wait for the payment status to be
                    confirmed.
                </span>

            </div>


            <!-- CHECK PAYMENT -->

            <button
                class="secondary-btn full"
                @click="loadPayment"
                :disabled="loading"
            >

                <span
                    v-if="loading"
                    class="spinner-border spinner-border-sm"
                ></span>

                <i
                    v-else
                    class="bi bi-arrow-clockwise"
                ></i>

                {{
                    loading
                        ? "Checking..."
                        : "Check Payment Status"
                }}

            </button>

        </div>

    </div>

</div>
</template>

<style scoped>

/* =========================================================
   PAGE
========================================================= */

.payment-page{
    width:100%;
    padding:6px 8px;
}

.payment-card{
    width:100%;
    max-width:850px;
    margin:0 auto;
    background:#fff;
    border:1px solid #E5E7EB;
    border-radius:16px;
    padding:16px;
    box-shadow:0 5px 18px rgba(0,0,0,.05);
}


/* =========================================================
   HEADER
========================================================= */

.page-header{
    display:flex;
    align-items:center;
    gap:10px;
    padding-bottom:12px;
    border-bottom:1px solid #E5E7EB;
}

.header-icon{
    width:40px;
    height:40px;
    min-width:40px;
    border-radius:11px;
    background:#E9F5EF;
    color:#0B6B3A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.page-header h3{
    margin:0;
    color:#064E2A;
    font-size:20px;
    font-weight:800;
}

.page-header p{
    margin:2px 0 0;
    color:#6B7280;
    font-size:12px;
}


/* =========================================================
   ALERT
========================================================= */

.alert-box{
    margin-top:10px;
    padding:9px 12px;
    border-radius:10px;
    display:flex;
    align-items:center;
    gap:8px;
    font-size:12px;
    font-weight:600;
}

.alert-box.success{
    background:#DCFCE7;
    color:#166534;
}

.alert-box.error{
    background:#FEE2E2;
    color:#991B1B;
}


/* =========================================================
   LOADING
========================================================= */

.loading-box{
    min-height:220px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    color:#6B7280;
}

.loading-box .spinner-border{
    width:30px;
    height:30px;
    color:#0B6B3A;
}

.loading-box p{
    margin:10px 0 0;
    font-size:12px;
}


/* =========================================================
   STATUS BOX
========================================================= */

.status-box{
    display:flex;
    gap:12px;
    margin-top:14px;
    padding:14px;
    border-radius:13px;
    border:1px solid transparent;
}

.status-icon{
    width:38px;
    height:38px;
    min-width:38px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.status-content{
    min-width:0;
    flex:1;
}

.status-box h4{
    margin:0 0 4px;
    font-size:15px;
    font-weight:800;
}

.status-box p{
    margin:0 0 8px;
    font-size:12px;
    line-height:1.5;
}

.status-box.warning{
    background:#FFF7ED;
    color:#9A3412;
    border-color:#FED7AA;
}

.status-box.warning .status-icon{
    background:#FFEDD5;
    color:#EA580C;
}

.status-box.success{
    background:#F0FDF4;
    color:#166534;
    border-color:#BBF7D0;
}

.status-box.success .status-icon{
    background:#DCFCE7;
    color:#16A34A;
}

.status-box.info{
    background:#EFF6FF;
    color:#1E40AF;
    border-color:#BFDBFE;
}

.status-box.info .status-icon{
    background:#DBEAFE;
    color:#2563EB;
}

.status-box.danger{
    background:#FEF2F2;
    color:#991B1B;
    border-color:#FECACA;
}

.status-box.danger .status-icon{
    background:#FEE2E2;
    color:#DC2626;
}


/* =========================================================
   BUTTONS
========================================================= */

.primary-btn,
.pay-btn,
.secondary-btn{
    min-height:38px;
    border:none;
    border-radius:9px;
    padding:8px 13px;
    font-size:12px;
    font-weight:700;
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:7px;
    transition:.2s;
    cursor:pointer;
}

.primary-btn,
.pay-btn{
    background:#0B6B3A;
    color:#fff;
}

.primary-btn:hover,
.pay-btn:hover{
    background:#064E2A;
}

.secondary-btn{
    background:#E9F5EF;
    color:#064E2A;
}

.secondary-btn:hover{
    background:#D8EEE2;
}

.primary-btn:disabled,
.pay-btn:disabled,
.secondary-btn:disabled{
    opacity:.65;
    cursor:not-allowed;
}

.secondary-btn.full{
    width:100%;
}


/* =========================================================
   AMOUNT
========================================================= */

.amount-box{
    margin-top:14px;
    padding:13px 14px;
    border-radius:12px;
    background:#F7FAF8;
    border:1px solid #E5EEE8;
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:15px;
}

.amount-label{
    display:flex;
    flex-direction:column;
}

.amount-box span{
    color:#374151;
    font-size:13px;
    font-weight:700;
}

.amount-box small{
    color:#9CA3AF;
    font-size:10px;
    margin-top:2px;
}

.amount-box strong{
    color:#064E2A;
    font-size:22px;
    white-space:nowrap;
}


/* =========================================================
   PAYMENT OPTIONS
========================================================= */

.payment-options{
    margin-top:14px;
}

.section-heading{
    display:flex;
    align-items:center;
    gap:9px;
    margin-bottom:9px;
}

.section-icon{
    width:32px;
    height:32px;
    min-width:32px;
    border-radius:9px;
    background:#E9F5EF;
    color:#0B6B3A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
}

.section-heading h5{
    margin:0;
    color:#064E2A;
    font-size:14px;
    font-weight:800;
}

.section-heading p{
    margin:1px 0 0;
    color:#6B7280;
    font-size:10px;
}


/* =========================================================
   PAYMENT METHOD
========================================================= */

.payment-method{
    display:flex;
    gap:11px;
    padding:12px;
    border:1px solid #E5E7EB;
    border-radius:12px;
    margin-bottom:9px;
    transition:.2s;
}

.payment-method:hover{
    border-color:#B7D9C5;
    background:#FCFEFD;
}

.method-icon{
    width:40px;
    height:40px;
    min-width:40px;
    border-radius:10px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:17px;
}

.method-icon.qr{
    background:#E9F5EF;
    color:#064E2A;
}

.method-icon.paymongo{
    background:#EFF6FF;
    color:#2563EB;
}

.method-content{
    flex:1;
    min-width:0;
}

.method-header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:8px;
}

.method-content h4{
    margin:0;
    color:#1F2937;
    font-size:14px;
    font-weight:800;
}

.method-badge{
    display:inline-block;
    margin-top:3px;
    padding:2px 6px;
    border-radius:20px;
    background:#F3F4F6;
    color:#6B7280;
    font-size:9px;
    font-weight:700;
}

.method-content p{
    margin:5px 0 9px;
    color:#6B7280;
    font-size:11px;
    line-height:1.4;
}


/* =========================================================
   REFERENCE
========================================================= */

.reference{
    display:inline-flex;
    flex-direction:column;
    gap:2px;
    background:#fff;
    padding:7px 10px;
    border-radius:8px;
    border:1px solid #D1FAE5;
}

.reference span{
    font-size:9px;
    color:#6B7280;
}

.reference strong{
    font-size:11px;
    word-break:break-all;
}


/* =========================================================
   WORKING DAYS
========================================================= */

.working-days{
    display:inline-flex;
    align-items:center;
    gap:5px;
    font-size:11px;
}


/* =========================================================
   REJECTION
========================================================= */

.rejection-reason{
    background:#fff;
    padding:9px 10px;
    border-radius:9px;
    border:1px solid #FECACA;
}

.rejection-reason strong{
    display:block;
    font-size:11px;
    margin-bottom:3px;
}

.rejection-reason p{
    margin:0;
    font-size:11px;
    word-break:break-word;
}


/* =========================================================
   QR MODAL
========================================================= */

.qr-overlay{
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.65);
    display:flex;
    justify-content:center;
    align-items:center;
    z-index:9999;
    padding:12px;
}

.qr-modal{
    width:100%;
    max-width:430px;
    max-height:calc(100vh - 24px);
    overflow-y:auto;
    background:#fff;
    border-radius:18px;
    padding:18px;
    position:relative;
    text-align:center;
    box-shadow:0 20px 50px rgba(0,0,0,.2);
}

.close-btn{
    position:absolute;
    right:12px;
    top:12px;
    border:none;
    background:#F3F4F6;
    color:#374151;
    width:32px;
    height:32px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
}

.close-btn:hover{
    background:#E5E7EB;
}

.qr-header{
    padding-top:4px;
}

.qr-icon{
    width:45px;
    height:45px;
    margin:auto;
    border-radius:12px;
    background:#E9F5EF;
    color:#064E2A;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
}

.qr-header h3{
    margin:8px 0 3px;
    color:#064E2A;
    font-size:17px;
    font-weight:800;
}

.qr-header p{
    margin:0;
    color:#6B7280;
    font-size:11px;
}


/* =========================================================
   QR IMAGE
========================================================= */

.qr-container{
    margin:13px auto;
    width:220px;
    height:220px;
    border:1px solid #E5E7EB;
    border-radius:13px;
    background:#fff;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:10px;
}

.qr-image{
    width:100%;
    height:100%;
    object-fit:contain;
}

.qr-placeholder{
    width:100%;
    color:#064E2A;
    word-break:break-all;
}

.qr-placeholder i{
    display:block;
    font-size:75px;
    margin-bottom:7px;
}

.qr-placeholder p{
    margin:0 0 5px;
    font-size:11px;
}

.qr-placeholder small{
    display:block;
    color:#777;
    font-size:9px;
    max-height:70px;
    overflow:auto;
}


/* =========================================================
   QR PAYMENT INFO
========================================================= */

.qr-payment-info{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px;
    background:#F7FAF8;
    padding:10px;
    border-radius:10px;
    text-align:left;
}

.qr-payment-info div{
    min-width:0;
}

.qr-payment-info span{
    display:block;
    color:#777;
    font-size:9px;
    margin-bottom:2px;
}

.qr-payment-info strong{
    display:block;
    color:#064E2A;
    font-size:11px;
    word-break:break-word;
}


/* =========================================================
   QR NOTICE
========================================================= */

.qr-notice{
    margin:9px 0;
    padding:9px 10px;
    border-radius:9px;
    background:#EFF6FF;
    color:#1E40AF;
    display:flex;
    gap:7px;
    text-align:left;
    font-size:10px;
    line-height:1.4;
}


/* =========================================================
   TRANSITION
========================================================= */

.slide-enter-active,
.slide-leave-active{
    transition:.3s;
}

.slide-enter-from,
.slide-leave-to{
    opacity:0;
    transform:translateY(-8px);
}


/* =========================================================
   TABLET
========================================================= */

@media(max-width:768px){

    .payment-page{
        padding:4px 5px;
    }

    .payment-card{
        padding:13px;
        border-radius:14px;
    }

    .page-header{
        gap:8px;
        padding-bottom:10px;
    }

    .header-icon{
        width:36px;
        height:36px;
        min-width:36px;
        border-radius:9px;
        font-size:16px;
    }

    .page-header h3{
        font-size:18px;
    }

    .page-header p{
        font-size:11px;
    }

    .amount-box{
        padding:11px 12px;
    }

    .amount-box strong{
        font-size:20px;
    }

    .status-box{
        padding:12px;
    }

    .payment-method{
        padding:11px;
    }
}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:576px){

    .payment-page{
        padding:2px;
    }

    .payment-card{
        padding:10px;
        border-radius:13px;
        box-shadow:none;
    }

    .page-header{
        align-items:center;
    }

    .header-icon{
        width:34px;
        height:34px;
        min-width:34px;
        font-size:15px;
    }

    .page-header h3{
        font-size:16px;
    }

    .page-header p{
        font-size:10px;
    }

    .alert-box{
        padding:8px 9px;
        font-size:10px;
    }

    .loading-box{
        min-height:180px;
    }

    .status-box{
        gap:9px;
        padding:10px;
        border-radius:11px;
    }

    .status-icon{
        width:34px;
        height:34px;
        min-width:34px;
        font-size:15px;
        border-radius:9px;
    }

    .status-box h4{
        font-size:13px;
    }

    .status-box p{
        font-size:10px;
        line-height:1.45;
    }

    .primary-btn{
        width:100%;
        min-height:36px;
        font-size:11px;
    }

    .amount-box{
        margin-top:10px;
        padding:10px;
    }

    .amount-box span{
        font-size:11px;
    }

    .amount-box small{
        font-size:9px;
    }

    .amount-box strong{
        font-size:18px;
    }

    .payment-options{
        margin-top:11px;
    }

    .section-heading{
        margin-bottom:7px;
    }

    .section-icon{
        width:29px;
        height:29px;
        min-width:29px;
        font-size:13px;
    }

    .section-heading h5{
        font-size:12px;
    }

    .section-heading p{
        font-size:9px;
    }

    .payment-method{
        gap:8px;
        padding:9px;
        border-radius:10px;
        margin-bottom:7px;
    }

    .method-icon{
        width:34px;
        height:34px;
        min-width:34px;
        font-size:14px;
        border-radius:8px;
    }

    .method-content h4{
        font-size:12px;
    }

    .method-badge{
        font-size:8px;
        padding:1px 5px;
    }

    .method-content p{
        margin:4px 0 7px;
        font-size:9px;
    }

    .pay-btn,
    .secondary-btn{
        width:100%;
        min-height:34px;
        padding:7px 9px;
        font-size:10px;
    }

    .reference{
        max-width:100%;
    }

    .qr-overlay{
        padding:8px;
    }

    .qr-modal{
        max-height:calc(100vh - 16px);
        padding:14px;
        border-radius:15px;
    }

    .qr-icon{
        width:40px;
        height:40px;
        font-size:19px;
    }

    .qr-header h3{
        font-size:15px;
    }

    .qr-header p{
        font-size:10px;
    }

    .qr-container{
        width:190px;
        height:190px;
        margin:10px auto;
        padding:8px;
    }

    .qr-placeholder i{
        font-size:60px;
    }

    .qr-payment-info{
        padding:8px;
    }

    .qr-notice{
        font-size:9px;
        padding:8px;
    }
}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media(max-width:380px){

    .payment-card{
        padding:8px;
    }

    .page-header h3{
        font-size:15px;
    }

    .page-header p{
        font-size:9px;
    }

    .amount-box{
        flex-direction:column;
        align-items:flex-start;
        gap:3px;
    }

    .amount-box strong{
        font-size:17px;
    }

    .status-box{
        align-items:flex-start;
    }

    .payment-method{
        align-items:flex-start;
    }

    .method-content p{
        font-size:9px;
    }

    .qr-container{
        width:175px;
        height:175px;
    }
}

</style>

