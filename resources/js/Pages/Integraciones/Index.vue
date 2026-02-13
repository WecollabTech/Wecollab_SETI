<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, computed, onMounted } from "vue";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";
import ActionModal from "@/Components/Modal/ActionModal.vue";

// Componentes reutilizables
import Pagination from "@/Components/Pagination/Pagination.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import ActionButtons from "@/Components/Table/ActionButtons.vue";
import SearchWithReset from "@/Components/Formulario/SearchWithReset.vue";

// --------------------
// STATE
// --------------------
const integraciones = ref({
    data: [],
    current_page: 1,
    last_page: 1,
    total: 0,
});

const loading = ref(true);
const search = ref("");
const sortBy = ref("id"); // Columna por defecto
const sortDirection = ref("asc"); // Orden ascendente por defecto

// --------------------
// MODAL (ÚNICO)
// --------------------
const showModal = ref(false);
const modalType = ref("success"); // success | danger | info
const modalTitle = ref("");
const modalMessage = ref("");
const modalLoading = ref(false);
const integracionIdToDelete = ref(null);

// --------------------
// DATOS ORDENADOS (Frontend)
// --------------------
const sortedData = computed(() => {
    if (!integraciones.value?.data) return [];

    return [...integraciones.value.data].sort((a, b) => {
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

// --------------------
// DATA
// --------------------
const cargarIntegraciones = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/integraciones?page=${page}&search=${search.value}&sort_by=${sortBy.value}&sort_direction=${sortDirection.value}`,
        );
        integraciones.value = res.data;
    } catch (error) {
        console.error("Error al cargar integraciones:", error);
    } finally {
        loading.value = false;
    }
};

const buscarIntegraciones = () => {
    cargarIntegraciones(1);
};

// --------------------
// ORDENAMIENTO
// --------------------
const changeSort = (column) => {
    if (sortBy.value === column) {
        // Si ya está ordenado por esta columna, invertir dirección
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        // Nueva columna, orden ascendente por defecto
        sortBy.value = column;
        sortDirection.value = "asc";
    }
    cargarIntegraciones(integraciones.value.current_page);
};

const resetSort = () => {
    sortBy.value = "id";
    sortDirection.value = "asc";
    cargarIntegraciones(integraciones.value.current_page);
};

// --------------------
// ELIMINAR
// --------------------
const confirmarEliminar = (id) => {
    integracionIdToDelete.value = id;

    modalType.value = "danger";
    modalTitle.value = "Eliminar integración";
    modalMessage.value =
        "¿Estás seguro de eliminar esta integración? Esta acción no se puede deshacer.";

    showModal.value = true;
};

const eliminarIntegracion = async () => {
    if (!integracionIdToDelete.value) return;

    modalLoading.value = true;

    try {
        await axios.delete(`/api/integraciones/${integracionIdToDelete.value}`);

        // Cerrar el modal de confirmación
        showModal.value = false;

        // Mostrar mensaje de éxito después de un breve delay
        setTimeout(() => {
            modalType.value = "success";
            modalTitle.value = "¡Eliminado!";
            modalMessage.value = "La integración fue eliminada correctamente.";
            showModal.value = true;

            // Auto-cerrar después de 2 segundos
            setTimeout(() => {
                showModal.value = false;
            }, 2000);
        }, 300);

        cargarIntegraciones(integraciones.value.current_page);
    } catch (err) {
        console.error("Error al eliminar integración:", err);

        // Mostrar error en el modal
        showModal.value = false;
        setTimeout(() => {
            modalType.value = "danger";
            modalTitle.value = "Error";
            modalMessage.value =
                "No se pudo eliminar la integración. Por favor, intenta nuevamente.";
            showModal.value = true;
        }, 300);
    } finally {
        modalLoading.value = false;
        integracionIdToDelete.value = null;
    }
};

// --------------------
// ACCIONES
// --------------------
const verIntegracion = (id) => {
    router.get(route("integraciones.show", id));
};

const editarIntegracion = (id) => {
    router.get(route("integraciones.edit", id));
};

const cambiarEstado = async (item) => {
    try {
        await axios.patch(`/api/integraciones/${item.id}/estado`, {
            activo: !item.activo,
        });
        cargarIntegraciones(integraciones.value.current_page);
    } catch (err) {
        console.error("Error al cambiar estado:", err);
    }
};

const duplicarIntegracion = async (id) => {
    try {
        const res = await axios.post(route("integraciones.duplicar", id));

        modalType.value = "success";
        modalTitle.value = "Integración duplicada";
        modalMessage.value =
            res.data.message || "Integración duplicada correctamente.";

        showModal.value = true;
        cargarIntegraciones(integraciones.value.current_page);
    } catch (error) {
        console.error(error);
    }
};

const crearTareas = (id) => {
    router.get(route("integraciones_tarea.index", { integracion_id: id }));
};

// Para información
const mostrarInfo = () => {
    modalType.value = "info";
    modalTitle.value = "Información";
    modalMessage.value = "Esta acción no se puede deshacer.";
    showModal.value = true;
};

// --------------------
// MOUNT
// --------------------
onMounted(() => {
    cargarIntegraciones();
});
</script>

<template>
    <Head title="Integraciones" />

    <AppLayout>
        <template #title>
            <PageHeader title="Integraciones" />
        </template>

        <TablaSeccion :data="sortedData" title="Lista de Integraciones">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    createText="Nueva Integración"
                    :createHref="route('integraciones.create')"
                >
                    <template #left>
                        <SearchWithReset
                            v-model="search"
                            placeholder="Buscar integración..."
                            :sort-by="sortBy"
                            :sort-direction="sortDirection"
                            @search="buscarIntegraciones"
                            @reset="resetSort"
                        />
                    </template>
                </ToolbarBase>
            </template>

            <!-- HEAD -->
            <template #head>
                <tr class="bg-blue-50">
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
                        class="py-3 px-4 text-center border-b-2 border-blue-200"
                    >
                        <span
                            class="font-semibold text-blue-900 text-sm uppercase tracking-wider"
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
                    class="border-b hover:bg-blue-50/30 transition-colors"
                >
                    <td class="py-3 px-4 font-mono font-semibold text-blue-700">
                        #{{ item.id }}
                    </td>

                    <td class="py-3 px-4 font-medium text-gray-900">
                        {{ item.nombre }}
                    </td>

                    <td class="py-3 px-4 text-gray-700">
                        <div
                            class="line-clamp-2 max-w-[300px]"
                            :title="item.descripcion"
                        >
                            {{ item.descripcion ?? "Sin descripción" }}
                        </div>
                    </td>

                    <td class="py-3 px-3">
                        <div class="flex flex-wrap justify-center gap-2">
                            <!-- Botones estándar -->
                            <ActionButtons
                                :id="item.id"
                                edit-label="Editar"
                                delete-label="Eliminar"
                                duplicate-label="Duplicar"
                                view-label="Ver"
                                @edit="editarIntegracion(item.id)"
                                @delete="confirmarEliminar(item.id)"
                                @duplicate="duplicarIntegracion(item.id)"
                                @view="verIntegracion(item.id)"
                            >
                                <!-- Botón personalizado usando slot -->
                                <button
                                    @click="crearTareas(item.id)"
                                    class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-xs font-medium shadow-sm hover:shadow"
                                    title="Crear tareas"
                                >
                                    Crear tareas
                                </button>
                            </ActionButtons>

                            <!-- Botón adicional específico de integraciones -->
                            <!-- <button
                                @click="crearTareas(item.id)"
                                class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition text-xs font-medium shadow-sm hover:shadow"
                                title="Crear tareas"
                            >
                                Crear tareas
                            </button> -->
                        </div>
                    </td>
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td colspan="4" class="text-center py-8">
                        <div
                            class="flex flex-col items-center justify-center gap-3 text-gray-600"
                        >
                            <div class="relative">
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"
                                ></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700"
                                >Cargando integraciones...</span
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
                                    >No se encontraron integraciones</span
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

        <!-- PAGINACIÓN MEJORADA -->
        <Pagination :meta="integraciones" @page-changed="cargarIntegraciones" />

        <!-- MODAL ÚNICO -->
        <ActionModal
            v-model:show="showModal"
            :type="modalType"
            :title="modalTitle"
            :message="modalMessage"
            :loading="modalLoading"
            :show-cancel="modalType === 'danger'"
            :confirm-text="modalType === 'danger' ? 'Sí, eliminar' : 'Aceptar'"
            :cancel-text="'Cancelar'"
            @confirm="
                modalType === 'danger'
                    ? eliminarIntegracion()
                    : (showModal = false)
            "
            @update:show="
                (value) => {
                    if (!value && modalLoading) {
                        // Si intentan cerrar mientras carga, prevenir
                        return;
                    }
                    showModal = value;
                }
            "
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
