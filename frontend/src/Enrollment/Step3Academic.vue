<template>
    <div class="academic-page">

        <!-- ================= PAGE HEADER ================= -->

        <div class="page-header">

            <div class="step-badge">
                <i class="bi bi-mortarboard-fill"></i>
                Step 3
            </div>

            <h3>Educational Background</h3>

            <p>
                Provide your previous educational information.
            </p>

        </div>


        <!-- =========================================================
             FRESHMEN
        ========================================================== -->

        <div
            v-if="
                studentType === 'freshmen' ||
                studentType === 'freshman'
            "
            class="section-card"
        >

            <div class="section-title">

                <i class="bi bi-mortarboard-fill"></i>

                <div>
                    <span>Senior High School Information</span>
                    <small>Previous senior high school details</small>
                </div>

            </div>


            <div class="row">

                <!-- SCHOOL -->

                <div class="col-md-6 mb-3">

                    <label>Last School Attended</label>

                    <div class="school-search-wrapper">

                        <input
                            type="text"
                            class="form-control"
                            v-model="schoolSearch"
                            placeholder="Search school name..."
                            autocomplete="off"
                            @input="searchSchools"
                            @focus="showSchoolResults = true"
                        >

                        <div
                            v-if="searchingSchools"
                            class="search-loading"
                        >
                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>

                            <span>Searching...</span>
                        </div>


                        <!-- RESULTS -->

                        <div
                            v-if="
                                showSchoolResults &&
                                schoolSearch.trim().length >= 2 &&
                                schools.length
                            "
                            class="school-results"
                        >

                            <button
                                v-for="school in schools"
                                :key="school.id"
                                type="button"
                                class="school-option"
                                @click="selectSchool(school)"
                            >

                                <div class="school-name">
                                    {{ school.school_name }}
                                </div>

                                <div class="school-details">

                                    <span v-if="school.school_id">
                                        ID: {{ school.school_id }}
                                    </span>

                                    <span v-if="school.municipality">
                                        {{ school.municipality }}
                                    </span>

                                    <span v-if="school.province">
                                        {{ school.province }}
                                    </span>

                                </div>

                            </button>


                            <button
                                type="button"
                                class="manual-option"
                                @click="enableManualSchool"
                            >

                                <i class="bi bi-pencil-square"></i>

                                School not listed? Enter manually

                            </button>

                        </div>


                        <!-- NO RESULTS -->

                        <div
                            v-if="
                                showSchoolResults &&
                                schoolSearch.trim().length >= 2 &&
                                !searchingSchools &&
                                schools.length === 0
                            "
                            class="school-empty"
                        >

                            <div class="empty-icon">
                                <i class="bi bi-building"></i>
                            </div>

                            <div class="empty-content">

                                <strong>
                                    No school found
                                </strong>

                                <small>
                                    You may enter your school information manually.
                                </small>

                            </div>

                            <button
                                type="button"
                                class="manual-btn"
                                @click="enableManualSchool"
                            >
                                Enter Manually
                            </button>

                        </div>

                    </div>


                    <!-- SELECTED -->

                    <small
                        v-if="model.school_id"
                        class="selected-school-note"
                    >

                        <i class="bi bi-check-circle-fill"></i>

                        School selected from school directory

                    </small>


                    <!-- MANUAL -->

                    <small
                        v-else-if="manualSchool"
                        class="manual-school-note"
                    >

                        <i class="bi bi-pencil-fill"></i>

                        Manual school entry

                    </small>

                </div>


                <!-- SCHOOL ADDRESS -->

                <div class="col-md-6 mb-3">

                    <label>School Address</label>

                    <input
                        class="form-control"
                        v-model="model.school_address"
                        placeholder="School address"
                    >

                </div>


                <!-- STRAND -->

                <div class="col-md-4 mb-3">

                    <label>Strand</label>

                    <select
                        class="form-select"
                        v-model="model.strand"
                    >

                        <option value="">
                            Select Strand
                        </option>

                        <option value="STEM">
                            STEM
                        </option>

                        <option value="ABM">
                            ABM
                        </option>

                        <option value="HUMSS">
                            HUMSS
                        </option>

                        <option value="GAS">
                            GAS
                        </option>

                        <option value="TVL">
                            TVL
                        </option>

                        <option value="Arts and Design">
                            Arts and Design
                        </option>

                        <option value="Sports">
                            Sports
                        </option>

                    </select>

                </div>


                <!-- GRADUATION YEAR -->

                <div class="col-md-4 mb-3">

                    <label>Graduation Year</label>

                    <select
                        class="form-select"
                        v-model="model.graduation_year"
                    >

                        <option value="">
                            Select Graduation Year
                        </option>

                        <option
                            v-for="year in graduationYears"
                            :key="year"
                            :value="year"
                        >
                            {{ year }}
                        </option>

                    </select>

                </div>


                <!-- GWA -->

                <div class="col-md-4 mb-3">

                    <label>
                        General Weighted Average (GWA)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        inputmode="decimal"
                        class="form-control"
                        v-model="model.gwa"
                        placeholder="e.g. 90.50"
                    >

                </div>

            </div>

        </div>


        <!-- =========================================================
             TRANSFEREE
        ========================================================== -->

        <div
            v-else-if="
                studentType === 'transferee' ||
                studentType === 'transfer'
            "
            class="section-card"
        >

            <div class="section-title">

                <i class="bi bi-arrow-left-right"></i>

                <div>
                    <span>Previous College Information</span>
                    <small>Previous college records</small>
                </div>

            </div>


            <div class="row">

                <!-- SCHOOL -->

                <div class="col-md-6 mb-3">

                    <label>Previous School</label>

                    <div class="school-search-wrapper">

                        <input
                            type="text"
                            class="form-control"
                            v-model="schoolSearch"
                            placeholder="Search previous school..."
                            autocomplete="off"
                            @input="searchSchools"
                            @focus="showSchoolResults = true"
                        >

                        <div
                            v-if="searchingSchools"
                            class="search-loading"
                        >

                            <span
                                class="spinner-border spinner-border-sm"
                            ></span>

                            <span>Searching...</span>

                        </div>


                        <!-- RESULTS -->

                        <div
                            v-if="
                                showSchoolResults &&
                                schoolSearch.trim().length >= 2 &&
                                schools.length
                            "
                            class="school-results"
                        >

                            <button
                                v-for="school in schools"
                                :key="school.id"
                                type="button"
                                class="school-option"
                                @click="selectSchool(school)"
                            >

                                <div class="school-name">
                                    {{ school.school_name }}
                                </div>

                                <div class="school-details">

                                    <span v-if="school.school_id">
                                        ID: {{ school.school_id }}
                                    </span>

                                    <span v-if="school.municipality">
                                        {{ school.municipality }}
                                    </span>

                                    <span v-if="school.province">
                                        {{ school.province }}
                                    </span>

                                </div>

                            </button>


                            <button
                                type="button"
                                class="manual-option"
                                @click="enableManualSchool"
                            >

                                <i class="bi bi-pencil-square"></i>

                                School not listed? Enter manually

                            </button>

                        </div>


                        <!-- NO RESULTS -->

                        <div
                            v-if="
                                showSchoolResults &&
                                schoolSearch.trim().length >= 2 &&
                                !searchingSchools &&
                                schools.length === 0
                            "
                            class="school-empty"
                        >

                            <div class="empty-icon">
                                <i class="bi bi-building"></i>
                            </div>

                            <div class="empty-content">

                                <strong>
                                    No school found
                                </strong>

                                <small>
                                    You may enter your school information manually.
                                </small>

                            </div>

                            <button
                                type="button"
                                class="manual-btn"
                                @click="enableManualSchool"
                            >
                                Enter Manually
                            </button>

                        </div>

                    </div>


                    <small
                        v-if="model.school_id"
                        class="selected-school-note"
                    >

                        <i class="bi bi-check-circle-fill"></i>

                        School selected from school directory

                    </small>


                    <small
                        v-else-if="manualSchool"
                        class="manual-school-note"
                    >

                        <i class="bi bi-pencil-fill"></i>

                        Manual school entry

                    </small>

                </div>


                <!-- ADDRESS -->

                <div class="col-md-6 mb-3">

                    <label>School Address</label>

                    <input
                        class="form-control"
                        v-model="model.school_address"
                        placeholder="School address"
                    >

                </div>


                <!-- PREVIOUS COURSE -->

                <div class="col-md-4 mb-3">

                    <label>Previous Course</label>

                    <input
                        class="form-control"
                        v-model="model.previous_course"
                        placeholder="Previous course"
                    >

                </div>


                <!-- UNITS -->

                <div class="col-md-4 mb-3">

                    <label>Units Earned</label>

                    <input
                        type="number"
                        min="0"
                        inputmode="decimal"
                        class="form-control"
                        v-model="model.units_earned"
                        placeholder="Units earned"
                    >

                </div>


                <!-- GWA -->

                <div class="col-md-4 mb-3">

                    <label>
                        General Weighted Average (GWA)
                    </label>

                    <input
                        type="number"
                        step="0.01"
                        min="0"
                        max="100"
                        inputmode="decimal"
                        class="form-control"
                        v-model="model.gwa"
                        placeholder="e.g. 90.50"
                    >

                </div>


                <!-- SCHOOL YEAR -->

                <div class="col-md-6 mb-3">

                    <label>Last School Year</label>

                    <select
                        class="form-select"
                        v-model="model.last_school_year"
                    >

                        <option value="">
                            Select School Year
                        </option>

                        <option
                            v-for="year in schoolYears"
                            :key="year"
                            :value="year"
                        >
                            {{ year }}
                        </option>

                    </select>

                </div>


                <!-- SEMESTER -->

                <div class="col-md-6 mb-3">

                    <label>Last Semester</label>

                    <select
                        class="form-select"
                        v-model="model.last_semester"
                    >

                        <option value="">
                            Select Semester
                        </option>

                        <option value="First Semester">
                            First Semester
                        </option>

                        <option value="Second Semester">
                            Second Semester
                        </option>

                        <option value="Summer">
                            Summer
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <!-- =========================================================
             RETURNEE
        ========================================================== -->

        <div
            v-else-if="
                studentType === 'returnee' ||
                studentType === 'returning'
            "
            class="section-card"
        >

            <div class="section-title">

                <i class="bi bi-arrow-repeat"></i>

                <div>
                    <span>Previous Enrollment Information</span>
                    <small>Previous enrollment details</small>
                </div>

            </div>


            <div class="row">

                <!-- SCHOOL YEAR -->

                <div class="col-md-6 mb-3">

                    <label>Last School Year</label>

                    <select
                        class="form-select"
                        v-model="model.last_school_year"
                    >

                        <option value="">
                            Select School Year
                        </option>

                        <option
                            v-for="year in schoolYears"
                            :key="year"
                            :value="year"
                        >
                            {{ year }}
                        </option>

                    </select>

                </div>


                <!-- SEMESTER -->

                <div class="col-md-6 mb-3">

                    <label>Last Semester</label>

                    <select
                        class="form-select"
                        v-model="model.last_semester"
                    >

                        <option value="">
                            Select Semester
                        </option>

                        <option value="First Semester">
                            First Semester
                        </option>

                        <option value="Second Semester">
                            Second Semester
                        </option>

                        <option value="Summer">
                            Summer
                        </option>

                    </select>

                </div>

            </div>

        </div>


        <!-- =========================================================
             CONTINUING
        ========================================================== -->

        <div
            v-else-if="studentType === 'continuing'"
            class="section-card"
        >

            <div class="section-title">

                <i class="bi bi-book-fill"></i>

                <div>
                    <span>Continuing Student</span>
                    <small>Academic information</small>
                </div>

            </div>


            <div class="info-box">

                <div class="info-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <div>

                    <h5>
                        No Academic Information Required
                    </h5>

                    <p>
                        Your academic records already exist in the
                        Student Information System. You may proceed
                        to the next step.
                    </p>

                </div>

            </div>

        </div>


        <!-- =========================================================
             UNKNOWN
        ========================================================== -->

        <div
            v-else
            class="section-card"
        >

            <div class="info-box info-box-warning">

                <div class="info-icon">
                    <i class="bi bi-info-circle-fill"></i>
                </div>

                <div>

                    <h5>
                        Select Student Type
                    </h5>

                    <p>
                        Please select your student type in Step 1
                        to continue.
                    </p>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    computed,
    ref,
    onMounted,
    onBeforeUnmount
} from 'vue'


