<script setup>

import { ref, onMounted } from "vue"
import axios from "@/services/api"


/* =========================================================
   STATE
========================================================= */

const loading = ref(false)
const profileExists = ref(false)

const alert = ref({
    show: false,
    type: "",
    message: ""
})


/* =========================================================
   OPTIONS
========================================================= */

const nationalities = [
    "Filipino",
    "American",
    "Australian",
    "British",
    "Canadian",
    "Chinese",
    "French",
    "German",
    "Indian",
    "Indonesian",
    "Italian",
    "Japanese",
    "Korean",
    "Malaysian",
    "Singaporean",
    "Spanish",
    "Thai",
    "Vietnamese",
    "Other"
]

const religions = [
    "Roman Catholic",
    "Islam",
    "Iglesia ni Cristo",
    "Seventh-day Adventist",
    "Baptist",
    "Born Again Christian",
    "United Church of Christ in the Philippines",
    "Jehovah's Witnesses",
    "Church of Christ",
    "Aglipayan",
    "Protestant",
    "Other Christian",
    "Other",
    "None",
    "Prefer not to say"
]


/* =========================================================
   LOCATION
========================================================= */

const provinces = ref([])
const cities = ref([])
const barangays = ref([])

const loadingProvinces = ref(false)
const loadingCities = ref(false)
const loadingBarangays = ref(false)


/* =========================================================
   PROFILE
========================================================= */

const profile = ref({

    first_name: "",
    middle_name: "",
    last_name: "",
    email: "",
    contact_number: "",
    address: "",
    address_line: "",
    zip_code: "",
    birth_date: "",
    gender: "",
    civil_status: "",
    nationality: "",
    religion: "",

    province: "",
    province_code: "",

    city_municipality: "",
    city_municipality_code: "",

    barangay: "",
    barangay_code: ""

})


/* =========================================================
   SCROLL
========================================================= */

function scrollToTop() {

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    })

}


/* =========================================================
   ALERT
========================================================= */

function showAlert(type, message) {

    alert.value = {
        show: true,
        type,
        message
    }

    setTimeout(() => {

        alert.value.show = false

    }, 4000)

}


/* =========================================================
   FORMAT DATE
========================================================= */

function formatDate(date) {

    if (!date) {
        return ""
    }

    return date.substring(0, 10)

}


/* =========================================================
   FORMAT CONTACT
========================================================= */

function formatContactNumber(number) {

    if (!number) {
        return ""
    }

    return number
        .toString()
        .replace(/\D/g, "")
        .substring(0, 11)

}


function validateContactNumber() {

    let number =
        profile.value.contact_number || ""

    number = number
        .replace(/\D/g, "")
        .substring(0, 11)

    profile.value.contact_number = number

}


/* =========================================================
   GET PROVINCES
========================================================= */

async function getProvinces() {

    loadingProvinces.value = true

    try {

        const response = await fetch(
            "https://psgc.cloud/api/v2/provinces"
        )

        if (!response.ok) {
            throw new Error(
                "Unable to load provinces"
            )
        }

        const data =
            await response.json()

        provinces.value =
            data.data || data

    } catch (error) {

        console.error(
            "Province API Error:",
            error
        )

        showAlert(
            "error",
            "Unable to load provinces."
        )

    } finally {

        loadingProvinces.value = false

    }

}


/* =========================================================
   GET CITIES
========================================================= */

async function getCities(provinceCode) {

    cities.value = []
    barangays.value = []

    profile.value.city_municipality = ""
    profile.value.city_municipality_code = ""

    profile.value.barangay = ""
    profile.value.barangay_code = ""

    if (!provinceCode) {
        return
    }

    loadingCities.value = true

    try {

        const response = await fetch(
            `https://psgc.cloud/api/v2/provinces/${provinceCode}/cities-municipalities`
        )

        if (!response.ok) {
            throw new Error(
                "Unable to load cities"
            )
        }

        const data =
            await response.json()

        cities.value =
            data.data || data

    } catch (error) {

        console.error(
            "City API Error:",
            error
        )

        showAlert(
            "error",
            "Unable to load cities/municipalities."
        )

    } finally {

        loadingCities.value = false

    }

}


/* =========================================================
   GET BARANGAYS
========================================================= */

