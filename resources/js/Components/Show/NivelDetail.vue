<script setup>
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import { router } from "@inertiajs/vue3";

// Props para reutilización
const props = defineProps({
    apiUrl: { type: String, required: true }, // URL para cargar los datos
    title: { type: String, default: "Detalle" }, // título del header
    showActions: { type: Boolean, default: true }, // mostrar botones
    showStatus: { type: Boolean, default: true }, // mostrar indicador de estado
});

// Estado reactivo
const item = reactive({
    id: null,
    nombre: "",
    factor: 0,
    descripcion: "",
    estado: 1,
});
const loading = ref(true);
const error = ref("");

// Función para cargar datos
const cargarItem = async () => {
    loading.value = true;
    error.value = "";
    try {
        const res = await axios.get(props.apiUrl);
        if (res.data.success) {
            Object.assign(item, res.data.data);
        } else {
            error.value = "No se pudo cargar la información.";
        }
    } catch (err) {
        console.error(err);
        error.value = "Error al cargar desde el servidor.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => cargarItem());
</script>

<template>
    <div class="container my-8">
        <!-- Loading -->
        <div v-if="loading" class="flex flex-col items-center py-10">
            <div
                class="spinner-border animate-spin w-16 h-16 border-4 border-t-blue-500 border-gray-300 rounded-full"
            ></div>
            <p class="mt-4 text-gray-500 text-lg font-medium">Cargando...</p>
        </div>

        <!-- Error -->
        <div v-else-if="error" class="alert alert-danger text-center">
            {{ error }}
        </div>

        <!-- Dashboard Card -->
        <div
            v-else
            class="max-w-3xl mx-auto bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transition transform hover:scale-[1.01] duration-300"
        >
            <!-- Header -->
            <div
                class="bg-gradient-to-r from-blue-700 to-blue-900 text-white px-6 py-4 flex justify-between items-center"
            >
                <h2
                    class="text-2xl md:text-3xl font-bold flex items-center gap-2"
                >
                    {{ title }}: {{ item.nombre }}
                </h2>

                <div v-if="showStatus" class="flex items-center gap-2">
                    <span
                        :class="
                            item.estado
                                ? 'bg-green-100 text-green-800 px-3 py-1 rounded-full font-semibold animate-pulse'
                                : 'bg-red-100 text-red-800 px-3 py-1 rounded-full font-semibold animate-pulse'
                        "
                    >
                        {{ item.estado ? "Activo" : "Inactivo" }}
                    </span>
                    <div
                        :class="item.estado ? 'bg-green-500' : 'bg-red-500'"
                        class="w-4 h-4 rounded-full animate-ping"
                    ></div>
                </div>
            </div>

            <!-- Body -->
            <div
                class="px-6 py-6 md:py-8 grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8"
            >
                <!-- Factor -->
                <div
                    class="bg-gray-50 p-5 rounded-xl shadow-inner hover:shadow transition"
                >
                    <h3
                        class="font-semibold text-gray-600 text-sm uppercase mb-2"
                    >
                        Factor
                    </h3>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div
                            class="bg-blue-600 h-4 rounded-full transition-all duration-1000"
                            :style="{ width: item.factor + '%' }"
                        ></div>
                    </div>
                    <p class="mt-2 text-gray-700 font-semibold">
                        {{ item.factor }}%
                    </p>
                </div>

                <!-- Descripción -->
                <div
                    class="bg-gray-50 p-5 rounded-xl shadow-inner hover:shadow transition"
                >
                    <h3
                        class="font-semibold text-gray-600 text-sm uppercase mb-2"
                    >
                        Descripción
                    </h3>
                    <p class="text-gray-700 text-sm md:text-base">
                        {{ item.descripcion || "-" }}
                    </p>
                </div>

                <!-- Estado grande -->
                <div
                    class="col-span-1 md:col-span-2 bg-gray-50 p-5 rounded-xl shadow-inner flex flex-col md:flex-row items-center justify-between gap-4 hover:shadow transition"
                    v-if="showStatus"
                >
                    <div class="flex items-center gap-3">
                        <div
                            :class="
                                item.estado
                                    ? 'bg-green-500 w-12 h-12 rounded-full flex items-center justify-center animate-pulse'
                                    : 'bg-red-500 w-12 h-12 rounded-full flex items-center justify-center animate-pulse'
                            "
                        >
                            <svg
                                v-if="item.estado"
                                class="w-6 h-6 text-white"
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
                            <svg
                                v-else
                                class="w-6 h-6 text-white"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </div>
                        <span
                            class="text-gray-700 font-semibold text-lg md:text-xl"
                            >Estado actual</span
                        >
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div
                v-if="showActions"
                class="px-6 py-4 flex justify-end gap-3 bg-gray-50 border-t border-gray-100"
            >
                <button
                    @click="router.get(route('niveles.index'))"
                    class="px-5 py-2 rounded-lg bg-gray-300 hover:bg-gray-400 text-gray-800 font-medium transition"
                >
                    Volver
                </button>
                <button
                    @click="router.get(route('niveles.edit', item.id))"
                    class="px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition"
                >
                    Editar
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.spinner-border {
    @apply border-4 border-t-4 border-t-blue-500 border-gray-300 rounded-full;
    width: 4rem;
    height: 4rem;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
</style>
