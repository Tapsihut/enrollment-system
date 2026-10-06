<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import api from "@/services/api";

const dashboard = ref({
    enrollment_status: "No Enrollment",
    enrollment_fee: 1500,
    payment_status: "Not Available",
    progress: 0,
    steps: {
        profile: false,
        enrollment: false,
        payment: false,
        processing: false,
        completed: false
    }
});

let dashboardInterval = null;


/*
|--------------------------------------------------------------------------
| LOAD DASHBOARD
|--------------------------------------------------------------------------
*/

const loadDashboard = async () => {

    try {

        const response = await api.get(
            "/student/dashboard"
        );

        console.log(
            "Dashboard Data:",
            response.data
        );

        dashboard.value = {

            ...dashboard.value,

            ...response.data,

            steps: {
                ...dashboard.value.steps,
                ...(response.data.steps || {})
            }

        };

    }
    catch (error) {

        console.log(
            "Dashboard Error:",
            error.response?.data || error
        );

    }

};


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    // Load immediately
    loadDashboard();

    // Refresh when returning to the tab
    window.addEventListener(
        "focus",
        loadDashboard
    );

    // Check for status changes every 30 seconds
    dashboardInterval = setInterval(() => {

        loadDashboard();

    }, 30000);

});


onUnmounted(() => {

    window.removeEventListener(
        "focus",
        loadDashboard
    );

    if (dashboardInterval) {

        clearInterval(
            dashboardInterval
        );

        dashboardInterval = null;

    }

});

</script>


<template>

