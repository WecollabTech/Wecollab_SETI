<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";

import ConfirmDeleteModal from "@/Components/Modal/ConfirmDeleteModal.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

import { ref, onMounted } from "vue";
import axios from "axios";
import SuccessModals from "@/Components/Modal/SuccessModals.vue";

// --- STATE ---
const tareas = ref({
    data: [],
    current_page: 1,
    last_page: 1,
});

const loading = ref(true);
const search = ref("");

// --- MODALES ---
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const deleting = ref(false);
const tareaIdToDelete = ref(null);

// --- FUNCIONES ---
const cargarTareas = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/tareas?page=${page}&search=${search.value}`
        );
        tareas.value = res.data;
    } catch (error) {
        console.error("Error al cargar tareas:", error);
    } finally {
        loading.value = false;
    }
};

const buscarTareas = () => {
    cargarTareas(1);
};

// --- ELIMINAR (MODAL) ---
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
    } finally {
        deleting.value = false;
        tareaIdToDelete.value = null;
    }
};

// --- DUPLICAR ---
const duplicarTarea = async (id) => {
    try {
        await axios.post(`/api/tareas/${id}/duplicate`);
        cargarTareas(tareas.value.current_page);
        showSuccessModal.value = true;
    } catch (err) {
        console.error("Error al duplicar tarea:", err);
    }
};

// --- MOUNT ---
onMounted(() => {
    cargarTareas();
});
</script>

<template>
    <Head title="Tareas" />

    <AppLayout>
        <template #title>
            <PageHeader title="Tareas" />
        </template>

        <TablaSeccion :data="tareas.data" title="Lista de Tareas">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    createText="Nueva Tarea"
                    :createHref="route('tareas.create')"
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

            <!-- HEADER MEJORADO -->
            <template #head>
                <tr
                    class="bg-blue-100 text-blue-900 uppercase text-sm font-semibold tracking-wide"
                >
                    <th class="py-3 px-3 text-left">Título</th>
                    <th class="py-3 px-3 text-left">Descripción</th>
                    <th class="py-3 px-3 text-left">Estado</th>
                    <th class="py-3 px-3 text-left">Duración</th>
                    <th class="py-3 px-3 text-left">Activo</th>
                    <th class="py-3 px-3 text-left">Fase</th>
                    <th class="py-3 px-3 text-center">Acciones</th>
                </tr>
            </template>

            <!-- MEJORADO: Tabla de Tareas -->
            <template #body>
                <tr
                    v-for="item in tareas.data"
                    :key="item.id"
                    class="border-b hover:bg-blue-50 transition-colors"
                >
                    <td class="py-3 px-3 font-medium text-gray-800">
                        {{ item.titulo }}
                    </td>
                    <td class="py-3 px-3 text-gray-600">
                        {{ item.descripcion ?? "-" }}
                    </td>
                    <td class="py-3 px-3">
                        <span
                            :class="[
                                item.estado === 'Activo'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800',
                                'px-2 py-1 rounded-full text-sm font-semibold',
                            ]"
                        >
                            {{ item.estado }}
                        </span>
                    </td>
                    <td class="py-3 px-3 text-gray-700">
                        {{ item.duracion_minuto ?? "-" }} min
                    </td>
                    <td class="py-3 px-3">
                        <span
                            :class="[
                                item.activo == 1
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800',
                                'px-2 py-1 rounded-full text-sm font-semibold',
                            ]"
                        >
                            {{ item.activo == 1 ? "Sí" : "No" }}
                        </span>
                    </td>

                    <td class="py-3 px-3 text-gray-700">
                        {{ item.fase?.nombre ?? "-" }}
                    </td>
                    <td class="py-3 px-3 flex justify-center gap-2">
                        <button
                            @click="router.get(route('tareas.edit', item.id))"
                            class="px-3 py-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded hover:from-blue-700 hover:to-blue-800 text-sm transition"
                        >
                            Editar
                        </button>
                        <button
                            @click="confirmarEliminar(item.id)"
                            class="px-3 py-1 bg-gradient-to-r from-red-600 to-red-700 text-white rounded hover:from-red-700 hover:to-red-800 text-sm transition"
                        >
                            Eliminar
                        </button>
                        <button
                            @click="duplicarTarea(item.id)"
                            class="px-3 py-1 bg-gradient-to-r from-green-600 to-green-700 text-white rounded hover:from-green-700 hover:to-green-800 text-sm transition"
                        >
                            Duplicar
                        </button>
                        <button
                            @click="router.get(route('tareas.show', item.id))"
                            class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 text-sm transition"
                        >
                            Ver
                        </button>
                    </td>
                </tr>

                <tr v-if="loading">
                    <td
                        colspan="6"
                        class="text-center py-4 text-gray-500 font-medium"
                    >
                        Cargando...
                    </td>
                </tr>
                <tr v-if="!loading && tareas.data.length === 0">
                    <td
                        colspan="6"
                        class="text-center py-4 text-gray-500 font-medium"
                    >
                        No se encontraron registros.
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <!-- PAGINACIÓN -->
        <div class="flex justify-center items-center mt-4 gap-3">
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="tareas.current_page === 1"
                @click="cargarTareas(tareas.current_page - 1)"
            >
                Anterior
            </button>
            <span
                >Página {{ tareas.current_page }} de
                {{ tareas.last_page }}</span
            >
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="tareas.current_page === tareas.last_page"
                @click="cargarTareas(tareas.current_page + 1)"
            >
                Siguiente
            </button>
        </div>

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
    </AppLayout>
</template>
