<template>

<div class="sidebar">

    <div class="text-center py-4">

        <img
            src="@/assets/images/logo-placeholder.png"
            width="80"
        >

        <h5 class="mt-3">

            SFXC Portal

        </h5>

    </div>

    <RouterLink to="/student/dashboard">

        <i class="bi bi-house-door me-2"></i>

        Dashboard

    </RouterLink>

    <RouterLink to="/student/profile">

        <i class="bi bi-person me-2"></i>

        My Profile

    </RouterLink>

    <RouterLink to="/student/guardian">

        <i class="bi bi-people me-2"></i>

        Guardian

    </RouterLink>

    <RouterLink to="/student/academic">

        <i class="bi bi-mortarboard me-2"></i>

        Academic Background

    </RouterLink>

    <RouterLink to="/student/enrollment">

        <i class="bi bi-journal-check me-2"></i>

        Enrollment

    </RouterLink>

    <RouterLink to="/student/subjects">

        <i class="bi bi-book me-2"></i>

        Subjects

    </RouterLink>

    <RouterLink to="/student/assessment">

        <i class="bi bi-receipt me-2"></i>

        Assessment

    </RouterLink>

    <RouterLink to="/student/payment">

        <i class="bi bi-credit-card me-2"></i>

        Payment

    </RouterLink>

    <RouterLink to="/student/receipt">

        <i class="bi bi-file-earmark-text me-2"></i>

        Receipt

    </RouterLink>

</div>

</template>

<style scoped>

.sidebar{

    width:270px;

    min-height:100vh;

    background:#0d6efd;

    color:white;

}

.sidebar a{

    display:block;

    color:white;

    padding:15px 25px;

    text-decoration:none;

}

.sidebar a:hover{

    background:rgba(255,255,255,.15);

}

.router-link-active{

    background:rgba(255,255,255,.2);

}

</style>