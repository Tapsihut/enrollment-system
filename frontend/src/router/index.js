import { createRouter, createWebHistory } from "vue-router"
import axios from "@/services/api"

import LandingPage from "@/pages/LandingPage.vue"
import Login from "@/pages/auth/Login.vue"
import Register from "@/pages/auth/Register.vue"

import StudentLayout from "@/layouts/StudentLayout.vue"
import RegistrarLayout from "@/layouts/RegistrarLayout.vue"
import CashierLayout from "@/layouts/CashierLayout.vue"
import AdminLayout from "@/layouts/AdminLayout.vue"

const routes = [

    // PUBLIC
    {
        path: "/",
        component: LandingPage
    },
    {
        path: "/login",
        component: Login
    },
    {
        path: "/register",
        component: Register
    },

    // STUDENT
    {
        path: "/student",
        component: StudentLayout,
        children: [

            {
                path: "dashboard",
                component: () => import("@/pages/Student/Dashboard.vue"),
                meta: { title: "Dashboard" }
            },

            {
                path: "profile",
                component: () => import("@/pages/Student/Profile.vue"),
                meta: { title: "Profile" }
            },

            {
                path: "enrollment",
                component: () => import("@/pages/Student/Enrollment.vue"),
                meta: {
                    title: "Enrollment",
                    requiresProfile: true
                }
            },

            {
                path: "payment",
                component: () => import("@/pages/Student/Payment.vue"),
                meta: {
                    title: "Payment",
                    requiresApproval: true
                }
            },

            {
                path: "receipt",
                component: () => import("@/pages/Student/Receipt.vue"),
                meta: { title: "Receipt" }
            },

            {
                path: "documents",
                component: () => import("@/pages/Student/Documents.vue"),
                meta: { title: "Documents" }
            }

        ]
    },

    // REGISTRAR
    {
        path: "/registrar",
        component: RegistrarLayout,
        children: [

            {
                path: "dashboard",
                component: () => import("@/pages/registrar/Dashboard.vue"),
                meta: { title: "Registrar Dashboard" }
            },

            {
                path: "applications",
                component: () => import("@/pages/registrar/Application.vue"),
                meta: { title: "Applications" }
            },

            {
                path: "applications/:id",
                component: () => import("@/pages/registrar/ViewApplication.vue"),
                meta: { title: "View Application" }
            },

            {
                path: "students",
                component: () => import("@/pages/registrar/Students.vue"),
                meta: { title: "Students" }
            },

            {
                path: "curriculum",
                component: () => import("@/pages/registrar/Curriculum.vue"),
                meta: { title: "Curriculum" }
            },

            {
                path: "reports",
                component: () => import("@/pages/registrar/Reports.vue"),
                meta: { title: "Reports" }
            }

        ]
    },

    // CASHIER
    {
        path: "/cashier",
        component: CashierLayout,
        children: [

            {
                path: "dashboard",
                component: () => import("@/pages/cashier/Dashboard.vue"),
                meta: { title: "Cashier Dashboard" }
            },

            {
                path: "payments",
                component: () => import("@/pages/cashier/Payments.vue"),
                meta: { title: "Payments" }
            },

            {
                path: "receipts",
                component: () => import("@/pages/cashier/Receipts.vue"),
                meta: { title: "Receipts" }
            },

            {
                path: "reports",
                component: () => import("@/pages/cashier/Reports.vue"),
                meta: { title: "Payment Reports" }
            }

        ]
    },

    // ADMIN
    {
        path: "/admin",
        component: AdminLayout,
        children: [

            {
                path: "dashboard",
                component: () => import("@/pages/admin/Dashboard.vue"),
                meta: { title: "Admin Dashboard" }
            }

        ]
    },

    // PAYMENT RESULT
    {
        path: "/student/payment/success",
        component: () => import("@/pages/Student/PaymentSuccess.vue")
    },

    {
        path: "/student/payment/failed",
        component: () => import("@/pages/Student/PaymentFailed.vue")
    },

    // NOT FOUND
    {
        path: "/:pathMatch(.*)*",
        redirect: "/"
    }

]

const router = createRouter({
    history: createWebHistory(),
    routes
})

// ROUTER GUARD
router.beforeEach(async (to) => {

    if (to.meta.requiresProfile) {

        try {

            const response = await axios.get(
                "/student/profile/completion"
            )

            if (!response.data.complete) {
                alert("Please complete your profile first.")
                return "/student/profile"
            }

        } catch (error) {

            console.log(error)
            return "/student/profile"

        }

    }

    return true
})

export default router