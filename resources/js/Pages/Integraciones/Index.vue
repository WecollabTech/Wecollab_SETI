<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";

import ConfirmDeleteModal from "@/Components/Modal/ConfirmDeleteModal.vue";
import SuccessModals from "@/Components/Modal/SuccessModal.vue";

import { ref, onMounted } from "vue";
import axios from "axios";

// --- STATE ---
const integraciones = ref({
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
const integracionIdToDelete = ref(null);
const successMessage = ref(""); // mensaje dinámico del modal

// --- FUNCIONES ---
const cargarIntegraciones = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/integraciones?page=${page}&search=${search.value}`,
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

// --- ELIMINAR ---
const confirmarEliminar = (id) => {
    integracionIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarIntegracion = async () => {
    if (!integracionIdToDelete.value) return;

    deleting.value = true;
    try {
        await axios.delete(`/api/integraciones/${integracionIdToDelete.value}`);
        showDeleteModal.value = false;
        successMessage.value = "La integración fue eliminada correctamente.";
        showSuccessModal.value = true;
        cargarIntegraciones(integraciones.value.current_page);
    } catch (err) {
        console.error("Error al eliminar integración:", err);
    } finally {
        deleting.value = false;
        integracionIdToDelete.value = null;
    }
};

// --- ACCIONES ---
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
    if (!confirm("¿Deseas duplicar esta integración junto con sus tareas?"))
        return;

    try {
        const res = await axios.post(route("integraciones.duplicar", id));
        successMessage.value =
            res.data.message || "Integración duplicada correctamente.";
        showSuccessModal.value = true;
        cargarIntegraciones(integraciones.value.current_page);
    } catch (error) {
        console.error(error.response?.data || error);
        alert("Error al duplicar la integración");
    }
};

// --- MOUNT ---
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

        <TablaSeccion :data="integraciones.data" title="Lista de Integraciones">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    createText="Nueva Integración"
                    :createHref="route('integraciones.create')"
                >
                    <template #left>
                        <input
                            v-model="search"
                            @input="buscarIntegraciones"
                            type="text"
                            placeholder="Buscar integración..."
                            class="border rounded-md px-3 py-2 text-sm w-64"
                        />
                    </template>
                </ToolbarBase>
            </template>

            <!-- HEAD -->
            <template #head>
                <tr
                    class="bg-blue-100 text-blue-900 uppercase text-sm font-semibold tracking-wide"
                >
                    <th class="py-3 px-3 text-left">Nombre</th>
                    <th class="py-3 px-3 text-left">Descripción</th>
                    <th class="py-3 px-3 text-center">Acciones</th>
                </tr>
            </template>

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in integraciones.data"
                    :key="item.id"
                    class="border-b hover:bg-gray-50 transition"
                >
                    <td class="py-3 px-3 font-medium">{{ item.nombre }}</td>
                    <td class="py-3 px-3">{{ item.descripcion ?? "-" }}</td>
                    <td class="py-3 px-3 flex justify-center gap-2 flex-wrap">
                        <button
                            @click="editarIntegracion(item.id)"
                            class="px-3 py-1 bg-blue-900 text-white rounded hover:bg-blue-800 text-sm"
                        >
                            Editar
                        </button>
                        <button
                            @click="confirmarEliminar(item.id)"
                            class="px-3 py-1 bg-red-700 text-white rounded hover:bg-red-600 text-sm"
                        >
                            Eliminar
                        </button>
                        <button
                            @click="duplicarIntegracion(item.id)"
                            class="px-3 py-1 bg-green-700 text-white rounded hover:bg-green-600 text-sm"
                        >
                            Duplicar
                        </button>
                        <button
                            @click="verIntegracion(item.id)"
                            class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-600 text-sm"
                        >
                            Ver
                        </button>
                        <button
                            @click="
                                router.get(
                                    route('integraciones_tarea.index', {
                                        integracion_id: item.id,
                                    }),
                                )
                            "
                            class="px-3 py-1 bg-purple-700 text-white rounded hover:bg-purple-600 text-sm"
                        >
                            Crear tareas
                        </button>
                    </td>
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td colspan="3" class="text-center py-4">Cargando...</td>
                </tr>

                <!-- EMPTY -->
                <tr v-if="!loading && integraciones.data.length === 0">
                    <td colspan="3" class="text-center py-4">
                        No se encontraron registros.
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <!-- PAGINACION -->
        <div class="flex justify-center items-center mt-4 gap-3">
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="integraciones.current_page === 1"
                @click="cargarIntegraciones(integraciones.current_page - 1)"
            >
                Anterior
            </button>

            <span
                >Página {{ integraciones.current_page }} de
                {{ integraciones.last_page }}</span
            >

            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="
                    integraciones.current_page === integraciones.last_page
                "
                @click="cargarIntegraciones(integraciones.current_page + 1)"
            >
                Siguiente
            </button>
        </div>

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar integración"
            message="¿Estás seguro de eliminar esta integración? Esta acción no se puede deshacer."
            :loading="deleting"
            @confirm="eliminarIntegracion"
        />

        <SuccessModals
            v-model:show="showSuccessModal"
            title="Operación exitosa"
            :message="successMessage"
        />
    </AppLayout>
</template>
