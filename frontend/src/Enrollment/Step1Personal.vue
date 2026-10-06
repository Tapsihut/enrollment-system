<template>
    <div class="student-type-page">

        <!-- ================= PAGE HEADER ================= -->

        <div class="page-header">

            <div class="step-badge">
                <i class="bi bi-mortarboard-fill"></i>
                Step 1
            </div>

            <h3>
                Student Type
            </h3>

            <p>
                Select the type of enrollment that best describes you.
            </p>

        </div>


        <!-- ================= STUDENT TYPE ================= -->

        <div class="section-card">

            <div class="section-title">

                <i class="bi bi-person-badge-fill"></i>

                <div>
                    <span>Enrollment Type</span>

                    <small>
                        Select your current student status
                    </small>
                </div>

            </div>


            <!-- ================= OPTIONS ================= -->

            <div class="row">

                <!-- FRESHMEN -->

                <div class="col-md-6 mb-3">

                    <div
                        class="type-card"
                        :class="{
                            active: model.student_type === 'Freshmen'
                        }"
                        @click="selectType('Freshmen')"
                    >

                        <div class="type-icon">

                            <i class="bi bi-mortarboard-fill"></i>

                        </div>

                        <div class="type-content">

                            <h5>
                                Freshmen
                            </h5>

                            <p>
                                First time entering college.
                            </p>

                        </div>


                        <div
                            v-if="model.student_type === 'Freshmen'"
                            class="selected-indicator"
                        >

                            <i class="bi bi-check-circle-fill"></i>

                            Selected

                        </div>

                    </div>

                </div>


                <!-- TRANSFEREE -->

                <div class="col-md-6 mb-3">

                    <div class="type-card disabled">

                        <div class="type-icon">

                            <i class="bi bi-arrow-left-right"></i>

                        </div>

                        <div class="type-content">

                            <h5>
                                Transferee
                            </h5>

                            <p>
                                Coming from another college or university.
                            </p>

                        </div>


                        <div class="unavailable-indicator">

                            Currently unavailable

                        </div>

                    </div>

                </div>


                <!-- CONTINUING -->

                <div class="col-md-6 mb-3">

                    <div
                        class="type-card"
                        :class="{
                            active: model.student_type === 'Continuing'
                        }"
                        @click="selectType('Continuing')"
                    >

                        <div class="type-icon">

                            <i class="bi bi-book-fill"></i>

                        </div>

                        <div class="type-content">

                            <h5>
                                Continuing
                            </h5>

                            <p>
                                Currently enrolled student.
                            </p>

                        </div>


                        <div
                            v-if="model.student_type === 'Continuing'"
                            class="selected-indicator"
                        >

                            <i class="bi bi-check-circle-fill"></i>

                            Selected

                        </div>

                    </div>

                </div>


                <!-- RETURNEE -->

                <div class="col-md-6 mb-3">

                    <div class="type-card disabled">

                        <div class="type-icon">

                            <i class="bi bi-arrow-repeat"></i>

                        </div>

                        <div class="type-content">

                            <h5>
                                Returnee
                            </h5>

                            <p>
                                Returning after stopping enrollment.
                            </p>

                        </div>


                        <div class="unavailable-indicator">

                            Currently unavailable

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

const model = defineModel()

function selectType(type) {

    model.value.student_type = type

    console.log(
        "Selected Student Type:",
        model.value.student_type
    )

}

</script>


<style scoped>

* {
    box-sizing: border-box;
}


/* =========================================================
   PAGE
========================================================= */

.student-type-page {
    width: 100%;
    max-width: 100%;
    padding: 10px;
    overflow-x: hidden;
}


/* =========================================================
   PAGE HEADER
========================================================= */

.page-header {
    margin-bottom: 25px;
}


/* STEP BADGE */

.step-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    background: #E8F5EE;
    color: #064E2A;

    border-radius: 999px;

    padding: 6px 12px;

    font-size: 12px;
    font-weight: 700;

    margin-bottom: 10px;
}


/* TITLE */

.page-header h3 {
    font-weight: 800;
    color: #064E2A;

    margin-bottom: 6px;

    font-size: 27px;
}


/* DESCRIPTION */

.page-header p {
    color: #6B7280;

    margin: 0;

    line-height: 1.5;
}


/* =========================================================
   SECTION CARD
========================================================= */

.section-card {
    background: white;

    border-radius: 22px;

    padding: 30px;

    margin-bottom: 25px;

    box-shadow: 0 10px 30px rgba(0,0,0,.06);

    border: 1px solid #E5E7EB;
}


/* =========================================================
   SECTION TITLE
========================================================= */

.section-title {

    background: linear-gradient(
        135deg,
        #064E2A,
        #0B6B3A
    );

    color: white;

    padding: 14px 18px;

    border-radius: 12px;

    font-size: 18px;

    font-weight: 700;

    margin-bottom: 25px;

    display: flex;

    align-items: center;

    gap: 10px;

}

.section-title i {

    font-size: 20px;

    flex-shrink: 0;

}

.section-title div {

    display: flex;

    flex-direction: column;

}

.section-title small {

    font-size: 11px;

    font-weight: 400;

    opacity: .8;

    margin-top: 2px;

}


/* =========================================================
   TYPE CARD
========================================================= */

.type-card {

    position: relative;

    background: white;

    border: 2px solid #E5E7EB;

    border-radius: 16px;

    padding: 25px 20px;

    min-height: 190px;

    height: 100%;

    cursor: pointer;

    transition: .25s;

    display: flex;

    flex-direction: column;

    align-items: center;

    justify-content: center;

    text-align: center;

}


/* HOVER */

.type-card:hover {

    border-color: #0B6B3A;

    transform: translateY(-3px);

    box-shadow: 0 8px 20px rgba(0,0,0,.07);

}


