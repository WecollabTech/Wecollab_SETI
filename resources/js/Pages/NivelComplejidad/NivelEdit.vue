<script setup>
import { Head, router } from "@inertiajs/vue3";
import { reactive, ref, onMounted } from "vue";
import axios from "axios";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

// --- Props ---
const props = defineProps({
    nivelId: { type: [Number, String], required: true },
});

const form = reactive({
    nombre: "",
    factor: 0,
    descripcion: "",
    estado: 1,
    rubricas: "",
});
const errors = reactive({});
const sending = ref(false);
const showModal = ref(false);
const modalMessage = ref("");

const cargarNivel = async () => {
    try {
        const res = await axios.get(`/api/niveles/${props.nivelId}`);
        Object.assign(form, res.data.data);
    } catch (err) {
        console.error("Error al cargar nivel:", err);
    }
};

onMounted(() => cargarNivel());

const submit = async () => {
    sending.value = true;
    Object.keys(errors).forEach((k) => (errors[k] = null));
    try {
        const res = await axios.put(`/api/niveles/${props.nivelId}`, form);
        if (res.data.success) {
            modalMessage.value = res.data.message;
            showModal.value = true;
            setTimeout(() => router.get(route("niveles.index")), 2000);
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (k) => (errors[k] = validationErrors[k][0])
            );
        } else console.error("Error al actualizar nivel:", err);
    } finally {
        sending.value = false;
    }
};

const cancel = () => router.get(route("niveles.index"));
</script>

<template>
    <Head title="Editar Nivel" />
    <AppLayout>
        <template #title>
            <PageHeader title="Editar Nivel" />
        </template>

        <FormWrapper title="Actualizar Nivel de Complejidad">
            <FormInput
                label="Nombre"
                v-model="form.nombre"
                :error="errors.nombre"
                placeholder="Nombre del nivel"
            />
            <FormInput
                label="Factor"
                v-model="form.factor"
                type="number"
                :error="errors.factor"
                placeholder="Factor numérico"
            />
            <FormInput
                label="Descripción"
                type="textarea"
                v-model="form.descripcion"
                :error="errors.descripcion"
                placeholder="Descripción"
            />
            <FormInput
                label="Rúbricas"
                type="textarea"
                v-model="form.rubricas"
                :error="errors.rubricas"
                placeholder="Ingresa las rúbricas del nivel"
            />

            <FormInput
                label="Estado"
                type="select"
                v-model="form.estado"
                :options="[
                    { value: 1, label: 'Activo' },
                    { value: 0, label: 'Inactivo' },
                ]"
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

        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            type="success"
            :auto-close="2000"
        />
    </AppLayout>
</template>
