<script setup>
import DeleteUserForm from "./Partials/DeleteUserForm.vue";
import UpdatePasswordForm from "./Partials/UpdatePasswordForm.vue";
import UpdateProfileInformationForm from "./Partials/UpdateProfileInformationForm.vue";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed } from "vue";

defineProps({
    mustVerifyEmail: Boolean,
    status: String,
});

// Tabs
const activeTab = ref("profile");

const tabs = [
    {
        id: "profile",
        label: "Información del Perfil",
        icon: "user",
        color: "purple",
        description: "Gestiona tu información personal",
    },
    {
        id: "password",
        label: "Contraseña",
        icon: "lock",
        color: "rose",
        description: "Actualiza tu contraseña de acceso",
    },
    {
        id: "delete",
        label: "Eliminar Cuenta",
        icon: "trash",
        color: "red",
        description: "Elimina tu cuenta permanentemente",
    },
];

// Colores para cada tab
const tabColors = {
    purple: {
        base: "bg-gradient-to-r from-purple-500 to-indigo-600",
        hover: "hover:from-purple-600 hover:to-indigo-700",
        active: "bg-gradient-to-r from-purple-600 to-indigo-700",
        text: "text-purple-600 dark:text-purple-400",
        ring: "ring-purple-500/30",
        glow: "shadow-purple-500/20",
    },
    rose: {
        base: "bg-gradient-to-r from-rose-500 to-pink-600",
        hover: "hover:from-rose-600 hover:to-pink-700",
        active: "bg-gradient-to-r from-rose-600 to-pink-700",
        text: "text-rose-600 dark:text-rose-400",
        ring: "ring-rose-500/30",
        glow: "shadow-rose-500/20",
    },
    red: {
        base: "bg-gradient-to-r from-red-500 to-orange-600",
        hover: "hover:from-red-600 hover:to-orange-700",
        active: "bg-gradient-to-r from-red-600 to-orange-700",
        text: "text-red-600 dark:text-red-400",
        ring: "ring-red-500/30",
        glow: "shadow-red-500/20",
    },
};

const getTabColor = (color) => tabColors[color] || tabColors.purple;

// Obtener el tab activo
const activeTabData = computed(() => {
    return tabs.find((tab) => tab.id === activeTab.value) || tabs[0];
});
</script>

