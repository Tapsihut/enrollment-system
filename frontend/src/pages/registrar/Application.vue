<template>

<div class="applications">

    <!-- Header -->

    <div class="page-header">

        <div>

            <h2>Enrollment Applications</h2>

            <p>
                Review and manage student enrollment applications.
            </p>

        </div>

        <button
            class="btn btn-success"
            @click="loadApplications"
        >
            <i class="bi bi-arrow-clockwise me-2"></i>

            Refresh

        </button>

    </div>


    <!-- FILTER CARD -->

    <div class="filter-card">

        <div class="row g-3">

            <!-- SEARCH -->

            <div class="col-lg-3">

                <label class="form-label">

                    Search

                </label>

                <input

                    v-model="search"

                    @keyup.enter="loadApplications"

                    class="form-control"

                    placeholder="Student name..."

                >

            </div>


            <!-- COURSE -->

            <div class="col-lg-2">

                <label class="form-label">

                    Course

                </label>

                <select

                    class="form-select"

                    v-model="course"

                    @change="loadApplications"

                >

                    <option value="">

                        All Courses

                    </option>

                    <option

                        v-for="c in courses"

                        :key="c.id"

                        :value="c.name"

                    >

                        {{ c.code }}

                    </option>

                </select>

            </div>


            <!-- STATUS -->

            <div class="col-lg-2">

                <label class="form-label">

                    Status

                </label>

                <select

                    class="form-select"

                    v-model="status"

                    @change="loadApplications"

                >

                    <option value="">

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


            <!-- SORT -->

            <div class="col-lg-2">

                <label class="form-label">

                    Sort

                </label>

                <select

                    class="form-select"

                    v-model="sort"

                    @change="loadApplications"

                >

                    <option value="desc">

                        Newest

                    </option>

                    <option value="asc">

                        Oldest

                    </option>

                </select>

            </div>


            <!-- ROWS -->

            <div class="col-lg-2">

                <label class="form-label">

                    Show

                </label>

                <select

                    class="form-select"

                    v-model="perPage"

                    @change="loadApplications"

                >

                    <option :value="10">10</option>

                    <option :value="20">20</option>

                    <option :value="30">30</option>

                    <option :value="50">50</option>

                </select>

            </div>


            <!-- SEARCH BUTTON -->

            <div class="col-lg-1 d-grid">

                <label class="form-label">

                    &nbsp;

                </label>

                <button

                    class="btn btn-success"

                    @click="loadApplications"

                >

                    <i class="bi bi-search"></i>

                </button>

            </div>

        </div>

    </div>


    <!-- TABLE -->

    <div class="table-card">

        <div
            v-if="loading"
            class="text-center py-5"
        >

            <div class="spinner-border text-success"></div>

            <p class="mt-3">

                Loading...

            </p>

        </div>

        <div
            v-else
            class="table-responsive"
        >

            <table class="table table-hover align-middle">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Student</th>

                        <th>Course</th>

                        <th>Year</th>

                        <th>School Year</th>

                        <th>Semester</th>

                        <th>Status</th>

                        <th width="120">

                            Action

                        </th>

                    </tr>

                </thead>

                <tbody>

                    <tr

                        v-for="(item,index) in applications"

                        :key="item.id"

                    >

                        <td>

                            {{ index + 1 + ((currentPage-1) * perPage) }}

                        </td>

                        <td>

                            {{ item.student?.first_name }}

                            {{ item.student?.last_name }}

                        </td>

                        <td>

                            {{ item.course?.name }}

                        </td>

                        <td>

                            {{ item.year_level }}

                        </td>

                        <td>

                            {{ item.schoolYear?.school_year }}

                        </td>

                        <td>

                            {{ item.semester?.name }}

                        </td>

                        <td>

                            <span

                                class="badge"

                                :class="{

                                    'bg-warning text-dark':item.status==='Pending',

                                    'bg-success':item.status==='Approved',

                                    'bg-danger':item.status==='Rejected',
                                    'bg-info':item.status==='Enrolled'

                                }"

                            >

                                {{ item.status }}

                            </span>

                        </td>

                        <td>

                            <router-link

                                :to="`/registrar/applications/${item.id}`"

                                class="btn btn-primary btn-sm"

                            >

                                <i class="bi bi-eye"></i>

                                View

                            </router-link>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>


    <!-- FOOTER -->

    <div class="table-footer">

        <div class="table-info">

            Showing

            <strong>

                {{ from }}

            </strong>

            -

            <strong>

                {{ to }}

            </strong>

            of

            <strong>

                {{ total }}

            </strong>

            entries

        </div>

        <nav>

            <ul class="pagination mb-0">

                <li

                    class="page-item"

                    :class="{disabled:currentPage===1}"

                >

                    <button

                        class="page-link"

                        @click="changePage(currentPage-1)"

                    >

                        Previous

                    </button>

                </li>

                <li

                    v-for="page in lastPage"

                    :key="page"

                    class="page-item"

                    :class="{active:page===currentPage}"

                >

                    <button

                        class="page-link"

                        @click="changePage(page)"

                    >

                        {{ page }}

                    </button>

                </li>

                <li

                    class="page-item"

                    :class="{disabled:currentPage===lastPage}"

                >

                    <button

                        class="page-link"

                        @click="changePage(currentPage+1)"

                    >

                        Next

                    </button>

                </li>

            </ul>

        </nav>

    </div>

