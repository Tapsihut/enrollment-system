import api from "./api"



export default {



/*
PAYMENT LIST
*/

payments(){

return api.get(

'/cashier/payments'

)

},




/*
PAYMENT DETAILS
*/

payment(id){

return api.get(

`/cashier/payment/${id}`

)

},





/*
VERIFY PAYMENT
*/


verify(id){

return api.post(

`/cashier/payment/${id}/verify`

)

},




/*
RECEIPT
*/


receipt(id){

return api.get(

`/cashier/payment/${id}/receipt`

)

},





/*
REPORTS
*/


reports(){

return api.get(

'/cashier/reports'

)

}



}