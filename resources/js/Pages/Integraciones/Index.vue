<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";
import axios from "axios";

import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";
import ActionModal from "@/Components/Modal/ActionModal.vue";

// --------------------
// STATE
// --------------------
const integraciones = ref({
    data: [],
    current_page: 1,
    last_page: 1,
});

const loading = ref(true);
const search = ref("");

// --------------------
// MODAL (ÚNICO)
// --------------------
const showModal = ref(false);
const modalType = ref("success"); // success | danger
const modalTitle = ref("");
const modalMessage = ref("");
const modalLoading = ref(false);
const integracionIdToDelete = ref(null);

// --------------------
// DATA
// --------------------
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
// Para información
const mostrarInfo = () => {
    modalType.value = "info"; // ℹ️ Nuevo tipo
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
                    class="bg-blue-100 text-blue-900 uppercase text-sm font-semibold"
                >
                    <th class="py-3 px-3 text-left">ID</th>
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
                    <td class="py-3 px-3 font-medium">
                        {{ item.id }}
                    </td>
                    <td class="py-3 px-3 font-medium">
                        {{ item.nombre }}
                    </td>

                    <td
                        class="py-3 px-3 max-w-[250px] truncate"
                        :title="item.descripcion"
                    >
                        {{ item.descripcion ?? "-" }}
                    </td>

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

        <!-- PAGINACIÓN -->
        <div class="flex justify-center items-center mt-4 gap-3">
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="integraciones.current_page === 1"
                @click="cargarIntegraciones(integraciones.current_page - 1)"
            >
                Anterior
            </button>

            <span>
                Página {{ integraciones.current_page }} de
                {{ integraciones.last_page }}
            </span>

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

        <!-- MODAL ÚNICO -->
        <!-- MODAL ÚNICO -->
        <ActionModal
            v-model:show="showModal"
            :type="modalType"
            :title="modalTitle"
            :message="modalMessage"
            :loading="modalLoading"
            :showCancel="modalType === 'danger'"
            :confirmText="modalType === 'danger' ? 'Sí, eliminar' : 'Aceptar'"
            :cancelText="'Cancelar'"
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
