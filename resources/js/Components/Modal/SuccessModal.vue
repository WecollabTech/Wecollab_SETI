<script setup>
import { watch, ref } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
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
    { immediate: true }
);

const close = () => {
    visible.value = false;
    emit("update:show", false);
};
</script>

<template>
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-90"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-90"
    >
        <div
            v-if="visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-500/50 p-4"
        >
            <div
                class="bg-white rounded-xl shadow-lg max-w-md w-full p-6 text-center relative"
            >
                <!-- ICONO -->
                <div
                    class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-green-100 mb-4"
                >
                    <span class="text-green-500 text-3xl">✔️</span>
                </div>

                <!-- TITULO ADAPTADO -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    ¡Registro exitoso!
                </h3>

                <!-- MENSAJE -->
                <p class="text-gray-500 text-sm mb-6">
                    {{ props.message }}
                </p>

                <!-- BOTÓN PRINCIPAL -->
                <div class="flex justify-center gap-4">
                    <button
                        @click="close"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition"
                    >
                        Aceptar
                    </button>
                </div>

                <!-- BOTÓN X ARRIBA DERECHA -->
                <button
                    @click="close"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-700 transition text-lg"
                >
                    ✕
                </button>
            </div>
        </div>
    </transition>
</template>
