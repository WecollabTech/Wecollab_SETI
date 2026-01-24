<template>
    <header class="sticky top-0 z-[9999] font-sans">
        <!-- ================= NIVEL 1 ================= -->
        <div
            class="h-[22px] bg-gradient-to-r from-[#241345] via-[#341e68] to-[#4f2aa8]"
        ></div>

        <!-- ================= NIVEL 2 ================= -->
        <div
            class="relative flex h-[48px] bg-slate-100/80 backdrop-blur-xl z-[20]"
        >
            <div
                class="w-[580px] h-full bg-gradient-to-r from-[#2e165f] to-[#5b34c9] rounded-br-[100px] shadow-[0_8px_22px_rgba(0,0,0,.25)]"
            ></div>

            <!-- MENU -->
            <div
                class="absolute right-5 top-[70%] -translate-y-1/2 mt-2 group z-[99999]"
            >
                <!-- BOTÓN -->
                <button
                    class="relative flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white/90 backdrop-blur border border-white/40 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-[1.04]"
                >
                    <span
                        class="hidden md:inline text-slate-700 font-semibold tracking-wide"
                    >
                        Menú
                    </span>

                    <span class="flex flex-col gap-[3px] md:hidden">
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                    </span>
                </button>

                <ul
                    class="absolute right-0 mt-5 min-w-[260px] bg-white/95 backdrop-blur-2xl rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,.35)] opacity-0 invisible translate-y-[-14px] translate-x-0 transition-all duration-300 ease-out group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:translate-x-4 z-[999999]"
                >
                    <li
                        v-for="item in menu"
                        :key="item.text"
                        class="relative group/item"
                    >
                        <!-- ITEM -->
                        <div
                            class="flex items-center justify-between gap-3 px-6 py-3.5 text-[#341e68] font-medium cursor-pointer hover:bg-slate-100/80 transition"
                        >
                            <span class="flex items-center gap-3">
                                <i :class="item.icon"></i> {{ item.text }}
                            </span>

                            <i
                                v-if="item.children"
                                class="fa-solid fa-chevron-left text-xs opacity-60"
                            ></i>
                        </div>

                        <!-- SUBMENU (abre hacia la izquierda) -->
                        <ul
                            v-if="item.children"
                            class="absolute top-0 right-full mr-2 min-w-[230px] bg-white/95 backdrop-blur-xl rounded-2xl shadow-[0_16px_40px_rgba(0,0,0,.35)] opacity-0 invisible translate-x-[8px] transition-all duration-300 group-hover/item:opacity-100 group-hover/item:visible group-hover/item:translate-x-0 z-[999999]"
                        >
                            <li
                                v-for="child in item.children"
                                :key="child.text"
                            >
                                <Link
                                    :href="child.href"
                                    class="flex items-center gap-3 px-5 py-3 text-[#341e68] font-medium hover:bg-slate-100/80 transition"
                                >
                                    <i :class="child.icon"></i> {{ child.text }}
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <!-- SALIR -->
                    <li class="border-t border-slate-200/60">
                        <Link
                            href="#"
                            @click.prevent="logout"
                            class="flex items-center gap-3 px-6 py-3.5 text-red-600 font-medium hover:bg-red-50 transition"
                        >
                            <i class="fa-solid fa-right-from-bracket"></i> Salir
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- ================= NIVEL 3 ================= -->
        <div
            class="flex items-center h-[74px] bg-slate-100/85 backdrop-blur-xl shadow-[0_10px_28px_rgba(0,0,0,.22)] z-[10]"
        >
            <div class="flex items-center gap-4 px-6">
                <img
                    src="/img/wecollab.png"
                    class="w-[58px] h-[58px] drop-shadow-lg"
                    alt="We-Collab"
                />

                <h1
                    class="text-[32px] font-extrabold tracking-tight bg-gradient-to-r from-[#2e165f] via-[#4b2fa3] to-[#6c4cff] bg-clip-text text-transparent"
                >
                    We-collab SETI
                </h1>
            </div>
        </div>
    </header>
</template>

<script setup>
import { Link, router } from "@inertiajs/vue3";

const menu = [
    { text: "Inicio", href: "/", icon: "fa-solid fa-house" },

    {
        text: "Implementación",
        icon: "fa-solid fa-layer-group",
        children: [
            {
                text: "Tipo Implementación",
                href: "/tipoimplementacion",
                icon: "fa-solid fa-file-alt",
            },
            {
                text: "Fases",
                href: "/fases",
                icon: "fa-solid fa-project-diagram",
            },
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
                icon: "fa-solid fa-chart-pie",
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
                icon: "fa-solid fa-stopwatch",
            },
        ],
    },
];

const logout = () => {
    router.post("/logout");
};
</script>
