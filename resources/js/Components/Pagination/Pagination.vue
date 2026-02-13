<script setup>
const props = defineProps({
    meta: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && "current_page" in value && "last_page" in value;
        },
    },
});

const emit = defineEmits(["page-changed"]);

const goToPage = (page) => {
    if (
        page < 1 ||
        page > props.meta.last_page ||
        page === props.meta.current_page
    )
        return;
    emit("page-changed", page);
};
</script>

<template>
    <div
        class="flex flex-col sm:flex-row sm:items-center justify-center gap-3 mt-6 p-3 sm:p-4 bg-white rounded-xl shadow-sm border border-gray-200 max-w-fit mx-auto"
    >
        <!-- Botón Primera Página -->
        <button
            class="p-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white"
            :disabled="meta.current_page === 1"
            @click="goToPage(1)"
            aria-label="Primera página"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M11 19l-7-7 7-7m8 14l-7-7 7-7"
                />
            </svg>
        </button>

        <!-- Botón Anterior -->
        <button
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white"
            :disabled="meta.current_page === 1"
            @click="goToPage(meta.current_page - 1)"
            aria-label="Página anterior"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
            <span class="hidden xs:inline">Anterior</span>
        </button>

        <!-- Indicador de página -->
        <div
            class="flex items-center bg-gray-50 border border-gray-300 rounded-lg px-5 py-2 min-w-[9rem] justify-center"
        >
            <span class="text-gray-800 font-semibold">
                <span class="font-mono text-lg">{{ meta.current_page }}</span>
                <span class="mx-2 text-gray-400">de</span>
                <span class="font-mono text-gray-500">{{
                    meta.last_page
                }}</span>
            </span>
        </div>

        <!-- Botón Siguiente -->
        <button
            class="flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 bg-white text-gray-700 font-medium hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white"
            :disabled="meta.current_page === meta.last_page"
            @click="goToPage(meta.current_page + 1)"
            aria-label="Página siguiente"
        >
            <span class="hidden xs:inline">Siguiente</span>
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </button>

        <!-- Botón Última Página -->
        <button
            class="p-2 rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:bg-white"
            :disabled="meta.current_page === meta.last_page"
            @click="goToPage(meta.last_page)"
            aria-label="Última página"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-4 w-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M13 5l7 7-7 7M5 5l7 7-7 7"
                />
            </svg>
        </button>
    </div>
</template>
