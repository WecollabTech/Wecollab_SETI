<template>
    <header class="sticky top-0 z-[9999] font-sans m-0 p-0">
        <!-- ================= NIVEL 1 ================= -->
        <div
            class="h-[16px] md:h-[22px] bg-gradient-to-r from-[#241345] via-[#341e68] to-[#4f2aa8]"
        ></div>

        <!-- ================= NIVEL 2 (DESKTOP) ================= -->
        <div
            class="relative h-[40px] md:h-[48px] bg-slate-100/80 backdrop-blur-xl z-[30] md:flex hidden"
        >
            <div
                class="w-[70%] md:w-[580px] h-full bg-gradient-to-r from-[#2e165f] to-[#5b34c9] rounded-br-[80px] md:rounded-br-[100px] shadow-[0_8px_22px_rgba(0,0,0,.25)]"
            ></div>

            <!-- ================= MENU CON SESIÓN (DESKTOP) ================= -->
            <div
                v-if="isAuthenticated"
                class="absolute right-6 top-[78%] -translate-y-1/2 group z-[99999]"
            >
                <button
                    class="relative flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white/90 backdrop-blur border border-white/40 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-[1.04]"
                >
                    <span class="text-slate-700 font-semibold tracking-wide"
                        >Hola, {{ userName }}</span
                    >
                    <span class="flex flex-col gap-[3px]">
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                    </span>
                </button>

                <!-- DROPDOWN DESKTOP -->
                <ul
                    class="absolute right-0 mt-5 min-w-[260px] bg-white/95 backdrop-blur-2xl rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,.35)] opacity-0 invisible translate-y-[-14px] transition-all duration-300 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:translate-x-4 z-[999999]"
                >
                    <li v-for="(item, i) in menu" :key="item.text">
                        <Link
                            v-if="!item.children"
                            :href="item.href"
                            class="flex items-center gap-3 px-6 py-3.5 text-[#341e68] font-medium hover:bg-slate-100/80"
                        >
                            <i :class="item.icon"></i> {{ item.text }}
                        </Link>

                        <div
                            v-else
                            @click="toggleSub(i)"
                            class="flex justify-between items-center px-6 py-3.5 cursor-pointer text-[#341e68] font-medium hover:bg-slate-100/80"
                        >
                            <span class="flex gap-3 items-center">
                                <i :class="item.icon"></i> {{ item.text }}
                            </span>
                            <i
                                :class="[
                                    'fa-solid fa-chevron-left text-xs transition',
                                    openSub === i ? 'rotate-90' : '',
                                ]"
                            ></i>
                        </div>

                        <ul
                            v-if="item.children && openSub === i"
                            class="ml-4 bg-white/90 rounded-xl shadow-inner mt-1"
                        >
                            <li
                                v-for="child in item.children"
                                :key="child.text"
                            >
                                <Link
                                    :href="child.href"
                                    class="flex gap-3 items-center px-5 py-2 text-[#341e68] hover:bg-slate-100/80"
                                >
                                    <i :class="child.icon"></i> {{ child.text }}
                                </Link>
                            </li>
                        </ul>
                    </li>
                    <!-- MI PERFIL -->
                    <li class="border-b">
                        <Link
                            href="/profile"
                            class="flex items-center gap-3 px-6 py-3.5 text-[#341e68] font-semibold hover:bg-slate-100/80"
                        >
                            <i class="fa-solid fa-user-circle"></i>
                            Mi perfil
                        </Link>
                    </li>

                    <li class="border-t">
                        <Link
                            href="#"
                            @click.prevent="logout"
                            class="flex gap-3 items-center px-6 py-3.5 text-red-600 hover:bg-red-50"
                        >
                            <i class="fa-solid fa-right-from-bracket"></i> Salir
                        </Link>
                    </li>
                </ul>
            </div>

            <!-- ================= BOTONES SIN SESIÓN (DESKTOP) ================= -->
            <div
                v-else
                class="absolute right-6 top-[78%] -translate-y-1/2 flex gap-3 z-[99999]"
            >
                <Link
                    v-if="!isLogin"
                    href="/login"
                    class="px-5 py-2.5 rounded-2xl font-semibold text-white text-center shadow-lg bg-[#5B34C9] /* Default */ hover:bg-[#35B749] /* Hover */ disabled:bg-[#90C097] /* Disabled */ disabled:cursor-not-allowed"
                >
                    Iniciar sesión
                </Link>

                <!-- <Link
                    v-if="!isRegister"
                    href="/register"
                    class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-[#2e165f] to-[#5b34c9] text-white font-semibold shadow"
                >
                    Registrarse
                </Link> -->
            </div>
        </div>

        <!-- ================= NIVEL 3 ================= -->
        <div
            class="flex items-center justify-between h-[60px] md:h-[74px] px-5 bg-slate-100/85 backdrop-blur-xl shadow"
        >
            <Link href="/dashboard" class="flex items-center gap-3 group">
                <img
                    src="/img/seti.png"
                    class="w-[36px] md:w-[58px] h-[36px] md:h-[58px] transition-transform duration-300 group-hover:scale-105"
                />
                <h1
                    class="text-[18px] md:text-[30px] font-extrabold bg-gradient-to-r from-[#2e165f] via-[#4b2fa3] to-[#6c4cff] bg-clip-text text-transparent transition-opacity duration-300 group-hover:opacity-90"
                >
                    SynergyFlow™ PlanCore
                </h1>
            </Link>

            <!-- BOTÓN HAMBURGUESA SOLO MÓVIL -->
            <button
                v-if="
                    isAuthenticated ||
                    (!isAuthenticated && !isLogin && !isRegister)
                "
                @click="toggleMenu"
                class="md:hidden px-4 py-2 rounded-xl bg-white shadow"
            >
                ☰
            </button>
        </div>

        <!-- ================= MENÚ HAMBURGUESA MÓVIL ================= -->
        <div v-if="openMenu" class="fixed inset-0 z-[99999] md:hidden">
            <!-- Overlay -->
            <div class="absolute inset-0 bg-black/60" @click="toggleMenu"></div>

            <!-- Drawer -->
            <aside
                class="absolute right-0 top-0 h-full w-[85%] bg-white shadow-2xl p-6 overflow-y-auto"
            >
                <ul class="space-y-2">
                    <!-- MENU SOLO SI HAY SESIÓN -->
                    <template v-if="isAuthenticated">
                        <li>
                            <button
                                @click="navigate('/perfil')"
                                class="flex gap-3 p-3 w-full text-left rounded-xl hover:bg-slate-100"
                            >
                                <i class="fa-solid fa-user-circle"></i>
                                Mi perfil
                            </button>
                        </li>

                        <li v-for="(item, i) in menu" :key="item.text">
                            <button
                                v-if="!item.children"
                                @click="navigate(item.href)"
                                class="flex gap-3 p-3 w-full text-left rounded-xl hover:bg-slate-100"
                            >
                                <i :class="item.icon"></i> {{ item.text }}
                            </button>

                            <div
                                v-else
                                @click="toggleSub(i)"
                                class="flex justify-between p-3 rounded-xl cursor-pointer hover:bg-slate-100"
                            >
                                <span class="flex gap-3">
                                    <i :class="item.icon"></i> {{ item.text }}
                                </span>
                                <i
                                    :class="[
                                        'fa-solid fa-chevron-down transition',
                                        openSub === i ? 'rotate-180' : '',
                                    ]"
                                ></i>
                            </div>

                            <ul
                                v-if="item.children && openSub === i"
                                class="ml-4"
                            >
                                <li
                                    v-for="child in item.children"
                                    :key="child.text"
                                >
                                    <button
                                        @click="navigate(child.href)"
                                        class="flex gap-3 p-2 w-full text-left rounded-lg hover:bg-slate-100"
                                    >
                                        <i :class="child.icon"></i>
                                        {{ child.text }}
                                    </button>
                                </li>
                            </ul>
                        </li>

                        <li class="pt-4 border-t">
                            <button
                                @click="logout"
                                class="flex gap-3 p-3 w-full text-red-600 rounded-xl hover:bg-red-50"
                            >
                                <i class="fa-solid fa-right-from-bracket"></i>
                                Salir
                            </button>
                        </li>
                    </template>

                    <!-- BOTONES SESIÓN / REGISTRO MÓVIL -->
                    <template v-else>
                        <li class="pt-4 border-t flex flex-col gap-2">
                            <Link
                                v-if="!isLogin"
                                href="/login"
                                @click="toggleMenu"
                                :class="[
                                    'px-5 py-2 rounded-2xl font-semibold text-white text-center transition-colors duration-300',
                                    isDisabled
                                        ? 'bg-gray-400 cursor-not-allowed' // estado disabled
                                        : 'bg-gradient-to-r from-[#FF7F2D] via-[#35B749] to-[#5B34C9] hover:brightness-90 shadow-lg', // default + hover
                                ]"
                            >
                                Iniciar sesión
                            </Link>

                            <!-- <Link
                                v-if="!isRegister"
                                href="/register"
                                @click="toggleMenu"
                                class="px-5 py-2 rounded-2xl bg-gradient-to-r from-[#2e165f] to-[#5b34c9] text-white font-semibold shadow text-center"
                            >
                                Registrarse
                            </Link> -->
                        </li>
                    </template>
                </ul>
            </aside>
        </div>
    </header>