async function getBarangays(cityCode) {

    barangays.value = []

    profile.value.barangay = ""
    profile.value.barangay_code = ""

    if (!cityCode) {
        return
    }

    loadingBarangays.value = true

    try {

        const response = await fetch(
            `https://psgc.cloud/api/v2/cities-municipalities/${cityCode}/barangays`
        )

        if (!response.ok) {
            throw new Error(
                "Unable to load barangays"
            )
        }

        const data =
            await response.json()

        barangays.value =
            data.data || data

    } catch (error) {

        console.error(
            "Barangay API Error:",
            error
        )

        showAlert(
            "error",
            "Unable to load barangays."
        )

    } finally {

        loadingBarangays.value = false

    }

}


/* =========================================================
   PROVINCE CHANGED
========================================================= */

async function provinceChanged() {

    const selected =
        provinces.value.find(
            province =>
                province.code ===
                profile.value.province_code
        )

    if (!selected) {

        cities.value = []
        barangays.value = []

        return

    }

    profile.value.province =
        selected.name

    await getCities(
        selected.code
    )

}


/* =========================================================
   CITY CHANGED
========================================================= */

async function cityChanged() {

    const selected =
        cities.value.find(
            city =>
                city.code ===
                profile.value.city_municipality_code
        )

    if (!selected) {

        barangays.value = []

        return

    }

    profile.value.city_municipality =
        selected.name

    await getBarangays(
        selected.code
    )

}


/* =========================================================
   BARANGAY CHANGED
========================================================= */

function barangayChanged() {

    const selected =
        barangays.value.find(
            barangay =>
                barangay.code ===
                profile.value.barangay_code
        )

    if (!selected) {
        return
    }

    profile.value.barangay =
        selected.name

}


/* =========================================================
   BUILD ADDRESS
========================================================= */

function buildAddress() {

    const parts = []

    if (profile.value.address_line) {
        parts.push(
            profile.value.address_line
        )
    }

    if (profile.value.barangay) {
        parts.push(
            profile.value.barangay
        )
    }

    if (profile.value.city_municipality) {
        parts.push(
            profile.value.city_municipality
        )
    }

    if (profile.value.province) {
        parts.push(
            profile.value.province
        )
    }

    if (profile.value.zip_code) {
        parts.push(
            profile.value.zip_code
        )
    }

    profile.value.address =
        parts.join(", ")

}


/* =========================================================
   GET PROFILE
========================================================= */

async function getProfile() {

    try {

        const response =
            await axios.get(
                "/student/profile"
            )

        if (!response.data.student) {
            return
        }

        const student =
            response.data.student

        profile.value = {

            ...profile.value,

            ...student,

            birth_date:
                formatDate(
                    student.birth_date
                ),

            contact_number:
                formatContactNumber(
                    student.contact_number
                ),

            address_line:
                student.address_line || "",

            zip_code:
                student.zip_code || "",

            province:
                student.province || "",

            province_code:
                student.province_code || "",

            city_municipality:
                student.city_municipality || "",

            city_municipality_code:
                student.city_municipality_code || "",

            barangay:
                student.barangay || "",

            barangay_code:
                student.barangay_code || ""

        }

        profileExists.value = true


        /* LOAD EXISTING LOCATION */

        if (profile.value.province_code) {

            await getCities(
                profile.value.province_code
            )

        }

        if (
            profile.value.city_municipality_code
        ) {

            await getBarangays(
                profile.value.city_municipality_code
            )

        }

    } catch (error) {

        console.error(
            "Failed to load profile:",
            error
        )

        showAlert(
            "error",
            "Unable to load student profile"
        )

    }

}


/* =========================================================
   SAVE PROFILE
========================================================= */

