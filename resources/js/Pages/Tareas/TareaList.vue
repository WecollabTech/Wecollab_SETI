<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";

import ContentModal from "@/Components/Modal/ContentModal.vue";
import ConfirmDeleteModal from "@/Components/Modal/ConfirmDeleteModal.vue";
import SuccessModals from "@/Components/Modal/SuccessModals.vue";

// Componentes reutilizables
import Pagination from "@/Components/Pagination/Pagination.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import ActionButtons from "@/Components/Table/ActionButtons.vue";

import { ref, computed, onMounted } from "vue";
import axios from "axios";

// --- MODALES ---
const showContentModal = ref(false);
const contentTitle = ref("");
const contentUrl = ref("");
const contentType = ref("text");

const showAvisoModal = ref(false);
const avisoMessage = ref("");

// --- STATE ---
const tareas = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
const loading = ref(true);
const search = ref("");
const sortBy = ref("id");
const sortDirection = ref("asc");

// --- MODALES DE ELIMINAR ---
const showDeleteModal = ref(false);
const deleting = ref(false);
const tareaIdToDelete = ref(null);
const showSuccessModal = ref(false);

// --- DATOS ORDENADOS ---
const sortedData = computed(() => {
    if (!tareas.value?.data) return [];
    return [...tareas.value.data].sort((a, b) => {
        const aValue = a[sortBy.value];
        const bValue = b[sortBy.value];
        if (aValue == null) return 1;
        if (bValue == null) return -1;
        if (typeof aValue === "string")
            return sortDirection.value === "asc"
                ? aValue.localeCompare(bValue, "es", { sensitivity: "base" })
                : bValue.localeCompare(aValue, "es", { sensitivity: "base" });
        return sortDirection.value === "asc"
            ? aValue - bValue
            : bValue - aValue;
    });
});

