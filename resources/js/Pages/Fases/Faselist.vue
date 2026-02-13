<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";
import ConfirmDeleteModal from "@/Components/Modal/ConfirmDeleteModal.vue";
import SuccessModals from "@/Components/Modal/SuccessModal.vue";

// Componentes reutilizables
import Pagination from "@/Components/Pagination/Pagination.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import ActionButtons from "@/Components/Table/ActionButtons.vue";
import SearchWithReset from "@/Components/Formulario/SearchWithReset.vue";

import { ref, computed, onMounted } from "vue";
import axios from "axios";

// --- STATE ---
const fases = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    total: 0,
});

const loading = ref(true);
const search = ref("");
const sortBy = ref("id"); // Columna por defecto
const sortDirection = ref("asc"); // ✅ ORDEN ASCENDENTE POR DEFECTO (1, 2, 3...)

// --- MODALES ---
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const deleting = ref(false);
const faseIdToDelete = ref(null);

// --- DATOS ORDENADOS (Frontend) ---
const sortedData = computed(() => {
    if (!fases.value?.data) return [];

    return [...fases.value.data].sort((a, b) => {
        const aValue = a[sortBy.value];
        const bValue = b[sortBy.value];

        // Manejar valores null/undefined
        if (aValue === null || aValue === undefined) return 1;
        if (bValue === null || bValue === undefined) return -1;

        // Comparar según tipo de dato
        if (typeof aValue === "string" && typeof bValue === "string") {
            return sortDirection.value === "asc"
                ? aValue.localeCompare(bValue, "es", { sensitivity: "base" })
                : bValue.localeCompare(aValue, "es", { sensitivity: "base" });
        }

        // Para números
        return sortDirection.value === "asc"
            ? aValue - bValue
            : bValue - aValue;
    });
});

