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

// --- STATE ---
const tipos = ref({
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
const tipoIdToDelete = ref(null);

// --- FUNCIONES ---
const cargarTipos = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/tipoimplementacion?page=${page}&search=${search.value}`,
        );
        tipos.value = res.data;
    } catch (error) {
        console.error("Error al cargar tipos:", error);
    } finally {
        loading.value = false;
    }
};

const buscarTipos = () => cargarTipos(1);

const confirmarEliminar = (id) => {
    tipoIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarTipo = async () => {
    if (!tipoIdToDelete.value) return;
    deleting.value = true;
    try {
        await axios.delete(`/api/tipoimplementacion/${tipoIdToDelete.value}`);
        showDeleteModal.value = false;
        showSuccessModal.value = true;
        cargarTipos(tipos.value.current_page);
    } catch (err) {
        console.error("Error al eliminar tipo:", err);
    } finally {
        deleting.value = false;
        tipoIdToDelete.value = null;
    }
};

// --- MOUNT ---
onMounted(() => cargarTipos());
</script>

<template>
    <Head title="Tipos de Implementación" />

    <AppLayout>
        <template #title>
            <PageHeader title="Tipos de Implementación" />
        </template>

        <TablaSeccion
            :data="tipos.data"
            title="Lista de Tipos de Implementación"
        >
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    createText="Nuevo Tipo de Implementación"
                    :createHref="route('tipoimplementacion.create')"
                >
                    <template #left>
                        <input
                            v-model="search"
                            @input="buscarTipos"
                            type="text"
                            placeholder="Buscar tipo..."
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
                    <th class="py-3 px-3 text-left">Alcance</th>
                    <th class="py-3 px-3 text-center">Estado</th>
                    <th class="py-3 px-3 text-center">Acciones</th>
                </tr>
            </template>

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in tipos.data"
                    :key="item.id"
                    class="border-b hover:bg-blue-50 transition-colors"
                >
                    <td class="py-3 px-3 font-medium text-gray-800">
                        {{ item.nombre }}
                    </td>
                    <td class="py-3 px-3 text-gray-600">
                        {{ item.alcance ?? "-" }}
                    </td>
                    <td class="py-3 px-3 text-center">
                        <span
                            :class="[
                                item.estado
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800',
                                'px-2 py-1 rounded-full text-sm font-semibold',
                            ]"
                        >
                            {{ item.estado ? "Activo" : "Inactivo" }}
                        </span>
                    </td>
                    <td class="py-3 px-3 flex justify-center gap-2">
                        <button
                            @click="
                                router.get(
                                    route('tipoimplementacion.edit', item.id),
                                )
                            "
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
                            @click="
                                router.get(
                                    route('tipoimplementacion.show', item.id),
                                )
                            "
                            class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-800 text-sm transition"
                        >
                            Ver
                        </button>
                    </td>
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td
                        colspan="4"
                        class="text-center py-4 text-gray-500 font-medium"
                    >
                        Cargando...
                    </td>
                </tr>

                <!-- SIN DATOS -->
                <tr v-if="!loading && tipos.data.length === 0">
                    <td
                        colspan="4"
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
                :disabled="tipos.current_page === 1"
                @click="cargarTipos(tipos.current_page - 1)"
            >
                Anterior
            </button>

            <span
                >Página {{ tipos.current_page }} de {{ tipos.last_page }}</span
            >

            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="tipos.current_page === tipos.last_page"
                @click="cargarTipos(tipos.current_page + 1)"
            >
                Siguiente
            </button>
        </div>

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar tipo de implementación"
            message="¿Estás seguro de eliminar este tipo? Esta acción no se puede deshacer."
            :loading="deleting"
            @confirm="eliminarTipo"
        />
        <SuccessModals
            v-model:show="showSuccessModal"
            title="Operación exitosa"
            message="La acción se realizó correctamente."
        />
    </AppLayout>
</template>
