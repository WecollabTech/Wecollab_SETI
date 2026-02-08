<script setup>
import { computed } from "vue";

/* =======================
   PROPS & EMITS
======================= */
const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "" },
    message: { type: String, default: "" },
    loading: { type: Boolean, default: false },
    type: {
        type: String,
        default: "success", // success | danger | warning | info
    },
    showCancel: { type: Boolean, default: true },
    confirmText: { type: String, default: "Confirmar" },
    cancelText: { type: String, default: "Cancelar" },
});

const emit = defineEmits(["update:show", "confirm"]);

/* =======================
   ESTILOS DINÁMICOS
======================= */
const styles = computed(() => {
    const map = {
        success: {
            header: "from-emerald-500 to-green-600",
            iconBg: "bg-emerald-100 dark:bg-emerald-900/30",
            iconText: "text-emerald-600 dark:text-emerald-400",
            button: "from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700",
        },
        danger: {
            header: "from-red-600 to-orange-600",
            iconBg: "bg-red-100 dark:bg-red-900/30",
            iconText: "text-red-600 dark:text-red-400",
            button: "from-red-600 to-orange-600 hover:from-red-700 hover:to-orange-700",
        },
        warning: {
            header: "from-yellow-500 to-amber-600",
            iconBg: "bg-yellow-100 dark:bg-yellow-900/30",
            iconText: "text-yellow-600 dark:text-yellow-400",
            button: "from-yellow-500 to-amber-600 hover:from-yellow-600 hover:to-amber-700",
        },
        info: {
            header: "from-sky-500 to-blue-600",
            iconBg: "bg-sky-100 dark:bg-sky-900/30",
            iconText: "text-sky-600 dark:text-sky-400",
            button: "from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700",
        },
    };

    return map[props.type] ?? map.success;
});
</script>

<template>
    <transition name="fade-scale">
        <div
            v-if="props.show"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/70 backdrop-blur-sm px-4"
        >
            <div
                class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden"
            >
                <!-- HEADER -->
                <div
                    class="bg-gradient-to-r px-6 py-5 text-white"
                    :class="styles.header"
                >
                    <div class="flex items-center gap-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <!-- ICON HEADER -->
                            <svg
                                v-if="props.type === 'success'"
                                class="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>

                            <svg
                                v-else-if="props.type === 'danger'"
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

                            <svg
                                v-else
                                class="w-7 h-7"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 9v2m0 4h.01"
                                />
                            </svg>
                        </div>

                        <h3 class="text-2xl font-bold">
                            {{ props.title }}
                        </h3>
                    </div>
                </div>

                <!-- BODY -->
                <div class="px-6 py-8 text-center">
                    <div
                        class="mx-auto mb-5 flex items-center justify-center w-20 h-20 rounded-2xl"
                        :class="styles.iconBg"
                    >
                        <!-- ICON BODY -->
                        <svg
                            v-if="props.type === 'success'"
                            class="w-10 h-10"
                            :class="styles.iconText"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>

                        <svg
                            v-else-if="props.type === 'danger'"
                            class="w-10 h-10"
                            :class="styles.iconText"
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
                            class="w-10 h-10"
                            :class="styles.iconText"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 9v2m0 4h.01"
                            />
                        </svg>
                    </div>

                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ props.message }}
                    </p>
                </div>

                <!-- FOOTER -->
                <div
                    class="flex flex-col sm:flex-row gap-4 px-6 py-6 bg-gray-50 dark:bg-gray-900 border-t dark:border-gray-700"
                >
                    <button
                        v-if="props.showCancel"
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-semibold hover:bg-gray-400 dark:hover:bg-gray-600 transition"
                        @click="emit('update:show', false)"
                        :disabled="props.loading"
                    >
                        ✖ {{ props.cancelText }}
                    </button>

                    <button
                        class="flex-1 flex items-center justify-center gap-2 py-3 rounded-xl bg-gradient-to-r text-white font-bold shadow-lg transition disabled:opacity-60"
                        :class="styles.button"
                        @click="emit('confirm')"
                        :disabled="props.loading"
                    >
                        <svg
                            v-if="props.loading"
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

                        {{
                            props.loading ? "Procesando..." : props.confirmText
                        }}
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
