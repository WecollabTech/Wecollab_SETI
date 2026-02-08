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
    <transition name="fade-scale">
        <div
            v-if="show"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm px-4"
        >
            <div
                class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden"
            >
                <!-- HEADER -->
                <div
                    class="bg-gradient-to-r from-red-600 to-orange-600 px-6 py-5 text-white"
                >
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <!-- Warning icon -->
                            <svg
                                class="w-7 h-7"
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

                        <h3 class="text-2xl font-bold">
                            {{ title }}
                        </h3>
                    </div>
                </div>

                <!-- BODY -->
                <div class="px-6 py-8 text-center">
                    <div
                        class="mx-auto mb-5 flex items-center justify-center w-20 h-20 rounded-2xl bg-red-100 dark:bg-red-900/30"
                    >
                        <!-- Trash icon -->
                        <svg
                            class="w-10 h-10 text-red-600 dark:text-red-400"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3"
                            />
                        </svg>
                    </div>

                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ message }}
                        <span
                            class="block mt-2 font-semibold text-red-600 dark:text-red-400"
                        >
                            Esta acción no se puede deshacer.
                        </span>
                    </p>
                </div>

                <!-- FOOTER -->
                <div
                    class="flex flex-col sm:flex-row gap-4 px-6 py-6 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700"
                >
                    <button
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-400 dark:hover:bg-gray-600 transition"
                        :disabled="loading"
                        @click="$emit('update:show', false)"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                        Cancelar
                    </button>

                    <button
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700 text-white font-bold shadow-lg transition disabled:opacity-60"
                        :disabled="loading"
                        @click="$emit('confirm')"
                    >
                        <svg
                            v-if="!loading"
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7"
                            />
                        </svg>

                        <svg
                            v-else
                            class="animate-spin w-5 h-5"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                            />
                        </svg>

                        {{ loading ? "Eliminando..." : "Eliminar" }}
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.25s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
