<script setup>
import { ref, watch } from "vue";

// Definir props correctamente
const props = defineProps({
    show: Boolean,
    message: String,
    autoClose: {
        type: Number,
        default: 4000,
    },
});

const emit = defineEmits(["update:show"]);

const visible = ref(false);

// Watcher que reacciona a props.show
watch(
    () => props.show,
    (val) => {
        if (val) {
            visible.value = true;

            if (props.autoClose > 0) {
                setTimeout(() => {
                    visible.value = false;
                    emit("update:show", false);
                }, props.autoClose);
            }
        }
    },
    { immediate: true }
);

// Cerrar manual
const close = () => {
    visible.value = false;
    emit("update:show", false);
};
</script>

<template>
    <transition name="fade">
        <div
            v-if="visible"
            class="fixed top-5 right-5 z-50 max-w-sm w-full bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center justify-between space-x-4"
        >
            <div class="font-medium">{{ props.message }}</div>
            <button
                @click="close"
                class="text-white hover:text-gray-200 font-bold"
            >
                ✕
            </button>
        </div>
    </transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}
.fade-enter-from {
    opacity: 0;
    transform: translateY(-20px);
}
.fade-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
