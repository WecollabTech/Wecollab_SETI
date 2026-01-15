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
const fases = ref({
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
const faseIdToDelete = ref(null);

// --- FUNCIONES ---
const cargarFases = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/fases?page=${page}&search=${search.value}`
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
    } finally {
        deleting.value = false;
        faseIdToDelete.value = null;
    }
};

// --- DUPLICAR ---
const duplicarFase = async (id) => {
    try {
        await axios.post(`/api/fases/${id}/duplicate`);
        cargarFases(fases.value.current_page);
        showSuccessModal.value = true;
    } catch (err) {
        console.error("Error al duplicar fase:", err);
    }
};

// --- MOUNT ---
onMounted(() => {
    cargarFases();
});
</script>

<template>
    <Head title="Fases" />

    <AppLayout>
        <template #title>
            <PageHeader title="Fases" />
        </template>

        <TablaSeccion :data="fases.data" title="Lista de Fases">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    createText="Nueva Fase"
                    :createHref="route('fases.create')"
                >
                    <template #left>
                        <input
                            v-model="search"
                            @input="buscarFases"
                            type="text"
                            placeholder="Buscar fase..."
                            class="border rounded-md px-3 py-2 text-sm w-64"
                        />
                    </template>
                </ToolbarBase>
            </template>

            <!-- HEAD -->
            <template #head>
                <tr>
                    <th class="py-3 px-3 text-left">Nombre</th>
                    <th class="py-3 px-3 text-left">Descripción</th>
                    <th class="py-3 px-3 text-left">Minutos Base</th>
                    <th class="py-3 px-3 text-center">Acciones</th>
                </tr>
            </template>

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in fases.data"
                    :key="item.id"
                    class="border-b hover:bg-gray-50 transition"
                >
                    <td class="py-3 px-3 font-medium">
                        {{ item.nombre }}
                    </td>

                    <td class="py-3 px-3">
                        {{ item.descripcion ?? "-" }}
                    </td>

                    <td class="py-3 px-3">
                        {{ item.minutos_base }}
                    </td>

                    <td class="py-3 px-3 flex justify-center gap-2">
                        <button
                            @click="router.get(route('fases.edit', item.id))"
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
                            @click="duplicarFase(item.id)"
                            class="px-3 py-1 bg-green-700 text-white rounded hover:bg-green-600 text-sm"
                        >
                            Duplicar
                        </button>

                        <button
                            @click="router.get(route('fases.show', item.id))"
                            class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-600 text-sm"
                        >
                            Ver
                        </button>
                    </td>
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td colspan="4" class="text-center py-4">Cargando...</td>
                </tr>

                <!-- EMPTY -->
                <tr v-if="!loading && fases.data.length === 0">
                    <td colspan="4" class="text-center py-4">
                        No se encontraron registros.
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <!-- PAGINACION -->
        <div class="flex justify-center items-center mt-4 gap-3">
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="fases.current_page === 1"
                @click="cargarFases(fases.current_page - 1)"
            >
                Anterior
            </button>

            <span>
                Página {{ fases.current_page }} de {{ fases.last_page }}
            </span>

            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="fases.current_page === fases.last_page"
                @click="cargarFases(fases.current_page + 1)"
            >
                Siguiente
            </button>
        </div>

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
            message="La acción se realizó correctamente."
        />
    </AppLayout>
</template>
