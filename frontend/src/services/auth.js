import axios from "axios"


const API = axios.create({

    baseURL:"http://localhost:8000/api",

    headers:{
        Accept:"application/json"
    }

})



export default {


    login(data){

        return API.post('/login',data)

    },


    register(data){

        return API.post('/register',data)

    },


    logout(){

        return API.post('/logout')

    },


    user(){

        return API.get('/user')

    }


}