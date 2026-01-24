<script setup>
import { Head } from "@inertiajs/vue3";
import { reactive, ref, watch, onMounted } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import CardInput from "@/Components/Formulario/CardInput.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

// --- PROPS ---
const props = defineProps({
    integracionId: {
        type: [Number, String],
        required: true,
    },
});

const integracionIdNumber = Number(props.integracionId);

// --- FORM ---
const form = reactive({
    nombre: "",
    descripcion: "",
});

const errors = reactive({
    nombre: null,
    descripcion: null,
});

const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- CARGAR INTEGRACIÓN ---
const cargarIntegracion = async () => {
    try {
        const res = await axios.get(
            `/api/integraciones/${integracionIdNumber}`,
        );

        form.nombre = res.data.data.nombre;
        form.descripcion = res.data.data.descripcion;
    } catch (error) {
        console.error("Error al cargar integración:", error);
    }
};

onMounted(() => {
    cargarIntegracion();
});

// --- WATCHERS (IGUAL QUE CREATE) ---
watch(
    () => form.nombre,
    () => (errors.nombre = null),
);

watch(
    () => form.descripcion,
    () => (errors.descripcion = null),
);

// --- SUBMIT ---
const submit = async () => {
    if (sending.value) return;
    sending.value = true;

    Object.keys(errors).forEach((k) => (errors[k] = null));

    try {
        const res = await axios.put(
            `/api/integraciones/${integracionIdNumber}`,
            form,
        );

        if (res.data.success) {
            modalMessage.value =
                res.data.message || "Integración actualizada correctamente";
            modalType.value = "success";

            showModal.value = false;
            setTimeout(() => (showModal.value = true), 50);

            setTimeout(() => router.get("/integraciones"), 4000);
        }
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else {
            console.error("Error al actualizar:", err);
        }
    } finally {
        sending.value = false;
    }
};

const cancel = () => router.get("/integraciones");
</script>

<template>
    <Head title="Editar Integración" />

    <AppLayout>
        <template #title>
            <PageHeader title="Editar Integración" />
        </template>
        <FormWrapper title="Actualizar datos de la Integración">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- NOMBRE -->
                <CardInput>
                    <FormInput
                        label="Nombre"
                        v-model="form.nombre"
                        :error="errors.nombre"
                        placeholder="Nombre de la integración"
                    />
                    <p v-if="errors.nombre" class="text-red-600 text-sm mt-1">
                        {{ errors.nombre }}
                    </p>
                </CardInput>

                <!-- DESCRIPCIÓN -->
                <CardInput>
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción breve"
                    />
                    <p
                        v-if="errors.descripcion"
                        class="text-red-600 text-sm mt-1"
                    >
                        {{ errors.descripcion }}
                    </p>
                </CardInput>
            </div>

            <!-- ACCIONES -->
            <template #actions>
                <div
                    class="flex flex-col md:flex-row justify-center md:justify-end gap-4 mt-6"
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