const model = defineModel()


/*
|--------------------------------------------------------------------------
| STUDENT TYPE
|--------------------------------------------------------------------------
*/

const studentType = computed(() => {

    return String(
        model.value.student_type || ''
    )
        .trim()
        .toLowerCase()

})


/*
|--------------------------------------------------------------------------
| API
|--------------------------------------------------------------------------
*/

const API_BASE_URL =
    import.meta.env.VITE_API_URL ||
    'https://sfxc-enrollment.free.nf/api'

/*
|--------------------------------------------------------------------------
| SCHOOL SEARCH
|--------------------------------------------------------------------------
*/

const schoolSearch = ref('')

const schools = ref([])

const searchingSchools = ref(false)

const showSchoolResults = ref(false)

const manualSchool = ref(false)

let searchTimer = null


async function searchSchools() {

    clearTimeout(searchTimer)

    model.value.school_id = null

    manualSchool.value = false

    const search =
        schoolSearch.value.trim()


    if (search.length < 2) {

        schools.value = []

        showSchoolResults.value = false

        return

    }


    showSchoolResults.value = true


    searchTimer = setTimeout(
        async () => {

            searchingSchools.value = true

            try {

                const response =
                    await fetch(
                        `${API_BASE_URL}/schools/search?search=${encodeURIComponent(search)}`,
                        {
                            headers: {
                                Accept:
                                    'application/json'
                            }
                        }
                    )


                if (!response.ok) {

                    throw new Error(
                        'Unable to search schools.'
                    )

                }


                const data =
                    await response.json()


                schools.value =
                    Array.isArray(
                        data.data
                    )
                        ? data.data
                        : []

            }
            catch (error) {

                console.error(
                    'School search error:',
                    error
                )

                schools.value = []

            }
            finally {

                searchingSchools.value = false

            }

        },
        350
    )

}