async function saveProfile() {

    if (
        !/^09\d{9}$/.test(
            profile.value.contact_number
        )
    ) {

        showAlert(
            "error",
            "Contact number must start with 09 and contain exactly 11 digits."
        )

        scrollToTop()

        return

    }


    if (!profile.value.nationality) {

        showAlert(
            "error",
            "Please select your nationality."
        )

        scrollToTop()

        return

    }


    if (!profile.value.religion) {

        showAlert(
            "error",
            "Please select your religion."
        )

        scrollToTop()

        return

    }


    if (!profile.value.province_code) {

        showAlert(
            "error",
            "Please select your province."
        )

        scrollToTop()

        return

    }


    if (!profile.value.city_municipality_code) {

        showAlert(
            "error",
            "Please select your city or municipality."
        )

        scrollToTop()

        return

    }


    if (!profile.value.barangay_code) {

        showAlert(
            "error",
            "Please select your barangay."
        )

        scrollToTop()

        return

    }


    if (!profile.value.address_line) {

        showAlert(
            "error",
            "Please enter your house number, street, or purok."
        )

        scrollToTop()

        return

    }


    buildAddress()

    loading.value = true


    try {

        let response


        if (profileExists.value) {

            response = await axios.put(
                "/student/profile",
                profile.value
            )

        } else {

            response = await axios.post(
                "/student/profile",
                profile.value
            )

            profileExists.value = true

        }


        showAlert(
            "success",
            "Profile saved successfully!"
        )

        scrollToTop()

        console.log(
            response.data
        )

    } catch (error) {

        console.error(
            "Save profile error:",
            error.response
        )

        let message =
            "Unable to save profile"


        if (
            error.response?.data?.errors
        ) {

            message =
                Object.values(
                    error.response.data.errors
                )[0][0]

        } else if (
            error.response?.data?.message
        ) {

            message =
                error.response.data.message

        }


        showAlert(
            "error",
            message
        )

    } finally {

        loading.value = false

    }

}


/* =========================================================
   INITIAL LOAD
========================================================= */

onMounted(async () => {

    await getProvinces()

    await getProfile()

})

</script>


<template>