/* ACTIVE */

.type-card.active {

    background: #F0FDF4;

    border-color: #0B6B3A;

    box-shadow: 0 8px 20px rgba(11,107,58,.10);

}


/* =========================================================
   TYPE ICON
========================================================= */

.type-icon {

    width: 58px;

    height: 58px;

    border-radius: 14px;

    background: #E8F5EE;

    color: #0B6B3A;

    display: flex;

    align-items: center;

    justify-content: center;

    margin-bottom: 12px;

    font-size: 29px;

    transition: .25s;

}

.type-card.active .type-icon {

    background: #0B6B3A;

    color: white;

}


/* =========================================================
   TYPE CONTENT
========================================================= */

.type-content h5 {

    font-weight: 700;

    color: #1F2937;

    margin: 0 0 5px;

    font-size: 17px;

}

.type-card.active .type-content h5 {

    color: #064E2A;

}

.type-content p {

    color: #6B7280;

    margin: 0;

    font-size: 13px;

    line-height: 1.45;

}

.type-card.active .type-content p {

    color: #4B6356;

}


/* =========================================================
   SELECTED INDICATOR
========================================================= */

.selected-indicator {

    display: flex;

    align-items: center;

    gap: 5px;

    margin-top: 12px;

    color: #0B6B3A;

    font-size: 11px;

    font-weight: 700;

}

.selected-indicator i {

    font-size: 13px;

}


/* =========================================================
   DISABLED
========================================================= */

.type-card.disabled {

    opacity: .55;

    cursor: not-allowed;

    background: #F3F4F6;

    border-color: #D1D5DB;

    filter: grayscale(30%);

}


.type-card.disabled:hover {

    border-color: #D1D5DB;

    transform: none;

    box-shadow: none;

}


.type-card.disabled .type-icon {

    background: #E5E7EB;

    color: #6B7280;

}


.type-card.disabled .type-content h5 {

    color: #4B5563;

}


.type-card.disabled .type-content p {

    color: #6B7280;

}


/* =========================================================
   UNAVAILABLE
========================================================= */

.unavailable-indicator {

    margin-top: 12px;

    padding: 4px 9px;

    border-radius: 999px;

    background: #E5E7EB;

    color: #6B7280;

    font-size: 10px;

    font-weight: 600;

}


/* =========================================================
   TABLET
========================================================= */

@media(max-width:768px) {

    .student-type-page {

        padding: 6px;

    }


    .page-header {

        margin-bottom: 20px;

    }


    .page-header h3 {

        font-size: 24px;

    }


    .page-header p {

        font-size: 14px;

    }


    .section-card {

        padding: 20px;

        border-radius: 18px;

        margin-bottom: 18px;

    }


    .section-title {

        padding: 13px 15px;

        font-size: 16px;

        margin-bottom: 20px;

    }


    .type-card {

        min-height: 165px;

        padding: 20px 15px;

        border-radius: 14px;

    }


    .type-icon {

        width: 50px;

        height: 50px;

        font-size: 25px;

        margin-bottom: 9px;

    }


    .type-content h5 {

        font-size: 15px;

    }


    .type-content p {

        font-size: 12px;

    }

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width:576px) {

    .student-type-page {

        padding: 2px;

    }


    .page-header {

        margin: 2px 4px 18px;

    }


    .step-badge {

        font-size: 11px;

        padding: 5px 10px;

        margin-bottom: 8px;

    }


    .page-header h3 {

        font-size: 21px;

        line-height: 1.25;

        margin-bottom: 5px;

    }


    .page-header p {

        font-size: 13px;

        line-height: 1.5;

    }


    /* ================= CARD ================= */

    .section-card {

        padding: 14px;

        border-radius: 16px;

        margin-bottom: 14px;

        box-shadow: 0 5px 18px rgba(0,0,0,.05);

    }


    /* ================= SECTION TITLE ================= */

    .section-title {

        padding: 11px 13px;

        border-radius: 11px;

        font-size: 15px;

        margin-bottom: 17px;

        gap: 9px;

    }


    .section-title i {

        font-size: 18px;

    }


    .section-title small {

        font-size: 10px;

    }


    /* ================= ROW ================= */

    .row {

        --bs-gutter-x: .7rem;

        --bs-gutter-y: 0;

    }


    /* ================= TYPE CARD ================= */

    .type-card {

        min-height: 145px;

        padding: 16px 10px;

        border-radius: 12px;

    }


    .type-icon {

        width: 44px;

        height: 44px;

        border-radius: 11px;

        font-size: 22px;

        margin-bottom: 8px;

    }


    .type-content h5 {

        font-size: 14px;

        margin-bottom: 4px;

    }


    .type-content p {

        font-size: 11px;

        line-height: 1.4;

    }


    .selected-indicator {

        margin-top: 8px;

        font-size: 10px;

    }


    .selected-indicator i {

        font-size: 11px;

    }


    .unavailable-indicator {

        margin-top: 8px;

        padding: 3px 7px;

        font-size: 9px;

    }

}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media(max-width:380px) {

    .section-card {

        padding: 12px;

        border-radius: 14px;

    }


    .page-header h3 {

        font-size: 20px;

    }


    .section-title {

        font-size: 14px;

        padding: 10px 11px;

    }


    .section-title i {

        font-size: 17px;

    }


    .type-card {

        min-height: 130px;

        padding: 13px 8px;

    }


    .type-icon {

        width: 40px;

        height: 40px;

        font-size: 20px;

        border-radius: 10px;

    }


    .type-content h5 {

        font-size: 13px;

    }


    .type-content p {

        font-size: 10px;

    }


    .selected-indicator {

        font-size: 9px;

    }


    .unavailable-indicator {

        font-size: 8px;

    }

}

</style>
