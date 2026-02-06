<script setup>
import { ref, watch } from "vue";
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
});

// Usuario autenticado
const user = usePage().props.auth.user;

// Formulario
const form = useForm({
    name: user.name,
    email: user.email,
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
</script>

<template>
    <section class="font-sans">
        <!-- Encabezado -->
        <header class="mb-6">
            <h2
                class="text-2xl font-bold text-purple-800 dark:text-purple-300 tracking-wide"
            >
                Información del perfil
            </h2>
            <p
                class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed"
            >
                Actualiza la información de tu cuenta y correo electrónico.
            </p>
        </header>

        <!-- Formulario -->
        <form
            @submit.prevent="form.patch(route('profile.update'))"
            class="mt-6 space-y-6 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg"
        >
            <!-- Nombre -->
            <div>
                <InputLabel
                    for="name"
                    value="Nombre"
                    class="text-gray-700 dark:text-gray-200"
                />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError
                    class="mt-2 text-red-500"
                    :message="form.errors.name"
                />
            </div>

            <!-- Correo -->
            <div>
                <InputLabel
                    for="email"
                    value="Correo electrónico"
                    class="text-gray-700 dark:text-gray-200"
                />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError
                    class="mt-2 text-red-500"
                    :message="form.errors.email"
                />
            </div>

            <!-- Verificación de correo -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Tu correo electrónico no está verificado.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="ml-1 text-purple-600 underline hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-500 rounded"
                    >
                        Haz clic aquí para re-enviar el correo de verificación.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400"
                >
                    Se ha enviado un nuevo enlace de verificación a tu correo.
                </div>
            </div>

            <!-- Botón Guardar -->
            <div class="flex items-center gap-4 mt-4">
                <PrimaryButton
                    :disabled="form.processing"
                    class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-semibold px-6 py-3 rounded-xl shadow-md transition"
                >
                    Guardar
                </PrimaryButton>

                <p
                    v-if="form.recentlySuccessful"
                    class="text-sm text-gray-500 dark:text-gray-300"
                >
                    Guardado.
                </p>
            </div>
        </form>

        <!-- Modal de éxito -->
        <SuccessModal
            v-model:show="showModal"
            title="Perfil actualizado"
            message="Tu perfil se ha actualizado correctamente."
        />
    </section>
</template>
