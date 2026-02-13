<script setup>
import { Head, router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import PageHeader from "@/Components/Layout/PageHeader.vue";
import FormInput from "@/Components/Formulario/FormInput.vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import CardInput from "@/Components/Formulario/CardInput.vue";
import SuccessModal from "@/Components/Modal/SuccessModal.vue";
import { ref, onMounted, watch } from "vue";
import axios from "axios";

const page = usePage();

// ===== LEER INTEGRACIÓN DESDE URL =====
const integracionFromUrl =
    page.props?.query?.integracion_id ||
    new URLSearchParams(window.location.search).get("integracion_id");

// --- FORMULARIO ---
const form = ref({
    titulo: "",
    id_proceso: "",
    descripcion: "",
    activo: true,
    duracion_minuto: null,
    orden: 1,
    url_contenido: "",
    integracion_id: integracionFromUrl ? Number(integracionFromUrl) : "",
});

// --- ERRORES ---
const errors = ref({
    url_contenido: null,
});

const integraciones = ref([]);
const sending = ref(false);
const showModal = ref(false);
const modalMessage = ref("");
const modalType = ref("success");

// --- CARGAR INTEGRACIONES ---
onMounted(async () => {
    try {
        const res = await axios.get("/api/integraciones");
        integraciones.value = res.data.data ?? [];
    } catch (err) {
        console.error("Error al cargar integraciones:", err);
    }
});

// --- GUARDAR ---
const submit = async () => {
    if (sending.value) return;

    sending.value = true;
    errors.value = {};

    try {
        const res = await axios.post("/api/integraciones_tareas", form.value);

        modalMessage.value =
            res.data.message || "Tarea registrada correctamente.";
        modalType.value = "success";
        showModal.value = true;
    } catch (error) {
        if (error.response?.status === 422) {
            errors.value = error.response.data.errors;
        } else {
            console.error(error.response?.data || error);
        }
    } finally {
        sending.value = false;
    }
};

// --- LIMPIAR ERRORES AL ESCRIBIR ---
watch(
    () => form.value.titulo,
    () => delete errors.value.titulo,
);
watch(
    () => form.value.id_proceso,
    () => delete errors.value.id_proceso,
);
watch(
    () => form.value.descripcion,
    () => delete errors.value.descripcion,
);
watch(
    () => form.value.integracion_id,
    () => delete errors.value.integracion_id,
);
watch(
    () => form.value.duracion_minuto,
    () => delete errors.value.duracion_minuto,
);
watch(
    () => form.value.orden,
    () => delete errors.value.orden,
);

// --- REDIRECCIÓN ---
const onModalClose = () => {
    router.get("/integraciones_tarea");
};

const cancel = () => {
    router.get("/integraciones");
};
</script>
<template>
    <Head title="Nueva Tarea de Integración" />

    <AppLayout>
        <template #title>
            <PageHeader title="Nueva Tarea de Integración" />
        </template>

        <FormWrapper title="Ingresa los datos de la Tarea" @submit="submit">
            <!-- ================= INFORMACIÓN ================= -->
            <div class="space-y-4">
                <h3 class="text-lg font-semibold text-gray-800">
                    Información de la tarea
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <CardInput>
                        <FormInput
                            label="Título"
                            v-model="form.titulo"
                            placeholder="Título de la tarea"
                            :error="errors.titulo?.[0]"
                        />
                    </CardInput>

                    <CardInput>
                        <FormInput
                            label="ID Proceso"
                            v-model="form.id_proceso"
                            placeholder="Id del proceso"
                            :error="errors.id_proceso?.[0]"
                        />
                    </CardInput>

                    <CardInput>
                        <FormInput
                            label="Descripción"
                            type="textarea"
                            v-model="form.descripcion"
                            placeholder="Descripción de la tarea"
                            :error="errors.descripcion?.[0]"
                        />
                    </CardInput>

                    <CardInput>
                        <FormInput
                            label="Integración"
                            type="select"
                            v-model="form.integracion_id"
                            :disabled="integracionFromUrl"
                            :options="
                                integraciones.map((i) => ({
                                    label: i.nombre,
                                    value: i.id,
                                }))
                            "
                            :error="errors.integracion_id?.[0]"
                        />
                    </CardInput>
                </div>
            </div>

            <!-- ================= CONFIGURACIÓN ================= -->
            <div class="space-y-4 mt-8">
                <h3 class="text-lg font-semibold text-gray-800">
                    Configuración
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <CardInput>
                        <FormInput
                            label="Duración (minutos)"
                            type="number"
                            v-model="form.duracion_minuto"
                            :error="errors.duracion_minuto?.[0]"
                        />
                    </CardInput>

                    <CardInput>
                        <FormInput
                            label="Orden"
                            type="number"
                            v-model="form.orden"
                            :error="errors.orden?.[0]"
                        />
                    </CardInput>

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
                    <!-- URL de Contenido -->
                    <div class="card md:col-span-2">
                        <FormInput
                            label="URL de Contenido"
                            v-model="form.url_contenido"
                            :error="errors.url_contenido"
                            type="url"
                            placeholder="https://youtube.com/... / https://drive.google.com/... / https://loom.com/..."
                        />
                        <p class="text-xs text-gray-400 mt-1">
                            Puedes ingresar link de YouTube, Loom, Drive o
                            archivo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- ACCIONES -->
            <template #actions>
                <div
                    class="flex flex-col md:flex-row justify-center md:justify-end gap-4 mt-10"
                >
                    <button
                        type="button"
                        @click="cancel"
                        class="px-6 py-2.5 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
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
