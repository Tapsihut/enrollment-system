<template>

<div class="receipt-page">


    <div class="receipt-card" ref="receiptArea">


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





        <div v-if="loading" class="loading">


            <div class="spinner"></div>


            <p>
                Generating receipt...
            </p>


        </div>





        <div v-else class="receipt-body">
<div class="success">


    <span>
        ✓
    </span>


    <h3>
        Payment Successful
    </h3>


    <div class="notice">

        <strong>
            Notice:
        </strong>

        This is not an official receipt (O.R.).
        The official receipt will be issued after you submit and complete all required original documents
        and your enrollment requirements have been verified.

    </div>


</div>

            <div class="receipt-info">



                <div class="row">


                    <span>
                        Student
                    </span>


                    <strong>
                        {{receipt.student}}
                    </strong>


                </div>




                <div class="row">


                    <span>
                        Reference Number
                    </span>


                    <strong>
                        {{receipt.reference}}
                    </strong>


                </div>





                <div class="row">


                    <span>
                        Payment Method
                    </span>


                    <strong>
                        GCash
                    </strong>


                </div>






                <div class="row">


                    <span>
                        Amount Paid
                    </span>


                    <strong class="amount">

                        ₱{{formatAmount(receipt.amount)}}

                    </strong>


                </div>






                <div class="row">


                    <span>
                        Status
                    </span>


                    <span class="badge">

                        {{receipt.status}}

                    </span>


                </div>



            </div>





            <button
            class="print-btn"
            @click="printReceipt"
            >

                🖨 Print Receipt

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
async function loadReceipt(){


    try{


        const response =
        await api.get(
            '/student/receipt'
        );


        receipt.value =
        response.data;


    }


    catch(error){


        console.log(
            error
        );


    }


    finally{


        loading.value=false;


    }


}





function formatAmount(value){


    if(!value)
        return "0.00";


    return Number(value)
    .toLocaleString(
        "en-US",
        {
            minimumFractionDigits:2
        }
    );


}

function printReceipt(){

    const content =
    receiptArea.value.innerHTML;


    const original =
    document.body.innerHTML;


    document.body.innerHTML = content;


    window.print();


    document.body.innerHTML = original;


    window.location.reload();

}


onMounted(loadReceipt);



</script>





<style scoped>

.notice{

    margin-top:20px;

    padding:15px;

    background:#fff3cd;

    border-left:5px solid #ffc107;

    border-radius:10px;

    color:#664d03;

    font-size:14px;

    text-align:left;

    line-height:1.5;

}
.receipt-page{


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




.receipt-card{


    width:450px;

    background:white;

    border-radius:25px;

    overflow:hidden;


    box-shadow:

    0 20px 40px rgba(0,0,0,.25);


}





.receipt-header{


    background:#198754;

    color:white;

    text-align:center;

    padding:35px;


}




.logo{


    font-size:50px;


}




.receipt-header h2{


    font-weight:800;

    margin-top:10px;


}




.receipt-header p{


    opacity:.9;


}




.receipt-body{


    padding:35px;


}





.success{


    text-align:center;

    margin-bottom:25px;


}





.success span{


    display:flex;

    justify-content:center;

    align-items:center;


    margin:auto;


    width:70px;

    height:70px;


    border-radius:50%;


    background:#198754;

    color:white;


    font-size:40px;


}





.success h3{


    color:#198754;

    margin-top:15px;


}






.receipt-info{


    background:#f8f9fa;

    padding:20px;

    border-radius:15px;


}





.row{


    display:flex;

    justify-content:space-between;

    padding:12px 0;


    border-bottom:

    1px solid #ddd;


}





.row:last-child{


    border:none;


}





.row span{


    color:#777;


}





.amount{


    color:#198754;

    font-size:20px;


}





.badge{


    background:#198754;

    color:white!important;

    padding:6px 15px;

    border-radius:20px;


}





.print-btn{


    width:100%;

    margin-top:25px;


    border:none;


    background:#198754;

    color:white;


    padding:14px;


    border-radius:15px;


    font-size:18px;

    font-weight:bold;


}





.print-btn:hover{


    background:#146c43;


}





.loading{


    text-align:center;

    padding:50px;


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





@keyframes spin{


    from{

        transform:rotate(0deg);

    }


    to{

        transform:rotate(360deg);

    }


}





@media print{


    .print-btn{

        display:none;

    }


    .receipt-page{

        background:white;

    }


}



</style>