// --- FUNCIONES ---
const cargarTareas = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/tareas?page=${page}&search=${search.value}&sort_by=${sortBy.value}&sort_direction=${sortDirection.value}`,
        );
        tareas.value = res.data;
    } catch (error) {
        console.error("Error al cargar tareas:", error);
    } finally {
        loading.value = false;
    }
};

const buscarTareas = () => cargarTareas(1);

const changeSort = (column) => {
    if (sortBy.value === column)
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    else {
        sortBy.value = column;
        sortDirection.value = "asc";
    }
    cargarTareas(tareas.value.current_page);
};

const resetSort = () => {
    sortBy.value = "id";
    sortDirection.value = "asc";
    cargarTareas(tareas.value.current_page);
};

// --- ELIMINAR ---
const confirmarEliminar = (id) => {
    tareaIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarTarea = async () => {
    if (!tareaIdToDelete.value) return;
    deleting.value = true;
    try {
        await axios.delete(`/api/tareas/${tareaIdToDelete.value}`);
        showDeleteModal.value = false;
        showSuccessModal.value = true;
        cargarTareas(tareas.value.current_page);
    } catch (err) {
        console.error("Error al eliminar tarea:", err);
        alert("Error al eliminar la tarea.");
    } finally {
        deleting.value = false;
        tareaIdToDelete.value = null;
    }
};

// --- DUPLICAR ---
const duplicarTarea = async (id) => {
    loading.value = true;
    try {
        await axios.post(`/api/tareas/${id}/duplicate`);
        cargarTareas(tareas.value.current_page);
        showSuccessModal.value = true;
    } catch (err) {
        console.error("Error al duplicar tarea:", err);
        alert("Error al duplicar la tarea.");
    } finally {
        loading.value = false;
    }
};

// --- ABRIR CONTENIDO ---
const openContenidoModal = (item) => {
    if (!item.url_contenido) {
        avisoMessage.value = "Esta tarea no tiene contenido disponible.";
        showAvisoModal.value = true;
        return;
    }

    contentTitle.value = item.titulo;
    contentUrl.value = item.url_contenido;

    if (item.url_contenido.endsWith(".mp4")) contentType.value = "video";
    else if (item.url_contenido.endsWith(".pdf")) contentType.value = "pdf";
    else if (
        item.url_contenido.includes("youtube.com") ||
        item.url_contenido.includes("youtu.be")
    )
        contentType.value = "youtube";
    else contentType.value = "image"; // Por defecto imagen

    showContentModal.value = true;
};

// --- NAVEGACIÓN ---
const irACrear = () => router.get(route("tareas.create"));
const irAEditar = (id) => router.get(route("tareas.edit", id));
const irAVer = (id) => router.get(route("tareas.show", id));

// --- MOUNT ---
onMounted(() => {
    cargarTareas();
});
</script>

<template>
    <Head title="Tareas" />

    <AppLayout>
        <TablaSeccion :data="sortedData" title="Lista de Tareas">
            <template #toolbar>
                <ToolbarBase
                    title="Tarea"
                    createText="Crear Nueva Tarea"
                    createRoute="tareas.create"
                >
                    <template #left>
                        <input
                            v-model="search"
                            @input="buscarTareas"
                            type="text"
                            placeholder="Buscar tarea..."
                            class="border rounded-md px-3 py-2 text-sm w-64"
                        />
                    </template>
                </ToolbarBase>
            </template>

            <template #head>
                <tr class="bg-blue-50">
                    <SortableHeader
                        label="Id"
                        column="id"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />
                    <SortableHeader
                        label="Título"
                        column="titulo"
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
                    <SortableHeader
                        label="Duración"
                        column="duracion_minuto"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />
                    <SortableHeader
                        label="Activo"
                        column="activo"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />
                    <SortableHeader
                        label="Fase"
                        column="fase.nombre"
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

            <template #body>
                <tr
                    v-for="item in sortedData"
                    :key="item.id"
                    class="border-b hover:bg-blue-50/30 transition-colors"
                >
                    <td class="py-3 px-4 font-medium text-gray-800">
                        {{ item.id }}
                    </td>
                    <td class="py-3 px-4 font-medium text-gray-800">
                        {{ item.titulo }}
                    </td>
                    <td class="py-3 px-4 text-gray-600">
                        <div class="line-clamp-2">
                            {{ item.descripcion ?? "Sin descripción" }}
                        </div>
                    </td>
                    <td class="py-3 px-4 text-gray-700 font-medium">
                        {{ item.duracion_minuto ?? "-" }} min
                    </td>
                    <td class="py-3 px-4">
                        <span
                            :class="[
                                item.activo == 1
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800',
                                'px-3 py-1 rounded-full text-xs font-semibold',
                            ]"
                        >
                            {{ item.activo == 1 ? "Sí" : "No" }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-gray-700">
                        {{ item.fase?.nombre ?? "-" }}
                    </td>

                    <ActionButtons
                        :id="item.id"
                        :show-duplicate="false"
                        :show-view-content="true"
                        edit-label="Editar"
                        delete-label="Eliminar"
                        view-label="Ver detalle"
                        view-content-label="Ver contenido"
                        @edit="irAEditar(item.id)"
                        @delete="confirmarEliminar(item.id)"
                        @viewDetail="irAVer(item.id)"
                        @viewContent="() => openContenidoModal(item)"
                    />
                </tr>

                <tr v-if="loading">
                    <td colspan="6" class="text-center py-8">
                        Cargando tareas...
                    </td>
                </tr>
                <tr v-if="!loading && sortedData.length === 0">
                    <td colspan="6" class="text-center py-10">
                        No se encontraron tareas
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <Pagination :meta="tareas" @page-changed="cargarTareas" />

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar tarea"
            message="¿Estás seguro de eliminar esta tarea? Esta acción no se puede deshacer."
            :loading="deleting"
            @confirm="eliminarTarea"
        />

        <SuccessModals
            v-model:show="showSuccessModal"
            title="Operación exitosa"
            message="La acción se realizó correctamente."
        />

        <!-- Aviso solo cuando NO hay contenido -->
        <SuccessModals
            v-model:show="showAvisoModal"
            title="Aviso"
            :message="avisoMessage"
        />

        <!-- Modal de contenido solo si hay contenido -->
        <ContentModal
            v-model:show="showContentModal"
            :title="contentTitle"
            :content="contentUrl"
            :type="contentType"
        />
    </AppLayout>
</template>