/*
|--------------------------------------------------------------------------
| SELECT SCHOOL
|--------------------------------------------------------------------------
*/

function selectSchool(school) {

    model.value.school_id =
        school.id


    model.value.last_school =
        school.school_name


    model.value.school_address =
        school.address ||
        buildSchoolAddress(school)


    schoolSearch.value =
        school.school_name


    manualSchool.value = false

    schools.value = []

    showSchoolResults.value = false

}


/*
|--------------------------------------------------------------------------
| MANUAL SCHOOL
|--------------------------------------------------------------------------
*/

function enableManualSchool() {

    model.value.school_id = null


    model.value.last_school =
        schoolSearch.value.trim()


    manualSchool.value = true

    schools.value = []

    showSchoolResults.value = false

}


/*
|--------------------------------------------------------------------------
| BUILD SCHOOL ADDRESS
|--------------------------------------------------------------------------
*/

function buildSchoolAddress(school) {

    const parts = []


    if (school.address) {

        parts.push(
            school.address
        )

    }


    if (
        school.barangay &&
        !String(
            school.address || ''
        )
            .toLowerCase()
            .includes(
                String(
                    school.barangay
                )
                    .toLowerCase()
            )
    ) {

        parts.push(
            school.barangay
        )

    }


    if (
        school.municipality &&
        !String(
            school.address || ''
        )
            .toLowerCase()
            .includes(
                String(
                    school.municipality
                )
                    .toLowerCase()
            )
    ) {

        parts.push(
            school.municipality
        )

    }


    if (
        school.province &&
        !String(
            school.address || ''
        )
            .toLowerCase()
            .includes(
                String(
                    school.province
                )
                    .toLowerCase()
            )
    ) {

        parts.push(
            school.province
        )

    }


    return parts.join(', ')

}


