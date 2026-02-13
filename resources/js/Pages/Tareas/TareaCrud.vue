<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { Head, router } from "@inertiajs/vue3";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

import FormActions from "@/Components/Formulario/FormActions.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
// import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";
// import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";

const page = usePage();

const showFases = ref(true);

// --- Formulario reactivo ---
const form = reactive({
    titulo: "",
    id_proceso: "",
    descripcion: "",
    estado: "pendiente",
    activo: true,
    duracion_minuto: null,
    orden: 1,
    fase_id: "",
    url_contenido: "",
    integraciones: [],
    fases: [],
});

const faseFromUrl =
    page.props?.query?.fase_id ||
    new URLSearchParams(window.location.search).get("fase_id");

if (faseFromUrl) {
    form.fase_id = Number(faseFromUrl);
}

// --- Errores ---
const errors = reactive({
    titulo: null,
    id_proceso: null,
    descripcion: null,
    estado: null,
    activo: null,
    duracion_minuto: null,
    orden: null,
    fase_id: null,
    url_contenido: null,
    integraciones: null,
    fases: null,
});

// --- Modal ---
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- Opciones ---
const fasesOptions = ref([]);
const integracionesOptions = ref([]);

// --- Cargar fases desde API ---
const cargarFases = async () => {
    try {
        const res = await axios.get("/api/listafases");
        // console.log("Fases API:", res.data);
        fasesOptions.value = res.data;
    } catch (err) {
        console.error("Error al cargar fases:", err);
    }
};

const cargarIntegraciones = async () => {
    try {
        const res = await axios.get("/api/integraciones");
        integracionesOptions.value = res.data.data ?? [];
    } catch (err) {
        console.error("Error al cargar integraciones:", err);
    }
};

onMounted(() => {
    cargarFases();
    cargarIntegraciones();
});

// --- Watchers para limpiar errores en tiempo real ---
watch(
    () => form.titulo,
    () => (errors.titulo = null),
);
watch(
    () => form.id_proceso,
    () => (errors.id_proceso = null),
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null),
);
watch(
    () => form.estado,
    () => (errors.estado = null),
);
watch(
    () => form.duracion_minuto,
    () => (errors.duracion_minuto = null),
);
watch(
    () => form.orden,
    () => (errors.orden = null),
);
watch(
    () => form.fase_id,
    () => (errors.fase_id = null),
);
watch(
    () => form.integraciones,
    () => (errors.integraciones = null),
);
watch(
    () => form.fases,
    () => (errors.fases = null),
);
watch(
    () => form.activo,
    () => (errors.activo = null),
);

// --- Reset Formulario ---
const resetForm = () => {
    form.titulo = "";
    form.id_proceso = "";
    form.descripcion = "";
    form.estado = "pendiente";
    form.activo = true;
    form.duracion_minuto = null;
    form.orden = 1;
    form.fase_id = "";
    form.integraciones = [];
    form.fases = [];
    Object.keys(errors).forEach((k) => (errors[k] = null));
};

// --- Submit ---
const submit = async () => {
    if (sending.value) return;

    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const payload = {
            ...form,
            integraciones: form.integraciones.map((i) => i.id),
            fases: form.fases.map((f) => f.id),
        };

        const res = await axios.post("/api/tareas", payload);

        modalMessage.value =
            res.data.message || "Tarea creada correctamente ✅";
        modalType.value = "success";
        showModal.value = true;

        resetForm();
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else {
            modalMessage.value =
                err.response?.data?.message || "Error al guardar la tarea";
            modalType.value = "error";
            showModal.value = true;
        }
    } finally {
        sending.value = false;
    }
};

// --- Cancelar ---
const cancel = () => router.get("/tareas");

// --- Modal cerrar ---
const onModalClose = () => router.get("/tareas");
</script>