</template>

<script setup>
import { ref, computed, nextTick } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name);
const isAuthenticated = computed(() => !!page.props.auth?.user);
const isLogin = computed(() => page.url.startsWith("/login"));
const isRegister = computed(() => page.url.startsWith("/register"));

const openMenu = ref(false);
const openSub = ref(null);

const toggleMenu = () => {
    openMenu.value = !openMenu.value;
    openSub.value = null;
};

const toggleSub = (i) => {
    openSub.value = openSub.value === i ? null : i;
};

const logout = () => router.post("/logout");

// FUNCION PARA NAVEGAR EN MÓVIL
const navigate = (href) => {
    console.log("Redirigiendo a:", href);
    openMenu.value = false;
    openSub.value = null;
    nextTick(() => {
        router.visit(href);
    });
};

const menu = [
    { text: "Inicio", href: "/dashboard", icon: "fa-solid fa-house" },
    {
        text: "Implementación",
        icon: "fa-solid fa-layer-group",
        children: [
            {
                text: "Tipo Implementación",
                href: "/tipoimplementacion",
                icon: "fa-solid fa-file-lines",
            },
            { text: "Fases", href: "/fases", icon: "fa-solid fa-layer-group" },
            {
                text: "Integraciones",
                href: "/integraciones",
                icon: "fa-solid fa-plug",
            },
        ],
    },
    {
        text: "Gestión",
        icon: "fa-solid fa-gears",
        children: [
            { text: "Tareas", href: "/tareas", icon: "fa-solid fa-tasks" },
            {
                text: "Nivel Complejidad",
                href: "/niveles",
                icon: "fa-solid fa-signal",
            },
        ],
    },
    {
        text: "Estimación",
        icon: "fa-solid fa-clock",
        children: [
            {
                text: "Estimar Horas",
                href: "/estimacion",
                icon: "fa-solid fa-hourglass-half",
            },
            {
                text: "Lista de Estimación",
                href: "/estimaciones",
                icon: "fa-solid fa-list-check",
            },
        ],
    },
];
</script>
