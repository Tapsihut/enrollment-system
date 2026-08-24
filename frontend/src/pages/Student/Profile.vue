<script setup>

import { ref, onMounted } from "vue"

import axios from "@/services/api"



const loading = ref(false)

const profileExists = ref(false)



const alert = ref({

    show:false,

    type:"",

    message:""

})



const profile = ref({

    first_name:"",

    middle_name:"",

    last_name:"",

    email:"",

    contact_number:"",

    address:"",

    birth_date:"",

    gender:"",

    civil_status:"",

    nationality:"",

    religion:""

})

function scrollToTop() {

    window.scrollTo({

        top: 0,

        behavior: "smooth"

    });

}



function showAlert(type,message){


    alert.value = {

        show:true,

        type,

        message

    }



    setTimeout(()=>{

        alert.value.show=false

    },4000)


}



function formatDate(date){

    if(!date){
        return ""
    }

    return date.substring(0,10)

}

function formatContactNumber(number){

    if(!number){
        return ""
    }

    return number.toString()
        .replace(/\D/g,'')
        .substring(0,11)

}

function validateContactNumber(){

    let number = profile.value.contact_number

    number = number.replace(/\D/g,'')


    if(number.length > 11){

        number = number.substring(0,11)

    }


    profile.value.contact_number = number

}


async function getProfile(){


try{


const response = await axios.get(

"/student/profile"

)



if(response.data.student){


profile.value = {

    ...response.data.student,

    birth_date: formatDate(
        response.data.student.birth_date
    ),

    contact_number: formatContactNumber(
        response.data.student.contact_number
    )

}

    profileExists.value=true


}



}



catch(error){


console.log(error)


showAlert(

"error",

"Unable to load student profile"

)


}



}









async function saveProfile(){
if(
    !/^09\d{9}$/.test(profile.value.contact_number)
){

    showAlert(
        "error",
        "Contact number must start with 09 and contain exactly 11 digits."
    )

    scrollToTop()

    return

}

loading.value=true
console.log("Saving profile:", JSON.parse(JSON.stringify(profile.value)))


try{


let response



if(profileExists.value){



response = await axios.put(

"/student/profile",

profile.value

)



}

else{


response = await axios.post(

"/student/profile",

profile.value

)



profileExists.value=true


}





showAlert(

"success",

"Profile saved successfully!"

)

scrollToTop()



console.log(response.data)



}



catch(error){


console.log(error.response)



let message="Unable to save profile"



if(error.response?.data?.errors){


message = Object.values(

error.response.data.errors

)[0][0]


}



showAlert(

"error",

message

)



}



finally{


loading.value=false


}



}








onMounted(()=>{

getProfile()

})



</script>







<template>


<div class="profile-page">





<h2 class="page-title">

Student Profile

</h2>







<transition name="slide">


<div

v-if="alert.show"

class="alert-box"

:class="alert.type"

>


<i

v-if="alert.type==='success'"

class="bi bi-check-circle-fill">

</i>



<i

v-if="alert.type==='error'"

class="bi bi-exclamation-circle-fill">

</i>



<span>

{{alert.message}}

</span>



</div>


</transition>








<div class="profile-card">





<div class="profile-header">





<div class="avatar">


{{

profile.first_name

?

profile.first_name.charAt(0)

:

"S"

}}


</div>





<div>


<h4>


{{profile.first_name}}

{{profile.last_name}}


</h4>


<p>

Student Information

</p>


</div>



</div>









<form @submit.prevent="saveProfile">





<div class="row">






<div class="col-md-4 mb-3">


<label>

First Name

</label>


<input

class="form-control"

v-model="profile.first_name"

required

>


</div>







<div class="col-md-4 mb-3">


<label>

Middle Name

</label>


<input

class="form-control"

v-model="profile.middle_name"

>


</div>







<div class="col-md-4 mb-3">


<label>

Last Name

</label>


<input

class="form-control"

v-model="profile.last_name"

required

>


</div>







<div class="col-md-6 mb-3">


<label>

Email

</label>


<input

class="form-control"

v-model="profile.email"

>


</div>







<div class="col-md-6 mb-3">


<label>

Contact Number

</label>


<input

class="form-control"

v-model="profile.contact_number"

@input="validateContactNumber"

maxlength="11"

placeholder="09XXXXXXXXX"

required

>


</div>








<div class="col-md-6 mb-3">


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








<div class="col-md-6 mb-3">


<label>

Gender

</label>


<select

class="form-control"

v-model="profile.gender"

required

>


<option value="">

Select Gender

</option>


<option>

Male

</option>


<option>

Female

</option>


</select>



</div>








<div class="col-md-6 mb-3">


<label>

Civil Status

</label>


<select

class="form-control"

v-model="profile.civil_status"

required

>


<option value="">

Select Status

</option>


<option>

Single

</option>


<option>

Married

</option>


</select>


</div>







<div class="col-md-6 mb-3">


<label>

Nationality

</label>


<input

class="form-control"

v-model="profile.nationality"

required

>


</div>







<div class="col-md-6 mb-3">


<label>

Religion

</label>


<input

class="form-control"

v-model="profile.religion"

>


</div>







<div class="col-md-12 mb-3">


<label>

Address

</label>


<textarea

class="form-control"

rows="3"

v-model="profile.address"

required

></textarea>


</div>






</div>







<button

class="save-btn"

type="submit"

:disabled="loading"

>


<i class="bi bi-check-circle"></i>


{{loading ? "Saving..." : "Save Profile"}}



</button>






</form>





</div>





</div>


</template>









<style scoped>


.profile-page{

padding:10px;

}



.page-title{

font-weight:800;

color:#064E2A;

margin-bottom:25px;

}





.profile-card{


background:white;

border-radius:25px;

padding:35px;


box-shadow:

0 10px 35px rgba(0,0,0,.08);


}







.profile-header{


display:flex;

align-items:center;

gap:20px;


padding-bottom:25px;


margin-bottom:30px;


border-bottom:1px solid #eee;


}







.avatar{


height:90px;

width:90px;


border-radius:50%;


background:

linear-gradient(

135deg,

#064E2A,

#0B6B3A

);


color:white;


display:flex;

justify-content:center;

align-items:center;


font-size:35px;


font-weight:800;


}







.form-control{


height:50px;


border-radius:12px;


}





textarea.form-control{


height:auto;


}







.save-btn{


background:#0B6B3A;


color:white;


border:none;


padding:14px 35px;


border-radius:15px;


font-weight:700;


transition:.3s;


}





.save-btn:hover{


background:#064E2A;


}






.alert-box{


display:flex;


align-items:center;


gap:12px;


padding:15px 20px;


border-radius:15px;


margin-bottom:25px;


font-weight:600;


}





.alert-box i{


font-size:22px;


}




.alert-box.success{


background:#dcfce7;


color:#166534;


border-left:5px solid #0B6B3A;


}




.alert-box.error{


background:#fee2e2;


color:#991b1b;


border-left:5px solid #dc2626;


}





.slide-enter-active,
.slide-leave-active{


transition:.3s;


}





.slide-enter-from,
.slide-leave-to{


opacity:0;


transform:translateY(-15px);


}



</style>