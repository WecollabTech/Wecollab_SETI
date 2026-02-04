<script setup>
import { watch, ref } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "Operación exitosa" },
    message: {
        type: String,
        default: "La operación se realizó correctamente.",
    },
    mode: {
        type: String,
        default: "success", // success | confirm
    },
    autoClose: { type: Number, default: 4000 },
});

const emit = defineEmits(["update:show", "confirm"]);

const visible = ref(props.show);

watch(
    () => props.show,
    (val) => {
        visible.value = val;

        // ✅ Auto cierre SOLO en success
        if (val && props.mode === "success" && props.autoClose > 0) {
            setTimeout(() => {
                close();
            }, props.autoClose);
        }
    },
    { immediate: true },
);

const close = () => {
    visible.value = false;
    emit("update:show", false);
};

const confirmAction = () => {
    emit("confirm");
    close();
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
                class="bg-gradient-to-br from-purple-500 to-purple-700 rounded-2xl shadow-2xl max-w-md w-full p-6 text-center relative"
            >
                <!-- ICONO -->
                <div class="mb-5 flex justify-center">
                    <div
                        class="w-20 h-20 flex items-center justify-center rounded-full bg-white/20 backdrop-blur"
                    >
                        <span class="text-white text-4xl">
                            {{ mode === "confirm" ? "⚠️" : "🎉" }}
                        </span>
                    </div>
                </div>

                <!-- TITULO -->
                <h3 class="text-white font-semibold text-xl sm:text-2xl mb-3">
                    {{ title }}
                </h3>

                <!-- MENSAJE -->
                <p class="text-white/80 text-sm mb-6">
                    {{ message }}
                </p>

                <!-- BOTONES -->
                <div
                    class="flex gap-3"
                    :class="mode === 'confirm' ? 'flex-row' : 'flex-col'"
                >
                    <button
                        v-if="mode === 'confirm'"
                        @click="close"
                        class="w-full py-3 bg-white/20 text-white font-semibold rounded-xl hover:bg-white/30 transition"
                    >
                        Cancelar
                    </button>

                    <button
                        @click="mode === 'confirm' ? confirmAction() : close()"
                        class="w-full py-3 bg-white text-purple-700 font-semibold rounded-xl hover:bg-white/90 active:scale-95 transition"
                    >
                        {{ mode === "confirm" ? "Confirmar" : "Aceptar" }}
                    </button>
                </div>

                <!-- CERRAR -->
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
