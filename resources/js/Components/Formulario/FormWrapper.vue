<script setup>
import { defineEmits, defineProps } from "vue";

defineProps({
    title: String,
    submitText: {
        type: String,
        default: "Guardar",
    },
});

const emit = defineEmits(["submit"]);

const handleSubmit = () => {
    // No necesitamos e.preventDefault() porque lo hacemos en el template
    emit("submit"); // Emite el evento al padre
};
</script>

<template>
    <!-- FormWrapper.vue -->
    <section
        class="max-w-[1100px] mx-auto bg-white rounded-2xl shadow-2xl border border-gray-300"
    >
        <!-- HEADER -->
        <div
            class="p-6 border-b text-center flex justify-center items-center gap-2"
        >
            <h2 class="text-1xl font-bold" style="color: #422782">
                {{ title }}
            </h2>
            <slot name="headerExtra" />
        </div>

        <!-- FORM -->
        <form class="p-8 space-y-6" @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <slot />
            </div>

            <!-- ACTIONS -->
            <div class="flex justify-center gap-4 p-6 border-t">
                <slot name="actions">
                    <button
                        type="submit"
                        class="px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow transition"
                    >
                        {{ submitText }}
                    </button>
                </slot>
            </div>
        </form>
    </section>
</template>
