<template>

<div class="success-page">


    <div class="success-card">


        <div class="icon-wrapper">

            ✓

        </div>



        <h1>
            Payment Successful!
        </h1>



        <p class="subtitle">

            Your enrollment payment has been recorded successfully.

        </p>




        <div class="payment-details">


            <div class="detail-item">

                <span>
                    Payment Status
                </span>

                <strong class="paid">

                    PAID

                </strong>

            </div>



            <div class="detail-item">

                <span>
                    Amount Paid
                </span>

                <strong>

                    ₱1,500

                </strong>

            </div>



            <div class="detail-item">

                <span>
                    Payment Method
                </span>

                <strong>

                    GCash

                </strong>

            </div>


        </div>




        <div class="actions">


            <button
                class="confirm-btn"
                @click="confirmPayment"
                :disabled="processing"
            >

                <span v-if="!processing">
                    Confirm Payment
                </span>


                <span v-else>
                    Processing...
                </span>


            </button>



        </div>




        <p class="note">

            Your official enrollment receipt will be generated after confirmation.

        </p>



    </div>


</div>


</template>



<script setup>

import { ref } from "vue";
import axios from "@/services/api";


const processing = ref(false);



const confirmPayment = async()=>{


    try{


        processing.value = true;


        const response = await axios.post(

            "/student/payment/confirm"

        );



        console.log(
            "Payment Confirmation:",
            response.data
        );



        alert(
            "Payment confirmed successfully!"
        );



        window.location.href =
        "/student/receipt";


    }


    catch(error){


        console.log(error);


        alert(
            "Payment confirmation failed."
        );


    }


    finally{

        processing.value = false;

    }


}


</script>



<style scoped>


.success-page{


    min-height:90vh;

    display:flex;

    justify-content:center;

    align-items:center;

    background:
    linear-gradient(
        135deg,
        #198754,
        #0f5132
    );

    padding:30px;

}



.success-card{


    background:white;

    width:450px;

    border-radius:25px;

    padding:40px;

    text-align:center;

    box-shadow:
    0 20px 50px rgba(0,0,0,.2);


}



.icon-wrapper{


    width:90px;

    height:90px;

    border-radius:50%;

    background:#198754;

    color:white;

    font-size:55px;

    display:flex;

    justify-content:center;

    align-items:center;

    margin:auto;

    margin-bottom:20px;


}



h1{


    color:#198754;

    font-weight:700;

}



.subtitle{


    color:#6c757d;

    margin-bottom:30px;

}



.payment-details{


    background:#f8f9fa;

    border-radius:15px;

    padding:20px;

    margin-bottom:25px;


}



.detail-item{


    display:flex;

    justify-content:space-between;

    padding:12px 0;

    border-bottom:1px solid #ddd;


}



.detail-item:last-child{


    border-bottom:none;


}



.detail-item span{


    color:#6c757d;


}



.paid{


    color:#198754;

}



.confirm-btn{


    width:100%;

    padding:14px;

    border:none;

    border-radius:12px;

    background:#198754;

    color:white;

    font-size:18px;

    font-weight:600;

    transition:.3s;


}



.confirm-btn:hover{


    background:#146c43;

    transform:translateY(-2px);


}



.confirm-btn:disabled{


    opacity:.6;

}



.note{


    margin-top:20px;

    font-size:14px;

    color:#6c757d;

}



</style>