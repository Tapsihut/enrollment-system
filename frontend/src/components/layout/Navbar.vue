<template>

<nav class="navbar navbar-expand-lg bg-white shadow-sm px-4">

    <div class="container-fluid">


        <span class="navbar-brand fw-bold">

            SFXC Enrollment System

        </span>



        <div class="dropdown">


            <button
            class="btn btn-light dropdown-toggle"
            data-bs-toggle="dropdown">


                <i class="bi bi-person-circle"></i>

                User


            </button>



            <ul class="dropdown-menu dropdown-menu-end">


                <li>

                    <router-link
                    class="dropdown-item"
                    to="/student/profile">

                        Profile

                    </router-link>

                </li>



                <li>

                    <a class="dropdown-item"
                    href="#">

                        Settings

                    </a>

                </li>


                <li>
                    <hr class="dropdown-divider">
                </li>



                <li>

                    <a class="dropdown-item text-danger"
                    href="#">

                        Logout

                    </a>

                </li>


            </ul>


        </div>


    </div>


</nav>


</template>