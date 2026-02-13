<script setup>
const props = defineProps({
    sending: {
        type: Boolean,
        default: false,
    },
    cancelText: {
        type: String,
        default: "Cancelar",
    },
    submitText: {
        type: String,
        default: "Guardar",
    },
});

const emit = defineEmits(["cancel", "submit"]);
</script>

<template>
    <div
        class="flex flex-col-reverse sm:flex-row justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-700"
    >
        <!-- Cancelar -->
        <button
            type="button"
            @click="$emit('cancel')"
            class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-semibold rounded-xl border-2 border-gray-300 text-gray-700 bg-white transition-all duration-200 hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:bg-gray-900 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-800"
        >
            {{ cancelText }}
        </button>

        <!-- Guardar -->
        <button
            type="button"
            @click="$emit('submit')"
            :disabled="sending"
            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-semibold rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white transition-all duration-200 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-60 disabled:cursor-not-allowed shadow-md hover:shadow-lg"
        >
            <!-- Loader -->
            <svg
                v-if="sending"
                class="w-4 h-4 animate-spin"
                fill="none"
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

            <span>
                {{ sending ? "Guardando..." : submitText }}
            </span>
        </button>
    </div>
</template>
