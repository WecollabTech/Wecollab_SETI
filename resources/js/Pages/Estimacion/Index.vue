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
import ConfirmaciondeBitrix from "@/Components/Modal/ConfirmaciondeBitrix.vue";

// --- STATE ---
const estimaciones = ref({
    data: [],
    current_page: 1,
    last_page: 1,
});

const loading = ref(true);
const search = ref("");

// --- CONFIRMAR INICIO DE PROYECTO ---
const showConfirmProjectModal = ref(false);
const estimacionIdToStart = ref(null);

// --- MODALES ---
const showDeleteModal = ref(false);
const showSuccessModal = ref(false);
const deleting = ref(false);
const estimacionIdToDelete = ref(null);

// 🔥 MENSAJES DINÁMICOS
const successTitle = ref("");
const successMessage = ref("");

// --- BITRIX ---
const creandoProyecto = ref(null);

//funcion de confirmar inico de proyecto
const confirmarIniciarProyecto = (id) => {
    estimacionIdToStart.value = id;
    showConfirmProjectModal.value = true;
};

const iniciarProyectoConfirmado = () => {
    iniciarProyectoBitrix(estimacionIdToStart.value);
    estimacionIdToStart.value = null;
};

const iniciarProyectoBitrix = async (estimacionId) => {
    if (creandoProyecto.value === estimacionId) return;

    creandoProyecto.value = estimacionId;

    try {
        // 1️⃣ Obtener estimación
        const { data: estimacion } = await axios.get(
            `/api/estimaciones/${estimacionId}`,
        );

        // 2️⃣ Crear grupo en Bitrix
        const grupoResponse = await fetch(
            "https://wecollab.bitrix24.mx/rest/281/s5qyzs1hdkmr09kz/sonet_group.create",
            {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    NAME:
                        estimacion.nombre_empresa ??
                        `Proyecto Estimación #${estimacion.id}`,
                    DESCRIPTION: estimacion.comentarios ?? "",
                    VISIBLE: "Y",
                    OPENED: "Y",
                }),
            },
        );

        const grupoData = await grupoResponse.json();

        if (!grupoResponse.ok || grupoData.error) {
            throw new Error(grupoData.error_description);
        }

        const groupId = grupoData.result;

        // 3️⃣ Unir tareas
        const tareas = [
            ...(estimacion.fases || []).flatMap((f) => f.tareas),
            ...(estimacion.integraciones || []).flatMap((i) => i.tareas),
        ];

        // 4️⃣ Crear tareas en Bitrix
        for (const tarea of tareas) {
            await fetch(
                "https://wecollab.bitrix24.mx/rest/281/s5qyzs1hdkmr09kz/tasks.task.add",
                {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        fields: {
                            TITLE: tarea.nombre_tarea,
                            DESCRIPTION: `Duración: ${tarea.duracion_minuto} min`,
                            GROUP_ID: groupId,
                            RESPONSIBLE_ID: 1,
                        },
                    }),
                },
            );
        }

        // ✅ MODAL ÉXITO
        successTitle.value = "Proyecto creado";
        successMessage.value =
            "El proyecto y sus tareas fueron creados correctamente en Bitrix24.";
        showSuccessModal.value = true;
    } catch (error) {
        console.error(error);

        // ❌ MODAL ERROR (reutilizamos el mismo)
        successTitle.value = "Error";
        successMessage.value =
            "Ocurrió un error al crear el proyecto en Bitrix.";
        showSuccessModal.value = true;
    } finally {
        creandoProyecto.value = null;
    }
};

// --- PDF ---
const abrirPdf = (id) => {
    window.open(route("estimaciones.pdf", id), "_blank");
};

// --- LISTADO ---
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

        successTitle.value = "Estimación eliminada";
        successMessage.value = "La estimación se eliminó correctamente.";
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

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in estimaciones.data"
                    :key="item.id"
                    class="border-b hover:bg-indigo-50 transition"
                >
                    <td class="py-3 px-3">{{ item.id }}</td>
                    <td class="py-3 px-3">
                        {{ item.nombre_tipo_implementacion }}
                    </td>
                    <td class="py-3 px-3">{{ item.nombre_empresa }}</td>
                    <td class="py-3 px-3">
                        {{ item.complejidad?.nombre ?? "-" }}
                    </td>
                    <td class="py-3 px-3">{{ item.total_horas }} h</td>
                    <td class="py-3 px-3">
                        {{ new Date(item.created_at).toLocaleString("es-MX") }}
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
    </AppLayout>
</template>
