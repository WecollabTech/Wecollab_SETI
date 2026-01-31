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

import Multiselect from "vue-multiselect";
import "vue-multiselect/dist/vue-multiselect.css";

// --- PROPS ---
const props = defineProps({
    tipo: {
        type: Object,
        required: true,
    },
});

// --- FORMULARIO ---
const form = reactive({
    nombre: props.tipo.nombre ?? "",
    descripcion: props.tipo.descripcion ?? "",
    rubrica: props.tipo.rubrica ?? "",
    alcance: props.tipo.alcance ?? "", // 👈 ALCANCE
    activo: props.tipo.estado ?? true,
    integraciones: [],
    fases: [],
});

// --- ERRORES ---
const errors = reactive({
    nombre: null,
    descripcion: null,
    rubrica: null,
    alcance: null, // 👈 ALCANCE
    activo: null,
    integraciones: null,
    fases: null,
});

const integracionesList = ref([]);
const fasesList = ref([]);
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");
const sending = ref(false);

// --- CARGAR INTEGRACIONES ---
const cargarIntegraciones = async () => {
    try {
        const res = await axios.get("/api/integraciones");
        integracionesList.value = res.data.data ?? res.data;

        form.integraciones = integracionesList.value.filter((i) =>
            props.tipo.integraciones?.some((sel) => sel.id === i.id),
        );
    } catch (err) {
        console.error("Error al cargar integraciones:", err);
    }
};

// --- CARGAR FASES ---
const cargarFases = async () => {
    try {
        const res = await axios.get("/api/fases");
        fasesList.value = res.data.data ?? res.data;

        form.fases = fasesList.value.filter((f) =>
            props.tipo.fases?.some((sel) => sel.id === f.id),
        );
    } catch (err) {
        console.error("Error al cargar fases:", err);
    }
};

// --- WATCHERS (limpiar errores) ---
Object.keys(form).forEach((key) =>
    watch(
        () => form[key],
        () => (errors[key] = null),
    ),
);

// --- SUBMIT ---
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

        const res = await axios.put(
            `/api/tipoimplementacion/${props.tipo.id}`,
            payload,
        );

        modalMessage.value =
            res.data.message ||
            "Tipo de implementación actualizado correctamente.";
        modalType.value = "success";
        showModal.value = true;
    } catch (err) {
        if (err.response?.status === 422) {
            const validationErrors = err.response.data.errors;
            Object.keys(validationErrors).forEach(
                (f) => (errors[f] = validationErrors[f][0]),
            );
        } else {
            console.error("Error al actualizar tipo:", err);
        }
    } finally {
        sending.value = false;
    }
};

// --- CANCELAR ---
const cancel = () => router.get("/tipoimplementacion");
const onModalClose = () => router.get("/tipoimplementacion");

// --- MOUNT ---
onMounted(() => {
    cargarIntegraciones();
    cargarFases();
});
</script>

<template>
    <Head title="Editar Tipo de Implementación" />

    <AppLayout>
        <template #title>
            <PageHeader title="Editar Tipo de Implementación" />
        </template>

        <FormWrapper title="Actualizar los datos del Tipo de Implementación">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <CardInput>
                    <FormInput
                        label="Nombre"
                        v-model="form.nombre"
                        :error="errors.nombre"
                        placeholder="Nombre del tipo de implementación"
                    />
                </CardInput>

                <!-- Descripción -->
                <CardInput>
                    <FormInput
                        label="Descripción"
                        type="textarea"
                        v-model="form.descripcion"
                        :error="errors.descripcion"
                        placeholder="Descripción del tipo de implementación"
                    />
                </CardInput>

                <!-- Alcance -->
                <CardInput>
                    <FormInput
                        label="Alcance"
                        type="textarea"
                        v-model="form.alcance"
                        :error="errors.alcance"
                        placeholder="Describe el alcance de la implementación"
                    />
                    <p class="text-gray-400 text-sm mt-1">
                        {{ form.alcance.length }}/1000 caracteres
                    </p>
                </CardInput>

                <!-- Rúbrica -->
                <!-- <CardInput>
                    <FormInput
                        label="Rúbrica"
                        type="textarea"
                        v-model="form.rubrica"
                        :error="errors.rubrica"
                        placeholder="Rúbrica o explicación"
                    />
                </CardInput> -->

                <!-- Activo -->
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

                <!-- Integraciones -->
                <CardInput>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1">
                            Integraciones
                        </label>
                        <Multiselect
                            v-model="form.integraciones"
                            :options="integracionesList"
                            label="nombre"
                            track-by="id"
                            multiple
                            placeholder="Selecciona integraciones"
                        />
                        <p
                            v-if="errors.integraciones"
                            class="text-red-600 text-sm mt-1"
                        >
                            {{ errors.integraciones }}
                        </p>
                    </div>
                </CardInput>
                <!-- Fases -->
                <CardInput>
                    <div class="mt-4">
                        <label class="block text-sm font-medium mb-1"
                            >Fases</label
                        >
                        <Multiselect
                            v-model="form.fases"
                            :options="fasesList"
                            label="nombre"
                            track-by="id"
                            multiple
                            placeholder="Selecciona fases"
                        />
                        <p
                            v-if="errors.fases"
                            class="text-red-600 text-sm mt-1"
                        >
                            {{ errors.fases }}
                        </p>
                    </div>
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
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
                        :disabled="sending"
                    >
                        Guardar
                    </button>
                </div>
            </template>
        </FormWrapper>

        <!-- MODAL -->
        <SuccessModal
            :show.sync="showModal"
            :message="modalMessage"
            :type="modalType"
            :auto-close="4000"
            @update:show="(val) => !val && onModalClose()"
        />
    </AppLayout>
</template>
