<template>
    <div class="guardian-page">

        <!-- PAGE HEADER -->
        <div class="page-header">
            <div class="step-badge">
                <i class="bi bi-people-fill"></i>
                Step 2
            </div>

            <h3>Guardian Information</h3>

            <p>
                Please provide the information of your parents and legal guardian.
            </p>
        </div>


        <!-- ================= FATHER ================= -->

        <div class="section-card">

            <div class="section-title">
                <i class="bi bi-person-fill"></i>

                <div>
                    <span>Father's Information</span>
                    <small>Father's details</small>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Father's Full Name</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.father_name"
                        placeholder="Enter father's full name"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label>Occupation</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.father_occupation"
                        placeholder="Occupation"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label>Contact Number</label>

                    <input
                        class="form-control"
                        type="tel"
                        inputmode="numeric"
                        maxlength="11"
                        placeholder="09XXXXXXXXX"
                        v-model="model.father_contact"
                        @input="validateContact('father_contact')"
                    >
                </div>

            </div>

        </div>


        <!-- ================= MOTHER ================= -->

        <div class="section-card">

            <div class="section-title">
                <i class="bi bi-person-heart"></i>

                <div>
                    <span>Mother's Information</span>
                    <small>Mother's details</small>
                </div>
            </div>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label>Mother's Full Name</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.mother_name"
                        placeholder="Enter mother's full name"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label>Occupation</label>

                    <input
                        class="form-control"
                        type="text"
                        v-model="model.mother_occupation"
                        placeholder="Occupation"
                    >
                </div>

                <div class="col-md-3 mb-3">
                    <label>Contact Number</label>

                    <input
                        class="form-control"
                        type="tel"
                        inputmode="numeric"
                        maxlength="11"
                        placeholder="09XXXXXXXXX"
                        v-model="model.mother_contact"
                        @input="validateContact('mother_contact')"
                    >
                </div>

            </div>

        </div>


        <!-- ================= GUARDIAN ================= -->

        <div class="section-card">

            <div class="section-title">
                <i class="bi bi-people-fill"></i>

                <div>
                    <span>Guardian Information</span>
                    <small>Primary guardian details</small>
                </div>
            </div>

            <div class="row">

                <!-- GUARDIAN NAME -->

                <div class="col-md-6 mb-3">

                    <label>Guardian Name</label>

                    <input
                        class="form-control"
                        type="text"
                        placeholder="Enter guardian name"
                        v-model="model.guardian_name"
                    >

                </div>


                <!-- RELATIONSHIP -->

                <div class="col-md-3 mb-3">

                    <label>Relationship</label>

                    <select
                        class="form-select"
                        v-model="model.guardian_relationship"
                    >

                        <option value="">
                            Select Relationship
                        </option>

                        <option>Father</option>
                        <option>Mother</option>
                        <option>Brother</option>
                        <option>Sister</option>
                        <option>Grandfather</option>
                        <option>Grandmother</option>
                        <option>Uncle</option>
                        <option>Aunt</option>
                        <option>Legal Guardian</option>
                        <option>Others</option>

                    </select>

                </div>


                <!-- CONTACT -->

                <div class="col-md-3 mb-3">

                    <label>Contact Number</label>

                    <input
                        class="form-control"
                        type="tel"
                        inputmode="numeric"
                        maxlength="11"
                        placeholder="09XXXXXXXXX"
                        v-model="model.guardian_contact"
                        @input="validateContact('guardian_contact')"
                    >

                </div>


                <!-- ================= ADDRESS ================= -->

                <div class="col-md-12">

                    <div class="address-heading">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>Guardian Address</span>
                    </div>

                    <div class="row">

                        <!-- HOUSE / STREET -->

                        <div class="col-md-6 mb-3">

                            <label class="sub-label">
                                House No. / Street / Purok
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                v-model="guardianAddressLine"
                                placeholder="House No., Street, Purok"
                                @input="updateGuardianAddress"
                            >

                        </div>


                        <!-- ZIP CODE -->

                        <div class="col-md-6 mb-3">

                            <label class="sub-label">
                                ZIP Code
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                inputmode="numeric"
                                maxlength="4"
                                v-model="guardianZipCode"
                                placeholder="ZIP Code"
                                @input="updateGuardianAddress"
                            >

                        </div>


                        <!-- PROVINCE -->

                        <div class="col-md-4 mb-3">

                            <label class="sub-label">
                                Province
                            </label>

                            <select
                                class="form-select"
                                v-model="guardianProvinceCode"
                                @change="provinceChanged"
                                :disabled="loadingProvinces"
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


                        <!-- CITY / MUNICIPALITY -->

                        <div class="col-md-4 mb-3">

                            <label class="sub-label">
                                City / Municipality
                            </label>

                            <select
                                class="form-select"
                                v-model="guardianCityCode"
                                @change="cityChanged"
                                :disabled="
                                    !guardianProvinceCode ||
                                    loadingCities
                                "
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

                        <div class="col-md-4 mb-3">

                            <label class="sub-label">
                                Barangay
                            </label>

                            <select
                                class="form-select"
                                v-model="guardianBarangayCode"
                                @change="barangayChanged"
                                :disabled="
                                    !guardianCityCode ||
                                    loadingBarangays
                                "
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

                    </div>


                    <!-- ADDRESS PREVIEW -->

                    <div
                        v-if="guardianAddressPreview"
                        class="address-preview"
                    >

                        <div class="address-preview-icon">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>

                        <div class="address-preview-content">

                            <small>
                                Complete Guardian Address
                            </small>

                            <div class="address-text">
                                {{ guardianAddressPreview }}
                            </div>

                        </div>

                    </div>


                    <!-- SAVED ADDRESS -->

                    <textarea
                        class="form-control"
                        rows="3"
                        v-model="model.guardian_address"
                        style="display:none"
                    ></textarea>

                </div>

            </div>

        </div>

    </div>