</div>

</template>

<script setup>

import { ref,onMounted } from "vue"

import api from "@/services/api"

const loading = ref(false)

const applications = ref([])

const courses = ref([])

const search = ref("")

const course = ref("")

const status = ref("")

const sort = ref("desc")

const perPage = ref(10)

const currentPage = ref(1)

const lastPage = ref(1)

const total = ref(0)

const from = ref(0)

const to = ref(0)

async function loadOptions(){

    try{

        const {data}=await api.get("/enrollment/options")

        courses.value=data.courses

    }

    catch(error){

        console.log(error)

    }

}

async function loadApplications(){

    loading.value=true

    try{

        const {data}=await api.get(

            "/registrar/enrollments",

            {

                params:{

                    search:search.value,

                    course:course.value,

                    status:status.value,

                    sort:sort.value,

                    per_page:perPage.value,

                    page:currentPage.value

                }

            }

        )

        applications.value=data.data

        currentPage.value=data.current_page

        lastPage.value=data.last_page

        total.value=data.total

        from.value=data.from

        to.value=data.to

    }

    catch(error){

        console.log(error)

    }

    finally{

        loading.value=false

    }

}

function changePage(page){

    if(page<1)return

    if(page>lastPage.value)return

    currentPage.value=page

    loadApplications()

}

onMounted(()=>{

    loadOptions()

    loadApplications()

})
</script>
<style scoped>

.applications{
    padding:20px;
}

/* HEADER */

.page-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
    gap:15px;
    flex-wrap:wrap;
}

.page-header h2{
    margin:0;
    color:#064E2A;
    font-weight:800;
}

.page-header p{
    margin:5px 0 0;
    color:#6B7280;
}

/* FILTER */

.filter-card{
    background:#fff;
    border-radius:18px;
    padding:20px;
    margin-bottom:20px;
    box-shadow:0 8px 25px rgba(0,0,0,.08);
}

.filter-card label{
    font-weight:600;
    color:#064E2A;
}

.form-control,
.form-select{
    border-radius:10px;
    min-height:45px;
}

.form-control:focus,
.form-select:focus{
    border-color:#198754;
    box-shadow:0 0 0 .15rem rgba(25,135,84,.2);
}

/* TABLE */

.table-card{
    background:#fff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 10px 30px rgba(0,0,0,.08);
}

.table{
    margin:0;
}

.table thead{
    background:#0B6B3A;
    color:#fff;
}

.table thead th{
    border:none;
    padding:15px;
    white-space:nowrap;
}

.table tbody td{
    padding:15px;
    vertical-align:middle;
}

.table tbody tr:hover{
    background:#f8faf9;
}

.badge{
    padding:8px 14px;
    border-radius:50px;
    font-size:12px;
    font-weight:600;
}

/* FOOTER */

.table-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:15px;
    padding:20px;
    background:#fff;
    border-top:1px solid #eee;
}

.table-info{
    color:#6B7280;
    font-size:14px;
}

.pagination{
    margin:0;
}

.page-link{
    color:#0B6B3A;
    border-radius:8px;
    margin:0 3px;
}

.page-item.active .page-link{
    background:#0B6B3A;
    border-color:#0B6B3A;
    color:#fff;
}

.page-link:hover{
    background:#198754;
    color:#fff;
}

/* BUTTONS */

.btn{
    border-radius:10px;
}

.btn-primary{
    background:#0d6efd;
}

.btn-success{
    background:#198754;
}

/* LOADING */

.spinner-border{
    width:3rem;
    height:3rem;
}

/* MOBILE */

@media (max-width:992px){

    .filter-card .row>*{
        margin-bottom:12px;
    }

}

@media (max-width:768px){

    .applications{
        padding:12px;
    }

    .page-header{
        flex-direction:column;
        align-items:flex-start;
    }

    .table-footer{
        flex-direction:column;
        align-items:flex-start;
    }

    .pagination{
        overflow-x:auto;
        flex-wrap:nowrap;
    }

    .page-link{
        white-space:nowrap;
    }

}

@media (max-width:576px){

    .table{
        font-size:13px;
    }

    .page-header h2{
        font-size:24px;
    }

    .btn{
        width:100%;
    }

}

</style>