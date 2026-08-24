<template>

<div>

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold">
                Enrollment Applications
            </h2>

            <p class="text-muted">
                Review and process student enrollment requests
            </p>

        </div>


        <div>

            <button class="btn btn-primary">

                <i class="bi bi-arrow-clockwise"></i>
                Refresh

            </button>

        </div>

    </div>



    <!-- FILTERS -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">


            <div class="row">


                <div class="col-md-4">

                    <label class="form-label">
                        Search Student
                    </label>


                    <input
                        type="text"
                        class="form-control"
                        placeholder="Student name or ID"
                    >

                </div>



                <div class="col-md-3">

                    <label class="form-label">
                        Status
                    </label>


                    <select class="form-select">

                        <option>
                            All
                        </option>

                        <option>
                            Pending
                        </option>

                        <option>
                            Approved
                        </option>

                        <option>
                            Rejected
                        </option>


                    </select>

                </div>



                <div class="col-md-3">

                    <label class="form-label">
                        Course
                    </label>


                    <select class="form-select">

                        <option>
                            All Courses
                        </option>

                        <option>
                            BSIT
                        </option>

                        <option>
                            BSBA
                        </option>


                    </select>


                </div>



            </div>


        </div>

    </div>





    <!-- APPLICATION TABLE -->


    <div class="card shadow-sm border-0">


        <div class="card-body">


            <table class="table table-hover align-middle">


                <thead class="table-light">


                    <tr>

                        <th>
                            Student ID
                        </th>


                        <th>
                            Name
                        </th>


                        <th>
                            Course
                        </th>


                        <th>
                            Year Level
                        </th>


                        <th>
                            Status
                        </th>


                        <th>
                            Action
                        </th>


                    </tr>


                </thead>



                <tbody>


                    <tr>


                        <td>
                            2026-00001
                        </td>


                        <td>
                            Juan Dela Cruz
                        </td>


                        <td>
                            BSIT
                        </td>


                        <td>
                            1st Year
                        </td>



                        <td>

                            <span class="badge bg-warning text-dark">

                                Pending

                            </span>


                        </td>



                        <td>


                            <button
                            class="btn btn-sm btn-primary me-2"
                            data-bs-toggle="modal"
                            data-bs-target="#viewModal">

                                View

                            </button>



                            <button
                            class="btn btn-sm btn-success">

                                Approve

                            </button>



                            <button
                            class="btn btn-sm btn-danger ms-2">

                                Reject

                            </button>



                        </td>


                    </tr>



                </tbody>


            </table>



        </div>


    </div>







    <!-- STUDENT DETAILS MODAL -->


    <div
    class="modal fade"
    id="viewModal"
    tabindex="-1">


        <div class="modal-dialog modal-lg">


            <div class="modal-content">


                <div class="modal-header">


                    <h5 class="modal-title">

                        Student Information

                    </h5>


                    <button
                    class="btn-close"
                    data-bs-dismiss="modal">

                    </button>


                </div>




                <div class="modal-body">


                    <div class="row">


                        <div class="col-md-6 mb-3">

                            <label>
                                Student ID
                            </label>

                            <input
                            class="form-control"
                            value="2026-00001"
                            readonly>

                        </div>



                        <div class="col-md-6 mb-3">

                            <label>
                                Full Name
                            </label>

                            <input
                            class="form-control"
                            value="Juan Dela Cruz"
                            readonly>

                        </div>




                        <div class="col-md-6 mb-3">

                            <label>
                                Course
                            </label>

                            <input
                            class="form-control"
                            value="BSIT"
                            readonly>

                        </div>




                        <div class="col-md-6 mb-3">

                            <label>
                                Year Level
                            </label>

                            <input
                            class="form-control"
                            value="1"
                            readonly>

                        </div>



                    </div>



                    <hr>



                    <h5>
                        Requirements
                    </h5>



                    <ul class="list-group">


                        <li class="list-group-item">

                            ✅ Birth Certificate

                        </li>


                        <li class="list-group-item">

                            ✅ Form 138

                        </li>


                        <li class="list-group-item">

                            ✅ Good Moral

                        </li>


                        <li class="list-group-item">

                            ✅ 2x2 Picture

                        </li>



                    </ul>



                </div>




                <div class="modal-footer">


                    <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">

                        Close

                    </button>


                    <button
                    class="btn btn-success">

                        Approve Enrollment

                    </button>


                </div>



            </div>


        </div>


    </div>



</div>


</template>