<div class="profile-page">

    <!-- PAGE TITLE -->

    <h2 class="page-title">
        Student Profile
    </h2>


    <!-- ALERT -->

    <transition name="slide">

        <div
            v-if="alert.show"
            class="alert-box"
            :class="alert.type"
        >

            <i
                :class="
                    alert.type === 'success'
                        ? 'bi bi-check-circle-fill'
                        : 'bi bi-exclamation-circle-fill'
                "
            ></i>

            <span>
                {{ alert.message }}
            </span>

        </div>

    </transition>


    <!-- PROFILE CARD -->

    <div class="profile-card">


        <!-- PROFILE HEADER -->

        <div class="profile-header">

            <div class="avatar">

                {{
                    profile.first_name
                        ? profile.first_name
                            .charAt(0)
                            .toUpperCase()
                        : "S"
                }}

            </div>


            <div class="profile-header-info">

                <h4>

                    {{ profile.first_name || "Student" }}

                    {{ profile.last_name }}

                </h4>

                <p>
                    Student Information
                </p>

            </div>

        </div>


        <form
            @submit.prevent="saveProfile"
            autocomplete="off"
        >


            <!-- =================================================
                 PERSONAL INFORMATION
            ================================================== -->

            <div class="form-section">

                <div class="section-header">

                    <div class="section-icon">
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <div>
                        <h5>Personal Information</h5>

                        <p>
                            Please provide your basic personal information.
                        </p>
                    </div>

                </div>


                <div class="row g-2">


                    <!-- FIRST NAME -->

                    <div class="col-12 col-sm-6 col-md-4">

                        <label>
                            First Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            v-model="profile.first_name"
                            placeholder="First Name"
                            required
                        >

                    </div>


                    <!-- MIDDLE NAME -->

                    <div class="col-12 col-sm-6 col-md-4">

                        <label>
                            Middle Name
                            <span class="optional">
                                Optional
                            </span>
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            v-model="profile.middle_name"
                            placeholder="Middle Name"
                        >

                    </div>


                    <!-- LAST NAME -->

                    <div class="col-12 col-sm-6 col-md-4">

                        <label>
                            Last Name
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            v-model="profile.last_name"
                            placeholder="Last Name"
                            required
                        >

                    </div>


                    <!-- BIRTH DATE -->

                    <div class="col-12 col-sm-6 col-md-6">

                        <label>
                            Birth Date
                        </label>

                        <input
                            type="date"
                            class="form-control"
                            v-model="profile.birth_date"
                            required
                        >

                    </div>


                    <!-- GENDER -->

                    <div class="col-12 col-sm-6 col-md-6">

                        <label>
                            Gender
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.gender"
                            required
                        >

                            <option value="">
                                Select Gender
                            </option>

                            <option value="Male">
                                Male
                            </option>

                            <option value="Female">
                                Female
                            </option>

                        </select>

                    </div>


                    <!-- CIVIL STATUS -->

                    <div class="col-12 col-sm-6 col-md-4">

                        <label>
                            Civil Status
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.civil_status"
                            required
                        >

                            <option value="">
                                Select Civil Status
                            </option>

                            <option value="Single">
                                Single
                            </option>

                            <option value="Married">
                                Married
                            </option>

                        </select>

                    </div>


                    <!-- NATIONALITY -->

                    <div class="col-12 col-sm-6 col-md-4">

                        <label>
                            Nationality
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.nationality"
                            required
                        >

                            <option value="">
                                Select Nationality
                            </option>

                            <option
                                v-for="item in nationalities"
                                :key="item"
                                :value="item"
                            >
                                {{ item }}
                            </option>

                        </select>

                    </div>


                    <!-- RELIGION -->

                    <div class="col-12 col-sm-12 col-md-4">

                        <label>
                            Religion
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.religion"
                            required
                        >

                            <option value="">
                                Select Religion
                            </option>

                            <option
                                v-for="item in religions"
                                :key="item"
                                :value="item"
                            >
                                {{ item }}
                            </option>

                        </select>

                    </div>

                </div>

            </div>



            <!-- =================================================
                 CONTACT INFORMATION
            ================================================== -->

            <div class="form-section">

                <div class="section-header">

                    <div class="section-icon">
                        <i class="bi bi-telephone-fill"></i>
                    </div>

                    <div>
                        <h5>Contact Information</h5>

                        <p>
                            Make sure your contact information is active.
                        </p>
                    </div>

                </div>


                <div class="row g-2">


                    <!-- EMAIL -->

                    <div class="col-12 col-md-6">

                        <label>
                            Email Address
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            v-model="profile.email"
                            placeholder="example@email.com"
                            required
                        >

                    </div>


                    <!-- CONTACT -->

                    <div class="col-12 col-md-6">

                        <label>
                            Contact Number
                        </label>

                        <input
                            type="tel"
                            inputmode="numeric"
                            class="form-control"
                            v-model="profile.contact_number"
                            @input="validateContactNumber"
                            maxlength="11"
                            placeholder="09XXXXXXXXX"
                            required
                        >

                    </div>

                </div>

            </div>



            <!-- =================================================
                 ADDRESS INFORMATION
            ================================================== -->

            <div class="form-section">

                <div class="section-header">

                    <div class="section-icon">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>

                    <div>
                        <h5>Address Information</h5>

                        <p>
                            Select your location and provide your complete address.
                        </p>
                    </div>

                </div>


                <div class="row g-2">


                    <!-- PROVINCE -->

                    <div class="col-12 col-md-4">

                        <label>
                            Province
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.province_code"
                            @change="provinceChanged"
                            :disabled="loadingProvinces"
                            required
                        >

                            <option value="">

                                {{
                                    loadingProvinces
                                        ? "Loading provinces..."
                                        : "Select Province"
                                }}

                            </option>

                            <option
                                v-for="province in provinces"
                                :key="province.code"
                                :value="province.code"
                            >
                                {{ province.name }}
                            </option>

                        </select>

                    </div>


                    <!-- CITY -->

                    <div class="col-12 col-md-4">

                        <label>
                            City / Municipality
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.city_municipality_code"
                            @change="cityChanged"
                            :disabled="
                                !profile.province_code ||
                                loadingCities
                            "
                            required
                        >

                            <option value="">

                                {{
                                    loadingCities
                                        ? "Loading cities..."
                                        : "Select City / Municipality"
                                }}

                            </option>

                            <option
                                v-for="city in cities"
                                :key="city.code"
                                :value="city.code"
                            >
                                {{ city.name }}
                            </option>

                        </select>

                    </div>


                    <!-- BARANGAY -->

                    <div class="col-12 col-md-4">

                        <label>
                            Barangay
                        </label>

                        <select
                            class="form-select"
                            v-model="profile.barangay_code"
                            @change="barangayChanged"
                            :disabled="
                                !profile.city_municipality_code ||
                                loadingBarangays
                            "
                            required
                        >

                            <option value="">

                                {{
                                    loadingBarangays
                                        ? "Loading barangays..."
                                        : "Select Barangay"
                                }}

                            </option>

                            <option
                                v-for="barangay in barangays"
                                :key="barangay.code"
                                :value="barangay.code"
                            >
                                {{ barangay.name }}
                            </option>

                        </select>

                    </div>


                    <!-- HOUSE / STREET -->

                    <div class="col-12 col-md-8">

                        <label>
                            House No. / Street / Purok
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            v-model="profile.address_line"
                            placeholder="House No., Street, Purok"
                            required
                        >

                    </div>


                    <!-- ZIP -->

                    <div class="col-12 col-md-4">

                        <label>
                            ZIP Code
                            <span class="optional">
                                Optional
                            </span>
                        </label>

                        <input
                            type="text"
                            inputmode="numeric"
                            class="form-control"
                            v-model="profile.zip_code"
                            maxlength="10"
                            placeholder="ZIP Code"
                        >

                    </div>


                    <!-- COMPLETE ADDRESS -->

                    <div class="col-12">

                        <label>
                            Complete Address
                        </label>

                        <div class="address-preview">

                            <i class="bi bi-geo-alt-fill"></i>

                            <div class="address-text">

                                {{
                                    [
                                        profile.address_line,
                                        profile.barangay,
                                        profile.city_municipality,
                                        profile.province,
                                        profile.zip_code
                                    ]
                                    .filter(Boolean)
                                    .join(", ")
                                    || "Your complete address will appear here."
                                }}

                            </div>

                        </div>

                        <small class="address-help">

                            Your complete address is automatically generated
                            from the information above.

                        </small>

                    </div>

                </div>

            </div>



            <!-- =================================================
                 SAVE
            ================================================== -->

            <div class="save-area">

                <button
                    class="save-btn"
                    type="submit"
                    :disabled="loading"
                >

                    <span v-if="!loading">

                        <i class="bi bi-check-circle"></i>

                        Save Profile

                    </span>

                    <span v-else>

                        <span class="button-spinner"></span>

                        Saving...

                    </span>

                </button>

            </div>


        </form>

    </div>