</template>


<script setup>

import {
    ref,
    computed,
    onMounted
} from "vue"

const model = defineModel()


/*
|--------------------------------------------------------------------------
| PSGC DATA
|--------------------------------------------------------------------------
*/

const provinces = ref([])
const cities = ref([])
const barangays = ref([])

const loadingProvinces = ref(false)
const loadingCities = ref(false)
const loadingBarangays = ref(false)


/*
|--------------------------------------------------------------------------
| GUARDIAN ADDRESS
|--------------------------------------------------------------------------
*/

const guardianAddressLine = ref("")
const guardianZipCode = ref("")

const guardianProvinceCode = ref("")
const guardianCityCode = ref("")
const guardianBarangayCode = ref("")

const guardianProvinceName = ref("")
const guardianCityName = ref("")
const guardianBarangayName = ref("")


/*
|--------------------------------------------------------------------------
| PSGC API
|--------------------------------------------------------------------------
*/

const PSGC_API = "https://psgc.cloud/api/v2"


/*
|--------------------------------------------------------------------------
| GET PROVINCES
|--------------------------------------------------------------------------
*/

async function getProvinces() {

    loadingProvinces.value = true

    try {

        const response = await fetch(
            `${PSGC_API}/provinces`
        )

        const data = await response.json()

        provinces.value =
            data.data || data

    }
    catch (error) {

        console.error(
            "Unable to load provinces:",
            error
        )

    }
    finally {

        loadingProvinces.value = false

    }

}


/*
|--------------------------------------------------------------------------
| PROVINCE CHANGED
|--------------------------------------------------------------------------
*/

