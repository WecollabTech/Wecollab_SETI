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
                        <Link href="/">
                            <i class="fa-solid fa-house"></i> Inicio
                        </Link>
                    </li>

                    <li>
                        <Link href="/tipoimplementacion">
                            <i class="fa-solid fa-file-alt"></i> Tipo de
                            Implementación
                        </Link>
                    </li>

                    <li>
                        <Link href="/fases">
                            <i class="fa-solid fa-project-diagram"></i> Fases
                        </Link>
                    </li>

                    <li>
                        <Link href="/integraciones">
                            <i class="fa-solid fa-plug"></i> Integraciones
                        </Link>
                    </li>

                    <li>
                        <Link href="/tareas">
                            <i class="fa-solid fa-tasks"></i> Tareas
                        </Link>
                    </li>

                    <li>
                        <Link href="/niveles">
                            <i class="fa-solid fa-chart-pie"></i> Nivel de
                            Complejidad
                        </Link>
                    </li>

                    <li>
                        <Link href="/estimacion">
                            <i class="fa-solid fa-clock"></i> Estimar Hora de
                            Implementación
                        </Link>
                    </li>

                    <!-- SALIR -->
                    <li>
                        <Link href="#" @click.prevent="logout">
                            <i class="fa-solid fa-right-from-bracket"></i> Salir
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