/*
|--------------------------------------------------------------------------
| RESTORE EXISTING SCHOOL
|--------------------------------------------------------------------------
*/

async function loadExistingSchool() {

    if (!model.value.school_id) {

        schoolSearch.value =
            model.value.last_school || ''


        if (
            model.value.last_school
        ) {

            manualSchool.value = true

        }

        return

    }


    try {

        const response =
            await fetch(
                `${API_BASE_URL}/schools/${model.value.school_id}`,
                {
                    headers: {
                        Accept:
                            'application/json'
                    }
                }
            )


        if (!response.ok) {

            throw new Error(
                'Unable to load school.'
            )

        }


        const data =
            await response.json()


        const school =
            data.school ||
            data.data


        if (school) {

            schoolSearch.value =
                school.school_name


            model.value.last_school =
                school.school_name


            if (
                !model.value.school_address
            ) {

                model.value.school_address =
                    school.address ||
                    buildSchoolAddress(
                        school
                    )

            }


            manualSchool.value = false

        }

    }
    catch (error) {

        console.error(
            'Unable to restore school:',
            error
        )


        schoolSearch.value =
            model.value.last_school || ''


        manualSchool.value =
            !model.value.school_id

    }

}


/*
|--------------------------------------------------------------------------
| SCHOOL YEARS
|--------------------------------------------------------------------------
*/

