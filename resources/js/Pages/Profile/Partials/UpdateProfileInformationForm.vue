<script setup>
import { ref, watch, reactive, onMounted, onUnmounted } from "vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

// Props
defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    shouldAutofocus: {
        type: Boolean,
        default: false,
    },
});

// Usuario autenticado - crear copia reactiva
const page = usePage();
const userData = reactive({
    name: page.props.auth.user.name,
    email: page.props.auth.user.email,
    username:
        page.props.auth.user.username ||
        page.props.auth.user.email.split("@")[0],
});

// Formulario - sincronizar con userData
const form = useForm({
    name: userData.name,
    email: userData.email,
});

// Modal
const showModal = ref(false);

// Mostrar modal cuando la actualización fue exitosa
watch(
    () => form.recentlySuccessful,
    (val) => {
        if (val) {
            showModal.value = true;
            // ACTUALIZAR DATOS DEL PANEL DERECHO EN TIEMPO REAL
            userData.name = form.name;
            userData.email = form.email;
            userData.username = form.name.toLowerCase().replace(/\s+/g, "_");
        }
    },
);

const closeModal = () => {
    showModal.value = false;
};

// ===== SOLO DETECCIÓN DE CONEXIÓN (ESSENCIAL) =====
const isOnline = ref(navigator.onLine);

const handleOnline = () => {
    isOnline.value = true;
};

const handleOffline = () => {
    isOnline.value = false;
};

onMounted(() => {
    window.addEventListener("online", handleOnline);
    window.addEventListener("offline", handleOffline);
});

onUnmounted(() => {
    window.removeEventListener("online", handleOnline);
    window.removeEventListener("offline", handleOffline);
});
</script>

