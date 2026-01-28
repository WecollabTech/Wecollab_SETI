<script setup>
import { ref, watch } from "vue";
import { Head, useForm } from "@inertiajs/vue3";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    status: {
        type: String,
    },
});

// Formulario de recuperación
const form = useForm({
    email: "",
});

// Convertir email a minúsculas automáticamente
watch(
    () => form.email,
    (newVal) => {
        form.email = newVal.toLowerCase();
    },
);

const submit = () => {
    form.post(route("password.email"));
};
</script>

<template>
    <Head title="Recuperar contraseña" />

    <AppLayout>
        <div
            class="flex items-center justify-center min-h-[calc(100vh-80px)] px-4"
        >
            <form
                @submit.prevent="submit"
                class="w-full max-w-md bg-white shadow-2xl rounded-3xl p-10 relative overflow-hidden border-t-4 border-purple-600 border-l-2 border-r-2 border-b-2 border-gray-200 hover:scale-[1.02] transition-transform duration-300 animate-fade-in"
            >
                <!-- LOGO -->
                <div class="flex justify-center mb-6">
                    <img
                        src="/img/logo.png"
                        alt="Logo"
                        class="h-14 object-contain"
                    />
                </div>

                <!-- TÍTULO -->
                <h2
                    class="text-3xl font-extrabold text-center text-purple-700 mb-1"
                >
                    Recuperar contraseña
                </h2>
                <p class="text-center text-gray-600 mb-6">
                    Ingresa tu correo electrónico y te enviaremos un enlace para
                    restablecer tu contraseña
                </p>

                <!-- STATUS -->
                <div
                    v-if="status"
                    class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center"
                >
                    {{ status }}
                </div>

                <!-- EMAIL -->
                <div class="mb-6">
                    <InputLabel
                        for="email"
                        value="Correo electrónico"
                        class="text-gray-700"
                    />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 placeholder-gray-400"
                        v-model="form.email"
                        placeholder="correo@ejemplo.com"
                        required
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <!-- BOTÓN ENVIAR -->
                <div class="flex justify-center w-full">
                    <PrimaryButton
                        :disabled="form.processing"
                        class="px-10 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-lg flex items-center justify-center gap-2 mx-auto"
                    >
                        <span
                            v-if="form.processing"
                            class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"
                        ></span>
                        <span>{{
                            form.processing ? "Enviando..." : "Enviar enlace"
                        }}</span>
                    </PrimaryButton>
                </div>

                <!-- FOOTER -->
                <p class="mt-6 text-xs text-center text-gray-500">
                    🔒 Conexión segura · Tus datos están protegidos
                </p>
                <p class="mt-2 text-sm text-center text-gray-600">
                    ¿Ya recuerdas tu contraseña?
                    <Link
                        href="/login"
                        class="font-semibold text-purple-600 hover:underline"
                    >
                        Iniciar sesión
                    </Link>
                </p>
            </form>
        </div>
    </AppLayout>
</template>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fade-in {
    animation: fade-in 0.6s ease-out;
}
</style>