<template>
    <Head title="Perfil" />

    <AppLayout>
        <template #header>
            <div class="mx-auto max-w-8xl">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <div
                                class="w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center shadow-2xl"
                            >
                                <svg
                                    class="h-8 w-8 text-white"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>
                            <div
                                class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"
                            ></div>
                        </div>
                        <div>
                            <h2
                                class="text-3xl font-bold text-gray-900 dark:text-gray-100 flex items-center"
                            >
                                Mi Perfil
                            </h2>
                            <p
                                class="mt-1 text-sm text-gray-600 dark:text-gray-400"
                            >
                                {{ activeTabData.description }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-r from-purple-500 to-indigo-600 text-white px-4 py-2 rounded-xl shadow-lg flex items-center space-x-2"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <span class="font-medium">
                            {{
                                new Date().toLocaleDateString("es-ES", {
                                    weekday: "long",
                                    year: "numeric",
                                    month: "long",
                                    day: "numeric",
                                })
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </template>

        <!-- CONTENIDO -->
        <section class="relative pt-4 pb-12">
            <div class="mx-auto max-w-8xl px-4 sm:px-6 lg:px-8 space-y-6">
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden"
                >
                    <!-- Tabs - Diseño Premium -->
                    <div
                        class="border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800"
                    >
                        <div class="relative">
                            <!-- Línea de indicador animada -->
                            <div
                                class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-indigo-600 transition-all duration-500 ease-in-out"
                            ></div>

                            <nav
                                class="flex gap-2 sm:gap-3 px-3 sm:px-6 py-4 overflow-x-auto scrollbar-hide relative z-10"
                            >
                                <button
                                    v-for="tab in tabs"
                                    :key="tab.id"
                                    @click="activeTab = tab.id"
                                    :class="[
                                        'group relative flex flex-col sm:flex-row items-center gap-2 sm:gap-3 px-4 py-3 sm:px-6 sm:py-4 rounded-2xl font-semibold text-sm sm:text-base whitespace-nowrap transition-all duration-300 transform-gpu',
                                        'before:absolute before:inset-0 before:rounded-2xl before:transition-all before:duration-300',
                                        activeTab === tab.id
                                            ? `${getTabColor(tab.color).active} text-white shadow-2xl ${getTabColor(tab.color).glow} relative z-10`
                                            : `bg-white/50 dark:bg-gray-700/50 text-gray-700 dark:text-gray-300 hover:bg-white/80 dark:hover:bg-gray-700/80 ${getTabColor(tab.color).text}`,
                                    ]"
                                >
                                    <!-- Icono con efecto -->
                                    <div
                                        :class="[
                                            'flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-300',
                                            activeTab === tab.id
                                                ? 'bg-white/20'
                                                : `${getTabColor(tab.color).base} bg-opacity-15 group-hover:bg-opacity-25`,
                                        ]"
                                    >
                                        <svg
                                            v-if="tab.icon === 'user'"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                            />
                                        </svg>
                                        <svg
                                            v-if="tab.icon === 'lock'"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                            />
                                        </svg>
                                        <svg
                                            v-if="tab.icon === 'trash'"
                                            class="h-5 w-5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </div>

                                    <!-- Texto del tab -->
                                    <span class="text-center sm:text-left">{{
                                        tab.label
                                    }}</span>
                                </button>
                            </nav>
                        </div>
                    </div>

                    <!-- Content con animaciones - CORREGIDO PARA EVITAR WARNING -->
                    <div class="p-4 sm:p-6 lg:p-8">
                        <!-- SOLUCIÓN: Envolver cada componente en un div para evitar el warning -->
                        <div
                            v-show="activeTab === 'profile'"
                            class="animate-fade-in"
                        >
                            <div class="w-full">
                                <UpdateProfileInformationForm
                                    :must-verify-email="mustVerifyEmail"
                                    :status="status"
                                    :should-autofocus="activeTab === 'profile'"
                                />
                            </div>
                        </div>

                        <div
                            v-show="activeTab === 'password'"
                            class="animate-fade-in"
                        >
                            <div class="w-full">
                                <UpdatePasswordForm />
                            </div>
                        </div>

                        <div
                            v-show="activeTab === 'delete'"
                            class="animate-fade-in"
                        >
                            <div class="w-full">
                                <DeleteUserForm />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Decorative blobs con animaciones -->
                <div
                    class="hidden md:block absolute -top-16 -right-16 w-64 h-64 bg-purple-300/30 rounded-full blur-3xl animate-blob"
                ></div>
                <div
                    class="hidden md:block absolute -bottom-24 -left-16 w-80 h-80 bg-indigo-300/30 rounded-full blur-3xl animate-blob animation-delay-2000"
                ></div>
                <div
                    class="hidden md:block absolute top-1/3 right-1/4 w-48 h-48 bg-pink-300/30 rounded-full blur-3xl animate-blob animation-delay-4000"
                ></div>
            </div>
        </section>
    </AppLayout>
</template>

<style scoped>
/* Animación de blob decorativo */
@keyframes blob {
    0%,
    100% {
        transform: translate(0, 0) scale(1);
    }
    25% {
        transform: translate(20px, -10px) scale(1.1);
    }
    50% {
        transform: translate(-10px, 20px) scale(0.95);
    }
    75% {
        transform: translate(-20px, -10px) scale(1.05);
    }
}

.animate-blob {
    animation: blob 20s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Animación de fade */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

/* Custom scrollbar */
.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Efecto hover mejorado */
button:not([disabled]):hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

/* Efecto de pulsación al hacer click */
button:active {
    transform: translateY(0) scale(0.98);
}

/* Efecto glow en tabs activos */
button:focus-visible {
    outline: none;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.3);
}
</style>
