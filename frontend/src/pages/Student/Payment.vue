<template>

<div class="payment-page">


    <div class="payment-card">


        <div class="header">

            <div class="icon">

                💳

            </div>


            <h2>
                Enrollment Payment
            </h2>


            <p>
                Complete your enrollment by paying the required fee.
            </p>


        </div>




        <div class="body">


            <div v-if="loading" class="loading-box">


                <div class="spinner"></div>


                <p>
                    Loading payment information...
                </p>


            </div>



            <div v-else>


                <div class="fee-box">


                    <span>
                        Enrollment Fee
                    </span>


                    <h1>
                        ₱ {{ amount }}
                    </h1>


                </div>
                <div 
                v-if="paymentStatus === 'Approved'"
                >
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
                <div
                v-else-if="paymentStatus === 'Enrolled'"
                class="success-alert"
                >
                    ✅

                    <h4>
                        Enrollment Completed
                    </h4>


                    <p>
                        Your enrollment is already official.
                    </p>


                </div>
                <div

                v-else

                class="waiting-alert">
                    ⏳

                    <h4>
                        Waiting for Approval
                    </h4>


                    <p>
                        Your enrollment is still waiting for registrar approval.
                    </p>


                </div>




            </div>



        </div>


    </div>


</div>


</template>




<script setup>

import { ref,onMounted } from "vue";
import axios from "@/services/api";



const loading = ref(true);

const processing = ref(false);


const paymentStatus = ref('');

const amount = ref(0);



const paymentInfo = ref({

    enrollment_id:null,

    amount:0,

    status:''

});




onMounted(async()=>{


    try{


        const response = await axios.get(
            "/student/payment/info"
        );


        console.log(
            "Payment Info:",
            response.data
        );



        paymentInfo.value =
        response.data;



        paymentStatus.value =
        response.data.status;



        amount.value =
        response.data.amount;



    }


    catch(error){


        console.log(error);


    }


    finally{


        loading.value=false;


    }


});





const payNow = async()=>{


    try{


        processing.value=true;



        const response = await axios.post(

            `/student/payment/create/${paymentInfo.value.enrollment_id}`

        );



        console.log(
            "PayMongo Checkout:",
            response.data
        );



        window.location.href =
        response.data.checkout_url;



    }


    catch(error){


        console.log(
            "Payment Error:",
            error.response?.data || error
        );


        alert(

            error.response?.data?.message ||

            "Unable to create payment checkout."

        );


    }


    finally{


        processing.value=false;


    }


}


</script>





<style scoped>


.payment-page{


    min-height:90vh;

    display:flex;

    justify-content:center;

    align-items:center;

    padding:30px;


    background:

    linear-gradient(
        135deg,
        #198754,
        #064d2b
    );


}



.payment-card{


    width:450px;

    background:white;

    border-radius:25px;

    overflow:hidden;

    box-shadow:

    0 20px 40px rgba(0,0,0,.25);


}



.header{


    background:#198754;

    color:white;

    text-align:center;

    padding:35px;


}



.icon{


    font-size:50px;

    margin-bottom:10px;


}



.header h2{


    font-weight:700;


}



.header p{


    opacity:.9;


}




.body{


    padding:35px;


}




.fee-box{


    text-align:center;

    background:#f1fdf6;

    padding:25px;

    border-radius:20px;

    margin-bottom:20px;


}



.fee-box span{


    color:#777;


}



.fee-box h1{


    color:#198754;

    font-size:45px;

    font-weight:800;


}





.description{


    text-align:center;

    color:#666;

}





.pay-btn{


    width:100%;

    border:none;

    padding:15px;

    border-radius:15px;

    background:#198754;

    color:white;

    font-size:18px;

    font-weight:bold;

    transition:.3s;


}



.pay-btn:hover{


    background:#146c43;

    transform:translateY(-3px);


}



.pay-btn:disabled{


    opacity:.7;

    cursor:not-allowed;


}




.loading-btn{


    display:flex;

    justify-content:center;

    align-items:center;

    gap:10px;


}



.spinner{


    width:45px;

    height:45px;

    border:5px solid #ddd;

    border-top-color:#198754;

    border-radius:50%;

    animation:spin 1s linear infinite;

    margin:auto;


}



.small-spinner{


    width:20px;

    height:20px;

    border:3px solid white;

    border-top-color:transparent;

    border-radius:50%;

    animation:spin .8s linear infinite;


}




@keyframes spin{


    from{

        transform:rotate(0deg);

    }


    to{

        transform:rotate(360deg);

    }


}



.success-alert{


    background:#d1e7dd;

    color:#0f5132;

    padding:25px;

    text-align:center;

    border-radius:15px;


}



.waiting-alert{


    background:#fff3cd;

    color:#664d03;

    padding:25px;

    text-align:center;

    border-radius:15px;


}


</style>