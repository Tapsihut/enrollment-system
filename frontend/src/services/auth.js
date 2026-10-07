import axios from "axios"

const API = axios.create({
    baseURL: import.meta.env.VITE_API_URL || "https://sfxc-enrollment.free.nf/api",

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

        const body = new URLSearchParams()

        body.append("name", data.name)
        body.append("email", data.email)
        body.append("password", data.password)

        return API.post("/register", body, {
            headers: {
                Accept: "application/json",
                "Content-Type": "application/x-www-form-urlencoded",
            }
        })
    },

    logout() {
        return API.post("/logout")
    },

    user() {
        return API.get("/me")
    }

}