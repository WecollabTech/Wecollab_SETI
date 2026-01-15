<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";

import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import FormEstimacion from "@/Components/Formulario/FormEstimacion.vue";

const emit = defineEmits(["next"]);

// --- FORMULARIO ---
const estimacion = ref({
    tipoImplementacionId: null, // ahora siempre Number
    nombreTipoImplementacion: "", // para mostrar el nombre
    fecha: "",
    complejidad: "",
    integraciones: [],
    comentarios: "",
});

// --- LISTAS ---
const tiposImplementacion = ref([]);
const integracionesDisponibles = ref([]);

// --- Cargar tipos ---
onMounted(async () => {
    try {
        const res = await axios.get("/api/tipoimplementacion");
        tiposImplementacion.value = res.data.data ?? [];
    } catch (error) {
        console.error("Error cargando tipos de implementación:", error);
    }
});

// --- Cargar integraciones según tipo ---
watch(
    () => estimacion.value.tipoImplementacionId,
    async (nuevoTipoId) => {
        integracionesDisponibles.value = [];
        estimacion.value.integraciones = [];

        if (!nuevoTipoId) return;

        try {
            const res = await axios.get(
                `/api/tipoimplementacion/${nuevoTipoId}/integraciones`
            );
            integracionesDisponibles.value = res.data.data ?? [];
        } catch (error) {
            console.error("Error cargando integraciones:", error);
        }

        // Guardar el nombre del tipo de implementación seleccionado
        const tipo = tiposImplementacion.value.find(
            (t) => t.id === nuevoTipoId
        );
        estimacion.value.nombreTipoImplementacion = tipo ? tipo.nombre : "";
    }
);

// --- CONTINUAR ---
const continuar = () => {
    if (!estimacion.value.tipoImplementacionId) return;

    const tipoSeleccionado = tiposImplementacion.value.find(
        (t) => t.id === Number(estimacion.value.tipoImplementacionId)
    );

    emit("next", {
        tipoImplementacionId: Number(estimacion.value.tipoImplementacionId),
        nombreTipoImplementacion: tipoSeleccionado?.nombre || "",
        integraciones: estimacion.value.integraciones,
        complejidad: estimacion.value.complejidad,
        comentarios: estimacion.value.comentarios,
    });
};
</script>

<template>
    <FormWrapper
        title="Para poder realizar la estimación es necesario llenar el formulario"
    >
        <!-- Tipo de implementación -->
        <div class="flex flex-col">
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

            <!-- Descripción de la implementación -->
            <div v-if="estimacion.tipoImplementacionId" class="mt-1">
                <span class="text-gray-900 text-xs font-bold">
                    Descripción de la Implementación:
                </span>

                <p class="text-gray-600 text-sm ml-1 text-justify">
                    {{
                        tiposImplementacion.find(
                            (t) =>
                                t.id === Number(estimacion.tipoImplementacionId)
                        )?.descripcion || "Sin descripción disponible"
                    }}
                </p>
            </div>
        </div>

        <!-- Integraciones -->
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Integraciones
            </label>

            <div v-if="estimacion.tipoImplementacionId">
                <FormEstimacion
                    type="checkbox"
                    :options="
                        integracionesDisponibles.map((i) => ({
                            value: i.id,
                            label: i.nombre,
                        }))
                    "
                    v-model="estimacion.integraciones"
                />
            </div>

            <p v-else class="text-gray-400 text-sm italic">
                Selecciona primero un tipo de implementación.
            </p>
        </div>
        <!-- Complejidad -->
        <div class="mt-1">
            <label class="block text-gray-700 font-medium mb-1">
                Nivel de complejidad
            </label>

            <div class="flex gap-2">
                <button
                    v-for="nivel in ['Baja', 'Media', 'Alta']"
                    :key="nivel"
                    type="button"
                    @click="estimacion.complejidad = nivel"
                    :class="[
                        'w-24 h-10 rounded-lg text-sm font-semibold',
                        // Cambiar color de fondo según selección
                        estimacion.complejidad === nivel
                            ? nivel === 'Baja'
                                ? 'bg-green-500 text-white'
                                : nivel === 'Media'
                                ? 'bg-yellow-400 text-white'
                                : 'bg-red-600 text-white'
                            : 'bg-white border text-gray-800',
                    ]"
                >
                    {{ nivel }}
                </button>
            </div>

            <!-- Descripción de la complejidad -->
            <div v-if="estimacion.complejidad" class="mt-1">
                <span class="text-gray-900 text-xs font-medium"
                    >Rubrica de Complejidad:</span
                >
                <p class="text-gray-600 text-sm ml-1">
                    {{
                        {
                            Baja: "Requiere poco esfuerzo y pocas tareas.",
                            Media: "Requiere un esfuerzo moderado y varias tareas.",
                            Alta: "Requiere mucho esfuerzo y muchas tareas complejas.",
                        }[estimacion.complejidad]
                    }}
                </p>
            </div>
        </div>

        <!-- Comentarios -->
        <FormEstimacion
            label="Comentarios"
            type="textarea"
            v-model="estimacion.comentarios"
        />

        <!-- BOTONES -->
        <template #actions>
            <button
                type="button"
                class="px-6 py-2.5 bg-blue-600 text-white rounded-lg"
                @click="continuar"
            >
                Siguiente
            </button>
        </template>
    </FormWrapper>
</template>
