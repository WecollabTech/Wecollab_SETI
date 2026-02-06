<script setup>
import DangerButton from "@/Components/DangerButton.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import Modal from "@/Components/Modal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { useForm } from "@inertiajs/vue3";
import { nextTick, ref } from "vue";

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: "",
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route("profile.destroy"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <!-- Tarjeta de eliminación de cuenta con diseño premium -->
    <div
        class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden border border-gray-200 dark:border-gray-700"
    >
        <!-- Encabezado con acento visual de advertencia -->
        <div
            class="bg-gradient-to-r from-red-600 to-orange-700 p-6 md:p-8 text-white"
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
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                        Eliminar Cuenta
                    </h1>
                    <p class="mt-2 text-red-100 text-lg max-w-2xl">
                        Esta acción es permanente e irreversible
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
            <!-- Advertencia importante -->
            <div
                class="mb-8 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-400 p-5 rounded-r-xl"
            >
                <div class="flex items-start">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-red-600 dark:text-red-300 mt-1 flex-shrink-0"
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
                    <div class="ml-4">
                        <h3
                            class="text-lg font-bold text-red-900 dark:text-red-100"
                        >
                            Advertencia Importante
                        </h3>
                        <p class="mt-2 text-sm text-red-800 dark:text-red-200">
                            <span class="font-semibold"
                                >Una vez eliminada tu cuenta:</span
                            >
                        </p>
                        <ul
                            class="mt-2 space-y-1.5 text-sm text-red-700 dark:text-red-300"
                        >
                            <li class="flex items-start">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-red-500 mt-1 mr-2 flex-shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                Todos tus datos se borrarán permanentemente
                            </li>
                            <li class="flex items-start">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-red-500 mt-1 mr-2 flex-shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                No podrás recuperar tu información
                            </li>
                            <li class="flex items-start">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-red-500 mt-1 mr-2 flex-shrink-0"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                                Perderás acceso a todos los servicios
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sección de información -->
            <div class="space-y-6">
                <div>
                    <h2
                        class="text-xl font-bold text-gray-800 dark:text-white flex items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-red-500 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        ¿Estás seguro?
                    </h2>
                    <p
                        class="mt-3 text-gray-600 dark:text-gray-300 leading-relaxed"
                    >
                        Antes de proceder con la eliminación de tu cuenta, te
                        recomendamos encarecidamente que descargues cualquier
                        información importante que desees conservar. Esta acción
                        no se puede deshacer.
                    </p>
                </div>

                <!-- Recomendaciones -->
                <div
                    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-5"
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
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <div class="ml-4">
                            <h3
                                class="font-bold text-blue-900 dark:text-blue-100"
                            >
                                Recomendaciones antes de continuar
                            </h3>
                            <ul
                                class="mt-3 space-y-2 text-sm text-blue-800 dark:text-blue-200"
                            >
                                <li class="flex items-start">
                                    <span
                                        class="bg-blue-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center mr-2 mt-1"
                                        >1</span
                                    >
                                    <span
                                        >Exporta tus datos personales y
                                        configuraciones</span
                                    >
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-blue-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center mr-2 mt-1"
                                        >2</span
                                    >
                                    <span
                                        >Guarda copias de seguridad de
                                        documentos importantes</span
                                    >
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-blue-600 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center mr-2 mt-1"
                                        >3</span
                                    >
                                    <span
                                        >Considera alternativas como desactivar
                                        temporalmente tu cuenta</span
                                    >
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botón de eliminación -->
                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                    <DangerButton
                        @click="confirmUserDeletion"
                        class="w-full bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold text-lg py-4 rounded-xl shadow-lg hover:shadow-red-500/30 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-red-500/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800 flex items-center justify-center space-x-3"
                    >
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
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                        <span>Eliminar Cuenta Permanentemente</span>
                    </DangerButton>

                    <p
                        class="mt-3 text-center text-sm text-gray-500 dark:text-gray-400"
                    >
                        Esta acción requiere confirmación adicional por
                        seguridad
                    </p>
                </div>
            </div>
        </div>

        <!-- Footer decorativo -->
        <div
            class="bg-gradient-to-r from-red-50 to-orange-50 dark:from-gray-800 dark:to-gray-700/50 px-6 py-4 border-t border-gray-200 dark:border-gray-700"
        >
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div
                    class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-300"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-4 w-4 text-red-500"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    <span>Esta acción es irreversible y permanente</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <div
                        class="w-2 h-2 rounded-full bg-red-500 animate-pulse"
                    ></div>
                    <span
                        class="text-xs font-medium text-red-700 dark:text-red-300"
                        >Acción crítica</span
                    >
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación mejorado -->
    <Modal :show="confirmingUserDeletion" @close="closeModal">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden"
        >
            <!-- Header del modal -->
            <div
                class="bg-gradient-to-r from-red-600 to-orange-700 p-6 text-white"
            >
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-3 rounded-full">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8"
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
                    </div>
                    <h2 class="text-2xl font-bold">Confirmar Eliminación</h2>
                </div>
            </div>

            <!-- Contenido del modal -->
            <div class="p-6 md:p-8">
                <div class="text-center mb-6">
                    <div
                        class="inline-block bg-red-100 dark:bg-red-900/30 p-4 rounded-2xl mb-4"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-12 w-12 text-red-600 dark:text-red-400"
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
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                        ¿Estás absolutamente seguro?
                    </h3>
                    <p class="mt-3 text-gray-600 dark:text-gray-300">
                        Esta acción eliminará permanentemente tu cuenta y todos
                        tus datos.
                        <span
                            class="font-semibold text-red-600 dark:text-red-400"
                            >No se puede deshacer.</span
                        >
                    </p>
                </div>

                <div
                    class="mt-6 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-xl p-5"
                >
                    <p
                        class="text-sm text-gray-700 dark:text-gray-200 font-medium mb-3 flex items-start"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 text-red-500 mt-0.5 mr-2 flex-shrink-0"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        Para confirmar, por favor ingresa tu contraseña:
                    </p>

                    <div class="mt-4">
                        <InputLabel
                            for="password"
                            value="Contraseña"
                            class="sr-only"
                        />
                        <div class="relative">
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
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="pl-11 w-full px-4 py-3.5 rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-red-200 dark:focus:ring-red-900/50 focus:border-red-500 transition-all duration-200 shadow-sm hover:border-gray-400 dark:hover:border-gray-500"
                                placeholder="Ingresa tu contraseña"
                                @keyup.enter="deleteUser"
                            />
                        </div>
                        <InputError
                            :message="form.errors.password"
                            class="mt-2 text-red-500 text-sm flex items-center"
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

                <!-- Botones del modal -->
                <div
                    class="mt-8 flex flex-col sm:flex-row sm:justify-center gap-4"
                >
                    <SecondaryButton
                        @click="closeModal"
                        class="flex-1 bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-lg transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-gray-400/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 mr-2"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"
                            />
                        </svg>
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="flex-1 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold py-3.5 rounded-xl shadow-md hover:shadow-red-500/30 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-4 focus:ring-red-500/50 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-70 disabled:transform-none"
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
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
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
                                    ? "Eliminando..."
                                    : "Sí, Eliminar Cuenta"
                            }}
                        </span>
                    </DangerButton>
                </div>

                <!-- Advertencia final -->
                <div
                    class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 text-center"
                >
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Al hacer clic en "Sí, Eliminar Cuenta", confirmas que
                        comprendes las consecuencias permanentes de esta acción.
                    </p>
                </div>
            </div>
        </div>
    </Modal>

    <!-- Decoración sutil -->
    <div
        class="hidden md:block absolute top-10 right-10 w-32 h-32 bg-red-200 dark:bg-red-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"
    ></div>
    <div
        class="hidden md:block absolute bottom-16 left-10 w-40 h-40 bg-orange-200 dark:bg-orange-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"
    ></div>
    <div
        class="hidden md:block absolute top-1/3 left-1/4 w-28 h-28 bg-pink-200 dark:bg-pink-900/20 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-4000"
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
    color: #dc2626 !important;
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

/* Efecto de advertencia para el botón peligroso */
.danger-warning {
    position: relative;
    overflow: hidden;
}

.danger-warning::before {
    content: "";
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    animation: shine 2s infinite;
}

@keyframes shine {
    100% {
        left: 100%;
    }
}
</style>