<div class="dashboard-page">


    <!-- =====================================================
         WELCOME HEADER
    ====================================================== -->

    <div class="welcome-card">

        <div class="welcome-content">

            <div class="welcome-badge">

                <i class="bi bi-mortarboard-fill"></i>

                Student Portal

            </div>


            <h2>
                Welcome, Student 👋
            </h2>


            <p>
                Manage your enrollment, payments,
                and academic information.
            </p>

        </div>


        <div class="school-badge">
            SFXC
        </div>

    </div>



    <!-- =====================================================
         OVERVIEW
    ====================================================== -->

    <div class="section-heading">

        <div class="section-heading-icon">

            <i class="bi bi-grid-1x2-fill"></i>

        </div>

        <div>

            <h4>
                Student Overview
            </h4>

            <p>
                Your current enrollment and payment status
            </p>

        </div>

    </div>



    <!-- =====================================================
         OVERVIEW CARDS
    ====================================================== -->

    <div class="row g-3">


        <!-- ENROLLMENT STATUS -->

        <div class="col-12 col-md-4">

            <div class="dashboard-card">

                <div class="icon green">

                    <i class="bi bi-journal-text"></i>

                </div>


                <div class="card-content">

                    <h6>
                        Enrollment Status
                    </h6>


                    <h4>
                        {{
                            dashboard.enrollment_status ||
                            "No Enrollment"
                        }}
                    </h4>


                    <span
                        class="status-badge"
                        :class="{

                            pending:
                                dashboard.enrollment_status === 'Pending',

                            paid:
                                dashboard.enrollment_status === 'Paid',

                            processing:
                                dashboard.enrollment_status === 'Processing',

                            completed:
                                dashboard.enrollment_status === 'Completed',

                            rejected:
                                dashboard.enrollment_status === 'Rejected',

                            neutral:
                                !dashboard.enrollment_status ||
                                dashboard.enrollment_status === 'No Enrollment'

                        }"
                    >

                        {{
                            dashboard.enrollment_status ||
                            "No Enrollment"
                        }}

                    </span>

                </div>

            </div>

        </div>



        <!-- ENROLLMENT FEE -->

        <div class="col-12 col-md-4">

            <div class="dashboard-card">

                <div class="icon gold">

                    <i class="bi bi-wallet2"></i>

                </div>


                <div class="card-content">

                    <h6>
                        Enrollment Fee
                    </h6>


                    <h4>

                        ₱{{
                            Number(
                                dashboard.enrollment_fee ?? 1500
                            ).toLocaleString(
                                "en-US",
                                {
                                    minimumFractionDigits: 2
                                }
                            )
                        }}

                    </h4>


                    <span
                        class="status-badge warning"
                    >

                        {{
                            Number(
                                dashboard.enrollment_fee ?? 0
                            ) > 0
                                ? "Required Fee"
                                : "Payment Completed"
                        }}

                    </span>

                </div>

            </div>

        </div>



        <!-- PAYMENT -->

        <div class="col-12 col-md-4">

            <div class="dashboard-card">

                <div class="icon success">

                    <i class="bi bi-credit-card-fill"></i>

                </div>


                <div class="card-content">

                    <h6>
                        Payment
                    </h6>


                    <h4>
                        {{
                            dashboard.payment_status ||
                            "Not Available"
                        }}
                    </h4>


                    <span
                        class="status-badge"
                        :class="{

                            success:
                                dashboard.payment_status === 'Paid',

                            pending:
                                dashboard.payment_status === 'Pending',

                            danger:
                                dashboard.payment_status === 'Failed' ||
                                dashboard.payment_status === 'Rejected',

                            neutral:
                                !dashboard.payment_status ||
                                dashboard.payment_status === 'Not Available'

                        }"
                    >

                        {{
                            dashboard.payment_status ||
                            "Not Available"
                        }}

                    </span>

                </div>

            </div>

        </div>


    </div>



    <!-- =====================================================
         ENROLLMENT PROGRESS
    ====================================================== -->

    <div class="process-card">


        <!-- CARD HEADER -->

        <div class="process-header">

            <div class="process-icon">

                <i class="bi bi-list-check"></i>

            </div>

            <div>

                <h4>
                    Enrollment Progress
                </h4>

                <p>
                    Track the progress of your enrollment
                </p>

            </div>

        </div>



        <!-- =================================================
             PROGRESS BAR
        ================================================== -->

        <div class="progress-section">

            <div class="progress-label">

                <span>
                    Overall Progress
                </span>

                <strong>
                    {{ dashboard.progress }}%
                </strong>

            </div>


            <div class="progress">

                <div
                    class="progress-bar"
                    :style="{
                        width: dashboard.progress + '%'
                    }"
                >
                </div>

            </div>

        </div>



        <!-- =================================================
             STEPS
        ================================================== -->

        <div class="steps">


            <!-- PROFILE -->

            <div
                class="step"
                :class="{
                    active: dashboard.steps.profile
                }"
            >

                <div class="step-number">

                    <i
                        v-if="dashboard.steps.profile"
                        class="bi bi-check-lg"
                    ></i>

                    <span v-else>
                        1
                    </span>

                </div>


                <div class="step-content">

                    <strong>
                        Complete Profile
                    </strong>

                    <small>
                        Personal information
                    </small>

                </div>

            </div>



            <!-- ENROLLMENT -->

            <div
                class="step"
                :class="{
                    active: dashboard.steps.enrollment
                }"
            >

                <div class="step-number">

                    <i
                        v-if="dashboard.steps.enrollment"
                        class="bi bi-check-lg"
                    ></i>

                    <span v-else>
                        2
                    </span>

                </div>


                <div class="step-content">

                    <strong>
                        Submit Enrollment
                    </strong>

                    <small>
                        Enrollment application
                    </small>

                </div>

            </div>



            <!-- PAYMENT -->

            <div
                class="step"
                :class="{
                    active: dashboard.steps.payment
                }"
            >

                <div class="step-number">

                    <i
                        v-if="dashboard.steps.payment"
                        class="bi bi-check-lg"
                    ></i>

                    <span v-else>
                        3
                    </span>

                </div>


                <div class="step-content">

                    <strong>
                        Payment
                    </strong>

                    <small>
                        Enrollment fee
                    </small>

                </div>

            </div>



            <!-- PROCESSING -->

            <div
                class="step"
                :class="{
                    active: dashboard.steps.processing
                }"
            >

                <div class="step-number">

                    <i
                        v-if="dashboard.steps.processing"
                        class="bi bi-check-lg"
                    ></i>

                    <span v-else>
                        4
                    </span>

                </div>


                <div class="step-content">

                    <strong>
                        College Processing
                    </strong>

                    <small>
                        Enrollment verification
                    </small>

                </div>

            </div>



            <!-- COMPLETED -->

            <div
                class="step"
                :class="{
                    active: dashboard.steps.completed
                }"
            >

                <div class="step-number">

                    <i
                        v-if="dashboard.steps.completed"
                        class="bi bi-check-lg"
                    ></i>

                    <span v-else>
                        5
                    </span>

                </div>


                <div class="step-content">

                    <strong>
                        Completed
                    </strong>

                    <small>
                        Study load available
                    </small>

                </div>

            </div>


        </div>

    </div>


