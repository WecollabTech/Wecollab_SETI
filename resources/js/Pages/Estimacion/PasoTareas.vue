<script setup>
import { ref, computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    fases: {
        type: Array,
        required: true,
    },
    tipoImplementacionId: {
        type: Number,
        required: true,
    },
    nombreTipoImplementacion: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(["back", "finish"]);

const tareasSeleccionadas = ref([]);

// Inicializar tareas seleccionadas al cargar el componente
props.fases.forEach((fase) => {
    fase.tareas?.forEach((tarea) => {
        tareasSeleccionadas.value.push(tarea.id);
    });
});

// Computed: total de minutos
const totalMinutos = computed(() => {
    let total = 0;
    props.fases.forEach((fase) => {
        fase.tareas?.forEach((tarea) => {
            if (tareasSeleccionadas.value.includes(tarea.id)) {
                total += Number(tarea.duracion_minuto);
            }
        });
    });
    return total;
});

// Computed: total en horas
const totalHoras = computed(() => (totalMinutos.value / 60).toFixed(2));

// Computed: tiempo en H:M:S
const totalTiempoHMS = computed(() => {
    const horas = Math.floor(totalMinutos.value / 60);
    const minutos = totalMinutos.value % 60;
    const segundos = 0;
    return `${horas}h ${minutos}m ${segundos}s`;
});

// Finalizar
const finalizar = () => {
    emit("finish", {
        tareas: tareasSeleccionadas.value,
        totalMinutos: totalMinutos.value,
        totalHoras: totalHoras.value,
        tiempoHMS: totalTiempoHMS.value,
        tipoImplementacionId: props.tipoImplementacionId,
        nombreTipoImplementacion: props.nombreTipoImplementacion,
    });
};
</script>

<template>
    <FormWrapper title="Revisión final de tareas">
        <div
            v-for="fase in fases"
            :key="fase.id"
            class="mb-4 border rounded-xl p-4 bg-white shadow-sm"
        >
            <h3 class="font-semibold text-blue-700 mb-2">{{ fase.nombre }}</h3>

            <div
                v-for="tarea in fase.tareas"
                :key="tarea.id"
                class="flex justify-between items-center py-1 px-2 hover:bg-gray-50 rounded"
            >
                <div class="flex items-center gap-3">
                    <input
                        type="checkbox"
                        v-model="tareasSeleccionadas"
                        :value="tarea.id"
                    />
                    <span>{{ tarea.titulo }}</span>
                </div>
                <div class="text-gray-500">{{ tarea.duracion_minuto }} min</div>
            </div>
        </div>

        <!-- RESUMEN -->
        <div
            class="mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 space-y-3 shadow-sm"
        >
            <div class="flex justify-between">
                <span>Total minutos:</span>
                <strong>{{ totalMinutos }} M</strong>
            </div>
            <div class="flex justify-between">
                <span>Total horas:</span>
                <strong>{{ totalHoras }} h</strong>
            </div>
            <div class="flex justify-between">
                <span>Tiempo total:</span>
                <strong>{{ totalTiempoHMS }}</strong>
            </div>
        </div>

        <!-- BOTONES -->
        <template #actions>
            <div class="flex justify-between w-full mt-4">
                <button
                    @click="emit('back')"
                    class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
                >
                    Anterior
                </button>
                <button
                    @click="finalizar"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Finalizar
                </button>
            </div>
        </template>
    </FormWrapper>
</template>