<template>
    <!-- ✅ NODO RAÍZ ÚNICO - Solución al warning -->
    <div>
        <!-- Contenedor principal con layout dual-panel -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Panel Izquierdo: Formulario (2/3 del ancho) -->
            <div class="lg:col-span-2">
                <!-- Tarjeta de perfil con diseño premium -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700"
                >
                    <!-- Encabezado con acento visual -->
                    <div
                        class="bg-gradient-to-r from-purple-600 to-indigo-700 p-6 md:p-8 text-white"
                    >
                        <div
                            class="flex items-center justify-between flex-wrap gap-4"
                        >
                            <div>
                                <h1
                                    class="text-3xl md:text-4xl font-bold tracking-tight flex items-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-8 w-8 mr-3"
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
                                    Mi Perfil
                                </h1>
                                <p
                                    class="mt-2 text-purple-100 text-lg max-w-2xl"
                                >
                                    Gestiona tu información personal y
                                    preferencias de cuenta
                                </p>
                            </div>
                            <div
                                class="bg-white/10 backdrop-blur-sm p-3 rounded-2xl"
                            >
                                <div class="flex items-center space-x-2">
                                    <div
                                        class="bg-gradient-to-r from-yellow-300 to-yellow-400 p-1.5 rounded-full"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-gray-800"
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
                                    </div>
                                    <span class="font-medium">{{
                                        new Date().toLocaleDateString("es-ES", {
                                            weekday: "long",
                                            year: "numeric",
                                            month: "long",
                                            day: "numeric",
                                        })
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contenido principal -->
                    <div class="p-6 md:p-8">
                        <form
                            @submit.prevent="
                                form.patch(route('profile.update'))
                            "
                            class="space-y-7"
                        >
                            <!-- Sección de información personal -->
                            <div class="space-y-2">
                                <div
                                    class="flex items-center space-x-3 pb-2 border-b border-gray-200 dark:border-gray-700"
                                >
                                    <div
                                        class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-xl"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-purple-600 dark:text-purple-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                            />
                                        </svg>
                                    </div>
                                    <h2
                                        class="text-xl font-bold text-gray-800 dark:text-white"
                                    >
                                        Información Personal
                                    </h2>
                                </div>

                                <!-- Nombre -->
                                <div class="group">
                                    <InputLabel
                                        for="name"
                                        value="Nombre completo"
                                        class="text-gray-700 dark:text-gray-200 font-medium flex items-center"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1.5 text-purple-500"
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
                                        Nombre
                                    </InputLabel>
                                    <div class="mt-1.5 relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none opacity-70"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-gray-400 dark:text-gray-500"
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
                                        <TextInput
                                            id="name"
                                            type="text"
                                            class="pl-11 w-full px-4 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-purple-200 dark:focus:ring-purple-900/50 focus:border-purple-500 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                                            v-model="form.name"
                                            required
                                            :autofocus="shouldAutofocus"
                                            autocomplete="name"
                                        />
                                    </div>
                                    <InputError
                                        class="mt-1.5 text-red-500 text-sm flex items-center"
                                        :message="form.errors.name"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                    </InputError>
                                </div>

                                <!-- Correo -->
                                <div class="group">
                                    <InputLabel
                                        for="email"
                                        value="Dirección de correo electrónico"
                                        class="text-gray-700 dark:text-gray-200 font-medium flex items-center mt-1"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1.5 text-purple-500"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                        Correo electrónico
                                    </InputLabel>
                                    <div class="mt-1.5 relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none opacity-70"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 text-gray-400 dark:text-gray-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                                />
                                            </svg>
                                        </div>
                                        <TextInput
                                            id="email"
                                            type="email"
                                            class="pl-11 w-full px-4 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-purple-200 dark:focus:ring-purple-900/50 focus:border-purple-500 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                                            v-model="form.email"
                                            required
                                            autocomplete="username"
                                        />
                                    </div>
                                    <InputError
                                        class="mt-1.5 text-red-500 text-sm flex items-center"
                                        :message="form.errors.email"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-1"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                    </InputError>
                                </div>
                            </div>

                            <!-- Verificación de correo -->
                            <div
                                v-if="
                                    mustVerifyEmail &&
                                    page.props.auth.user.email_verified_at ===
                                        null
                                "
                                class="mt-2"
                            >
                                <div
                                    class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 dark:border-yellow-500 p-4 rounded-r-xl"
                                >
                                    <div class="flex items-start">
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-yellow-500 mt-0.5 flex-shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                            />
                                        </svg>
                                        <div class="ml-3">
                                            <p
                                                class="text-sm font-medium text-yellow-800 dark:text-yellow-200"
                                            >
                                                Verificación requerida
                                            </p>
                                            <p
                                                class="mt-1 text-sm text-yellow-700 dark:text-yellow-300"
                                            >
                                                Tu correo electrónico no está
                                                verificado.
                                                <Link
                                                    :href="
                                                        route(
                                                            'verification.send',
                                                        )
                                                    "
                                                    method="post"
                                                    as="button"
                                                    class="ml-1 font-semibold underline hover:text-yellow-900 dark:hover:text-yellow-100 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 dark:focus:ring-offset-gray-800 rounded"
                                                >
                                                    Reenviar correo de
                                                    verificación
                                                </Link>
                                            </p>

                                            <div
                                                v-show="
                                                    status ===
                                                    'verification-link-sent'
                                                "
                                                class="mt-3 flex items-start bg-green-50 dark:bg-green-900/20 p-3 rounded-lg border border-green-200 dark:border-green-800"
                                            >
                                                <svg
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    class="h-5 w-5 text-green-500 mt-0.5 flex-shrink-0"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                                <p
                                                    class="ml-2 text-sm font-medium text-green-800 dark:text-green-200"
                                                >
                                                    ¡Listo! Hemos enviado un
                                                    nuevo enlace de verificación
                                                    a tu bandeja de entrada.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón Guardar con estado de éxito -->
                            <div
                                class="pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                            >
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="bg-purple-50 dark:bg-purple-900/30 p-2.5 rounded-xl"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-6 w-6 text-purple-600 dark:text-purple-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M5 13l4 4L19 7"
                                            />
                                        </svg>
                                    </div>
                                    <div>
                                        <h3
                                            class="font-bold text-gray-800 dark:text-white"
                                        >
                                            ¿Listo para guardar los cambios?
                                        </h3>
                                        <p
                                            class="text-sm text-gray-500 dark:text-gray-400"
                                        >
                                            Tus actualizaciones se aplicarán de
                                            inmediato
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <Transition
                                        enter-active-class="transition ease-out duration-300"
                                        enter-from-class="opacity-0 translate-y-1"
                                        enter-to-class="opacity-100 translate-y-0"
                                        leave-active-class="transition ease-in duration-150"
                                        leave-from-class="opacity-100"
                                        leave-to-class="opacity-0"
                                    >
                                        <p
                                            v-if="form.recentlySuccessful"
                                            class="text-green-600 dark:text-green-400 font-medium flex items-center"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 mr-1.5 text-green-500"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            ¡Guardado exitosamente!
                                        </p>
                                    </Transition>

                                    <PrimaryButton
                                        :disabled="form.processing"
                                        class="relative bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold px-7 py-3.5 rounded-xl shadow-lg hover:shadow-purple-500/25 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-purple-500/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-70 disabled:transform-none disabled:hover:shadow-lg"
                                    >
                                        <span
                                            class="flex items-center justify-center"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-5 w-5 mr-2"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                                v-if="!form.processing"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                class="animate-spin h-5 w-5 mr-2 text-white"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    class="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    stroke-width="4"
                                                ></circle>
                                                <path
                                                    class="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            {{
                                                form.processing
                                                    ? "Guardando..."
                                                    : "Guardar cambios"
                                            }}
                                        </span>
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Footer decorativo -->
                    <div
                        class="bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
                    >
                        <div
                            class="flex flex-wrap items-center justify-between gap-3"
                        >
                            <div
                                class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-purple-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span
                                    >Tu información está protegida con cifrado
                                    de última generación</span
                                >
                            </div>
                            <div class="flex items-center space-x-1.5">
                                <div
                                    class="w-2 h-2 rounded-full bg-green-500 animate-pulse"
                                ></div>
                                <span
                                    class="text-xs font-medium text-green-700 dark:text-green-300"
                                    >Conexión segura</span
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Derecho: Información del Perfil (1/3 del ancho) -->
            <div class="lg:col-span-1">
                <div class="sticky top-8 space-y-6">
                    <!-- Tarjeta de Información del Perfil -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700"
                    >
                        <!-- Header del panel -->
                        <div
                            class="bg-gradient-to-r from-purple-600 to-indigo-700 p-6 text-white"
                        >
                            <div class="flex items-center space-x-3">
                                <div class="bg-white/20 p-2.5 rounded-xl">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-6 w-6"
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
                                <h3 class="text-xl font-bold">
                                    Información del Perfil
                                </h3>
                            </div>
                        </div>

                        <!-- Contenido del panel -->
                        <div class="p-6">
                            <!-- Avatar y Nombre - USANDO userData -->
                            <div
                                class="flex flex-col items-center mb-6 text-center"
                            >
                                <div class="relative mb-4">
                                    <div
                                        class="w-28 h-28 rounded-full bg-gradient-to-br from-purple-600 to-indigo-600 flex items-center justify-center text-4xl font-bold text-white shadow-2xl overflow-hidden border-4 border-white dark:border-gray-800"
                                    >
                                        {{
                                            userData.name
                                                .charAt(0)
                                                .toUpperCase()
                                        }}
                                    </div>
                                    <div
                                        class="absolute bottom-2 right-2 w-4 h-4 bg-green-500 rounded-full border-2 border-white dark:border-gray-800"
                                    ></div>
                                </div>
                                <h2
                                    class="text-2xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ userData.name }}
                                </h2>
                                <p class="text-gray-600 dark:text-gray-400">
                                    @{{ userData.username }}
                                </p>

                                <div
                                    class="mt-4 flex flex-wrap justify-center gap-2"
                                >
                                    <span
                                        class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 text-xs px-3 py-1 rounded-full font-medium"
                                        >Miembro Wecollab</span
                                    >
                                    <span
                                        class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs px-3 py-1 rounded-full font-medium"
                                        >Verificado</span
                                    >
                                </div>
                            </div>

                            <!-- Biografía -->
                            <div class="mb-6">
                                <div class="flex items-start space-x-3 mb-3">
                                    <div
                                        class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-purple-600 dark:text-purple-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.418 8-9.999 8-5.582 0-10-3.582-10-8s4.418-8 10-8c5.581 0 9.999 3.582 9.999 8z"
                                            />
                                        </svg>
                                    </div>
                                    <h4
                                        class="text-gray-900 dark:text-white font-semibold"
                                    >
                                        Acerca de Mí
                                    </h4>
                                </div>
                                <p
                                    class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed"
                                >
                                    Lorem ipsum dolor sit amet, consectetur
                                    adipiscing elit. Suspendisse varius enim in
                                    eros elementum tristique.
                                </p>
                            </div>

                            <!-- Información de Contacto - USANDO userData -->
                            <div class="mb-6">
                                <div class="flex items-start space-x-3 mb-3">
                                    <div
                                        class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                    </div>
                                    <h4
                                        class="text-gray-900 dark:text-white font-semibold"
                                    >
                                        Contacto
                                    </h4>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div
                                        class="flex items-center text-gray-600 dark:text-gray-400"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-2 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                                            />
                                        </svg>
                                        {{ userData.email }}
                                    </div>
                                    <div
                                        class="flex items-center text-gray-600 dark:text-gray-400"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-4 w-4 mr-2 text-gray-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                        Ciudad, País
                                    </div>
                                </div>
                            </div>

                            <!-- Última actividad - SOLO CONEXIÓN (ESSENCIAL) -->
                            <div
                                class="pt-4 border-t border-gray-200 dark:border-gray-700"
                            >
                                <div class="flex items-center space-x-2">
                                    <div
                                        :class="
                                            isOnline
                                                ? 'bg-green-500'
                                                : 'bg-gray-400'
                                        "
                                        class="w-2 h-2 rounded-full"
                                    ></div>
                                    <span
                                        class="text-sm font-medium"
                                        :class="
                                            isOnline
                                                ? 'text-gray-900 dark:text-white'
                                                : 'text-gray-500 dark:text-gray-400'
                                        "
                                    >
                                        {{
                                            isOnline
                                                ? "En línea"
                                                : "Desconectado"
                                        }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de Acciones Rápidas -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700"
                    >
                        <div class="p-6">
                            <h4
                                class="text-lg font-bold text-gray-900 dark:text-white mb-4"
                            >
                                Acciones Rápidas
                            </h4>
                            <div class="space-y-3">
                                <!-- Crear Proyecto - AJUSTA LA RUTA SEGÚN TU APLICACIÓN -->
                                <Link
                                    :href="route('dashboard')"
                                    class="w-full flex items-center space-x-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                >
                                    <div
                                        class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-gray-900 dark:text-white font-medium"
                                    >
                                        Ir al Dashboard
                                    </span>
                                </Link>

                                <!-- Configuración - AJUSTA LA RUTA SEGÚN TU APLICACIÓN -->
                                <Link
                                    :href="route('profile.edit')"
                                    class="w-full flex items-center space-x-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                >
                                    <div
                                        class="bg-green-100 dark:bg-green-900/30 p-2 rounded-lg"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-green-600 dark:text-green-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-gray-900 dark:text-white font-medium"
                                    >
                                        Editar Perfil
                                    </span>
                                </Link>

                                <!-- Crear Estimación Proyecto -->
                                <Link
                                    :href="route('estimacion')"
                                    class="w-full flex items-center space-x-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                >
                                    <div
                                        class="bg-blue-100 dark:bg-blue-900/30 p-2 rounded-lg"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-blue-600 dark:text-blue-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-gray-900 dark:text-white font-medium"
                                    >
                                        Crear Estimación Proyecto
                                    </span>
                                </Link>

                                <!-- Preferencias - AJUSTA LA RUTA SEGÚN TU APLICACIÓN -->
                                <button
                                    @click="$inertia.reload()"
                                    class="w-full flex items-center space-x-3 px-4 py-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                >
                                    <div
                                        class="bg-purple-100 dark:bg-purple-900/30 p-2 rounded-lg"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-purple-600 dark:text-purple-400"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                                            />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-gray-900 dark:text-white font-medium"
                                    >
                                        Recargar Página
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de éxito (sin cambios en lógica) -->
        <SuccessModal
            v-model:show="showModal"
            title="Perfil actualizado"
            message="Tu perfil se ha actualizado correctamente."
        />

        <!-- Decoración sutil -->
        <div
            class="hidden md:block absolute top-10 right-10 w-32 h-32 bg-purple-200 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"
        ></div>
        <div
            class="hidden md:block absolute bottom-16 left-10 w-40 h-40 bg-indigo-200 dark:bg-indigo-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"
        ></div>
        <div
            class="hidden md:block absolute top-1/3 left-1/4 w-28 h-28 bg-pink-200 dark:bg-pink-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"
        ></div>
    </div>
</template>

<style scoped>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -20px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

.animate-blob {
    animation: blob 15s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

/* Efecto hover sutil para los labels */
label:hover {
    color: #7c3aed !important;
}

/* Animación para el botón cuando está deshabilitado */
button:disabled {
    animation: pulse-btn 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse-btn {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}
</style>