</div>

</template>


<style scoped>

* {
    box-sizing: border-box;
}


/* =========================================================
   PAGE
========================================================= */

.dashboard-page {

    width: 100%;

    padding: 10px;

    overflow-x: hidden;

}


/* =========================================================
   WELCOME CARD
========================================================= */

.welcome-card {

    background: linear-gradient(
        135deg,
        #064E2A,
        #0B6B3A
    );

    color: white;

    padding: 30px;

    border-radius: 22px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    box-shadow:
        0 10px 30px rgba(0,0,0,.10);

    margin-bottom: 25px;

}


.welcome-content {

    min-width: 0;

}


.welcome-badge {

    display: inline-flex;

    align-items: center;

    gap: 6px;

    background: rgba(255,255,255,.14);

    border: 1px solid rgba(255,255,255,.18);

    padding: 6px 11px;

    border-radius: 999px;

    font-size: 11px;

    font-weight: 700;

    margin-bottom: 12px;

}


.welcome-card h2 {

    font-size: 27px;

    font-weight: 800;

    margin: 0 0 6px;

}


.welcome-card p {

    margin: 0;

    color: rgba(255,255,255,.78);

    line-height: 1.5;

}


.school-badge {

    width: 82px;

    height: 82px;

    min-width: 82px;

    border-radius: 50%;

    background: white;

    color: #0B6B3A;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 27px;

    font-weight: 900;

    border: 4px solid #9cffc8;

}


/* =========================================================
   SECTION HEADING
========================================================= */

.section-heading {

    display: flex;

    align-items: center;

    gap: 10px;

    margin: 0 0 15px;

}


.section-heading-icon {

    width: 38px;

    height: 38px;

    min-width: 38px;

    border-radius: 10px;

    background: #E8F5EE;

    color: #0B6B3A;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 17px;

}


.section-heading h4 {

    margin: 0;

    color: #064E2A;

    font-size: 18px;

    font-weight: 800;

}


.section-heading p {

    margin: 2px 0 0;

    color: #6B7280;

    font-size: 12px;

}


/* =========================================================
   DASHBOARD CARD
========================================================= */

.dashboard-card {

    background: white;

    border: 1px solid #E5E7EB;

    border-radius: 16px;

    padding: 20px;

    min-height: 125px;

    display: flex;

    align-items: center;

    gap: 14px;

    box-shadow:
        0 6px 20px rgba(0,0,0,.05);

    transition: .25s;

}


.dashboard-card:hover {

    transform: translateY(-3px);

    box-shadow:
        0 10px 25px rgba(0,0,0,.08);

}


.card-content {

    min-width: 0;

}


.dashboard-card h6 {

    color: #6B7280;

    font-size: 12px;

    font-weight: 600;

    margin: 0 0 3px;

}


.dashboard-card h4 {

    color: #1F2937;

    font-size: 19px;

    font-weight: 800;

    margin: 0 0 7px;

    word-break: break-word;

}


/* =========================================================
   ICONS
========================================================= */

.icon {

    width: 52px;

    height: 52px;

    min-width: 52px;

    border-radius: 13px;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 24px;

}


.icon.green {

    background: #DCFCE7;

    color: #0B6B3A;

}


.icon.gold {

    background: #FEF3C7;

    color: #D97706;

}