</div>

</template>


<style scoped>

/* =========================================================
   PAGE
========================================================= */

.profile-page {
    padding: 6px 8px;
}

.page-title {
    margin: 0 0 12px;
    color: #064E2A;
    font-size: 22px;
    font-weight: 800;
}


/* =========================================================
   PROFILE CARD
========================================================= */

.profile-card {
    background: #fff;
    border-radius: 15px;
    padding: 16px;
    border: 1px solid #E5E7EB;
    box-shadow: 0 4px 16px rgba(0,0,0,.05);
}


/* =========================================================
   PROFILE HEADER
========================================================= */

.profile-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding-bottom: 14px;
    margin-bottom: 14px;
    border-bottom: 1px solid #E5E7EB;
}

.avatar {
    width: 58px;
    height: 58px;
    flex: 0 0 58px;
    border-radius: 50%;
    background: linear-gradient(
        135deg,
        #064E2A,
        #0B6B3A
    );
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 23px;
    font-weight: 800;
}

.profile-header-info {
    min-width: 0;
}

.profile-header h4 {
    margin: 0 0 2px;
    color: #1F2937;
    font-size: 18px;
    font-weight: 800;
    overflow-wrap: anywhere;
}

.profile-header p {
    margin: 0;
    color: #6B7280;
    font-size: 12px;
}


/* =========================================================
   FORM SECTION
========================================================= */

.form-section {
    background: #FAFCFB;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 14px;
    margin-bottom: 11px;
}


/* =========================================================
   SECTION HEADER
========================================================= */

.section-header {
    display: flex;
    align-items: center;
    gap: 9px;
    margin-bottom: 11px;
    padding-bottom: 9px;
    border-bottom: 1px solid #E5E7EB;
}

