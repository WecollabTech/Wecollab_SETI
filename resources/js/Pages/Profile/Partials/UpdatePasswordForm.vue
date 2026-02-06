<script setup>
import { ref, watch } from "vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: "",
    password: "",
    password_confirmation: "",
});

// Modal
const showModal = ref(false);

// Mostrar modal cuando la actualización fue exitosa
watch(
    () => form.recentlySuccessful,
    (val) => {
        if (val) showModal.value = true;
    },
);

const closeModal = () => {
    showModal.value = false;
};

const updatePassword = () => {
    form.put(route("password.update"), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset("password", "password_confirmation");
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset("current_password");
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <!-- Tarjeta de cambio de contraseña con diseño premium -->
    <div
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700"
    >
        <!-- Encabezado con acento visual de seguridad -->
        <div
            class="bg-gradient-to-r from-rose-600 to-pink-700 p-6 md:p-8 text-white"
        >
            <div class="flex items-center justify-between flex-wrap gap-4">
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
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                            />
                        </svg>
                        Cambiar Contraseña
                    </h1>
                    <p class="mt-2 text-rose-100 text-lg max-w-2xl">
                        Protege tu cuenta con una contraseña segura y única
                    </p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm p-3 rounded-2xl">
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
            <!-- Información de seguridad -->
            <div
                class="mb-8 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 dark:border-blue-400 p-5 rounded-r-xl"
            >
                <div class="flex items-start">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-blue-600 dark:text-blue-300 mt-1 flex-shrink-0"
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
                    <div class="ml-4">
                        <h3
                            class="text-lg font-bold text-blue-900 dark:text-blue-100"
                        >
                            Consejos de seguridad
                        </h3>
                        <ul
                            class="mt-2 space-y-1.5 text-sm text-blue-800 dark:text-blue-200"
                        >
                            <li class="flex items-start">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-green-500 mt-1 mr-2 flex-shrink-0"
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
                                Usa al menos 12 caracteres con números, letras y
                                símbolos
                            </li>
                            <li class="flex items-start">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-green-500 mt-1 mr-2 flex-shrink-0"
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
                                Evita información personal fácilmente
                                identificable
                            </li>
                            <li class="flex items-start">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-green-500 mt-1 mr-2 flex-shrink-0"
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
                                No reutilices contraseñas de otras cuentas
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="updatePassword" class="space-y-7">
                <!-- Sección de contraseñas -->
                <div class="space-y-2">
                    <div
                        class="flex items-center space-x-3 pb-2 border-b border-gray-200 dark:border-gray-700"
                    >
                        <div
                            class="bg-rose-100 dark:bg-rose-900/30 p-2 rounded-xl"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-rose-600 dark:text-rose-400"
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
                        </div>
                        <h2
                            class="text-xl font-bold text-gray-800 dark:text-white"
                        >
                            Credenciales de Acceso
                        </h2>
                    </div>

                    <!-- Contraseña actual -->
                    <div class="group">
                        <InputLabel
                            for="current_password"
                            value="Contraseña actual"
                            class="text-gray-700 dark:text-gray-200 font-medium flex items-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-1.5 text-rose-500"
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
                            Contraseña actual
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
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                    />
                                </svg>
                            </div>
                            <TextInput
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                type="password"
                                class="pl-11 w-full px-4 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-rose-200 dark:focus:ring-rose-900/50 focus:border-rose-500 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                                autocomplete="current-password"
                            />
                        </div>
                        <InputError
                            class="mt-1.5 text-red-500 text-sm flex items-center"
                            :message="form.errors.current_password"
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

                    <!-- Nueva contraseña -->
                    <div class="group">
                        <InputLabel
                            for="password"
                            value="Nueva contraseña"
                            class="text-gray-700 dark:text-gray-200 font-medium flex items-center mt-1"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-1.5 text-rose-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                />
                            </svg>
                            Nueva contraseña
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
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                    />
                                </svg>
                            </div>
                            <TextInput
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="pl-11 w-full px-4 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-rose-200 dark:focus:ring-rose-900/50 focus:border-rose-500 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                                autocomplete="new-password"
                            />
                        </div>
                        <InputError
                            class="mt-1.5 text-red-500 text-sm flex items-center"
                            :message="form.errors.password"
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

                    <!-- Confirmar contraseña -->
                    <div class="group">
                        <InputLabel
                            for="password_confirmation"
                            value="Confirmar contraseña"
                            class="text-gray-700 dark:text-gray-200 font-medium flex items-center mt-1"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 mr-1.5 text-rose-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4M5 10l7-7m0 0l7 7m-7-7v18"
                                />
                            </svg>
                            Confirmar contraseña
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
                                        d="M9 12l2 2 4-4M5 10l7-7m0 0l7 7m-7-7v18"
                                    />
                                </svg>
                            </div>
                            <TextInput
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="pl-11 w-full px-4 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-rose-200 dark:focus:ring-rose-900/50 focus:border-rose-500 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                                autocomplete="new-password"
                            />
                        </div>
                        <InputError
                            class="mt-1.5 text-red-500 text-sm flex items-center"
                            :message="form.errors.password_confirmation"
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

                <!-- Botón Guardar con estado de éxito -->
                <div
                    class="pt-4 border-t border-gray-200 dark:border-gray-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                >
                    <div class="flex items-center space-x-3">
                        <div
                            class="bg-rose-50 dark:bg-rose-900/30 p-2.5 rounded-xl"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-rose-600 dark:text-rose-400"
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
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 dark:text-white">
                                ¿Listo para actualizar tu contraseña?
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Tus cambios se aplicarán de inmediato
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
                                ¡Contraseña actualizada!
                            </p>
                        </Transition>

                        <PrimaryButton
                            :disabled="form.processing"
                            class="relative bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-700 hover:to-pink-700 text-white font-bold px-7 py-3.5 rounded-xl shadow-lg hover:shadow-rose-500/25 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-rose-500/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-70 disabled:transform-none disabled:hover:shadow-lg"
                        >
                            <span class="flex items-center justify-center">
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
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
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
                                        ? "Actualizando..."
                                        : "Actualizar contraseña"
                                }}
                            </span>
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </div>

        <!-- Footer decorativo -->
        <div
            class="bg-gradient-to-r from-rose-50 to-pink-50 dark:from-gray-800 dark:to-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div
                    class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-rose-500"
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
                    <span>Tu contraseña está protegida con cifrado</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <div
                        class="w-2 h-2 rounded-full bg-green-500 animate-pulse"
                    ></div>
                    <span
                        class="text-xs font-medium text-green-700 dark:text-green-300"
                        >Seguridad máxima</span
                    >
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de éxito (sin cambios en lógica) -->
    <SuccessModal
        v-model:show="showModal"
        title="Contraseña actualizada"
        message="Tu contraseña se ha actualizado correctamente."
    />

    <!-- Decoración sutil -->
    <div
        class="hidden md:block absolute top-10 right-10 w-32 h-32 bg-rose-200 dark:bg-rose-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"
    ></div>
    <div
        class="hidden md:block absolute bottom-16 left-10 w-40 h-40 bg-pink-200 dark:bg-pink-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"
    ></div>
    <div
        class="hidden md:block absolute top-1/3 left-1/4 w-28 h-28 bg-purple-200 dark:bg-purple-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"
    ></div>
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
    color: #e11d48 !important;
}

/* Animación para el botón cuando está deshabilitado */
button:disabled {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

/* Efecto visual para campos de contraseña */
input[type="password"]:focus + svg {
    transform: scale(1.1);
    transition: transform 0.3s ease;
}
</style>
