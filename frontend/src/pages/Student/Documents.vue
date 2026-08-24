<template>

<div class="documents-page">


    <div class="documents-card">


        <!-- HEADER -->
        <div class="header">

            <h3>
                Student Documents
            </h3>

            <p>
                View and download your enrollment-related documents.
            </p>

        </div>




        <div class="document-area">


            <!-- Enrollment Form -->

            <div class="document-box">


                <div class="document-icon">

                    <i class="bi bi-file-earmark-pdf"></i>

                </div>



                <div class="document-content">


                    <h4>
                        Enrollment Form
                    </h4>


                    <p>
                        Student Copy + Registrar Copy
                    </p>



                    <div class="buttons">


                        <button
                            class="btn-view"
                            @click="viewEnrollmentForm"
                        >

                            <i class="bi bi-eye"></i>

                            View PDF

                        </button>



                        <button
                            class="btn-download"
                            @click="downloadEnrollmentForm"
                        >

                            <i class="bi bi-download"></i>

                            Download PDF

                        </button>


                    </div>


                </div>


            </div>





            <!-- Receipt -->


            <div class="document-box disabled">


                <div class="document-icon receipt">

                    <i class="bi bi-receipt"></i>

                </div>



                <div class="document-content">


                    <h4>
                        Official Receipt
                    </h4>


                    <p>
                        Download your official payment receipt.
                    </p>



                    <button
                        class="btn-disabled"
                        disabled
                    >

                        <i class="bi bi-clock"></i>

                        Coming Soon

                    </button>


                </div>


            </div>




        </div>


    </div>


</div>

</template>





<script setup>

import api from "@/services/api"



async function getEnrollmentPDF(){


    return await api.get(

        "/student/documents/enrollment-form",

        {

            responseType:"blob"

        }

    )


}





async function viewEnrollmentForm(){


    try{


        const response = await getEnrollmentPDF()



        const file = new Blob(

            [response.data],

            {

                type:"application/pdf"

            }

        )


        const url = URL.createObjectURL(file)


        window.open(url,"_blank")


    }
    catch(error){

        console.log(error)

    }


}






async function downloadEnrollmentForm(){


    try{


        const response = await getEnrollmentPDF()



        const file = new Blob(

            [response.data],

            {

                type:"application/pdf"

            }

        )



        const url = URL.createObjectURL(file)



        const link=document.createElement("a")


        link.href=url


        link.download="Enrollment_Form.pdf"


        link.click()



        URL.revokeObjectURL(url)



    }
    catch(error){

        console.log(error)

    }


}


</script>







<style scoped>


.documents-page{

padding:10px;

}





.documents-card{


background:white;

border-radius:25px;

padding:35px;


box-shadow:

0 10px 35px rgba(0,0,0,.08);


}





.header{


border-bottom:1px solid #eee;

padding-bottom:20px;

margin-bottom:30px;


}





.header h3{


font-weight:800;

color:#064E2A;


}



.header p{


color:#6b7280;


}







.document-area{


background:#fafafa;

border-radius:20px;

padding:25px;


}





.document-box{


display:flex;

align-items:center;

gap:25px;


background:white;

padding:25px;


border-radius:20px;


margin-bottom:20px;


box-shadow:

0 5px 20px rgba(0,0,0,.05);


}





.document-icon{


width:65px;

height:65px;


border-radius:50%;


display:flex;

align-items:center;

justify-content:center;


background:#0B6B3A;

color:white;


font-size:30px;


}





.document-icon.receipt{


background:#064E2A;


}





.document-content h4{


font-weight:800;

color:#064E2A;


margin-bottom:5px;


}





.document-content p{


color:#6b7280;


margin-bottom:20px;


}







.buttons{


display:flex;

gap:15px;


}





button{


padding:12px 25px;

border-radius:12px;

border:none;

font-weight:700;


}





.btn-view{


background:#0B6B3A;

color:white;


}



.btn-view:hover{


background:#064E2A;


}





.btn-download{


background:white;

color:#0B6B3A;

border:2px solid #0B6B3A;


}





.btn-download:hover{


background:#0B6B3A;

color:white;


}





.btn-disabled{


background:#e5e7eb;

color:#777;


}





.disabled{


opacity:.75;


}






@media(max-width:768px){


.document-box{


flex-direction:column;

align-items:flex-start;


}



.buttons{


flex-direction:column;


}



}





</style>