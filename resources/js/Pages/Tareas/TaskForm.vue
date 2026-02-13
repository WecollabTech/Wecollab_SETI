<script setup>
import { ref, reactive, onMounted, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";

import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import FormActions from "@/Components/Formulario/FormActions.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";

const props = defineProps({
    type: { type: String, required: true }, // tarea | integracion
    faseId: [Number, String],
    integracionId: [Number, String],
    storeUrl: String,
    redirectUrl: String,
});

const showFases = ref(true);

const form = reactive({
    titulo: "",
    id_proceso: "",
    descripcion: "",
    activo: true,
    duracion_minuto: null,
    orden: 1,
    fase_id: props.faseId || "",
    integracion_id: props.integracionId || "",
});

const errors = reactive({});
const sending = ref(false);
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");

const fasesOptions = ref([]);
const integracionesOptions = ref([]);

onMounted(async () => {
    if (props.type === "tarea") {
        const res = await axios.get("/api/listafases");
        fasesOptions.value = res.data;
    }

    if (props.type === "integracion") {
        const res = await axios.get("/api/integraciones");
        integracionesOptions.value = res.data.data ?? [];
    }
});

const submit = async () => {
    if (sending.value) return;

    sending.value = true;
    Object.keys(errors).forEach((k) => delete errors[k]);

    try {
        await axios.post(props.storeUrl, form);

        modalMessage.value = "Tarea guardada correctamente ✅";
        modalType.value = "success";
        showModal.value = true;
    } catch (err) {
        if (err.response?.status === 422) {
            Object.assign(errors, err.response.data.errors);
        }
    } finally {
        sending.value = false;
    }
};

const cancel = () => router.get(props.redirectUrl);
</script>

<template>
    <FormWrapper title="Datos de la tarea">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card">
                <FormInput
                    label="Título"
                    v-model="form.titulo"
                    :error="errors.titulo?.[0]"
                />
            </div>

            <div class="card">
                <FormInput
                    label="ID Proceso"
                    v-model="form.id_proceso"
                    :error="errors.id_proceso?.[0]"
                />
            </div>

            <div class="card md:col-span-2">
                <FormInput
                    label="Descripción"
                    type="textarea"
                    v-model="form.descripcion"
                    :error="errors.descripcion?.[0]"
                />
            </div>

            <div class="card">
                <FormInput
                    label="Duración (min)"
                    type="number"
                    v-model="form.duracion_minuto"
                />
            </div>

            <div class="card">
                <FormInput label="Orden" type="number" v-model="form.orden" />
            </div>

            <!-- SOLO INTEGRACIÓN -->
            <div v-if="type === 'integracion'" class="card md:col-span-2">
                <FormInput
                    label="Integración"
                    type="select"
                    v-model="form.integracion_id"
                    :options="
                        integracionesOptions.map((i) => ({
                            label: i.nombre,
                            value: i.id,
                        }))
                    "
                />
            </div>

            <!-- SOLO FASE -->
            <div v-if="type === 'tarea'" class="card md:col-span-2">
                <FormInput
                    label="Fase"
                    type="select"
                    v-model="form.fase_id"
                    :options="
                        fasesOptions.map((f) => ({
                            label: f.nombre,
                            value: f.id,
                        }))
                    "
                />
            </div>
        </div>

        <template #actions>
            <FormActions :sending="sending" @cancel="cancel" @submit="submit" />
        </template>
    </FormWrapper>

    <SuccessModal
        :show.sync="showModal"
        :message="modalMessage"
        :type="modalType"
        :auto-close="3000"
        @update:show="(v) => !v && router.get(redirectUrl)"
    />
</template>
