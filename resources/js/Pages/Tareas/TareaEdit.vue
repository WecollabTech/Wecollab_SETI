<script setup>
import { Head } from "@inertiajs/vue3";
import { reactive, ref, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

// --- PROPS ---
const props = defineProps({
    tareaId: {
        type: [Number, String],
        required: true,
    },
});

const tareaIdNumber = Number(props.tareaId);

// --- FORMULARIO ---
const form = reactive({
    titulo: "",
    descripcion: "",
    estado: "pendiente",
    activo: true,
    duracion_minuto: null,
    orden: null,
    fase_id: "",
});

const errors = reactive({
    titulo: null,
    descripcion: null,
    estado: null,
    activo: null,
    duracion_minuto: null,
    orden: null,
    fase_id: null,
});

const fases = ref([]);
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- CARGAR DATOS ---
const cargarTarea = async () => {
    try {
        const res = await axios.get(`/api/tareas/${tareaIdNumber}`);
        const data = res.data.data ?? res.data;

        form.titulo = data.titulo ?? "";
        form.descripcion = data.descripcion ?? "";
        form.estado = data.estado ?? "pendiente";
        form.activo = data.activo ?? true;
        form.duracion_minuto = data.duracion_minuto ?? null;
        form.orden = data.orden ?? null;
        form.fase_id = data.fase_id ?? "";
    } catch (err) {
        console.error("Error al cargar tarea:", err);
    }
};

const cargarFases = async () => {
    try {
        const res = await axios.get("/api/fases");
        fases.value = res.data.data ?? res.data;
    } catch (err) {
        console.error("Error al cargar fases:", err);
    }
};

// --- WATCHERS ---
watch(
    () => form.titulo,
    () => (errors.titulo = null)
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null)
);
watch(
    () => form.estado,
    () => (errors.estado = null)
);
watch(
    () => form.activo,
    () => (errors.activo = null)
);
watch(
    () => form.duracion_minuto,
    () => (errors.duracion_minuto = null)
);
watch(
    () => form.orden,
    () => (errors.orden = null)
);
watch(
    () => form.fase_id,
    () => (errors.fase_id = null)
);

// --- SUBMIT ---
const submit = async () => {
    if (sending.value) return;
    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const res = await axios.put(`/api/tareas/${tareaIdNumber}`, form);
        console.log("Respuesta del servidor:", res.data);

        modalMessage.value =
            res.data.message || "Tarea actualizada correctamente.";
        modalType.value = "success";
        showModal.value = true; // <-- aquí se muestra el modal
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0])
            );
        } else {
            console.error("Error al actualizar tarea:", err.message);
        }
    } finally {
        sending.value = false;
    }
};

// --- CERRAR / CANCELAR ---
const cancel = () => router.get("/tareas");

// --- REDIRECCIÓN DESPUÉS DEL MODAL ---
const onModalClose = () => {
    router.get("/tareas");
};

// --- MOUNT ---
onMounted(() => {
    cargarFases();
    cargarTarea();
});
</script>

<template>
    <Head title="Editar Tarea" />
    <AppLayout>
        <template #title>
            <PageHeader title="Editar Tarea" />
        </template>

        <FormWrapper title="Actualizar los datos de la Tarea">
            <!-- FORM INPUTS IGUAL QUE ANTES -->
            <FormInput
                label="Título"
                v-model="form.titulo"
                :error="errors.titulo"
                placeholder="Título de la tarea"
            />
            <p v-if="errors.titulo" class="text-red-600 text-sm mt-1">
                {{ errors.titulo }}
            </p>

            <FormInput
                label="Descripción"
                type="textarea"
                v-model="form.descripcion"
                :error="errors.descripcion"
                placeholder="Descripción de la tarea"
            />
            <p v-if="errors.descripcion" class="text-red-600 text-sm mt-1">
                {{ errors.descripcion }}
            </p>

            <FormInput
                label="Estado"
                type="select"
                v-model="form.estado"
                :options="[
                    { label: 'Pendiente', value: 'pendiente' },
                    { label: 'En proceso', value: 'en_proceso' },
                    { label: 'Completado', value: 'completado' },
                    { label: 'Cancelado', value: 'cancelado' },
                ]"
                :error="errors.estado"
            />
            <p v-if="errors.estado" class="text-red-600 text-sm mt-1">
                {{ errors.estado }}
            </p>

            <FormInput
                label="Duración (minutos)"
                type="number"
                v-model="form.duracion_minuto"
                :error="errors.duracion_minuto"
                placeholder="Ej. 30"
            />
            <p v-if="errors.duracion_minuto" class="text-red-600 text-sm mt-1">
                {{ errors.duracion_minuto }}
            </p>

            <FormInput
                label="Orden"
                type="number"
                v-model="form.orden"
                :error="errors.orden"
                placeholder="Ej. 1"
            />
            <p v-if="errors.orden" class="text-red-600 text-sm mt-1">
                {{ errors.orden }}
            </p>

            <FormInput
                label="Fase"
                type="select"
                v-model="form.fase_id"
                :options="fases.map((f) => ({ label: f.nombre, value: f.id }))"
                :error="errors.fase_id"
            />
            <p v-if="errors.fase_id" class="text-red-600 text-sm mt-1">
                {{ errors.fase_id }}
            </p>

            <FormInput
                label="Activa"
                type="checkbox"
                v-model="form.activo"
                :error="errors.activo"
            />

            <template #actions>
                <div
                    class="flex flex-col md:flex-row justify-center md:justify-end gap-4"
                >
                    <button
                        type="button"
                        @click="cancel"
                        class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        @click="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                        :disabled="sending"
                    >
                        Guardar
                    </button>
                </div>
            </template>
        </FormWrapper>

        <!-- MODAL DE ÉXITO -->
        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            :type="modalType"
            :auto-close="4000"
            @update:show="
                (val) => {
                    if (!val) onModalClose();
                }
            "
        />
    </AppLayout>
</template>