async function provinceChanged() {

    const province = provinces.value.find(
        item =>
            item.code === guardianProvinceCode.value
    )

    guardianProvinceName.value =
        province?.name || ""

    guardianCityCode.value = ""
    guardianBarangayCode.value = ""

    guardianCityName.value = ""
    guardianBarangayName.value = ""

    cities.value = []
    barangays.value = []

    updateGuardianAddress()

    if (!guardianProvinceCode.value) {
        return
    }

    loadingCities.value = true

    try {

        const response = await fetch(
            `${PSGC_API}/provinces/${guardianProvinceCode.value}/cities-municipalities`
        )

        const data = await response.json()

        cities.value =
            data.data || data

    }
    catch (error) {

        console.error(
            "Unable to load cities/municipalities:",
            error
        )

    }
    finally {

        loadingCities.value = false

    }

}


/*
|--------------------------------------------------------------------------
| CITY CHANGED
|--------------------------------------------------------------------------
*/

async function cityChanged() {

    const city = cities.value.find(
        item =>
            item.code === guardianCityCode.value
    )

    guardianCityName.value =
        city?.name || ""

    guardianBarangayCode.value = ""
    guardianBarangayName.value = ""

    barangays.value = []

    updateGuardianAddress()

    if (!guardianCityCode.value) {
        return
    }

    loadingBarangays.value = true

    try {

        const response = await fetch(
            `${PSGC_API}/cities-municipalities/${guardianCityCode.value}/barangays`
        )

        const data = await response.json()

        barangays.value =
            data.data || data

    }
    catch (error) {

        console.error(
            "Unable to load barangays:",
            error
        )

    }
    finally {

        loadingBarangays.value = false

    }

}


/*
|--------------------------------------------------------------------------
| BARANGAY CHANGED
|--------------------------------------------------------------------------
*/

function barangayChanged() {

    const barangay = barangays.value.find(
        item =>
            item.code === guardianBarangayCode.value
    )

    guardianBarangayName.value =
        barangay?.name || ""

    updateGuardianAddress()

}


/*
|--------------------------------------------------------------------------
| ADDRESS PREVIEW
|--------------------------------------------------------------------------
*/

const guardianAddressPreview = computed(() => {

    const parts = []

    if (guardianAddressLine.value) {
        parts.push(
            guardianAddressLine.value
        )
    }

    if (guardianBarangayName.value) {
        parts.push(
            `Brgy. ${guardianBarangayName.value}`
        )
    }

    if (guardianCityName.value) {
        parts.push(
            guardianCityName.value
        )
    }

    if (guardianProvinceName.value) {
        parts.push(
            guardianProvinceName.value
        )
    }

    if (guardianZipCode.value) {
        parts.push(
            guardianZipCode.value
        )
    }

    return parts.join(", ")

})


/*
|--------------------------------------------------------------------------
| UPDATE GUARDIAN ADDRESS
|--------------------------------------------------------------------------
*/

function updateGuardianAddress() {

    model.value.guardian_address =
        guardianAddressPreview.value

}


/*
|--------------------------------------------------------------------------
| CONTACT NUMBER
|--------------------------------------------------------------------------
*/

function validateContact(field) {

    let number =
        model.value[field] || ""

    number = number
        .replace(/\D/g, "")
        .substring(0, 11)

    model.value[field] = number

}


/*
|--------------------------------------------------------------------------
| LOAD EXISTING ADDRESS
|--------------------------------------------------------------------------
*/

async function loadExistingAddress() {

    const address =
        model.value.guardian_address

    if (!address) {
        return
    }

    guardianAddressLine.value = address

}


/*
|--------------------------------------------------------------------------
| INITIALIZE
|--------------------------------------------------------------------------
*/

onMounted(async () => {

    await getProvinces()

    await loadExistingAddress()

})

</script>


<style scoped>

* {
    box-sizing: border-box;
}

.guardian-page {
    width: 100%;
    max-width: 100%;
    padding: 10px;
    overflow-x: hidden;
}


/* ================= PAGE HEADER ================= */

.page-header {
    margin-bottom: 25px;
}

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

.page-header h3 {
    font-weight: 800;
    color: #064E2A;
    margin-bottom: 6px;
    font-size: 27px;
}

.page-header p {
    color: #6B7280;
    margin: 0;
    line-height: 1.5;
}


