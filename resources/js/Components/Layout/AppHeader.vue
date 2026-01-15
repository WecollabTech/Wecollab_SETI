<template>
    <!-- HEADER PRINCIPAL -->
    <header class="we-header">
        <!-- Nivel 1 -->
        <div class="we-header-top"></div>

        <!-- Nivel 2 -->
        <div class="headermenu">
            <div class="header-main"></div>

            <!-- MENU -->
            <div class="menu-wrapper">
                <a class="menu-btn" href="#">
                    <span class="menu-text">Menú ▾</span>
                    <span class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>

                <ul class="menu-dropdown">
                    <li>
                        <Link href="/cotizacion">
                            <i class="fas fa-house"></i> Inicio
                        </Link>
                    </li>

                    <li>
                        <Link href="/tipoimplementacion">
                            <i class="fas fa-file-invoice-dollar"></i>
                            Tipo de Implementación
                        </Link>
                    </li>

                    <li>
                        <Link href="#">
                            <i class="fas fa-diagram-project"></i> Proyectos
                        </Link>
                    </li>

                    <li>
                        <Link href="#">
                            <i class="fas fa-chart-line"></i> Reportes
                        </Link>
                    </li>

                    <!-- SALIR -->
                    <li>
                        <Link href="#" @click.prevent="logout">
                            <i class="fas fa-right-from-bracket"></i> Salir
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Nivel 3 -->
        <div class="we-header-main">
            <div class="we-header-content">
                <img src="/img/wecollab.png" alt="We-Collab" class="we-logo" />
                <h1 class="we-title">We-collab SETI</h1>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3";

const logout = () => {
    router.post("/logout");
};
</script>
