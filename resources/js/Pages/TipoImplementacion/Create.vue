<script setup>
import { ref, reactive, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import { Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";
import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.min.css";

// --- Formulario reactivo ---
const form = reactive({
    nombre: "",
    descripcion: "",
    rubrica: "",
    activo: true,
    integraciones: [], // Array de integraciones seleccionadas
    fases: [],
});

// --- Errores ---
const errors = reactive({
    nombre: null,
    descripcion: null,
    rubrica: null,
    activo: null,
    integraciones: null,
    fases: null,
});

// --- Modal ---
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- Opciones de integraciones ---
const integracionesOptions = ref([]);
const fasesOptions = ref([]);

// --- Cargar integraciones desde API ---
const cargarIntegraciones = async () => {
    try {
        const res = await axios.get("/api/integraciones");
        integracionesOptions.value = res.data.data;
    } catch (err) {
        console.error("Error al cargar integraciones:", err);
    }
};
// --- Cargar integraciones desde API ---
const cargarFases = async () => {
    try {
        const res = await axios.get("/api/fases");
        fasesOptions.value = res.data.data;
    } catch (err) {
        console.error("Error al cargar fases:", err);
    }
};

// --- Reset formulario ---
const resetForm = () => {
    form.nombre = "";
    form.descripcion = "";
    form.rubrica = "";
    form.activo = true;
    form.integraciones = [];
    Object.keys(errors).forEach((k) => (errors[k] = null));
};

// --- Modal de éxito ---
const handleSuccess = (message) => {
    modalMessage.value = message;
    modalType.value = "success";
    showModal.value = true;
    resetForm();
};

// --- Watchers para limpiar errores ---
watch(
    () => form.nombre,
    () => (errors.nombre = null)
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null)
);
watch(
    () => form.rubrica,
    () => (errors.rubrica = null)
);
watch(
    () => form.activo,
    () => (errors.activo = null)
);
watch(
    () => form.integraciones,
    () => (errors.integraciones = null)
);

// --- Validación frontend simple opcional ---
const validateFront = () => {
    let valid = true;

    if (!form.nombre || form.nombre.length < 3) {
        errors.nombre = "El nombre debe tener al menos 3 caracteres.";
        valid = false;
    }

    if (form.descripcion && form.descripcion.length > 1000) {
        errors.descripcion = "La descripción no puede superar 1000 caracteres.";
        valid = false;
    }

    if (form.rubrica && form.rubrica.length > 255) {
        errors.rubrica = "La rúbrica no puede superar 255 caracteres.";
        valid = false;
    }

    if (typeof form.activo !== "boolean") {
        errors.activo = "Debes seleccionar un estado válido.";
        valid = false;
    }

    return valid;
};

// --- Submit ---
const submit = async () => {
    if (sending.value) return;

    // Validación frontend
    if (!validateFront()) return;

    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const payload = {
            nombre: form.nombre,
            descripcion: form.descripcion,
            rubrica: form.rubrica,
            activo: form.activo,
            integraciones: form.integraciones.map((i) => i.id),
            fases: form.fases.map((f) => f.id),
        };

        const res = await axios.post("/api/tipoimplementacion", payload);

        // CORRECCIÓN: Accede correctamente a la respuesta
        if (res.data.success) {
            handleSuccess(res.data.message);
        } else {
            // Si por alguna razón success es false
            console.error("Error del servidor:", res.data.message);
        }
    } catch (err) {
        console.error("Error completo:", err);

        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            // Mapea los errores backend a nuestro objeto errors
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0])
            );
        } else if (err.response?.data?.message) {
            // Muestra error general del backend
            modalMessage.value = err.response.data.message;
            modalType.value = "error";
            showModal.value = true;
        } else {
            modalMessage.value = "Error al conectar con el servidor";
            modalType.value = "error";
            showModal.value = true;
            console.error(
                "Error al guardar:",
                err.response?.data?.message || err.message
            );
        }
    } finally {
        sending.value = false;
    }
};

// --- Cancelar ---
const cancel = () => router.get("/tipoimplementacion");

// --- On mounted ---
onMounted(() => cargarIntegraciones());
onMounted(() => cargarFases());
</script>

<template>
    <Head title="Nuevo Tipo de Implementación" />
    <AppLayout>
        <template #title>
            <PageHeader title="Nuevo Tipo de Implementación" />
        </template>

        <FormWrapper title="Ingresa los datos de la Implementación">
            <!-- Nombre -->
            <FormInput
                label="Nombre"
                v-model="form.nombre"
                :error="errors.nombre"
                placeholder="Nombre de la implementación"
            />

            <!-- Rúbrica -->
            <FormInput
                label="Rúbrica"
                v-model="form.rubrica"
                :error="errors.rubrica"
                placeholder="Rúbrica asociada"
            />

            <!-- Descripción -->
            <FormInput
                label="Descripción"
                type="textarea"
                v-model="form.descripcion"
                :error="errors.descripcion"
                placeholder="Descripción breve"
            />

            <!-- Integraciones -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Integraciones</label>
                <Multiselect
                    v-model="form.integraciones"
                    :options="integracionesOptions"
                    track-by="id"
                    label="nombre"
                    placeholder="Selecciona integraciones"
                    multiple
                    :close-on-select="false"
                    :hide-selected="true"
                />
                <p
                    v-if="errors.integraciones"
                    class="text-red-600 text-sm mt-1"
                >
                    {{ errors.integraciones }}
                </p>
            </div>

            <!-- Fases -->
            <div class="mb-4">
                <label class="block font-semibold mb-1">Fases</label>
                <Multiselect
                    v-model="form.fases"
                    :options="fasesOptions"
                    track-by="id"
                    label="nombre"
                    placeholder="Selecciona las Fases"
                    multiple
                    :close-on-select="false"
                    :hide-selected="true"
                />
                <p v-if="errors.fases" class="text-red-600 text-sm mt-1">
                    {{ errors.fases }}
                </p>
            </div>

            <!-- Activo como toggle debajo del título -->
            <div class="mb-6">
                <span class="block font-semibold text-gray-700 mb-1"
                    >Activo</span
                >
                <div
                    class="w-12 h-6 rounded-full p-0.5 flex items-center transition-colors duration-300 cursor-pointer"
                    :class="form.activo ? 'bg-green-500' : 'bg-gray-300'"
                    @click="form.activo = !form.activo"
                >
                    <div
                        class="bg-white w-5 h-5 rounded-full shadow-md transform transition-transform duration-300"
                        :class="form.activo ? 'translate-x-6' : 'translate-x-0'"
                    ></div>
                </div>
                <p v-if="errors.activo" class="text-red-600 text-sm mt-1">
                    {{ errors.activo }}
                </p>
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
                        :disabled="sending"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
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
        />
    </AppLayout>
</template>

<style>
.multiselect {
    width: 100%;
}
</style>
