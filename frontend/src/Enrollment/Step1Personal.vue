<template>
    <div>

        <h4 class="step-title">
            Step 1 - Student Type
        </h4>

        <p class="text-muted mb-4">
            Select the type of enrollment that best describes you.
        </p>

        <div class="row g-4">
<!-- Freshmen -->
<div class="col-md-6">

    <div
        class="type-card"
        :class="{ active: model.student_type === 'Freshmen' }"
        @click="selectType('Freshmen')"
    >

        <i class="bi bi-mortarboard-fill icon"></i>

        <h5>Freshmen</h5>

        <p>
            First time entering college.
        </p>

    </div>

</div>



<!-- Transferee -->
<div class="col-md-6">

    <div
        class="type-card"
        :class="{ active: model.student_type === 'Transferee' }"
        @click="selectType('Transferee')"
    >

        <i class="bi bi-arrow-left-right icon"></i>

        <h5>Transferee</h5>

        <p>
            Coming from another college or university.
        </p>

    </div>

</div>



<!-- Continuing -->
<div class="col-md-6">

    <div
        class="type-card"
        :class="{ active: model.student_type === 'Continuing' }"
        @click="selectType('Continuing')"
    >

        <i class="bi bi-book-fill icon"></i>

        <h5>Continuing</h5>

        <p>
            Currently enrolled student.
        </p>

    </div>

</div>



<!-- Returnee -->
<div class="col-md-6">

    <div
        class="type-card"
        :class="{ active: model.student_type === 'Returnee' }"
        @click="selectType('Returnee')"
    >

        <i class="bi bi-arrow-repeat icon"></i>

        <h5>Returnee</h5>

        <p>
            Returning after stopping enrollment.
        </p>

    </div>

</div>

        </div>

    </div>
</template>

<script setup>
const model = defineModel()


function selectType(type){

    model.value.student_type = type

    console.log(
        "Selected Student Type:",
        model.value.student_type
    )

}
</script>

<style scoped>

.step-title{

    color:#064E2A;

    font-weight:800;

}

.type-card{

    border:2px solid #E5E7EB;

    border-radius:18px;

    padding:30px;

    cursor:pointer;

    transition:.3s;

    text-align:center;

    height:100%;

    background:white;

}

.type-card:hover{

    border-color:#0B6B3A;

    transform:translateY(-5px);

    box-shadow:0 10px 25px rgba(0,0,0,.08);

}

.type-card.active{

    background:#0B6B3A;

    color:white;

    border-color:#0B6B3A;

}

.icon{

    font-size:40px;

    color:#0B6B3A;

    margin-bottom:15px;

}

.type-card.active .icon{

    color:white;

}

.type-card h5{

    font-weight:700;

}

.type-card p{

    margin:0;

    opacity:.8;

}

</style>