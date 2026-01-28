<template>
    <!-- ================== HERO / BIENVENIDA ================== -->
    <section class="px-2 md:px-6 pt-4 pb-10">
        <div
            class="relative max-w-[92rem] mx-auto bg-gradient-to-br from-[#2e165f] via-[#3c1f7a] to-[#5b34c9] text-white rounded-[32px] shadow-[0_20px_60px_rgba(0,0,0,0.35)] overflow-hidden"
        >
            <!-- Glow decorativo -->
            <div
                class="absolute -top-24 -right-24 w-96 h-96 bg-purple-400/30 rounded-full blur-3xl"
            ></div>

            <div class="relative px-6 md:px-14 py-8 md:py-12">
                <Swiper
                    :modules="[Autoplay, Pagination]"
                    :autoplay="{ delay: 4500, disableOnInteraction: false }"
                    :pagination="{ clickable: true }"
                    loop
                    class="w-full"
                >
                    <SwiperSlide v-for="(slide, index) in slides" :key="index">
                        <div
                            class="bg-white/10 backdrop-blur-2xl rounded-[28px] shadow-xl p-6 md:p-10 border border-white/10"
                        >
                            <div
                                class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-14 items-center"
                            >
                                <!-- TEXTO -->
                                <div class="space-y-5 text-center md:text-left">
                                    <span
                                        v-if="slide.badge"
                                        class="inline-flex items-center gap-2 px-4 py-1 text-xs font-semibold tracking-wide bg-white/20 rounded-full"
                                    >
                                        ✨ {{ slide.badge }}
                                    </span>

                                    <h1
                                        class="text-xl md:text-[2.2rem] xl:text-[2.6rem] font-semibold leading-snug tracking-[-0.015em] max-w-[36rem]"
                                    >
                                        {{ slide.title }}
                                    </h1>

                                    <p
                                        class="text-sm md:text-base xl:text-lg leading-relaxed text-white/90 max-w-xl mx-auto md:mx-0 text-justify"
                                    >
                                        {{ slide.description }}
                                    </p>

                                    <Link
                                        :href="slide.link"
                                        class="inline-flex items-center justify-center gap-2 px-7 py-3 bg-white text-purple-700 font-semibold rounded-xl shadow-lg hover:bg-gray-100 hover:shadow-xl hover:scale-[1.05] transition-all duration-200"
                                    >
                                        {{ slide.cta }}
                                        <span class="text-lg">→</span>
                                    </Link>
                                </div>

                                <!-- IMAGEN -->
                                <div class="flex justify-center">
                                    <div
                                        class="bg-white/10 rounded-3xl p-5 md:p-6 shadow-inner border border-white/10"
                                    >
                                        <img
                                            :src="slide.image"
                                            :alt="slide.title"
                                            class="h-[160px] md:h-[210px] xl:h-[240px] w-auto object-contain drop-shadow-2xl"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </SwiperSlide>
                </Swiper>
            </div>
        </div>
    </section>

    <!-- ================== BENEFICIOS ================== -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-6 md:px-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-12">
                ¿Por qué usar PlanCore?
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div
                    class="p-7 bg-gradient-to-br from-purple-600 to-purple-400 text-white rounded-2xl shadow-xl hover:scale-[1.05] transition-all"
                >
                    <h3 class="text-xl font-semibold mb-3">
                        📊 Planificación por fases
                    </h3>
                    <p class="text-white/90">
                        Divide cada proyecto en fases claras, medibles y
                        controlables.
                    </p>
                </div>

                <div
                    class="p-7 bg-gradient-to-br from-purple-600 to-purple-400 text-white rounded-2xl shadow-xl hover:scale-[1.05] transition-all"
                >
                    <h3 class="text-xl font-semibold mb-3">
                        ⏱️ Estimaciones confiables
                    </h3>
                    <p class="text-white/90">
                        Horas y esfuerzo calculados con criterio técnico y
                        trazabilidad.
                    </p>
                </div>

                <div
                    class="p-7 bg-gradient-to-br from-purple-600 to-purple-400 text-white rounded-2xl shadow-xl hover:scale-[1.05] transition-all"
                >
                    <h3 class="text-xl font-semibold mb-3">
                        🔗 Integración sencilla
                    </h3>
                    <p class="text-white/90">
                        Compatible con CRM y sistemas internos existentes.
                    </p>
                </div>

                <div
                    class="p-7 bg-gradient-to-br from-purple-600 to-purple-400 text-white rounded-2xl shadow-xl hover:scale-[1.05] transition-all"
                >
                    <h3 class="text-xl font-semibold mb-3">
                        🧠 Decisiones inteligentes
                    </h3>
                    <p class="text-white/90">
                        Información clara para decisiones rápidas y seguras.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================== CÓMO FUNCIONA ================== -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-6xl mx-auto px-6 md:px-12 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-14">
                Cómo funciona
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <div class="flex flex-col items-center">
                    <div
                        class="bg-purple-600 text-white rounded-full h-16 w-16 flex items-center justify-center mb-4 font-bold text-xl shadow-lg"
                    >
                        1
                    </div>
                    <p class="font-medium">Registrar requerimientos</p>
                </div>

                <div class="flex flex-col items-center">
                    <div
                        class="bg-purple-600 text-white rounded-full h-16 w-16 flex items-center justify-center mb-4 font-bold text-xl shadow-lg"
                    >
                        2
                    </div>
                    <p class="font-medium">Estimar horas por fase</p>
                </div>

                <div class="flex flex-col items-center">
                    <div
                        class="bg-purple-600 text-white rounded-full h-16 w-16 flex items-center justify-center mb-4 font-bold text-xl shadow-lg"
                    >
                        3
                    </div>
                    <p class="font-medium">Revisar resultados</p>
                </div>

                <div class="flex flex-col items-center">
                    <div
                        class="bg-purple-600 text-white rounded-full h-16 w-16 flex items-center justify-center mb-4 font-bold text-xl shadow-lg"
                    >
                        4
                    </div>
                    <p class="font-medium">Aprobar e integrar</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================== CTA FINAL ================== -->
    <section
        class="py-16 bg-gradient-to-r from-[#2e165f] to-[#5b34c9] text-white text-center rounded-[32px] mx-6 md:mx-12 mb-12 shadow-[0_25px_70px_rgba(0,0,0,0.4)]"
    >
        <h2 class="text-3xl md:text-4xl font-bold mb-5">
            Comienza a planificar hoy
        </h2>
        <p class="mb-8 text-white/90 max-w-2xl mx-auto">
            Inicia tus estimaciones con SynergyFlow™ PlanCore y asegura
            decisiones rápidas, claras y confiables.
        </p>
        <Link
            href="/estimacion"
            class="inline-flex items-center justify-center gap-2 px-9 py-4 bg-white text-purple-700 font-semibold rounded-xl shadow-xl hover:bg-gray-100 hover:scale-[1.07] transition-all"
        >
            Comenzar ahora <span class="text-lg">→</span>
        </Link>
    </section>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import { Swiper, SwiperSlide } from "swiper/vue";
