<script setup>
import { Head, router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import TablaSeccion from "@/Components/Layout/TablaSeccion.vue";
import ToolbarBase from "@/Components/Layout/ToolbarBase.vue";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

import ConfirmDeleteModal from "@/Components/Modal/ConfirmDeleteModal.vue";
import SuccessModals from "@/Components/Modal/SuccessModals.vue";

import { ref, onMounted } from "vue";
import axios from "axios";

// --- STATE ---
const estimaciones = ref({
    data: [],
    current_page: 1,
    last_page: 1,
});

//Funcion para el PDF de la estimacio
const abrirPdf = (id) => {
    window.open(route("estimaciones.pdf", id), "_blank");
};

const loading = ref(true);
const search = ref("");

// --- MODALES ---
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const deleting = ref(false);
const estimacionIdToDelete = ref(null);

// --- FUNCIONES ---
const cargarEstimaciones = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/estimaciones?page=${page}&search=${search.value}`,
        );
        estimaciones.value = res.data;
    } catch (error) {
        console.error("Error al cargar estimaciones:", error);
    } finally {
        loading.value = false;
    }
};

const buscarEstimaciones = () => {
    cargarEstimaciones(1);
};

// --- ELIMINAR ---
const confirmarEliminar = (id) => {
    estimacionIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarEstimacion = async () => {
    if (!estimacionIdToDelete.value) return;

    deleting.value = true;
    try {
        await axios.delete(`/api/estimaciones/${estimacionIdToDelete.value}`);
        showDeleteModal.value = false;
        showSuccessModal.value = true;
        cargarEstimaciones(estimaciones.value.current_page);
    } catch (err) {
        console.error("Error al eliminar estimación:", err);
    } finally {
        deleting.value = false;
        estimacionIdToDelete.value = null;
    }
};

// --- MOUNT ---
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
                <ToolbarBase>
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

            <!-- HEADER -->
            <template #head>
                <tr
                    class="bg-indigo-100 text-indigo-900 uppercase text-sm font-semibold"
                >
                    <th class="py-3 px-3 text-left">ID</th>
                    <th class="py-3 px-3 text-left">Tipo Implementación</th>
                    <th class="py-3 px-3 text-left">Empresa</th>
                    <th class="py-3 px-3 text-left">Complejidad</th>
                    <th class="py-3 px-3 text-left">Total Horas</th>
                    <!-- <th class="py-3 px-3 text-left">Total Minutos</th> -->
                    <th class="py-3 px-3 text-left">Fecha</th>
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
                    <td class="py-3 px-3 font-medium">
                        {{ item.id ?? item.id?.id }}
                    </td>

                    <td class="py-3 px-3 font-medium">
                        {{
                            item.nombre_tipo_implementacion ??
                            item.tipo_implementacion?.nombre
                        }}
                    </td>

                    <td class="py-3 px-3 font-medium">
                        {{
                            item.nombre_empresa ??
                            item.nombre_empresa?.nombre_empresa
                        }}
                    </td>

                    <td class="py-3 px-3">
                        {{ item.complejidad?.nombre ?? "-" }}
                    </td>

                    <td class="py-3 px-3 text-gray-700">
                        {{ item.total_horas }} h
                    </td>

                    <!-- <td class="py-3 px-3 text-gray-700">
                        {{ item.total_minutos }} min
                    </td> -->

                    <td class="py-3 px-3 text-gray-600">
                        {{ new Date(item.created_at).toLocaleString("es-MX") }}
                    </td>

                    <td class="py-3 px-3 flex justify-center gap-2">
                        <button
                            @click="
                                router.get(route('estimaciones.show', item.id))
                            "
                            class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 text-sm"
                        >
                            Ver
                        </button>

                        <!-- NUEVO BOTÓN PDF -->
                        <button
                            @click="abrirPdf(item)"
                            class="px-3 py-1 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm"
                        >
                            📄 PDF
                        </button>

                        <button
                            @click="confirmarEliminar(item.id)"
                            class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-sm"
                        >
                            Eliminar
                        </button>
                    </td>
                </tr>

                <tr v-if="loading">
                    <td colspan="6" class="text-center py-4 text-gray-500">
                        Cargando...
                    </td>
                </tr>

                <tr v-if="!loading && estimaciones.data.length === 0">
                    <td colspan="6" class="text-center py-4 text-gray-500">
                        No hay estimaciones registradas.
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <!-- PAGINACIÓN -->
        <div class="flex justify-center items-center mt-4 gap-3">
            <button
                class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                :disabled="estimaciones.current_page === 1"
                @click="cargarEstimaciones(estimaciones.current_page - 1)"
            >
                Anterior
            </button>

            <span>
                Página {{ estimaciones.current_page }} de
                {{ estimaciones.last_page }}
            </span>

            <button
                class="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
                :disabled="estimaciones.current_page === estimaciones.last_page"
                @click="cargarEstimaciones(estimaciones.current_page + 1)"
            >
                Siguiente
            </button>
        </div>

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar estimación"
            message="¿Estás seguro de eliminar esta estimación? Se eliminarán fases, integraciones y tareas."
            :loading="deleting"
            @confirm="eliminarEstimacion"
        />

        <SuccessModals
            v-model:show="showSuccessModal"
            title="Operación exitosa"
            message="La estimación se eliminó correctamente."
        />
    </AppLayout>
</template>
