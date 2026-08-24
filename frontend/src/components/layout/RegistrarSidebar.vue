<template>

<div class="sidebar">


    <!-- HEADER -->

    <div class="sidebar-header">


        <div class="logo-circle">

            <i class="bi bi-building"></i>

        </div>


        <div>

            <h5>

                SFXC

            </h5>

            <small>

                Registrar Portal

            </small>

        </div>


    </div>




    <!-- MENU -->

    <div class="menu">


        <p class="menu-title">

            MAIN MENU

        </p>



        <router-link 
        to="/registrar/dashboard"
        >

            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>

        </router-link>




        <router-link 
        to="/registrar/applications"
        >

            <i class="bi bi-file-earmark-text"></i>

            <span>
                Applications
            </span>

            <span class="badge">

                New

            </span>

        </router-link>





        <router-link 
        to="/registrar/students"
        >

            <i class="bi bi-people"></i>

            <span>
                Students
            </span>

        </router-link>





        <router-link 
        to="/registrar/curriculum"
        >

            <i class="bi bi-journal-bookmark"></i>

            <span>
                Curriculum
            </span>

        </router-link>





        <router-link 
        to="/registrar/reports"
        >

            <i class="bi bi-bar-chart-line"></i>

            <span>
                Reports
            </span>

        </router-link>



    </div>





    <!-- FOOTER -->

    <div class="sidebar-footer">


        <i class="bi bi-shield-check"></i>


        <div>

            <small>

                Logged in as

            </small>


            <strong>

                Registrar

            </strong>


        </div>


    </div>



</div>


</template>



<style scoped>


.sidebar{


width:270px;

min-height:100vh;

background:#064E2A;

color:white;

display:flex;

flex-direction:column;

padding:20px;

box-shadow:5px 0 20px rgba(0,0,0,.15);

}





/* HEADER */


.sidebar-header{


display:flex;

align-items:center;

gap:15px;

padding:20px 10px;

margin-bottom:30px;

border-bottom:1px solid rgba(255,255,255,.15);

}



.logo-circle{


width:55px;

height:55px;

border-radius:15px;

background:#0B6B3A;

display:flex;

align-items:center;

justify-content:center;

font-size:25px;

}



.sidebar-header h5{


margin:0;

font-size:22px;

font-weight:800;

}



.sidebar-header small{


color:#c7f9d8;

}





/* MENU */


.menu{


flex:1;

}



.menu-title{


font-size:12px;

letter-spacing:1px;

color:#b7d8c3;

margin-bottom:15px;

}




.menu a{


display:flex;

align-items:center;

gap:15px;

padding:14px 15px;

margin-bottom:8px;

border-radius:12px;

color:white;

text-decoration:none;

transition:.3s;

position:relative;

font-weight:500;

}




.menu a i{


font-size:20px;

width:25px;

}




.menu a:hover{


background:rgba(255,255,255,.12);

transform:translateX(5px);

}




.menu a.router-link-active{


background:white;

color:#064E2A;

font-weight:700;

box-shadow:0 8px 20px rgba(0,0,0,.15);

}





.badge{


margin-left:auto;

background:#9cffc8;

color:#064E2A;

font-size:11px;

padding:3px 8px;

border-radius:20px;

font-weight:bold;

}





/* FOOTER */


.sidebar-footer{


background:rgba(255,255,255,.1);

padding:15px;

border-radius:15px;

display:flex;

align-items:center;

gap:12px;

}



.sidebar-footer i{


font-size:25px;

color:#9cffc8;

}



.sidebar-footer small{


display:block;

color:#c7f9d8;

}



.sidebar-footer strong{


font-size:14px;

}



</style>