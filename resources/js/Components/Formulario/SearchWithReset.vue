<script setup>
import { ref, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "Buscar...",
    },
    sortBy: {
        type: String,
        default: "id",
    },
    sortDirection: {
        type: String,
        default: "asc",
    },
    showReset: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["update:modelValue", "search", "reset"]);

const search = ref(props.modelValue);

watch(search, (newValue) => {
    emit("update:modelValue", newValue);
    emit("search", newValue);
});

const handleReset = () => {
    search.value = "";
    emit("reset");
};
</script>

<template>
    <div
        class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto"
    >
        <div class="relative flex-1 sm:flex-none">
            <div
                class="absolute inset-y-0 left-3 flex items-center pointer-events-none"
            >
                <svg
                    class="h-4 w-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
            </div>
            <input
                v-model="search"
                type="text"
                :placeholder="placeholder"
                class="w-full sm:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition"
            />
        </div>

        <!-- Botón de limpiar ordenamiento -->
        <button
            v-if="showReset && (sortBy !== 'id' || sortDirection !== 'asc')"
            @click="handleReset"
            class="px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition flex items-center gap-1.5"
            title="Restablecer orden"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                />
            </svg>
            <span class="hidden xs:inline">Restablecer</span>
        </button>
    </div>
</template>