.icon.success {

    background: #D1FAE5;

    color: #059669;

}


/* =========================================================
   STATUS BADGES
========================================================= */

.status-badge {

    display: inline-flex;

    align-items: center;

    width: fit-content;

    padding: 5px 10px;

    border-radius: 999px;

    font-size: 10px;

    font-weight: 700;

    line-height: 1.2;

}


.status-badge.pending {

    background: #FEF3C7;

    color: #92400E;

}


.status-badge.paid {

    background: #D1FAE5;

    color: #065F46;

}


.status-badge.success {

    background: #D1FAE5;

    color: #065F46;

}


.status-badge.processing {

    background: #CFF4FC;

    color: #055160;

}


.status-badge.completed {

    background: #D1E7DD;

    color: #0F5132;

}


.status-badge.rejected,

.status-badge.danger {

    background: #FEE2E2;

    color: #991B1B;

}


.status-badge.warning {

    background: #FEF3C7;

    color: #92400E;

}


.status-badge.neutral {

    background: #E5E7EB;

    color: #4B5563;

}


/* =========================================================
   PROCESS CARD
========================================================= */

.process-card {

    background: white;

    border: 1px solid #E5E7EB;

    border-radius: 22px;

    padding: 28px;

    margin-top: 25px;

    box-shadow:
        0 8px 25px rgba(0,0,0,.06);

}


/* =========================================================
   PROCESS HEADER
========================================================= */

.process-header {

    display: flex;

    align-items: center;

    gap: 11px;

    margin-bottom: 24px;

}


.process-icon {

    width: 40px;

    height: 40px;

    min-width: 40px;

    border-radius: 10px;

    background: #E8F5EE;

    color: #0B6B3A;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 18px;

}


.process-header h4 {

    margin: 0;

    color: #064E2A;

    font-size: 18px;

    font-weight: 800;

}


.process-header p {

    margin: 2px 0 0;

    color: #6B7280;

    font-size: 12px;

}


/* =========================================================
   PROGRESS
========================================================= */

.progress-section {

    margin-bottom: 30px;

}


.progress-label {

    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-bottom: 7px;

    color: #6B7280;

    font-size: 12px;

}


.progress-label strong {

    color: #0B6B3A;

    font-size: 13px;

}


.progress {

    height: 13px;

    border-radius: 999px;

    background: #E5E7EB;

    overflow: hidden;

}


.progress-bar {

    height: 100%;

    background: linear-gradient(
        90deg,
        #0B6B3A,
        #34D399
    );

    border-radius: 999px;

    transition: width .4s ease;

}


/* =========================================================
   STEPS
========================================================= */

.steps {

    display: flex;

    justify-content: space-between;

    gap: 12px;

}


.step {

    position: relative;

    flex: 1;

    text-align: center;

    color: #9CA3AF;

}


.step-number {

    width: 44px;

    height: 44px;

    margin: 0 auto 9px;

    border-radius: 50%;

    background: #E5E7EB;

    color: #6B7280;

    display: flex;

    align-items: center;

    justify-content: center;

    font-weight: 800;

    font-size: 14px;

    transition: .25s;

}


.step.active {

    color: #0B6B3A;

}


.step.active .step-number {

    background: #0B6B3A;

    color: white;

    box-shadow:
        0 4px 12px rgba(11,107,58,.20);

}


.step-content {

    display: flex;

    flex-direction: column;

    gap: 2px;

}


.step-content strong {

    font-size: 12px;

    font-weight: 700;

}


.step-content small {

    color: #9CA3AF;

    font-size: 10px;

    line-height: 1.35;

}


.step.active .step-content small {

    color: #6B7280;

}


/* =========================================================
   TABLET
========================================================= */

