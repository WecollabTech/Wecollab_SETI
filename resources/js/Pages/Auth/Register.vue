<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

// Convertir email a minúsculas automáticamente
watch(
    () => form.email,
    (newVal) => {
        form.email = newVal.toLowerCase();
    },
);

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <Head title="Registro" />

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
                    Crear cuenta
                </h2>
                <p class="text-center text-gray-600 mb-6">
                    Completa el formulario para registrarte
                </p>

                <!-- NOMBRE -->
                <div class="mb-4">
                    <InputLabel
                        for="name"
                        value="Nombre completo"
                        class="text-gray-700"
                    />
                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 placeholder-gray-400"
                        v-model="form.name"
                        placeholder="Tu nombre completo"
                        required
                        autofocus
                    />
                    <InputError
                        class="mt-2 text-red-600"
                        :message="form.errors.name"
                    />
                </div>

                <!-- EMAIL -->
                <div class="mb-4">
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
                    />
                    <InputError
                        class="mt-2 text-red-600"
                        :message="form.errors.email"
                    />
                </div>

                <!-- PASSWORD -->
                <div class="mb-4">
                    <InputLabel
                        for="password"
                        value="Contraseña"
                        class="text-gray-700"
                    />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 placeholder-gray-400"
                        v-model="form.password"
                        placeholder="********"
                        required
                    />
                    <InputError
                        class="mt-2 text-red-600"
                        :message="form.errors.password"
                    />
                </div>

                <!-- PASSWORD CONFIRM -->
                <div class="mb-6">
                    <InputLabel
                        for="password_confirmation"
                        value="Confirmar contraseña"
                        class="text-gray-700"
                    />
                    <TextInput
                        id="password_confirmation"
                        type="password"
                        class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 placeholder-gray-400"
                        v-model="form.password_confirmation"
                        placeholder="********"
                        required
                    />
                    <InputError
                        class="mt-2 text-red-600"
                        :message="form.errors.password_confirmation"
                    />
                </div>

                <!-- BOTÓN REGISTRARSE -->
                <PrimaryButton
                    :disabled="form.processing"
                    class="w-full px-8 py-3 bg-gradient-to-r from-purple-600 to-purple-800 text-white font-semibold rounded-xl shadow-lg hover:scale-105 hover:brightness-110 transition-all duration-300 flex justify-center items-center gap-2 mb-4"
                >
                    <span
                        v-if="form.processing"
                        class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"
                    ></span>
                    <span>{{
                        form.processing ? "Registrando..." : "Crear cuenta"
                    }}</span>
                </PrimaryButton>

                <!-- SOCIAL LOGIN (placeholder) -->
                <button
                    type="button"
                    disabled
                    class="w-full flex items-center justify-center gap-3 px-4 py-2 border border-gray-300 rounded-xl text-gray-400 cursor-not-allowed mb-6"
                >
                    <i class="fa-brands fa-google"></i>
                    Continuar con Google (próximamente)
                </button>

                <!-- FOOTER -->
                <p class="mt-6 text-xs text-center text-gray-500">
                    🔒 Conexión segura · Tus datos están protegidos
                </p>
                <p class="mt-2 text-sm text-center text-gray-600">
                    ¿Ya tienes cuenta?
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