const schoolYears = computed(() => {

    const years = []

    const currentYear =
        new Date().getFullYear()


    for (
        let year = currentYear + 1;
        year >= currentYear - 15;
        year--
    ) {

        years.push(
            `${year - 1}-${year}`
        )

    }


    return years

})


/*
|--------------------------------------------------------------------------
| GRADUATION YEARS
|--------------------------------------------------------------------------
*/

const graduationYears = computed(() => {

    const years = []

    const currentYear =
        new Date().getFullYear()


    for (
        let year = currentYear + 5;
        year >= currentYear - 30;
        year--
    ) {

        years.push(year)

    }


    return years

})


/*
|--------------------------------------------------------------------------
| CLOSE SCHOOL RESULTS
|--------------------------------------------------------------------------
*/

function closeSchoolResults(event) {

    const target =
        event.target


    if (
        !target.closest(
            '.school-search-wrapper'
        )
    ) {

        showSchoolResults.value =
            false

    }

}


/*
|--------------------------------------------------------------------------
| LIFECYCLE
|--------------------------------------------------------------------------
*/

onMounted(() => {

    loadExistingSchool()


    document.addEventListener(
        'click',
        closeSchoolResults
    )

})


onBeforeUnmount(() => {

    clearTimeout(searchTimer)


    document.removeEventListener(
        'click',
        closeSchoolResults
    )

})

</script>


<style scoped>

/* =========================================================
   BASE
========================================================= */

* {
    box-sizing: border-box;
}

.academic-page {
    width: 100%;
    max-width: 100%;
    padding: 8px;
    overflow-x: hidden;
}


/* =========================================================
   PAGE HEADER
========================================================= */

.page-header {
    margin-bottom: 20px;
}

.step-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;

    padding: 5px 11px;

    border-radius: 999px;

    background: #E8F5EE;
    color: #064E2A;

    font-size: 11px;
    font-weight: 700;

    margin-bottom: 8px;
}

.page-header h3 {
    margin: 0 0 5px;

    font-size: 27px;
    line-height: 1.25;

    font-weight: 800;

    color: #064E2A;
}

.page-header p {
    margin: 0;

    color: #6B7280;

    font-size: 14px;
    line-height: 1.5;
}


/* =========================================================
   CARD
========================================================= */

.section-card {
    background: #fff;

    border-radius: 18px;

    padding: 22px;

    margin-bottom: 18px;

    border: 1px solid #E5E7EB;

    box-shadow:
        0 6px 20px rgba(0,0,0,.05);
}


/* =========================================================
   SECTION TITLE
========================================================= */

.section-title {
    display: flex;
    align-items: center;

    gap: 9px;

    padding: 12px 15px;

    margin-bottom: 20px;

    border-radius: 11px;

    color: #fff;

    background:
        linear-gradient(
            135deg,
            #064E2A,
            #0B6B3A
        );

    font-size: 16px;
    font-weight: 700;
}

.section-title i {
    flex-shrink: 0;
    font-size: 19px;
}

.section-title div {
    display: flex;
    flex-direction: column;
}

.section-title small {
    margin-top: 2px;

    font-size: 10px;
    font-weight: 400;

    opacity: .8;
}


/* =========================================================
   LABELS
========================================================= */

label {
    display: block;

    margin-bottom: 6px;

    color: #374151;

    font-size: 13px;
    font-weight: 600;
}


/* =========================================================
   INPUTS
========================================================= */

.form-control,
.form-select {
    width: 100%;

    height: 46px;

    border-radius: 10px;

    border: 1px solid #D1D5DB;

    font-size: 14px;

    padding-left: 13px;
    padding-right: 13px;

    transition: .2s;
}