/* ================= SECTION CARD ================= */

.section-card {
    background: white;
    border-radius: 22px;
    padding: 30px;
    margin-bottom: 25px;
    box-shadow: 0 10px 30px rgba(0,0,0,.06);
    border: 1px solid #E5E7EB;
}


/* ================= SECTION TITLE ================= */

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


/* ================= LABELS ================= */

label {
    font-weight: 600;
    margin-bottom: 8px;
    color: #374151;
    display: block;
}

.sub-label {
    font-size: 14px;
    margin-bottom: 7px;
}


/* ================= INPUTS ================= */

.form-control,
.form-select {
    width: 100%;
    height: 52px;
    border-radius: 12px;
    border: 1px solid #D1D5DB;
    transition: .25s;
    font-size: 15px;
    padding-left: 14px;
    padding-right: 14px;
}

.form-control::placeholder {
    color: #9CA3AF;
}

textarea.form-control {
    height: 120px;
    resize: none;
}

.form-control:focus,
.form-select:focus {
    border-color: #0B6B3A;
    box-shadow: 0 0 0 .2rem rgba(11,107,58,.15);
}

.form-select:disabled {
    background-color: #F3F4F6;
    cursor: not-allowed;
}


/* ================= ADDRESS HEADING ================= */

.address-heading {
    display: flex;
    align-items: center;
    gap: 7px;
    color: #064E2A;
    font-size: 16px;
    font-weight: 700;
    margin: 5px 0 17px;
}

.address-heading i {
    font-size: 18px;
}


/* ================= ADDRESS PREVIEW ================= */

.address-preview {
    display: flex;
    align-items: flex-start;
    gap: 12px;

    margin-top: 5px;
    margin-bottom: 10px;

    padding: 14px 16px;

    background: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 12px;

    color: #166534;

    overflow-wrap: anywhere;
}

.address-preview-icon {
    flex-shrink: 0;
}

.address-preview i {
    font-size: 20px;
}

.address-preview-content {
    min-width: 0;
}

.address-preview small {
    display: block;
    color: #6B7280;
    font-size: 12px;
    margin-bottom: 3px;
}

.address-text {
    line-height: 1.5;
    word-break: break-word;
}


/* =========================================================
   TABLET
   ========================================================= */

@media(max-width:768px) {

    .guardian-page {
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

}


/* =========================================================
   MOBILE
   ========================================================= */

@media(max-width:576px) {

    .guardian-page {
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


    /* CARD */

    .section-card {
        padding: 14px;
        border-radius: 16px;
        margin-bottom: 14px;
        box-shadow: 0 5px 18px rgba(0,0,0,.05);
    }


    /* SECTION TITLE */

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


    /* LABEL */

    label {
        font-size: 13px;
        margin-bottom: 6px;
    }

    .sub-label {
        font-size: 12px;
        margin-bottom: 6px;
    }


    /* INPUT */

    .form-control,
    .form-select {
        height: 48px;
        min-height: 48px;
        border-radius: 10px;
        font-size: 14px;
        padding-left: 12px;
        padding-right: 12px;
    }


    /* BOOTSTRAP ROW */

    .row {
        --bs-gutter-x: .7rem;
        --bs-gutter-y: 0;
    }


    /* ADDRESS */

    .address-heading {
        font-size: 14px;
        margin-top: 4px;
        margin-bottom: 13px;
    }

    .address-heading i {
        font-size: 16px;
    }


    /* ADDRESS PREVIEW */

    .address-preview {
        gap: 9px;
        padding: 12px;
        border-radius: 10px;
    }

    .address-preview i {
        font-size: 18px;
    }

    .address-preview small {
        font-size: 11px;
    }

    .address-text {
        font-size: 13px;
        line-height: 1.45;
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

    .form-control,
    .form-select {
        height: 46px;
        min-height: 46px;
        font-size: 13px;
    }

    label {
        font-size: 12px;
    }

    .address-text {
        font-size: 12px;
    }

}

</style>
