<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormEstimacion from "@/Components/Formulario/FormEstimacion.vue";
import CardInput from "@/Components/Formulario/CardInput.vue";
const emit = defineEmits(["next"]);

const estimacion = ref({
    tipoImplementacionId: null,
    nombreTipoImplementacion: "",
    fecha: "",
    complejidad: null,
    integraciones: [], // IDs seleccionadas
    comentarios: "",
    nombreEmpresa: "", // <-- aquí
    responsable: "", // <-- aquí
    userId: null, // <-- NUEVO
    idNegocio: "", // <-- aquí
});

const tiposImplementacion = ref([]);
const integracionesDisponibles = ref([]);
const nivelesComplejidad = ref([]);
const usuariosDisponibles = ref([]);

// Paleta de colores de respaldo para niveles desconocidos
const coloresRespaldo = [
    "#16a34a", // verde
    "#facc15", // amarillo
    "#ef4444", // rojo
    "#8b5cf6", // morado
    "#0ea5e9", // celeste
    "#f97316", // naranja
    "#db2777", // rosa
];

// Colores fijos por nombre (minúsculas)
const coloresPorNombre = {
    bajo: "#16a34a",
    medio: "#facc15",
    alto: "#ef4444",
    critico: "#8b5cf6",
};

onMounted(async () => {
    try {
        const resTipos = await axios.get("/api/tipoimplementacion");
        tiposImplementacion.value = resTipos.data.data ?? [];

        const resComplejidad = await axios.get("/api/nivel-complejidad");
        nivelesComplejidad.value = resComplejidad.data.data ?? [];

        // 🚀 Cargar usuarios
        const resUsuarios = await axios.get("/api/usuarios");
        // Asegurarse de obtener un array
        usuariosDisponibles.value = Array.isArray(resUsuarios.data)
            ? resUsuarios.data
            : (resUsuarios.data?.data ?? []);
    } catch (error) {
        console.error(error);
    }
});

watch(
    () => estimacion.value.tipoImplementacionId,
    async (nuevoTipoId) => {
        integracionesDisponibles.value = [];
        estimacion.value.integraciones = [];

        if (!nuevoTipoId) return;

        const res = await axios.get(
            `/api/tipoimplementacion/${nuevoTipoId}/integraciones`,
        );
        integracionesDisponibles.value = res.data.data ?? [];

        const tipo = tiposImplementacion.value.find(
            (t) => t.id === nuevoTipoId,
        );
        estimacion.value.nombreTipoImplementacion = tipo ? tipo.nombre : "";
    },
);

const continuar = () => {
    if (!estimacion.value.tipoImplementacionId || !estimacion.value.complejidad)
        return;

    const tipoSeleccionado = tiposImplementacion.value.find(
        (t) => t.id === Number(estimacion.value.tipoImplementacionId),
    );

    const integracionesSeleccionadas = integracionesDisponibles.value.filter(
        (i) =>
            estimacion.value.integraciones.map(Number).includes(Number(i.id)),
    );

    // Obtener el usuario seleccionado
    const usuarioSeleccionado = (usuariosDisponibles.value || []).find(
        (u) => u.id === estimacion.value.userId,
    );

    emit("next", {
        tipoImplementacionId: Number(estimacion.value.tipoImplementacionId),
        nombreTipoImplementacion: tipoSeleccionado?.nombre || "",
        nombreEmpresa: estimacion.value.nombreEmpresa, // ✅ agregado

        userId: usuarioSeleccionado?.id || null, // ID
        responsable: usuarioSeleccionado?.name || "", // Nombre

        // responsable: estimacion.value.responsable, // ✅ agregado
        idNegocio: estimacion.value.idNegocio, // ✅ agregado
        userId: estimacion.value.userId, // <-- NUEVO
        integraciones: integracionesSeleccionadas, // ✅ SOLO estas
        complejidad: estimacion.value.complejidad,
        comentarios: estimacion.value.comentarios,
    });
};

// Función para asignar color al nivel según nombre o dinámicamente
const colorNivel = (nivel, index) => {
    const nombre = nivel.nombre?.toLowerCase();
    if (coloresPorNombre[nombre]) {
        return coloresPorNombre[nombre];
    }
    // Si no está definido en coloresPorNombre, usar color de respaldo
    return coloresRespaldo[index % coloresRespaldo.length];
};
</script>

