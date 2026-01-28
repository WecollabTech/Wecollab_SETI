<script setup>
import { ref, watch } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const showPassword = ref(false);

const form = useForm({
    email: localStorage.getItem("last_email") || "",
    password: "",
    remember: false,
});

// Convertir email a minúsculas automáticamente
watch(
    () => form.email,
    (newVal) => {
        form.email = newVal.toLowerCase();
    },
);

const submit = () => {
    localStorage.setItem("last_email", form.email);

    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Iniciar sesión" />

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
                    Bienvenido de nuevo
                </h2>
                <p class="text-center text-gray-600 mb-6">
                    Accede a tu plataforma de planificación y estimación
                </p>

                <!-- STATUS -->
                <div
                    v-if="status"
                    class="mb-4 text-sm text-green-700 bg-green-50 border border-green-200 rounded-lg px-4 py-2 text-center"
                >
                    {{ status }}
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
                        autofocus
                    />
                    <InputError
                        class="mt-2 text-red-600"
                        :message="form.errors.email"
                    />
                </div>

                <!-- PASSWORD -->
                <div class="mb-4 relative">
                    <InputLabel
                        for="password"
                        value="Contraseña"
                        class="text-gray-700"
                    />
                    <TextInput
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="mt-1 block w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 placeholder-gray-400 pr-10"
                        v-model="form.password"
                        placeholder="********"
                        required
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute right-3 top-[38px] text-gray-500 hover:text-purple-600"
                        tabindex="-1"
                    >
                        <i
                            :class="
                                showPassword
                                    ? 'fa-solid fa-eye-slash'
                                    : 'fa-solid fa-eye'
                            "
                        ></i>
                    </button>
                    <InputError
                        class="mt-2 text-red-600"
                        :message="form.errors.password"
                    />
                </div>

                <!-- REMEMBER -->
                <div class="flex items-center mb-6">
                    <Checkbox v-model:checked="form.remember" />
                    <span class="ml-2 text-sm text-gray-600"
                        >Mantener sesión activa</span
                    >
                </div>

                <!-- BOTÓN ENTRAR -->
                <div class="flex flex-col items-center mb-6 w-full">
                    <div class="self-start mb-3 w-full">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm text-purple-600 hover:underline"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>
                    <PrimaryButton
                        :disabled="form.processing"
                        class="px-8 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-lg flex items-center gap-2 mx-auto"
                    >
                        <span
                            v-if="form.processing"
                            class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"
                        ></span>
                        <span>{{
                            form.processing ? "Accediendo..." : "Entrar"
                        }}</span>
                    </PrimaryButton>
                </div>

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
                <p class="mt-4 text-xs text-center text-gray-500">
                    🔒 Conexión segura · Tus datos están protegidos
                </p>
                <p class="mt-2 text-sm text-center text-gray-600">
                    ¿No tienes cuenta?
                    <Link
                        href="/register"
                        class="font-semibold text-purple-600 hover:underline"
                    >
                        Crear cuenta
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
