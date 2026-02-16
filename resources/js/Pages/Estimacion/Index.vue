<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";
import ConfirmDeleteModal from "@/Components/Modal/ConfirmDeleteModal.vue";
import SuccessModals from "@/Components/Modal/SuccessModals.vue";

import { ref, onMounted } from "vue";
import axios from "axios";

// =========================
// STATE
// =========================
const estimaciones = ref({
    data: [],
    current_page: 1,
    last_page: 1,
});

const loading = ref(true);
const search = ref("");

// =========================
// MODALES
// =========================
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const showProjectTypeModal = ref(false);

const deleting = ref(false);
const estimacionIdToDelete = ref(null);
const estimacionIdToStart = ref(null);

const successTitle = ref("");
const successMessage = ref("");

// =========================
// TIPO PROYECTO
// =========================
const tipoProyecto = ref("normal"); // normal | scrum
const creandoProyecto = ref(null);

const isCreatingProject = (id) => {
    return creandoProyecto.value === id;
};

// =========================
// LISTAR
// =========================
const cargarEstimaciones = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/estimaciones?page=${page}&search=${search.value}`,
        );
        estimaciones.value = res.data;
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};

let searchTimeout = null;
const buscarEstimaciones = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        cargarEstimaciones(1);
    }, 400);
};

// =========================
// CREAR PROYECTO
// =========================
const confirmarIniciarProyecto = (id) => {
    estimacionIdToStart.value = id;
    tipoProyecto.value = "normal";
    showProjectTypeModal.value = true;
};

const iniciarProyectoConfirmado = async () => {
    if (!estimacionIdToStart.value) return;

    const id = estimacionIdToStart.value;

    creandoProyecto.value = id;
    showProjectTypeModal.value = false;

    try {
        await axios.post(`/api/estimaciones/${id}/crear-proyecto`, {
            tipo_proyecto: tipoProyecto.value,
        });

        successTitle.value = "Proyecto creado";
        successMessage.value =
            tipoProyecto.value === "scrum"
                ? "Proyecto Scrum creado correctamente."
                : "Proyecto normal creado correctamente.";

        showSuccessModal.value = true;

        cargarEstimaciones();
    } catch (error) {
        successTitle.value = "Error";
        successMessage.value =
            error.response?.data?.message ?? "Error al crear proyecto.";
        showSuccessModal.value = true;
    } finally {
        creandoProyecto.value = null;
        estimacionIdToStart.value = null;
    }
};

// =========================
// ELIMINAR
// =========================
const confirmarEliminar = (id) => {
    estimacionIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarEstimacion = async () => {
    deleting.value = true;

    try {
        await axios.delete(`/api/estimaciones/${estimacionIdToDelete.value}`);
        showDeleteModal.value = false;

        successTitle.value = "Eliminado";
        successMessage.value = "Estimación eliminada correctamente.";
        showSuccessModal.value = true;

        cargarEstimaciones(estimaciones.value.current_page);
    } finally {
        deleting.value = false;
        estimacionIdToDelete.value = null;
    }
};

onMounted(() => {
    cargarEstimaciones();
});
</script>

<template>
    <Head title="Estimaciones" />

    <AppLayout>
        <template #title>
            <PageHeader title="Estimaciones Realizadas" />
        </template>

        <TablaSeccion :data="estimaciones.data" title="Lista de Estimaciones">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    title="Estimaciones"
                    createText="Crear Nueva Estimacion"
                    createRoute="estimacion"
                >
                    <template #left>
                        <input
                            v-model="search"
                            @input="buscarEstimaciones"
                            type="text"
                            placeholder="Buscar estimación..."
                            class="border rounded-md px-3 py-2 text-sm w-64"
                        />
                    </template>
                </ToolbarBase>
            </template>
            <template #head>
                <tr class="bg-gray-200 text-gray-800 text-sm uppercase">
                    <th class="py-3 px-3 text-left">ID</th>
                    <th class="py-3 px-3 text-center">Id de la Negociación</th>
                    <th class="py-3 px-3 text-left">Tipo Implementación</th>
                    <th class="py-3 px-3 text-left">Empresa</th>

                    <th class="py-3 px-3 text-left">Complejidad</th>
                    <th class="py-3 px-3 text-left">Horas</th>
                    <th class="py-3 px-3 text-center">Fecha</th>
                    <th class="py-3 px-3 text-center">Acciones</th>
                </tr>
            </template>

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in estimaciones.data"
                    :key="item.id"
                    class="border-b hover:bg-indigo-50 transition"
                >
                    <td class="py-3 px-3">{{ item.id }}</td>
                    <td class="py-3 px-3 text-center">{{ item.id_negocio }}</td>
                    <td class="py-3 px-3">
                        {{ item.nombre_tipo_implementacion }}
                    </td>
                    <td class="py-3 px-3">{{ item.nombre_empresa }}</td>

                    <td class="py-3 px-3">
                        {{ item.complejidad?.nombre ?? "-" }}
                    </td>
                    <td class="py-3 px-3">{{ item.total_horas }} h</td>
                    <td class="py-3 px-3 text-center">
                        {{
                            new Date(item.created_at).toLocaleDateString(
                                "es-MX",
                            )
                        }}
                    </td>

                    <td class="py-3 px-3 flex gap-2 justify-center">
                        <button
                            @click="
                                router.get(route('estimaciones.show', item.id))
                            "
                            class="px-3 py-1 bg-gray-700 text-white rounded"
                        >
                            Ver
                        </button>

                        <button
                            @click="abrirPdf(item.id)"
                            class="px-3 py-1 bg-indigo-600 text-white rounded"
                        >
                            📄 PDF
                        </button>

                        <button
                            @click="confirmarIniciarProyecto(item.id)"
                            class="px-3 py-1 bg-green-600 text-white rounded"
                        >
                            <span v-if="creandoProyecto === item.id">
                                ⏳ Creando...
                            </span>
                            <span v-else>🚀 Iniciar proyecto</span>
                        </button>

                        <button
                            @click="confirmarEliminar(item.id)"
                            class="px-3 py-1 bg-red-600 text-white rounded"
                        >
                            Eliminar
                        </button>
                    </td>
                </tr>
            </template>
        </TablaSeccion>
        <!-- PAGINACIÓN -->
        <div
            v-if="estimaciones.last_page > 1"
            class="flex justify-center items-center gap-2 mt-6"
        >
            <!-- ANTERIOR -->
            <button
                class="px-3 py-1 rounded border text-sm"
                :class="
                    estimaciones.current_page === 1
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                        : 'bg-white hover:bg-gray-100'
                "
                :disabled="estimaciones.current_page === 1"
                @click="cargarEstimaciones(estimaciones.current_page - 1)"
            >
                ← Anterior
            </button>

            <!-- NÚMEROS -->
            <button
                v-for="page in estimaciones.last_page"
                :key="page"
                @click="cargarEstimaciones(page)"
                class="px-3 py-1 rounded border text-sm transition"
                :class="
                    page === estimaciones.current_page
                        ? 'bg-indigo-600 text-white border-indigo-600'
                        : 'bg-white hover:bg-gray-100'
                "
            >
                {{ page }}
            </button>

            <!-- SIGUIENTE -->
            <button
                class="px-3 py-1 rounded border text-sm"
                :class="
                    estimaciones.current_page === estimaciones.last_page
                        ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                        : 'bg-white hover:bg-gray-100'
                "
                :disabled="estimaciones.current_page === estimaciones.last_page"
                @click="cargarEstimaciones(estimaciones.current_page + 1)"
            >
                Siguiente →
            </button>
        </div>

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar estimación"
            message="¿Estás seguro de eliminar esta estimación?"
            :loading="deleting"
            @confirm="eliminarEstimacion"
        />

        <SuccessModals
            v-model:show="showSuccessModal"
            :title="successTitle"
            :message="successMessage"
        />

        <ConfirmaciondeBitrix
            v-model:show="showConfirmProjectModal"
            mode="confirm"
            title="Iniciar proyecto"
            message="¿Estás seguro de iniciar este proyecto? Se crearán el grupo y todas las tareas en Bitrix24."
            @confirm="iniciarProyectoConfirmado"
        />

        <!-- MODAL SELECCION TIPO PROYECTO -->
        <div
            v-if="showProjectTypeModal"
            class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
        >
            <div class="bg-white rounded-xl shadow-xl p-6 w-96">
                <h2 class="text-lg font-semibold mb-4">
                    Seleccionar tipo de proyecto
                </h2>

                <div class="flex gap-4 mb-6">
                    <button
                        @click="tipoProyecto = 'normal'"
                        :class="
                            tipoProyecto === 'normal'
                                ? 'bg-indigo-600 text-white'
                                : 'bg-gray-200'
                        "
                        class="flex-1 py-2 rounded transition"
                    >
                        Proyecto Normal
                    </button>

                    <button
                        @click="tipoProyecto = 'scrum'"
                        :class="
                            tipoProyecto === 'scrum'
                                ? 'bg-green-600 text-white'
                                : 'bg-gray-200'
                        "
                        class="flex-1 py-2 rounded transition"
                    >
                        Proyecto Scrum
                    </button>
                </div>

                <div class="flex justify-end gap-2">
                    <button
                        @click="showProjectTypeModal = false"
                        class="px-4 py-2 bg-gray-300 rounded"
                    >
                        Cancelar
                    </button>

                    <button
                        @click="iniciarProyectoConfirmado"
                        class="px-4 py-2 bg-indigo-600 text-white rounded"
                    >
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