@media(max-width:768px) {

    .dashboard-page {

        padding: 6px;

    }


    .welcome-card {

        padding: 22px;

        border-radius: 18px;

        margin-bottom: 20px;

    }


    .welcome-card h2 {

        font-size: 23px;

    }


    .school-badge {

        width: 68px;

        height: 68px;

        min-width: 68px;

        font-size: 23px;

    }


    .dashboard-card {

        padding: 17px;

        min-height: 110px;

    }


    .process-card {

        padding: 22px;

        border-radius: 18px;

        margin-top: 20px;

    }


    .steps {

        gap: 8px;

    }


    .step-content strong {

        font-size: 11px;

    }

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:576px) {

    .dashboard-page {

        padding: 2px;

    }


    /* WELCOME */

    .welcome-card {

        padding: 17px;

        border-radius: 16px;

        margin-bottom: 17px;

        gap: 14px;

    }


    .welcome-badge {

        font-size: 9px;

        padding: 5px 9px;

        margin-bottom: 8px;

    }


    .welcome-card h2 {

        font-size: 19px;

        line-height: 1.25;

        margin-bottom: 5px;

    }


    .welcome-card p {

        font-size: 12px;

        line-height: 1.45;

    }


    .school-badge {

        width: 55px;

        height: 55px;

        min-width: 55px;

        border-width: 3px;

        font-size: 18px;

    }


    /* SECTION */

    .section-heading {

        gap: 8px;

        margin-bottom: 11px;

    }


    .section-heading-icon {

        width: 32px;

        height: 32px;

        min-width: 32px;

        border-radius: 8px;

        font-size: 14px;

    }


    .section-heading h4 {

        font-size: 15px;

    }


    .section-heading p {

        font-size: 10px;

    }


    /* DASHBOARD CARDS */

    .dashboard-card {

        min-height: 100px;

        padding: 13px;

        border-radius: 13px;

        gap: 11px;

    }


    .icon {

        width: 43px;

        height: 43px;

        min-width: 43px;

        border-radius: 11px;

        font-size: 20px;

    }


    .dashboard-card h6 {

        font-size: 10px;

        margin-bottom: 2px;

    }


    .dashboard-card h4 {

        font-size: 16px;

        margin-bottom: 5px;

    }


    .status-badge {

        padding: 4px 8px;

        font-size: 9px;

    }


    /* PROCESS */

    .process-card {

        padding: 15px;

        border-radius: 16px;

        margin-top: 16px;

    }


    .process-header {

        gap: 8px;

        margin-bottom: 17px;

    }


    .process-icon {

        width: 34px;

        height: 34px;

        min-width: 34px;

        border-radius: 8px;

        font-size: 15px;

    }


    .process-header h4 {

        font-size: 15px;

    }


    .process-header p {

        font-size: 10px;

    }


    .progress-section {

        margin-bottom: 22px;

    }


    .progress-label {

        font-size: 10px;

        margin-bottom: 5px;

    }


    .progress-label strong {

        font-size: 11px;

    }


    .progress {

        height: 9px;

    }


    /* STEPS */

    .steps {

        flex-direction: column;

        gap: 0;

    }


    .step {

        display: flex;

        align-items: center;

        text-align: left;

        gap: 11px;

        min-height: 54px;

    }


    .step-number {

        width: 35px;

        height: 35px;

        min-width: 35px;

        margin: 0;

        font-size: 12px;

    }


    .step-content {

        gap: 1px;

    }


    .step-content strong {

        font-size: 11px;

    }


    .step-content small {

        font-size: 9px;

    }

}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media(max-width:380px) {

    .welcome-card {

        padding: 14px;

    }


    .welcome-card h2 {

        font-size: 18px;

    }


    .welcome-card p {

        font-size: 11px;

    }


    .school-badge {

        width: 48px;

        height: 48px;

        min-width: 48px;

        font-size: 16px;

    }


    .dashboard-card {

        padding: 11px;

        min-height: 92px;

    }


    .icon {

        width: 39px;

        height: 39px;

        min-width: 39px;

        font-size: 18px;

    }


    .dashboard-card h4 {

        font-size: 15px;

    }


    .process-card {

        padding: 12px;

    }


    .step {

        min-height: 50px;

    }


    .step-number {

        width: 32px;

        height: 32px;

        min-width: 32px;

    }

}

</style>
