<script setup>
defineProps({
    show: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: "Operación exitosa",
    },
    message: {
        type: String,
        default: "La acción se realizó correctamente.",
    },
});
defineEmits(["update:show"]);
</script>

<template>
    <transition name="fade">
        <div
            v-if="show"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        >
            <div
                class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden text-center"
            >
                <!-- ICON -->
                <div class="pt-8 pb-4">
                    <div
                        class="mx-auto flex items-center justify-center w-16 h-16 rounded-full bg-green-100"
                    >
                        <svg
                            class="w-8 h-8 text-green-600"
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
                    </div>
                </div>

                <!-- CONTENT -->
                <div class="px-6 pb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">
                        {{ title }}
                    </h3>

                    <p class="text-gray-600 leading-relaxed">
                        {{ message }}
                    </p>
                </div>

                <!-- FOOTER -->
                <div class="px-6 py-4 border-t bg-gray-50">
                    <button
                        @click="$emit('update:show', false)"
                        class="px-6 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700 transition"
                    >
                        Aceptar
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