<template>
    <Head title="Nueva Tarea" />

    <AppLayout>
        <!-- <template #title>
            <PageHeader title="Nueva Tarea" />
        </template> -->

        <FormWrapper title="Datos de la Tarea">
            <!-- GRID PRINCIPAL -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- TÍTULO -->
                <div class="card">
                    <FormInput
                        label="Título"
                        v-model="form.titulo"
                        :error="errors.titulo"
                        placeholder="Mínimo 15 caracteres"
                    />
                </div>

                <!-- ID PROCESO -->
                <div class="card">
                    <FormInput
                        label="ID Proceso"
                        v-model="form.id_proceso"
                        :error="errors.id_proceso"
                        placeholder="ID del proceso"
                    />
                </div>

                <!-- DESCRIPCIÓN -->
                <div class="card">
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción breve"
                    />
                    <p class="text-xs text-gray-400 mt-1">
                        {{ form.descripcion.length }}/1000 caracteres
                    </p>
                </div>

                <!-- DURACIÓN -->
                <div class="card">
                    <FormInput
                        label="Duración (min)"
                        type="number"
                        v-model="form.duracion_minuto"
                        :error="errors.duracion_minuto"
                        placeholder="30"
                    />
                </div>

                <!-- ORDEN -->
                <div class="card">
                    <FormInput
                        label="Orden"
                        type="number"
                        v-model="form.orden"
                        :error="errors.orden"
                        placeholder="1"
                    />
                </div>

                <!-- INTEGRACIONES -->
                <!-- <div class="card md:col-span-2">
                    <label class="block text-sm font-semibold mb-1">
                        Integraciones
                    </label>

                    <Multiselect
                        v-model="form.integraciones"
                        :options="integracionesOptions"
                        track-by="id"
                        label="nombre"
                        placeholder="Selecciona integraciones"
                        multiple
                        :close-on-select="false"
                        :hide-selected="true"
                        taggable
                        class="multiselect-compact w-full"
                    />

                    <p
                        v-if="errors.integraciones"
                        class="text-red-600 text-xs mt-1"
                    >
                        {{ errors.integraciones }}
                    </p>
                </div> -->

                <!-- ACTIVO -->
                <div class="card">
                    <div
                        class="card flex items-center justify-between md:col-span-2"
                    >
                        <span class="text-sm font-semibold text-gray-700">
                            Activo
                        </span>

                        <div
                            class="w-12 h-6 rounded-full p-0.5 flex items-center transition cursor-pointer"
                            :class="
                                form.activo ? 'bg-green-500' : 'bg-gray-300'
                            "
                            @click="form.activo = !form.activo"
                        >
                            <div
                                class="bg-white w-5 h-5 rounded-full shadow transform transition"
                                :class="
                                    form.activo
                                        ? 'translate-x-6'
                                        : 'translate-x-0'
                                "
                            ></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FASE (COLLAPSE) -->
            <div class="card mt-4">
                <button
                    type="button"
                    class="w-full flex justify-between items-center text-sm font-semibold"
                    @click="showFases = !showFases"
                >
                    <span>
                        Fase
                        <span class="text-xs text-gray-400 ml-1">
                            {{
                                fasesOptions.find((f) => f.id === form.fase_id)
                                    ?.nombre || "Sin seleccionar"
                            }}
                        </span>
                    </span>

                    <svg
                        class="w-4 h-4 transition-transform"
                        :class="{ 'rotate-180': showFases }"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M19 9l-7 7-7-7"
                        />
                    </svg>
                </button>

                <transition name="fade">
                    <div
                        v-show="showFases"
                        class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-2"
                    >
                        <label
                            v-for="fase in fasesOptions"
                            :key="fase.id"
                            class="flex items-center gap-2 px-2 py-1.5 text-sm rounded-md border cursor-pointer hover:bg-gray-50"
                            :class="
                                form.fase_id === fase.id
                                    ? 'border-blue-500 bg-blue-50'
                                    : 'border-gray-200'
                            "
                        >
                            <input
                                type="radio"
                                :value="fase.id"
                                v-model="form.fase_id"
                                class="text-blue-600"
                            />
                            {{ fase.nombre }}
                        </label>
                    </div>
                </transition>

                <p v-if="errors.fase_id" class="text-red-600 text-xs mt-1">
                    {{ errors.fase_id }}
                </p>
            </div>

            <!-- URL de Contenido -->
            <div class="card md:col-span-2">
                <FormInput
                    label="URL de Contenido"
                    v-model="form.url_contenido"
                    :error="errors.url_contenido"
                    type="url"
                    placeholder="https://..."
                />
                <p class="text-xs text-gray-400 mt-1">
                    Puedes ingresar link de YouTube, Loom, Drive o archivo.
                </p>
            </div>

            <!-- ACCIONES -->
            <template #actions>
                <FormActions
                    :sending="sending"
                    @cancel="cancel"
                    @submit="submit"
                />
            </template>
        </FormWrapper>

        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            :type="modalType"
            :auto-close="4000"
            @update:show="
                (val) => {
                    if (!val && modalType === 'success') {
                        router.get('/tareas');
                    }
                }
            "
        />
    </AppLayout>
</template>

<style>
.card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 0.5rem;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: box-shadow 0.3s;
}
.card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
}

.fade-enter-active,
.fade-leave-active {
    transition: all 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    max-height: 0;
}
</style>
