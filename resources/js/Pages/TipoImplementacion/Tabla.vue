<script setup>
// Imports
import { ref, onMounted } from "vue";
import axios from "axios";
import Accion from "./Accion.vue";

// State
const proyectos = ref([]);
const loading = ref(true);

// Fetch data from API
onMounted(async () => {
    try {
        const response = await axios.get("/api/tipo-implementacion");

        proyectos.value = response.data.data; // 👈 SOLO EL ARRAY
    } catch (error) {
        console.error("Error al cargar tipos de implementación:", error);
    } finally {
        loading.value = false;
    }
});
</script>

<template>
    <!-- CONTENT -->
    <section
        class="max-w-[1100px] mx-auto mt-4 bg-white rounded-2xl shadow-2xl border border-gray-300 font-sans"
    >
        <!-- TOOLBAR -->
        <div class="flex justify-end p-6 border-b">
            <button
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition"
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
                        d="M12 4v16m8-8H4"
                    />
                </svg>
                Nuevo Tipo de Implementación
            </button>
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto p-6">
            <table class="w-full text-sm border rounded-lg overflow-hidden">
                <thead class="bg-gray-100 border-b">
                    <tr
                        class="text-xs uppercase tracking-wider text-[var(--wc-gray-600)]"
                    >
                        <th class="py-4 px-3 text-center">Proyecto</th>
                        <th class="py-4 px-3 text-center">Estado</th>
                        <th class="py-4 px-3 text-center">Acción</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- Loading -->
                    <tr v-if="loading">
                        <td colspan="3" class="py-6 text-center text-gray-500">
                            Cargando datos...
                        </td>
                    </tr>

                    <!-- Empty -->
                    <tr v-else-if="!proyectos.length">
                        <td colspan="3" class="py-6 text-center text-gray-500">
                            No hay registros disponibles
                        </td>
                    </tr>

                    <!-- Data -->
                    <tr
                        v-else
                        v-for="item in proyectos"
                        :key="item.id"
                        class="border-b last:border-0 hover:bg-gray-50 transition"
                    >
                        <td class="py-4 px-3 font-medium text-gray-800">
                            {{ item.nombre }}
                        </td>

                        <td class="py-4 px-3 text-center">
                            <span
                                class="inline-flex px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700"
                            >
                                {{ item.estado }}
                            </span>
                        </td>

                        <td class="py-4 px-3 text-center">
                            <button
                                type="button"
                                class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-[var(--wc-primary)] rounded-md hover:bg-indigo-50 hover:text-indigo-700 transition"
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
                                        d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                    />
                                </svg>
                                Editar
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ACTIONS -->
        <Accion />
    </section>
</template>
