<template>

<div class="payment-page">

    <div class="payment-card">

        <div class="header">

            <div class="icon">💳</div>

            <h2>Enrollment Payment</h2>

            <p>
                Complete your enrollment by paying the required fee.
            </p>

        </div>


        <div class="body">

            <div v-if="loading" class="loading-box">

                <div class="spinner"></div>

                <p>Loading payment information...</p>

            </div>


            <div v-else>

                <div class="fee-box">

                    <span>Enrollment Fee</span>

                    <h1>
                        ₱ {{ amount }}
                    </h1>

                </div>


                <!-- APPROVED -->

                <div v-if="paymentStatus === 'Approved'">

                    <p class="description">
                        Pay your enrollment fee using GCash.
                    </p>

                    <button
                        class="pay-btn"
                        @click="payNow"
                        :disabled="processing"
                    >

                        <span v-if="!processing">
                            Pay Now with GCash
                        </span>

                        <span v-else class="loading-btn">

                            <span class="small-spinner"></span>

                            Creating Checkout...

                        </span>

                    </button>

                </div>


                <!-- ENROLLED -->

                <div
                    v-else-if="paymentStatus === 'Enrolled'"
                    class="success-alert"
                >

                    <div class="alert-icon">
                        ✅
                    </div>

                    <h4>
                        Enrollment Completed
                    </h4>

                    <p>
                        Your enrollment is already official.
                    </p>

                </div>


                <!-- WAITING -->

                <div
                    v-else
                    class="waiting-alert"
                >

                    <div class="alert-icon">
                        ⏳
                    </div>

                    <h4>
                        Waiting for Approval
                    </h4>

                    <p>
                        Your enrollment is still waiting for
                        registrar approval.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

</template>


<script setup>

import { ref, onMounted } from "vue";
import axios from "@/services/api";


const loading = ref(true);
const processing = ref(false);

const paymentStatus = ref("");
const amount = ref(0);

const paymentInfo = ref({
    enrollment_id: null,
    amount: 0,
    status: ""
});


/*
|--------------------------------------------------------------------------
| Load Payment Information
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    try {

        const response = await axios.get(
            "/student/payment/info"
        );

        console.log(
            "Payment Info:",
            response.data
        );

        paymentInfo.value = response.data;

        paymentStatus.value =
            response.data.status;

        amount.value =
            response.data.amount;

    }

    catch (error) {

        console.log(
            "Payment Info Error:",
            error
        );

    }

    finally {

        loading.value = false;

    }

});


/*
|--------------------------------------------------------------------------
| Pay Now
|--------------------------------------------------------------------------
*/

const payNow = async () => {

    if (processing.value) {
        return;
    }


    try {

        processing.value = true;


        const enrollmentId =
            paymentInfo.value.enrollment_id;


        if (!enrollmentId) {

            alert(
                "Enrollment information is missing."
            );

            return;

        }


        const response = await axios.post(
            `/student/payment/create/${enrollmentId}`
        );


        console.log(
            "PayMongo Checkout:",
            response.data
        );


        /*
        |--------------------------------------------------------------------------
        | Open PayMongo Checkout
        |--------------------------------------------------------------------------
        */

        if (response.data.checkout_url) {

            window.location.href =
                response.data.checkout_url;

            return;

        }


        alert(
            "Unable to open PayMongo checkout."
        );

    }

    catch (error) {

        console.log(
            "Payment Error:",
            error.response?.data || error
        );


        alert(
            error.response?.data?.message ||
            "Unable to create payment checkout."
        );

    }

    finally {

        processing.value = false;

    }

};

</script>


<style scoped>

.payment-page {
    min-height: 90vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 15px;
    background: linear-gradient(
        135deg,
        #198754,
        #064d2b
    );
}


.payment-card {
    width: 430px;
    background: white;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 15px 30px rgba(0,0,0,.2);
}


.header {
    background: #198754;
    color: white;
    text-align: center;
    padding: 22px;
}


.icon {
    font-size: 38px;
    margin-bottom: 5px;
}


.header h2 {
    margin: 5px 0;
    font-weight: 700;
}


.header p {
    margin: 5px 0 0;
    opacity: .9;
}


.body {
    padding: 22px;
}


.fee-box {
    text-align: center;
    background: #f1fdf6;
    padding: 18px;
    border-radius: 14px;
    margin-bottom: 15px;
}


.fee-box span {
    color: #777;
}


.fee-box h1 {
    margin: 5px 0 0;
    color: #198754;
    font-size: 38px;
    font-weight: 800;
}


.description {
    text-align: center;
    color: #666;
    margin: 10px 0 15px;
}


.pay-btn {
    width: 100%;
    border: none;
    padding: 13px;
    border-radius: 12px;
    background: #198754;
    color: white;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    transition: .2s;
}


.pay-btn:hover {
    background: #146c43;
}


.pay-btn:disabled {
    opacity: .7;
    cursor: not-allowed;
}


.loading-btn {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
}


.spinner {
    width: 35px;
    height: 35px;
    border: 4px solid #ddd;
    border-top-color: #198754;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: auto;
}


.small-spinner {
    width: 18px;
    height: 18px;
    border: 3px solid white;
    border-top-color: transparent;
    border-radius: 50%;
    animation: spin .8s linear infinite;
}


.loading-box {
    text-align: center;
}


.loading-box p {
    margin-top: 10px;
    color: #666;
}


.success-alert,
.waiting-alert {
    padding: 18px;
    text-align: center;
    border-radius: 12px;
}


.success-alert {
    background: #d1e7dd;
    color: #0f5132;
}


.waiting-alert {
    background: #fff3cd;
    color: #664d03;
}


.alert-icon {
    font-size: 28px;
}


.success-alert h4,
.waiting-alert h4 {
    margin: 8px 0;
}


.success-alert p,
.waiting-alert p {
    margin: 0;
}


@keyframes spin {

    to {
        transform: rotate(360deg);
    }

}

</style>