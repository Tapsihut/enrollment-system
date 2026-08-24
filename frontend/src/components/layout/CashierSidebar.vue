```vue
<template>

<div class="sidebar">

    <!-- HEADER -->
    <div class="sidebar-header">

        <div class="logo-circle">
            <i class="bi bi-building"></i>
        </div>

        <div>
            <h5>SFXC</h5>

            <small>
                Cashier Portal
            </small>
        </div>

    </div>


    <!-- MENU -->
    <nav class="menu">

        <p class="menu-title">
            MAIN MENU
        </p>


        <!-- DASHBOARD -->
        <router-link
            to="/cashier/dashboard"
        >
            <i class="bi bi-speedometer2"></i>

            <span>
                Dashboard
            </span>
        </router-link>


        <!-- PAYMENTS -->
        <router-link
            to="/cashier/payments"
        >
            <i class="bi bi-wallet2"></i>

            <span>
                Payments
            </span>
        </router-link>


        <!-- RECEIPTS -->
        <router-link
            to="/cashier/receipts"
        >
            <i class="bi bi-receipt"></i>

            <span>
                Receipts
            </span>
        </router-link>


        <!-- REPORTS -->
        <router-link
            to="/cashier/reports"
        >
            <i class="bi bi-bar-chart-line"></i>

            <span>
                Reports
            </span>
        </router-link>

    </nav>


    <!-- FOOTER -->
    <div class="sidebar-footer">

        <i class="bi bi-shield-check"></i>

        <div>

            <small>
                Logged in as
            </small>

            <strong>
                Cashier
            </strong>

        </div>

    </div>

</div>

</template>


<style scoped>

/* SIDEBAR */

.sidebar {
    width: 240px;
    min-height: 100vh;
    background: #064E2A;
    color: white;
    display: flex;
    flex-direction: column;
    padding: 15px;
    box-sizing: border-box;
    box-shadow: 5px 0 20px rgba(0,0,0,.15);
}


/* HEADER */

.sidebar-header {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px 8px;
    margin-bottom: 20px;
    border-bottom: 1px solid rgba(255,255,255,.15);
}


.logo-circle {
    width: 45px;
    height: 45px;
    flex-shrink: 0;
    border-radius: 12px;
    background: #0B6B3A;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 21px;
}


.sidebar-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 800;
}


.sidebar-header small {
    color: #c7f9d8;
    font-size: 12px;
}


/* MENU */

.menu {
    flex: 1;
}


.menu-title {
    font-size: 11px;
    letter-spacing: 1px;
    color: #b7d8c3;
    margin: 0 0 10px 8px;
}


/* MENU ITEMS */

.menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 11px 12px;
    margin-bottom: 5px;
    border-radius: 10px;
    color: white;
    text-decoration: none;
    transition: .3s;
    position: relative;
    font-size: 14px;
    font-weight: 500;
}


.menu a i {
    font-size: 18px;
    width: 22px;
    min-width: 22px;
    text-align: center;
}


.menu a:hover {
    background: rgba(255,255,255,.12);
    transform: translateX(4px);
}


.menu a.router-link-active {
    background: white;
    color: #064E2A;
    font-weight: 700;
    box-shadow: 0 6px 15px rgba(0,0,0,.12);
}


/* FOOTER */

.sidebar-footer {
    background: rgba(255,255,255,.1);
    padding: 11px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    gap: 10px;
}


.sidebar-footer i {
    font-size: 21px;
    color: #9cffc8;
}


.sidebar-footer small {
    display: block;
    color: #c7f9d8;
    font-size: 10px;
}


.sidebar-footer strong {
    font-size: 13px;
}


/* TABLET + MOBILE */

@media (max-width: 992px) {

    .sidebar {
        position: fixed;
        top: 0;
        left: -240px;
        width: 240px;
        height: 100vh;
        min-height: 100vh;
        z-index: 1000;
        transition: .3s;
    }


    .sidebar.open {
        left: 0;
    }

}

</style>
```
