<script setup>
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: "Confirmar eliminación",
    },
    message: {
        type: String,
        default: "Esta acción no se puede deshacer.",
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

defineEmits(["update:show", "confirm"]);
</script>

<template>
    <transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        >
            <div
                class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden"
            >
                <!-- HEADER -->
                <div class="flex items-center gap-3 px-6 py-4 border-b">
                    <div
                        class="flex items-center justify-center w-10 h-10 rounded-full bg-red-100"
                    >
                        <svg
                            class="w-5 h-5 text-red-600"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v2m0 4h.01M5.07 19h13.86L12 5 5.07 19z"
                            />
                        </svg>
                    </div>

                    <h3 class="text-lg font-semibold text-gray-800">
                        {{ title }}
                    </h3>
                </div>

                <!-- BODY -->
                <div class="px-6 py-5 text-center">
                    <p class="text-gray-600 leading-relaxed">
                        {{ message }}
                    </p>
                </div>

                <!-- FOOTER -->
                <div
                    class="flex justify-end gap-3 px-6 py-4 border-t bg-gray-50"
                >
                    <button
                        class="px-5 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300 transition"
                        :disabled="loading"
                        @click="$emit('update:show', false)"
                    >
                        Cancelar
                    </button>

                    <button
                        class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700 transition disabled:opacity-50"
                        :disabled="loading"
                        @click="$emit('confirm')"
                    >
                        {{ loading ? "Eliminando..." : "Eliminar" }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