import { Autoplay, Pagination } from "swiper/modules";

import "swiper/css";
import "swiper/css/pagination";

const slides = [
    {
        badge: "Bienvenidos",
        title: "Bienvenidos a SynergyFlow™ PlanCore",
        description:
            "Tu punto de partida para estimaciones precisas y planificación eficiente. Estructura requerimientos, calcula horas por fase y complejidad, y toma decisiones con información confiable y trazable.",
        image: "/img/Bannerlogofondo.jpg",
        link: "/estimacion",
        cta: "Comenzar",
    },
    {
        badge: "Estándar interno We-collab",
        title: "SynergyFlow™ PlanCore",
        description:
            "Sistema interno de We-collab para la estimación y planificación de implementaciones, alineado a criterios técnicos, trazabilidad y consistencia corporativa.",
        image: "/img/logov2.png",
        link: "/estimacion",
        cta: "Comenzar",
    },
    {
        badge: "Proceso corporativo",
        title: "Estimaciones claras y confiables",
        description:
            "Todas las estimaciones oficiales de alcance y horas se realizan mediante PlanCore, garantizando resultados consistentes y confiables.",
        image: "/img/Banner2.jpg",
        link: "/estimacion",
        cta: "Ver planificación",
    },
    {
        badge: "Flexible e integrable",
        title: "Un módulo alineado a We-collab",
        description:
            "PlanCore puede operar como módulo interno o integración independiente, manteniendo coherencia visual, técnica y metodológica.",
        image: "/img/logov2.png",
        link: "/estimacion",
        cta: "Empezar ahora",
    },
];
</script>
