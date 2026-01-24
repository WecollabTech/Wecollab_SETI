<template>
    <header class="sticky top-0 z-[9999] font-sans m-0 p-0">
        <!-- ================= NIVEL 1 ================= -->
        <div
            class="h-[16px] md:h-[22px] bg-gradient-to-r from-[#241345] via-[#341e68] to-[#4f2aa8]"
        ></div>

        <!-- ================= NIVEL 2 ================= -->
        <div
            class="relative h-[40px] md:h-[48px] bg-slate-100/80 backdrop-blur-xl z-[30] md:flex hidden"
        >
            <div
                class="w-[70%] md:w-[580px] h-full bg-gradient-to-r from-[#2e165f] to-[#5b34c9] rounded-br-[80px] md:rounded-br-[100px] shadow-[0_8px_22px_rgba(0,0,0,.25)]"
            ></div>

            <!-- MENU DESKTOP -->
            <div
                class="absolute right-6 top-[78%] -translate-y-1/2 group z-[99999]"
            >
                <button
                    class="relative flex items-center gap-3 px-5 py-2.5 rounded-2xl bg-white/90 backdrop-blur border border-white/40 shadow-lg transition-all duration-300 hover:shadow-2xl hover:scale-[1.04]"
                >
                    <span class="text-slate-700 font-semibold tracking-wide"
                        >Menú</span
                    >
                    <span class="flex flex-col gap-[3px]">
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                        <span class="w-4 h-[2px] bg-slate-700 rounded"></span>
                    </span>
                </button>

                <!-- MENU DROPDOWN DESKTOP -->
                <ul
                    class="absolute right-0 mt-5 min-w-[260px] bg-white/95 backdrop-blur-2xl rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,.35)] opacity-0 invisible translate-y-[-14px] transition-all duration-300 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 group-hover:translate-x-4 z-[999999]"
                >
                    <li
                        v-for="(item, i) in menu"
                        :key="item.text"
                        class="relative group/item"
                    >
                        <!-- Item sin children -->
                        <div v-if="!item.children">
                            <Link
                                :href="item.href"
                                class="flex items-center gap-3 px-6 py-3.5 text-[#341e68] font-medium hover:bg-slate-100/80 transition"
                            >
                                <i :class="item.icon"></i> {{ item.text }}
                            </Link>
                        </div>

                        <!-- Item con children -->
                        <div
                            v-else
                            @click="toggleSub(i)"
                            class="flex items-center justify-between gap-3 px-6 py-3.5 text-[#341e68] font-medium cursor-pointer hover:bg-slate-100/80 transition"
                        >
                            <span class="flex items-center gap-3">
                                <i :class="item.icon"></i> {{ item.text }}
                            </span>
                            <i
                                :class="[
                                    'fa-solid fa-chevron-left text-xs opacity-60 transition-transform',
                                    openSub === i ? 'rotate-90' : '',
                                ]"
                            ></i>
                        </div>

                        <!-- SUBMENU ACORDEÓN DESKTOP -->
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
                                    class="flex items-center gap-3 px-5 py-2 text-[#341e68] font-medium hover:bg-slate-100/80 transition"
                                >
                                    <i
                                        :class="
                                            child.icon ||
                                            'fa-solid fa-circle-dot text-xs'
                                        "
                                    ></i>
                                    {{ child.text }}
                                </Link>
                            </li>
                        </ul>
                    </li>

                    <!-- Logout -->
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
            class="flex items-center justify-between h-[60px] md:h-[74px] px-5 bg-slate-100/85 backdrop-blur-xl shadow-[0_10px_28px_rgba(0,0,0,.22)] z-[20]"
        >
            <!-- LOGO -->
            <div class="flex items-center gap-3">
                <img
                    src="/img/wecollab.png"
                    class="w-[36px] md:w-[58px] h-[36px] md:h-[58px] drop-shadow-lg"
                />
                <h1
                    class="text-[20px] md:text-[32px] font-extrabold tracking-tight bg-gradient-to-r from-[#2e165f] via-[#4b2fa3] to-[#6c4cff] bg-clip-text text-transparent"
                >
                    We-collab SETI
                </h1>
            </div>

            <!-- MENU MOBILE -->
            <button
                @click="toggleMenu"
                class="md:hidden flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow hover:shadow-xl transition"
            >
                <span class="flex flex-col gap-1">
                    <span class="w-5 h-[2px] bg-slate-700"></span>
                    <span class="w-5 h-[2px] bg-slate-700"></span>
                    <span class="w-5 h-[2px] bg-slate-700"></span>
                </span>
            </button>
        </div>

        <!-- ================= MOBILE MENU ================= -->
        <div
            v-if="openMenu"
            class="md:hidden bg-white/95 backdrop-blur-xl shadow-xl"
        >
            <div v-for="(item, i) in menu" :key="i">
                <!-- Item sin children -->
                <div v-if="!item.children">
                    <Link
                        :href="item.href"
                        class="flex gap-3 items-center px-6 py-4 font-medium text-[#341e68]"
                        @click="toggleMenu"
                    >
                        <i :class="item.icon"></i> {{ item.text }}
                    </Link>
                </div>

                <!-- Item con children -->
                <div
                    v-else
                    @click="toggleSub(i)"
                    class="flex justify-between px-6 py-4 font-medium text-[#341e68] cursor-pointer"
                >
                    <span class="flex gap-3 items-center">
                        <i :class="item.icon"></i> {{ item.text }}
                    </span>
                    <i
                        :class="[
                            'fa-solid fa-chevron-down transition',
                            openSub === i ? 'rotate-180' : '',
                        ]"
                    ></i>
                </div>

                <!-- Submenu Mobile -->
                <div
                    v-if="item.children && openSub === i"
                    class="pl-10 pb-3 text-sm text-slate-600"
                >
                    <Link
                        v-for="c in item.children"
                        :key="c.text"
                        :href="c.href"
                        class="block py-2"
                        @click="toggleMenu"
                    >
                        <i
                            :class="c.icon || 'fa-solid fa-circle-dot text-xs'"
                        ></i>
                        {{ c.text }}
                    </Link>
                </div>
            </div>

            <!-- Logout -->
            <button
                @click="logout"
                class="w-full text-left px-6 py-4 text-red-600 border-t"
            >
                Salir
            </button>
        </div>
    </header>
</template>

<script setup>
import { ref } from "vue";
import { Link, router } from "@inertiajs/vue3";

const openMenu = ref(false);
const openSub = ref(null);

const toggleMenu = () => {
    openMenu.value = !openMenu.value;
    openSub.value = null;
};

const toggleSub = (i) => {
    openSub.value = openSub.value === i ? null : i;
};

const menu = [
    { text: "Inicio", href: "/", icon: "fa-solid fa-house" },
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
        ],
    },
];

const logout = () => router.post("/logout");
</script>

<style>
/* Animar los iconos de flechas */
.fa-chevron-left,
.fa-chevron-down {
    transition: transform 0.3s ease;
}
</style>