.section-icon {
    width: 34px;
    height: 34px;
    flex: 0 0 34px;
    border-radius: 8px;
    background: #E8F5ED;
    color: #0B6B3A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}

.section-header h5 {
    margin: 0;
    color: #064E2A;
    font-size: 14px;
    font-weight: 800;
}

.section-header p {
    margin: 2px 0 0;
    color: #6B7280;
    font-size: 11px;
    line-height: 1.3;
}


/* =========================================================
   LABELS
========================================================= */

label {
    display: block;
    margin-bottom: 4px;
    color: #374151;
    font-size: 12px;
    font-weight: 600;
}

.optional {
    margin-left: 3px;
    color: #9CA3AF;
    font-size: 10px;
    font-weight: 500;
}


/* =========================================================
   INPUTS
========================================================= */

.form-control,
.form-select {
    height: 41px;
    min-height: 41px;
    padding: 7px 10px;
    border: 1px solid #D1D5DB;
    border-radius: 8px;
    background-color: #fff;
    font-size: 13px;
    transition: .2s;
}

.form-control:focus,
.form-select:focus {
    border-color: #0B6B3A;
    box-shadow: 0 0 0 .15rem rgba(11,107,58,.11);
}

.form-control::placeholder {
    color: #9CA3AF;
}

.form-select:disabled {
    background-color: #F3F4F6;
    color: #9CA3AF;
    cursor: not-allowed;
}


/* =========================================================
   ADDRESS PREVIEW
========================================================= */

.address-preview {
    min-height: 54px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 9px 10px;
    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 8px;
    color: #166534;
    font-size: 12px;
    line-height: 1.4;
}

.address-preview i {
    flex: 0 0 auto;
    margin-top: 1px;
    font-size: 15px;
}

.address-text {
    min-width: 0;
    overflow-wrap: anywhere;
}

.address-help {
    display: block;
    margin-top: 4px;
    color: #6B7280;
    font-size: 10px;
    line-height: 1.3;
}


/* =========================================================
   SAVE
========================================================= */

.save-area {
    display: flex;
    justify-content: flex-end;
    padding-top: 2px;
}

.save-btn {
    min-width: 145px;
    padding: 10px 18px;
    border: 0;
    border-radius: 8px;
    background: #0B6B3A;
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    transition: .2s;
}

.save-btn:hover {
    background: #064E2A;
}

.save-btn:disabled {
    opacity: .7;
    cursor: not-allowed;
}


/* =========================================================
   BUTTON SPINNER
========================================================= */

.button-spinner {
    display: inline-block;
    width: 14px;
    height: 14px;
    margin-right: 5px;
    border: 2px solid rgba(255,255,255,.4);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .8s linear infinite;
    vertical-align: -2px;
}


/* =========================================================
   ALERT
========================================================= */

.alert-box {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 10px 12px;
    margin-bottom: 11px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.4;
}

.alert-box i {
    flex: 0 0 auto;
    font-size: 15px;
}

.alert-box.success {
    background: #DCFCE7;
    color: #166534;
    border-left: 4px solid #0B6B3A;
}

.alert-box.error {
    background: #FEE2E2;
    color: #991B1B;
    border-left: 4px solid #DC2626;
}


/* =========================================================
   ANIMATION
========================================================= */

.slide-enter-active,
.slide-leave-active {
    transition: .25s;
}

.slide-enter-from,
.slide-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}

@keyframes spin {

    to {
        transform: rotate(360deg);
    }

}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {

    .profile-page {
        padding: 3px 2px;
    }

    .page-title {
        margin-bottom: 8px;
        font-size: 18px;
    }

    .profile-card {
        padding: 10px;
        border-radius: 11px;
    }

    .profile-header {
        gap: 9px;
        padding-bottom: 10px;
        margin-bottom: 10px;
    }

    .avatar {
        width: 46px;
        height: 46px;
        flex-basis: 46px;
        font-size: 19px;
    }

    .profile-header h4 {
        font-size: 15px;
    }

    .profile-header p {
        font-size: 10px;
    }

    .form-section {
        padding: 10px;
        margin-bottom: 8px;
        border-radius: 9px;
    }

    .section-header {
        gap: 7px;
        margin-bottom: 8px;
        padding-bottom: 7px;
    }

    .section-icon {
        width: 29px;
        height: 29px;
        flex-basis: 29px;
        border-radius: 7px;
        font-size: 13px;
    }

    .section-header h5 {
        font-size: 12px;
    }

    .section-header p {
        font-size: 9px;
    }

    label {
        margin-bottom: 3px;
        font-size: 11px;
    }

    .optional {
        font-size: 9px;
    }

    .form-control,
    .form-select {
        height: 39px;
        min-height: 39px;
        padding: 6px 9px;
        border-radius: 7px;
        font-size: 12px;
    }

    .address-preview {
        min-height: 48px;
        padding: 8px;
        font-size: 11px;
    }

    .address-preview i {
        font-size: 13px;
    }

    .address-help {
        font-size: 9px;
    }

    .alert-box {
        padding: 8px 10px;
        margin-bottom: 8px;
        font-size: 11px;
    }

    .alert-box i {
        font-size: 13px;
    }

    .save-area {
        padding-top: 1px;
    }

    .save-btn {
        width: 100%;
        min-width: 0;
        padding: 10px;
        border-radius: 8px;
        font-size: 12px;
    }

}


/* =========================================================
   EXTRA SMALL PHONES
========================================================= */

@media (max-width: 380px) {

    .profile-page {
        padding: 2px 1px;
    }

    .profile-card {
        padding: 8px;
    }

    .form-section {
        padding: 8px;
    }

    .profile-header h4 {
        font-size: 14px;
    }

    .section-header p {
        display: none;
    }

    .form-control,
    .form-select {
        font-size: 11px;
    }

}

</style>
