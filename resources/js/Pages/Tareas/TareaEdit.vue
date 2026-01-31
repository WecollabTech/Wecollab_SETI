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
import CardInput from "@/Components/Formulario/CardInput.vue";
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
    id_proceso: "", // <-- agregado
});

const errors = reactive({
    titulo: null,
    descripcion: null,
    estado: null,
    activo: null,
    duracion_minuto: null,
    orden: null,
    fase_id: null,
    id_proceso: null, // <-- agregado
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
        form.id_proceso = data.id_proceso ?? ""; // <-- agregado
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
Object.keys(form).forEach((key) => {
    watch(
        () => form[key],
        () => (errors[key] = null),
    );
});

// --- SUBMIT ---
const submit = async () => {
    if (sending.value) return;
    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    // Construir payload con tipos correctos
    const payload = {
        titulo: form.titulo,
        descripcion: form.descripcion,
        estado: form.estado,
        activo: Boolean(form.activo),
        duracion_minuto: form.duracion_minuto
            ? Number(form.duracion_minuto)
            : null,
        orden: form.orden ? Number(form.orden) : null,
        fase_id: form.fase_id ? Number(form.fase_id) : null,
        id_proceso: form.id_proceso, // obligatorio
    };

    try {
        const res = await axios.put(`/api/tareas/${tareaIdNumber}`, payload);
        console.log("Respuesta del servidor:", res.data);

        modalMessage.value =
            res.data.message || "Tarea actualizada correctamente.";
        modalType.value = "success";
        showModal.value = true;
    } catch (err) {
        if (err.response?.status === 422) {
            console.log("Errores de validación:", err.response.data.errors);
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else {
            console.error("Error al actualizar tarea:", err);
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
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <CardInput>
                    <FormInput
                        label="Título"
                        v-model="form.titulo"
                        :error="errors.titulo"
                        placeholder="Título de la tarea"
                    />
                </CardInput>

                <CardInput>
                    <FormInput
                        label="Id Proceso"
                        type="text"
                        v-model="form.id_proceso"
                        :error="errors.id_proceso"
                        placeholder="ID del proceso"
                    />
                </CardInput>

                <CardInput>
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción de la tarea"
                    />
                </CardInput>

                <!-- <CardInput>
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
                </CardInput> -->

                <CardInput>
                    <FormInput
                        label="Duración (minutos)"
                        type="number"
                        v-model="form.duracion_minuto"
                        :error="errors.duracion_minuto"
                        placeholder="Ej. 30"
                    />
                </CardInput>

                <CardInput>
                    <FormInput
                        label="Orden"
                        type="number"
                        v-model="form.orden"
                        :error="errors.orden"
                        placeholder="Ej. 1"
                    />
                </CardInput>

                <CardInput>
                    <FormInput
                        label="Fase"
                        type="select"
                        v-model="form.fase_id"
                        :options="
                            fases.map((f) => ({ label: f.nombre, value: f.id }))
                        "
                        :error="errors.fase_id"
                    />
                </CardInput>

                <CardInput>
                    <div class="mt-4">
                        <span
                            class="block text-sm font-medium text-gray-700 mb-1"
                        >
                            Activo
                        </span>
                        <div
                            class="w-12 h-6 rounded-full p-0.5 flex items-center transition-colors duration-300 cursor-pointer"
                            :class="
                                form.activo ? 'bg-green-500' : 'bg-gray-300'
                            "
                            @click="form.activo = !form.activo"
                        >
                            <div
                                class="bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300"
                                :class="
                                    form.activo
                                        ? 'translate-x-6'
                                        : 'translate-x-0'
                                "
                            ></div>
                        </div>
                        <p
                            v-if="errors.activo"
                            class="text-red-600 text-sm mt-1"
                        >
                            {{ errors.activo }}
                        </p>
                    </div>
                </CardInput>
            </div>
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
