<script setup>
import { computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    nombreProyecto: { type: String, default: "" },
    bloquesFases: { type: Array, default: () => [] },
    bloquesIntegraciones: { type: Array, default: () => [] },
    totalHoras: { type: Number, default: 0 },
});

const emit = defineEmits(["back", "finish"]);

// Calcular horas por bloque
const horasPorBloque = (bloque) => {
    if (!bloque?.tareas || !bloque.tareas.length) return 0;
    const total = bloque.tareas.reduce(
        (acc, t) => acc + Number(t.duracion_minuto || 0),
        0,
    );
    return Number((total / 60).toFixed(2));
};

// Filtrar bloques que tengan tareas
const bloquesFasesValidos = computed(() =>
    props.bloquesFases.filter((b) => b.tareas && b.tareas.length),
);
const bloquesIntegracionesValidos = computed(() =>
    props.bloquesIntegraciones.filter((b) => b.tareas && b.tareas.length),
);
</script>

<template>
    <FormWrapper title="">
        <template #headerExtra>
            <div class="bg-gray-50 border rounded-xl p-4 shadow-sm w-full mt-2">
                <div class="text-lg font-bold text-blue-700">
                    Resumen de Estimación
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    Tipo de implementación:
                    <span class="font-semibold text-blue-600">{{
                        nombreProyecto
                    }}</span>
                </div>
                <div
                    v-if="
                        bloquesIntegracionesValidos &&
                        bloquesIntegracionesValidos.length
                    "
                    class="text-sm text-gray-700 mt-1"
                >
                    Integraciones seleccionadas:
                    <span class="font-semibold text-blue-600">
                        {{
                            bloquesIntegracionesValidos
                                .map((i) => i.nombre)
                                .join(", ")
                        }}
                    </span>
                </div>
            </div>
        </template>

        <!-- FASES -->
        <h2
            v-if="bloquesFasesValidos.length"
            class="mt-4 mb-2 font-bold text-lg"
        >
            Fases de Implementación
        </h2>
        <div v-if="bloquesFasesValidos.length" class="space-y-4 col-span-2">
            <div
                v-for="fase in bloquesFasesValidos"
                :key="`fase-${fase.id}`"
                class="border rounded-2xl bg-white shadow-md hover:shadow-lg transition"
            >
                <div
                    class="px-6 py-4 bg-blue-50 text-blue-700 rounded-t-2xl font-semibold flex justify-between"
                >
                    <span>{{ fase.nombre }}</span>
                    <span>{{ horasPorBloque(fase) }} h</span>
                </div>
                <div class="divide-y">
                    <div
                        v-for="tarea in fase.tareas"
                        :key="tarea.tarea_id"
                        class="px-6 py-3 flex justify-between hover:bg-gray-50 transition"
                    >
                        <span>{{ tarea.titulo }}</span>
                        <span class="text-gray-500"
                            >{{ tarea.duracion_minuto }} min</span
                        >
                    </div>
                </div>
            </div>
        </div>
        <p v-else class="text-center text-gray-400 italic mt-2">
            No hay fases registradas
        </p>

        <!-- INTEGRACIONES -->
        <h2
            v-if="bloquesIntegracionesValidos.length"
            class="mt-6 mb-2 font-bold text-lg"
        >
            Integraciones Asociadas
        </h2>
        <div
            v-if="bloquesIntegracionesValidos.length"
            class="space-y-4 col-span-2"
        >
            <div
                v-for="intg in bloquesIntegracionesValidos"
                :key="`intg-${intg.id}`"
                class="border rounded-2xl bg-white shadow-md hover:shadow-lg transition"
            >
                <div
                    class="px-6 py-4 bg-yellow-50 text-yellow-800 rounded-t-2xl font-semibold flex justify-between"
                >
                    <span>{{ intg.nombre }}</span>
                    <span>{{ horasPorBloque(intg) }} h</span>
                </div>
                <div class="divide-y">
                    <div
                        v-for="tarea in intg.tareas"
                        :key="tarea.tarea_id"
                        class="px-6 py-3 flex justify-between hover:bg-gray-50 transition"
                    >
                        <span>{{ tarea.titulo }}</span>
                        <span class="text-gray-500"
                            >{{ tarea.duracion_minuto }} min</span
                        >
                    </div>
                </div>
            </div>
        </div>
        <p v-else class="text-center text-gray-400 italic mt-2">
            No hay integraciones registradas
        </p>

        <!-- RESUMEN CENTRADO Y AMPLIADO -->
        <div
            class="col-span-2 mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 shadow-sm"
        >
            <div class="text-center text-lg font-bold text-blue-700">
                Total de horas de implementación: {{ totalHoras }} h
            </div>
        </div>

        <!-- ACCIONES -->
        <template #actions>
            <div class="flex justify-between w-full mt-4">
                <button
                    type="button"
                    class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
                    @click="emit('back')"
                >
                    Anterior
                </button>
                <button
                    type="button"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                    @click="
                        emit('finish', {
                            bloquesFases,
                            bloquesIntegraciones,
                            totalHoras,
                        })
                    "
                >
                    Confirmar Estimación
                </button>
            </div>
        </template>
    </FormWrapper>
</template>
