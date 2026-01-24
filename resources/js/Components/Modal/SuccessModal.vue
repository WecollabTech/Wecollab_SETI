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
    <transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 scale-100"
        leave-to-class="opacity-0 scale-95"
    >
        <div
            v-if="visible"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        >
            <div
                role="dialog"
                aria-modal="true"
                aria-labelledby="modal-title"
                class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl shadow-2xl max-w-md w-full p-6 text-center relative"
            >
                <!-- ICONO PRINCIPAL -->
                <div class="mb-5 flex justify-center">
                    <div
                        class="w-20 h-20 flex items-center justify-center rounded-full bg-white/20 backdrop-blur"
                    >
                        <span class="text-white text-4xl">🎉</span>
                    </div>
                </div>

                <!-- TITULO -->
                <h3
                    id="modal-title"
                    class="w-full text-white font-semibold text-xl sm:text-2xl mb-3 break-words leading-snug text-center"
                >
                    {{ message }}
                </h3>

                <!-- MENSAJE -->
                <p
                    class="text-white/80 text-sm mb-6 break-words leading-relaxed text-center"
                >
                    {{ message }}
                </p>

                <!-- BOTÓN -->
                <button
                    @click="close"
                    class="w-full py-3 bg-white text-purple-700 font-semibold rounded-xl hover:bg-white/90 active:scale-95 transition"
                >
                    Aceptar
                </button>

                <!-- BOTÓN CERRAR -->
                <button
                    @click="close"
                    class="absolute top-3 right-3 text-white/80 hover:text-white transition text-xl"
                >
                    ✕
                </button>
            </div>
        </div>
    </transition>
</template>
