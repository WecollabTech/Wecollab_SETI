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
    <section class="font-sans">
        <!-- Encabezado -->
        <header class="mb-6">
            <h2
                class="text-2xl font-bold text-purple-800 dark:text-purple-300 tracking-wide"
            >
                Actualizar Contraseña
            </h2>
            <p
                class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed"
            >
                Asegúrate de que tu cuenta utilice una contraseña larga y
                segura.
            </p>
        </header>

        <!-- Formulario -->
        <form
            @submit.prevent="updatePassword"
            class="mt-6 space-y-6 bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-lg"
        >
            <!-- Contraseña actual -->
            <div>
                <InputLabel
                    for="current_password"
                    value="Contraseña actual"
                    class="text-gray-700 dark:text-gray-200"
                />
                <TextInput
                    id="current_password"
                    ref="currentPasswordInput"
                    v-model="form.current_password"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                    autocomplete="current-password"
                />
                <InputError
                    :message="form.errors.current_password"
                    class="mt-2 text-red-500"
                />
            </div>

            <!-- Nueva contraseña -->
            <div>
                <InputLabel
                    for="password"
                    value="Nueva contraseña"
                    class="text-gray-700 dark:text-gray-200"
                />
                <TextInput
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                    autocomplete="new-password"
                />
                <InputError
                    :message="form.errors.password"
                    class="mt-2 text-red-500"
                />
            </div>

            <!-- Confirmar contraseña -->
            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirmar contraseña"
                    class="text-gray-700 dark:text-gray-200"
                />
                <TextInput
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"
                    autocomplete="new-password"
                />
                <InputError
                    :message="form.errors.password_confirmation"
                    class="mt-2 text-red-500"
                />
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
            title="Contraseña actualizada"
            message="Tu contraseña se ha actualizado correctamente."
        />
    </section>
</template>
