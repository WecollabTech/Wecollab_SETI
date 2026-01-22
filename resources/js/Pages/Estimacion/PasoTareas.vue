<script setup>
import { computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    nombreProyecto: {
        type: String,
        default: "",
    },
    bloques: {
        type: Array,
        default: () => [],
    },
    totalMinutos: {
        type: Number,
        default: 0,
    },
    totalHoras: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits(["back", "finish"]);

const horasPorBloque = (bloque) => {
    if (!bloque?.tareas) return 0;

    const total = bloque.tareas.reduce(
        (acc, t) => acc + Number(t.duracion_minuto || 0),
        0,
    );

    return Number((total / 60).toFixed(2));
};

const totalTareas = computed(() =>
    props.bloques.reduce((acc, b) => acc + (b.tareas?.length || 0), 0),
);
</script>

<template>
    <FormWrapper title="Resumen de Estimación">
        <template #headerExtra>
            <span class="text-blue-600 font-semibold">
                {{ nombreProyecto }}
            </span>
        </template>

        <!-- BLOQUES -->
        <div v-if="bloques.length" class="space-y-4 col-span-2">
            <div
                v-for="bloque in bloques"
                :key="`${bloque.tipo}-${bloque.id}`"
                class="border rounded-2xl bg-white shadow-sm"
            >
                <div
                    :class="
                        bloque.tipo === 'fase'
                            ? 'bg-blue-50 text-blue-700'
                            : 'bg-yellow-50 text-yellow-800'
                    "
                    class="px-6 py-4 rounded-t-2xl font-semibold flex justify-between"
                >
                    <span>
                        {{ bloque.nombre }}
                        <small class="ml-2 italic text-sm">
                            ({{ bloque.tipo }})
                        </small>
                    </span>
                    <span>{{ horasPorBloque(bloque) }} h</span>
                </div>

                <div class="divide-y">
                    <div
                        v-for="tarea in bloque.tareas"
                        :key="tarea.id"
                        class="px-6 py-3 flex justify-between"
                    >
                        <span>{{ tarea.titulo }}</span>
                        <span class="text-gray-500">
                            {{ tarea.duracion_minuto }} min
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- SI NO HAY DATOS -->
        <p v-else class="text-center text-gray-400 italic mt-6">
            No hay datos para mostrar
        </p>

        <!-- RESUMEN -->
        <div
            class="col-span-2 mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 shadow-sm"
        >
            <div class="flex justify-between font-semibold">
                <span>Total de tareas:</span>
                <span>{{ totalTareas }}</span>
            </div>
            <div class="flex justify-between font-semibold mt-2">
                <span>Total de minutos:</span>
                <span>{{ totalMinutos }} min</span>
            </div>
            <div
                class="flex justify-between text-lg font-bold text-blue-700 mt-2"
            >
                <span>Total de horas:</span>
                <span>{{ totalHoras }} h</span>
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
                            bloques,
                            totalMinutos,
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