.form-control::placeholder {
    color: #9CA3AF;
}

.form-control:focus,
.form-select:focus {
    border-color: #0B6B3A;

    box-shadow:
        0 0 0 .15rem
        rgba(11,107,58,.12);
}


/* =========================================================
   SCHOOL SEARCH
========================================================= */

.school-search-wrapper {
    position: relative;
    width: 100%;
}

.search-loading {
    position: absolute;

    right: 12px;
    top: 50%;

    transform: translateY(-50%);

    display: flex;
    align-items: center;

    gap: 5px;

    color: #6B7280;

    font-size: 11px;

    pointer-events: none;
}


/* =========================================================
   SCHOOL RESULTS
========================================================= */

.school-results {
    position: absolute;

    top: calc(100% + 4px);

    left: 0;
    right: 0;

    z-index: 1000;

    background: #fff;

    border: 1px solid #D1D5DB;

    border-radius: 11px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.12);

    max-height: 300px;

    overflow-y: auto;

    -webkit-overflow-scrolling: touch;
}

.school-option {
    display: block;

    width: 100%;

    border: 0;

    background: #fff;

    text-align: left;

    padding: 12px 13px;

    border-bottom: 1px solid #F1F5F9;

    cursor: pointer;
}

.school-option:hover {
    background: #ECFDF5;
}

.school-name {
    color: #064E2A;

    font-size: 13px;
    font-weight: 700;

    line-height: 1.35;

    word-break: break-word;
}

.school-details {
    display: flex;
    flex-wrap: wrap;

    gap: 4px 8px;

    margin-top: 4px;

    color: #6B7280;

    font-size: 10px;

    line-height: 1.4;
}


/* =========================================================
   MANUAL OPTION
========================================================= */

.manual-option {
    width: 100%;

    border: 0;
    border-top: 1px solid #E5E7EB;

    background: #F8FAFC;

    color: #0B6B3A;

    text-align: left;

    padding: 11px 13px;

    font-size: 12px;
    font-weight: 600;

    cursor: pointer;
}

.manual-option:hover {
    background: #ECFDF5;
}


/* =========================================================
   NO SCHOOL FOUND
========================================================= */

.school-empty {
    position: absolute;

    top: calc(100% + 4px);

    left: 0;
    right: 0;

    z-index: 1000;

    display: flex;
    align-items: center;

    gap: 10px;

    padding: 13px;

    background: #fff;

    border: 1px solid #D1D5DB;

    border-radius: 11px;

    box-shadow:
        0 10px 25px rgba(0,0,0,.12);
}

.empty-icon {
    width: 35px;
    height: 35px;

    flex-shrink: 0;

    display: flex;
    align-items: center;
    justify-content: center;

    border-radius: 9px;

    background: #ECFDF5;
    color: #0B6B3A;

    font-size: 17px;
}

.empty-content {
    min-width: 0;
    flex: 1;
}

.school-empty strong {
    display: block;

    color: #374151;

    font-size: 12px;
}

.school-empty small {
    display: block;

    margin-top: 2px;

    color: #6B7280;

    font-size: 10px;

    line-height: 1.4;
}

.manual-btn {
    flex-shrink: 0;

    border: 0;

    border-radius: 7px;

    padding: 8px 10px;

    background: #064E2A;
    color: #fff;

    font-size: 10px;
    font-weight: 600;

    cursor: pointer;
}

.manual-btn:hover {
    background: #0B6B3A;
}


/* =========================================================
   SCHOOL STATUS
========================================================= */

.selected-school-note,
.manual-school-note {
    display: block;

    margin-top: 6px;

    font-size: 11px;

    line-height: 1.4;
}

.selected-school-note {
    color: #0B6B3A;
}

.manual-school-note {
    color: #B45309;
}


/* =========================================================
   INFO BOX
========================================================= */

.info-box {
    display: flex;

    align-items: flex-start;

    gap: 12px;

    padding: 17px;

    border-radius: 11px;

    background: #ECFDF5;

    border: 1px solid #A7F3D0;
}

.info-icon {
    flex-shrink: 0;
}

.info-box i {
    color: #0B6B3A;

    font-size: 28px;
}

.info-box h5 {
    margin: 0 0 5px;

    color: #064E2A;

    font-size: 15px;
    font-weight: 700;
}

