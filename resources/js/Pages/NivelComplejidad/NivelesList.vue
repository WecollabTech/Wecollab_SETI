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
const niveles = ref({
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
const nivelIdToDelete = ref(null);

// --- FUNCIONES ---
// Cargar niveles desde la API
const cargarNiveles = async (page = 1) => {
    loading.value = true;
    try {
        const res = await axios.get(
            `/api/niveles?page=${page}&search=${search.value}`
        );
        niveles.value = {
            data: res.data.data ?? [],
            current_page: res.data.current_page ?? 1,
            last_page: res.data.last_page ?? 1,
        };
    } catch (error) {
        console.error("Error al cargar niveles:", error);
        niveles.value.data = [];
        niveles.value.current_page = 1;
        niveles.value.last_page = 1;
    } finally {
        loading.value = false;
    }
};

// Buscar niveles (reinicia a la primera página)
const buscarNiveles = () => {
    cargarNiveles(1);
};

// --- ELIMINAR ---
const confirmarEliminar = (id) => {
    nivelIdToDelete.value = id;
    showDeleteModal.value = true;
};

const eliminarNivel = async () => {
    if (!nivelIdToDelete.value) return;
    deleting.value = true;

    try {
        await axios.delete(`/api/niveles/${nivelIdToDelete.value}`);
        showDeleteModal.value = false;
        showSuccessModal.value = true;
        cargarNiveles(niveles.value.current_page);
    } catch (err) {
        console.error("Error al eliminar nivel:", err);
    } finally {
        deleting.value = false;
        nivelIdToDelete.value = null;
    }
};

// --- MOUNT ---
onMounted(() => {
    cargarNiveles();
});
</script>

<template>
    <Head title="Niveles de Complejidad" />

    <AppLayout>
        <template #title>
            <PageHeader title="Niveles de Complejidad" />
        </template>

        <TablaSeccion :data="niveles.data" title="Lista de Niveles">
            <!-- TOOLBAR -->
            <template #toolbar>
                <ToolbarBase
                    createText="Nuevo Nivel"
                    :createHref="route('niveles.create')"
                >
                    <template #left>
                        <input
                            v-model="search"
                            @input="buscarNiveles"
                            type="text"
                            placeholder="Buscar nivel..."
                            class="border rounded-md px-3 py-2 text-sm w-64"
                        />
                    </template>
                </ToolbarBase>
            </template>

            <!-- HEAD -->
            <template #head>
                <tr>
                    <th class="py-3 px-3 text-left">Nombre</th>
                    <!-- <th class="py-3 px-3 text-left">Descripción</th> -->
                    <th class="py-3 px-3 text-left">Factor</th>
                    <th class="py-3 px-3 text-left">Rubricas</th>
                    <th class="py-3 px-3 text-center">Estado</th>
                    <th class="py-3 px-3 text-center">Acciones</th>
                </tr>
            </template>

            <!-- BODY -->
            <template #body>
                <tr
                    v-for="item in niveles.data"
                    :key="item.id"
                    class="border-b hover:bg-gray-50 transition"
                >
                    <td class="py-3 px-3 font-medium">{{ item.nombre }}</td>
                    <!-- <td class="py-3 px-3">{{ item.descripcion ?? "-" }}</td> -->
                    <td class="py-3 px-3">{{ item.factor }}</td>
                    <td class="py-3 px-3">{{ item.rubricas }}</td>
                    <td class="py-3 px-3 text-center">
                        <span
                            :class="
                                item.estado ? 'text-green-600' : 'text-red-600'
                            "
                        >
                            {{ item.estado ? "Activo" : "Inactivo" }}
                        </span>
                    </td>
                    <td class="py-3 px-3 flex justify-center gap-2">
                        <button
                            @click="router.get(route('niveles.edit', item.id))"
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
                            @click="router.get(route('niveles.show', item.id))"
                            class="px-3 py-1 bg-gray-700 text-white rounded hover:bg-gray-600 text-sm"
                        >
                            Ver
                        </button>
                    </td>
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td colspan="5" class="text-center py-4">Cargando...</td>
                </tr>

                <!-- EMPTY -->
                <tr v-if="!loading && niveles.data.length === 0">
                    <td colspan="5" class="text-center py-4">
                        No se encontraron registros.
                    </td>
                </tr>
            </template>
        </TablaSeccion>

        <!-- PAGINACIÓN -->
        <div class="flex justify-center items-center mt-4 gap-3">
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="niveles.current_page === 1"
                @click="cargarNiveles(niveles.current_page - 1)"
            >
                Anterior
            </button>
            <span
                >Página {{ niveles.current_page }} de
                {{ niveles.last_page }}</span
            >
            <button
                class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50"
                :disabled="niveles.current_page === niveles.last_page"
                @click="cargarNiveles(niveles.current_page + 1)"
            >
                Siguiente
            </button>
        </div>

        <!-- MODALES -->
        <ConfirmDeleteModal
            v-model:show="showDeleteModal"
            title="Eliminar nivel"
            message="¿Estás seguro de eliminar este nivel? Esta acción no se puede deshacer."
            :loading="deleting"
            @confirm="eliminarNivel"
        />

        <SuccessModals
            v-model:show="showSuccessModal"
            title="Operación exitosa"
            message="La acción se realizó correctamente."
        />
    </AppLayout>
</template>
