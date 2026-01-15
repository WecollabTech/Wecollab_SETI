<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

// Props
const props = defineProps({
    tipoImplementacionId: {
        type: Number,
        required: true,
    },
    nombreProyecto: { type: String, required: true }, // sigue siendo el nombre del tipo de implementación
});

// Emit
const emit = defineEmits(["next", "back"]);

// Estados
const fases = ref([]);
const faseActiva = ref(null);
const tareasSeleccionadas = ref([]);

// 🔹 Costo estimado (tarifa $500 MXN por hora)
const costoEstimado = computed(() => {
    return (totalHoras.value * 500).toFixed(2);
});

// 🔹 Cargar fases con tareas
onMounted(async () => {
    try {
        const res = await axios.get(
            `/api/tipoimplementacion/${props.tipoImplementacionId}/fases`
        );
        fases.value = res.data.data ?? [];
    } catch (error) {
        console.error("Error cargando fases:", error);
    }
});

// 🔹 Abrir / cerrar fase
const toggleFase = (faseId) => {
    faseActiva.value = faseActiva.value === faseId ? null : faseId;
};

// 🔹 Total de minutos
const totalMinutos = computed(() => {
    let total = 0;
    fases.value.forEach((fase) => {
        fase.tareas?.forEach((tarea) => {
            if (tareasSeleccionadas.value.includes(tarea.id)) {
                total += Number(tarea.duracion_minuto);
            }
        });
    });
    return total;
});

// 🔹 Total en horas (con decimales)
const totalHoras = computed(() => (totalMinutos.value / 60).toFixed(2));

// 🔹 Total en formato H:M:S
const totalTiempoHMS = computed(() => {
    let horas = Math.floor(totalMinutos.value / 60);
    let minutos = totalMinutos.value % 60;
    let segundos = 0;
    return `${horas}h ${minutos}m ${segundos}s`;
});

const continuar = () => {
    // Genera un array de fases con solo las tareas seleccionadas
    const fasesSeleccionadas = fases.value.map((fase) => ({
        ...fase,
        tareas:
            fase.tareas?.filter((tarea) =>
                tareasSeleccionadas.value.includes(tarea.id)
            ) ?? [],
    }));

    emit("next", {
        fases: fasesSeleccionadas,
        totalMinutos: totalMinutos.value,
        totalHoras: totalHoras.value,
        tiempoHMS: totalTiempoHMS.value,
        tipoImplementacionId: props.tipoImplementacionId,
        nombreTipoImplementacion: props.nombreProyecto,
    });
};
</script>

<template>
    <FormWrapper title="Fases y tareas de implementación">
        <!-- Tipo de Implementación seleccionado -->
        <template #headerExtra>
            <span class="text-blue-600 font-semibold">
                {{ nombreProyecto }}
            </span>
        </template>
        <!-- LISTADO DE FASES -->
        <div class="space-y-4 col-span-2">
            <div
                v-for="fase in fases"
                :key="fase.id"
                class="border rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition"
            >
                <!-- HEADER FASE -->
                <button
                    type="button"
                    class="w-full px-6 py-4 flex justify-between items-center bg-blue-50 hover:bg-blue-100 transition font-semibold text-blue-700"
                    @click="toggleFase(fase.id)"
                >
                    <span>{{ fase.nombre }}</span>
                    <span class="text-2xl font-bold">
                        {{ faseActiva === fase.id ? "−" : "+" }}
                    </span>
                </button>

                <!-- TAREAS -->
                <div v-if="faseActiva === fase.id" class="bg-white divide-y">
                    <div
                        v-for="tarea in fase.tareas"
                        :key="tarea.id"
                        class="px-6 py-3 flex justify-between items-center hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                :value="tarea.id"
                                v-model="tareasSeleccionadas"
                                class="rounded border-gray-300"
                            />
                            <span class="text-gray-800">{{
                                tarea.titulo
                            }}</span>
                        </div>
                        <div class="text-gray-500 font-medium">
                            {{ tarea.duracion_minuto }} min
                        </div>
                    </div>

                    <div
                        v-if="!fase.tareas || fase.tareas.length === 0"
                        class="px-6 py-3 text-sm text-gray-400 italic"
                    >
                        Esta fase no tiene tareas registradas.
                    </div>
                </div>
            </div>
        </div>

        <!-- RESUMEN -->
        <div
            class="col-span-2 mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 space-y-3 shadow-sm"
        >
            <div class="flex justify-between text-sm">
                <span>Duración total (minutos):</span>
                <strong>{{ totalMinutos }} M</strong>
            </div>

            <div class="flex justify-between text-sm">
                <span>Duración total (horas):</span>
                <strong>{{ totalHoras }} h</strong>
            </div>

            <div
                class="flex justify-between items-center text-lg font-bold text-blue-700 gap-x-2"
            >
                <span>Tiempo total estimado:</span>
                <strong>{{ totalTiempoHMS }}</strong>
            </div>
        </div>

        <!-- ACCIONES -->
        <template #actions>
            <div class="flex justify-between w-full mt-4">
                <button
                    type="button"
                    class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition"
                    @click="emit('back')"
                >
                    Anterior
                </button>

                <button
                    type="button"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    @click="continuar"
                >
                    Siguiente
                </button>
            </div>
        </template>
    </FormWrapper>
</template>
