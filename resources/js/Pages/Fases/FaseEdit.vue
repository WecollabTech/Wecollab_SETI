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
    faseId: {
        type: [Number, String], // acepta string o number desde URL
        required: true,
    },
});

// Convertimos a número
const faseIdNumber = Number(props.faseId);

// --- FORMULARIO ---
const form = reactive({
    nombre: "",
    descripcion: "",
    minutos_base: 0,
});

const errors = reactive({
    nombre: null,
    descripcion: null,
    minutos_base: null,
});

const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- CARGAR DATOS DE LA FASE ---
const cargarFase = async () => {
    try {
        const res = await axios.get(`/api/fases/${faseIdNumber}`);
        form.nombre = res.data.data.nombre;
        form.descripcion = res.data.data.descripcion;
        form.minutos_base = res.data.data.minutos_base;
    } catch (err) {
        console.error("Error al cargar fase:", err);
    }
};

onMounted(() => {
    cargarFase();
});

// --- WATCHERS PARA VALIDACION ---
watch(
    () => form.nombre,
    () => (errors.nombre = null),
);
watch(
    () => form.descripcion,
    () => (errors.descripcion = null),
);
watch(
    () => form.minutos_base,
    () => (errors.minutos_base = null),
);

// --- FUNCIONES ---
const submit = async () => {
    if (sending.value) return;
    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const res = await axios.put(`/api/fases/${faseIdNumber}`, form);
        if (res.data.success) {
            modalMessage.value = res.data.message;
            modalType.value = "success";
            showModal.value = false;
            setTimeout(() => (showModal.value = true), 50);
            // Redirigir al listado de fases
            setTimeout(() => router.get("/fases"), 3000);
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else console.error("Error al actualizar:", err.message);
    } finally {
        sending.value = false;
    }
};

const cancel = () => router.get("/fases");
</script>

<template>
    <Head title="Editar Fase" />
    <AppLayout>
        <template #title>
            <PageHeader title="Editar Fase" />
        </template>

        <FormWrapper title="Actualizar los datos de la Fase">
            <CardInput>
                <FormInput
                    label="Nombre"
                    v-model="form.nombre"
                    :error="errors.nombre"
                    placeholder="Nombre de la fase"
                />
                <p v-if="errors.nombre" class="text-red-600 text-sm mt-1">
                    {{ errors.nombre }}
                </p>
            </CardInput>
            <CardInput>
                <FormInput
                    label="Descripción"
                    type="textarea"
                    v-model="form.descripcion"
                    :error="errors.descripcion"
                    placeholder="Descripción breve"
                />
                <p v-if="errors.descripcion" class="text-red-600 text-sm mt-1">
                    {{ errors.descripcion }}
                </p>
            </CardInput>
            <!-- <FormInput
                label="Minutos Base"
                type="number"
                v-model="form.minutos_base"
                :error="errors.minutos_base"
                placeholder="Duración en minutos"
            />
            <p v-if="errors.minutos_base" class="text-red-600 text-sm mt-1">
                {{ errors.minutos_base }}
            </p> -->

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
        />
    </AppLayout>
</template>