<template>
    <FormWrapper
        title="Para poder realizar la estimación es necesario llenar el formulario"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipo de Implementación -->
            <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <FormEstimacion
                        label="Tipo de implementación"
                        type="select"
                        :options="
                            tiposImplementacion.map((t) => ({
                                value: t.id,
                                label: t.nombre,
                            }))
                        "
                        v-model="estimacion.tipoImplementacionId"
                    />
                    <div
                        v-if="estimacion.tipoImplementacionId"
                        class="mt-2 text-gray-600 text-sm"
                    >
                        {{
                            tiposImplementacion.find(
                                (t) =>
                                    t.id ===
                                    Number(estimacion.tipoImplementacionId),
                            )?.descripcion || "Sin descripción disponible"
                        }}
                    </div>
                </div>
            </CardInput>
            <!-- Integraciones -->
            <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Integraciones</label
                    >
                    <ul
                        class="divide-y divide-gray-200 max-h-60 overflow-y-auto"
                    >
                        <li
                            v-for="i in integracionesDisponibles"
                            :key="i.id"
                            class="flex items-center justify-between py-2"
                        >
                            <div class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    :value="i.id"
                                    v-model="estimacion.integraciones"
                                    :id="'integracion-' + i.id"
                                    class="h-5 w-5 text-blue-600 rounded"
                                />
                                <label
                                    :for="'integracion-' + i.id"
                                    class="text-gray-800 text-sm break-words"
                                    :title="i.nombre"
                                >
                                    {{ i.nombre }}
                                </label>
                            </div>
                            <span
                                v-if="i.descripcion"
                                class="text-xs text-gray-400 italic"
                                >{{ i.descripcion }}</span
                            >
                        </li>
                    </ul>
                </div>
            </CardInput>
            <!-- Complejidad -->
            <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2"
                        >Nivel de complejidad</label
                    >
                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="(nivel, index) in nivelesComplejidad"
                            :key="nivel.id"
                            type="button"
                            @click="estimacion.complejidad = nivel"
                            :style="{
                                backgroundColor:
                                    estimacion.complejidad?.id === nivel.id
                                        ? colorNivel(nivel, index)
                                        : '#fff',
                                color:
                                    estimacion.complejidad?.id === nivel.id
                                        ? '#fff'
                                        : '#1f2937',
                                borderColor:
                                    estimacion.complejidad?.id === nivel.id
                                        ? colorNivel(nivel, index)
                                        : '#d1d5db',
                            }"
                            class="px-4 py-2 rounded-lg text-sm font-semibold border transition-colors"
                        >
                            {{ nivel.nombre }}
                        </button>
                    </div>
                    <div
                        v-if="estimacion.complejidad"
                        class="mt-2 text-gray-600 text-sm"
                    >
                        {{ estimacion.complejidad.descripcion }}
                    </div>
                </div>
            </CardInput>
            <!-- Comentarios -->
            <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <FormEstimacion
                        label="Comentarios"
                        type="textarea"
                        v-model="estimacion.comentarios"
                    />
                </div>
            </CardInput>

            <CardInput>
                <!-- Nombre de la empresa -->
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <FormEstimacion
                        label="Nombre de la empresa"
                        type="text"
                        v-model="estimacion.nombreEmpresa"
                    />
                </div>
            </CardInput>

            <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <FormEstimacion
                        label="Responsable"
                        type="select"
                        :options="
                            usuariosDisponibles.map((u) => ({
                                value: u.id,
                                label: u.name,
                            }))
                        "
                        v-model="estimacion.userId"
                    />
                </div>
            </CardInput>

            <!-- Responsable -->
            <!-- <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <FormEstimacion
                        label="Responsable"
                        type="text"
                        v-model="estimacion.responsable"
                    />
                </div>
            </CardInput> -->
            <!-- ID del negocio -->
            <CardInput>
                <div class="bg-white rounded-xl shadow p-4 mb-4">
                    <FormEstimacion
                        label="ID del negocio"
                        type="text"
                        v-model="estimacion.idNegocio"
                    />
                </div>
            </CardInput>
        </div>
        <!-- Botón Siguiente -->
        <template #actions>
            <div class="flex justify-end mt-4">
                <button
                    type="button"
                    class="px-6 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition"
                    @click="continuar"
                >
                    Siguiente
                </button>
            </div>
        </template>
    </FormWrapper>
</template>
