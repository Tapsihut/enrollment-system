import axios from "axios"

const API = axios.create({
    baseURL: "http://192.168.1.3:8000/api",

    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    }
})

// Attach token automatically
API.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("token")

        if (token) {
            config.headers.Authorization = `Bearer ${token}`
        }

        return config
    },
    (error) => {
        return Promise.reject(error)
    }
)

export default {

    login(data) {
        return API.post("/login", data)
    },

    register(data) {
        return API.post("/register", data)
    },

    logout() {
        return API.post("/logout")
    },

    user() {
        return API.get("/me")
    }

}