.info-box p {
    margin: 0;

    color: #4B5563;

    font-size: 13px;

    line-height: 1.5;
}

.info-box-warning {
    background: #FFFBEB;
    border-color: #FDE68A;
}

.info-box-warning i {
    color: #B45309;
}

.info-box-warning h5 {
    color: #92400E;
}


/* =========================================================
   TABLET
========================================================= */

@media(max-width: 768px) {

    .academic-page {
        padding: 5px;
    }

    .page-header {
        margin-bottom: 16px;
    }

    .page-header h3 {
        font-size: 24px;
    }

    .section-card {
        padding: 16px;

        border-radius: 15px;

        margin-bottom: 15px;
    }

    .section-title {
        padding: 11px 13px;

        margin-bottom: 16px;

        font-size: 15px;
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media(max-width: 576px) {

    .academic-page {
        padding: 2px;
    }


    /* HEADER */

    .page-header {
        margin: 2px 4px 16px;
    }

    .step-badge {
        font-size: 10px;

        padding: 5px 9px;

        margin-bottom: 7px;
    }

    .page-header h3 {
        font-size: 21px;

        line-height: 1.25;
    }

    .page-header p {
        font-size: 13px;

        line-height: 1.45;
    }


    /* CARD */

    .section-card {
        padding: 13px;

        border-radius: 15px;

        margin-bottom: 13px;

        box-shadow:
            0 4px 15px rgba(0,0,0,.045);
    }


    /* TITLE */

    .section-title {
        padding: 10px 12px;

        border-radius: 10px;

        margin-bottom: 15px;

        gap: 8px;

        font-size: 14px;
    }

    .section-title i {
        font-size: 17px;
    }

    .section-title small {
        font-size: 9px;
    }


    /* LABEL */

    label {
        font-size: 12px;

        margin-bottom: 5px;
    }


    /* INPUT */

    .form-control,
    .form-select {
        height: 46px;

        min-height: 46px;

        border-radius: 9px;

        font-size: 13px;

        padding-left: 11px;
        padding-right: 11px;
    }


    /* BOOTSTRAP ROW */

    .row {
        --bs-gutter-x: .65rem;
        --bs-gutter-y: 0;
    }


    /* SCHOOL SEARCH */

    .school-results {
        max-height: 260px;

        border-radius: 10px;
    }

    .school-option {
        padding: 11px;
    }

    .school-name {
        font-size: 12px;
    }

    .school-details {
        font-size: 9px;

        gap: 3px 6px;
    }

    .manual-option {
        padding: 10px 11px;

        font-size: 11px;
    }


    /* NO RESULTS */

    .school-empty {
        flex-wrap: wrap;

        align-items: flex-start;

        padding: 11px;
    }

    .empty-icon {
        width: 32px;
        height: 32px;

        font-size: 15px;
    }

    .school-empty strong {
        font-size: 11px;
    }

    .school-empty small {
        font-size: 9px;
    }

    .manual-btn {
        width: 100%;

        margin-left: 42px;

        padding: 8px;

        font-size: 10px;
    }


    /* STATUS */

    .selected-school-note,
    .manual-school-note {
        font-size: 10px;
    }


    /* INFO */

    .info-box {
        padding: 14px;

        gap: 9px;
    }

    .info-box i {
        font-size: 23px;
    }

    .info-box h5 {
        font-size: 13px;
    }

    .info-box p {
        font-size: 11px;

        line-height: 1.5;
    }

}


/* =========================================================
   VERY SMALL PHONES
========================================================= */

@media(max-width: 380px) {

    .section-card {
        padding: 11px;

        border-radius: 13px;
    }

    .page-header h3 {
        font-size: 20px;
    }

    .page-header p {
        font-size: 12px;
    }

    .section-title {
        font-size: 13px;

        padding: 9px 10px;
    }

    .section-title i {
        font-size: 16px;
    }

    .form-control,
    .form-select {
        height: 44px;

        min-height: 44px;

        font-size: 12px;
    }

    label {
        font-size: 11px;
    }

    .school-name {
        font-size: 11px;
    }

    .info-box h5 {
        font-size: 12px;
    }

    .info-box p {
        font-size: 10px;
    }

}
</style>