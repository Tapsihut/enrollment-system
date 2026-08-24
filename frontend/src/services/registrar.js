import api from "./api"



export default {



/*
VIEW APPLICATIONS
*/

applications(){

return api.get(
'/registrar/applications'
)

},




/*
VIEW STUDENT
*/

student(id){

return api.get(
`/registrar/student/${id}`
)

},




/*
APPROVE APPLICATION
*/


approve(id){

return api.post(

`/registrar/application/${id}/approve`

)

},





/*
REJECT APPLICATION
*/


reject(id){

return api.post(

`/registrar/application/${id}/reject`

)

},




/*
AUTO ASSIGN CURRICULUM
*/


assignSubjects(data){

return api.post(

'/registrar/assign-subjects',

data

)

},




/*
CREATE ASSESSMENT
*/


createAssessment(data){

return api.post(

'/registrar/assessment',

data

)

}



}