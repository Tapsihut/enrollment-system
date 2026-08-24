import api from "./api"



export default {



/*
USERS
*/


users(){

return api.get(

'/admin/users'

)

},




createUser(data){

return api.post(

'/admin/users',

data

)

},




/*
COURSES
*/


courses(){

return api.get(

'/admin/courses'

)

},




createCourse(data){

return api.post(

'/admin/courses',

data

)

},





/*
SUBJECTS
*/


subjects(){

return api.get(

'/admin/subjects'

)

},





createSubject(data){

return api.post(

'/admin/subjects',

data

)

},





/*
CURRICULUM
*/


curriculum(){

return api.get(

'/admin/curriculum'

)

},




saveCurriculum(data){

return api.post(

'/admin/curriculum',

data

)

},





/*
FEES
*/


fees(){

return api.get(

'/admin/fees'

)

},




updateFees(data){

return api.post(

'/admin/fees',

data

)

}



}