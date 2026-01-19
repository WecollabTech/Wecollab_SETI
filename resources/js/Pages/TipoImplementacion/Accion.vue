<script>
// Actions
const onBack = () => {
    console.log("Regresar");
};
</script>
<template>
    <!-- ACTIONS -->
    <footer class="flex justify-between gap-4 p-6 border-t">
        <button
            type="button"
            @click="onBack"
            class="flex items-center gap-2 px-4 py-2 font-semibold text-gray-800 bg-gray-300 rounded-lg hover:bg-gray-400 transition"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M15 19l-7-7 7-7"
                />
            </svg>
            Regresar
        </button>

        <button
            type="button"
            @click="onNext"
            class="flex items-center gap-2 px-4 py-2 font-semibold text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition"
        >
            Siguiente
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </button>
    </footer>
</template>