// --- FUNCIONES ---
const cargarFases = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/fases?page=${page}&search=${search.value}&sort_by=${sortBy.value}&sort_direction=${sortDirection.value}`,
        );
        fases.value = res.data;
    } catch (error) {
        console.error("Error al cargar fases:", error);
    } finally {
        loading.value = false;
    }
};

const buscarFases = () => {
    cargarFases(1);
};

// --- ORDENAMIENTO ---
const changeSort = (column) => {
    if (sortBy.value === column) {
        // Si ya está ordenado por esta columna, invertir dirección
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        // Nueva columna, orden ascendente por defecto
        sortBy.value = column;
        sortDirection.value = "asc";
    }
    cargarFases(fases.value.current_page);
};

const resetSort = () => {
    sortBy.value = "id";
    sortDirection.value = "asc";
    cargarFases(fases.value.current_page);
};

// --- ELIMINAR (MODAL) ---
const confirmarEliminar = (id) => {
    faseIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarFase = async () => {
    if (!faseIdToDelete.value) return;

    deleting.value = true;
    try {
        await axios.delete(`/api/fases/${faseIdToDelete.value}`);
        showDeleteModal.value = false;
        showSuccessModal.value = true;
        cargarFases(fases.value.current_page);
    } catch (err) {
        console.error("Error al eliminar fase:", err);
        alert("Error al eliminar la fase. Por favor, intenta nuevamente.");
    } finally {
        deleting.value = false;
        faseIdToDelete.value = null;
    }
};

// --- DUPLICAR ---
const duplicarFase = async (id) => {
    loading.value = true;
    try {
        await axios.post(`/api/fases/${id}/duplicate`);
        cargarFases(fases.value.current_page);
        showSuccessModal.value = true;
    } catch (err) {
        console.error("Error al duplicar fase:", err);
        alert("Error al duplicar la fase. Por favor, intenta nuevamente.");
    } finally {
        loading.value = false;
    }
};

const irAEditar = (id) => {
    window.location.href = `/fases/${id}/edit`;
};

const irAVer = (id) => {
    window.location.href = `/fases/${id}`;
};

// --- MOUNT ---
onMounted(() => {
    cargarFases();
});

const crearTareas = (id) => {
    router.get("/tareas/create", {
        fase_id: id,
    });
};
</script>

<template>
    <Head title="Fases" />

    <AppLayout>
        <TablaSeccion :data="sortedData" title="Lista de Fases">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    title="Tarea"
                    createText="Crear Nueva Fase"
                    createRoute="fases.create"
                >
                    <template #left>
                        <SearchWithReset
                            v-model="search"
                            placeholder="Buscar fase..."
                            :sort-by="sortBy"
                            :sort-direction="sortDirection"
                            @search="buscarFases"
                            @reset="resetSort"
                        />
                    </template>
                </ToolbarBase>
            </template>

            <!-- HEAD -->
            <template #head>
                <tr class="bg-gray-50">
                    <SortableHeader
                        label="ID"
                        column="id"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />

                    <SortableHeader
                        label="Nombre"
                        column="nombre"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />

                    <SortableHeader
                        label="Descripción"
                        column="descripcion"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />

                    <th
                        class="py-3 px-4 text-center border-b-2 border-gray-200"
                    >
                        <span
                            class="font-semibold text-gray-700 text-sm uppercase tracking-wider"
                            >Acciones</span
                        >
                    </th>
                </tr>
            </template>

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in sortedData"
                    :key="item.id"
                    class="border-b hover:bg-gray-50/50 transition-colors"
                >
                    <td
                        class="py-3 px-4 font-mono font-semibold text-indigo-700"
                    >
                        #{{ item.id }}
                    </td>

                    <td class="py-3 px-4 font-medium text-gray-900">
                        {{ item.nombre }}
                    </td>

                    <td class="py-3 px-4 text-gray-700">
                        <div class="line-clamp-2">
                            {{ item.descripcion ?? "Sin descripción" }}
                        </div>
                    </td>

                    <ActionButtons
                        :id="item.id"
                        edit-label="Editar"
                        delete-label="Eliminar"
                        duplicate-label="Duplicar"
                        view-label="Ver"
                        @edit="irAEditar(item.id)"
                        @delete="confirmarEliminar(item.id)"
                        @duplicate="duplicarFase(item.id)"
                        @view="irAVer(item.id)"
                    >
                        <!-- Botón personalizado usando slot -->
                        <button
                            @click="crearTareas(item.id)"
                            class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-xs font-medium shadow-sm hover:shadow"
                            title="Crear tareas para esta fase"
                        >
                            Crear tareas
                        </button>
                    </ActionButtons>
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td colspan="4" class="text-center py-8">
                        <div
                            class="flex flex-col items-center justify-center gap-3 text-gray-600"
                        >
                            <div class="relative">
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"
                                ></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700"
                                >Cargando fases...</span
                            >
                        </div>
                    </td>
                </tr>

                <!-- EMPTY -->
                <tr v-if="!loading && sortedData.length === 0">
                    <td colspan="4" class="text-center py-10">
                        <div
                            class="flex flex-col items-center gap-3 text-gray-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 opacity-75"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                />
                            </svg>
                            <div class="text-center">
                                <span class="text-lg font-medium text-gray-900"
                                    >No se encontraron fases</span
                                >
                                <p class="text-sm mt-1">
                                    No hay registros que coincidan con tu
                                    búsqueda
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <!-- PAGINACIÓN CON COMPONENTE REUTILIZABLE -->
        <Pagination :meta="fases" @page-changed="cargarFases" />

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar fase"
            message="¿Estás seguro de eliminar esta fase? Esta acción no se puede deshacer."
            :loading="deleting"
            @confirm="eliminarFase"
        />

        <SuccessModals
            v-model:show="showSuccessModal"
            title="Operación exitosa"
            message="La fase ha sido procesada correctamente."
        />
    </AppLayout>
</template>

<style scoped>
/* Efecto visual para columna activa */
th:has(.text-indigo-600) {
    @apply bg-indigo-50/50;
}

/* Transiciones suaves */
tr:hover td {
    @apply transition-colors duration-200;
}
</style>
