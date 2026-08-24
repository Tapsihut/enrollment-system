import api from "./api"



export default {


/*
GET PROFILE
*/

getProfile(){

return api.get('/student/profile')

},



/*
UPDATE PROFILE
*/

updateProfile(data){

return api.post(
'/student/profile',
data
)

},




/*
GUARDIAN
*/


saveGuardian(data){

return api.post(
'/student/guardian',
data
)

},




/*
ACADEMIC BACKGROUND
*/


saveAcademic(data){

return api.post(
'/student/academic',
data
)

},




/*
SUBMIT ENROLLMENT
*/


submitEnrollment(data){

return api.post(
'/student/enrollment',
data
)

},




/*
GET SUBJECTS
*/


getSubjects(){

return api.get(
'/student/subjects'
)

},




/*
GET ASSESSMENT
*/


getAssessment(){

return api.get(
'/student/assessment'
)

},




/*
CREATE PAYMENT
*/


createPayment(){

return api.post(
'/student/payment/create'
)

}



}