<script setup>
import { watch, ref } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "¡Registro exitoso!" },
    message: {
        type: String,
        default: "El registro se ha guardado correctamente.",
    },
    autoClose: { type: Number, default: 4000 },
});

const emit = defineEmits(["update:show"]);
const visible = ref(props.show);

watch(
    () => props.show,
    (val) => {
        visible.value = val;
        if (val && props.autoClose > 0) {
            setTimeout(() => {
                visible.value = false;
                emit("update:show", false);
            }, props.autoClose);
        }
    },
    { immediate: true },
);

const close = () => {
    visible.value = false;
    emit("update:show", false);
};
</script>

<template>
    <transition name="fade-scale">
        <div
            v-if="visible"
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm px-4"
        >
            <div
                class="bg-white dark:bg-gray-800 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden"
            >
                <!-- HEADER -->
                <div
                    class="bg-gradient-to-r from-emerald-500 to-green-600 p-6 text-white"
                >
                    <div class="flex items-center space-x-3">
                        <div class="bg-white/20 p-3 rounded-full">
                            <!-- Check icon -->
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
                                    d="M5 13l4 4L19 7"
                                />
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold">
                            {{ title }}
                        </h2>
                    </div>
                </div>

                <!-- CONTENT -->
                <div class="p-6 md:p-8 text-center">
                    <!-- ICON -->
                    <div
                        class="inline-flex items-center justify-center bg-emerald-100 dark:bg-emerald-900/30 p-5 rounded-2xl mb-5"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-14 w-14 text-emerald-600 dark:text-emerald-400"
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
                    </div>

                    <p
                        class="text-gray-700 dark:text-gray-300 leading-relaxed mb-6"
                    >
                        {{ message }}
                    </p>

                    <!-- BUTTON -->
                    <button
                        @click="close"
                        class="w-full bg-gradient-to-r from-emerald-500 to-green-600 hover:from-emerald-600 hover:to-green-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-emerald-500/30 transition-all duration-300 transform hover:-translate-y-0.5 active:scale-95"
                    >
                        Entendido
                    </button>
                </div>

                <!-- FOOTER -->
                <div
                    class="px-6 pb-4 text-center border-t border-gray-200 dark:border-gray-700"
                >
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Este mensaje se cerrará automáticamente
                    </p>
                </div>

                <!-- CLOSE -->
                <button
                    @click="close"
                    class="absolute top-4 right-4 text-white/80 hover:text-white transition"
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
                </button>
            </div>
        </div>
    </transition>
</template>

<style scoped>
.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: all 0.3s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: scale(0.95);
}
</style>
