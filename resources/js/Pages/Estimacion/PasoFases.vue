<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    tipoImplementacionId: { type: Number, required: true },
    nombreProyecto: { type: String, required: true },
    complejidad: { type: Object, required: true },
});

const emit = defineEmits(["next", "back"]);

const fases = ref([]);
const faseActiva = ref(null);
const tareasSeleccionadas = ref([]);

onMounted(async () => {
    const res = await axios.get(`/api/tipoimplementacion/${props.tipoImplementacionId}/fases`);
    fases.value = res.data.data ?? [];

    fases.value.forEach(f => {
        f.tareas?.forEach(t => tareasSeleccionadas.value.push(t.id));
    });
});

// ✅ Seleccionar todo en una fase
const seleccionarTodoFase = (fase) => {
    fase.tareas?.forEach((tarea) => {
        if (!tareasSeleccionadas.value.includes(tarea.id)) {
            tareasSeleccionadas.value.push(tarea.id);
        }
    });
};

// ✅ Limpiar una fase
const limpiarFase = (fase) => {
    tareasSeleccionadas.value = tareasSeleccionadas.value.filter(
        (id) => !fase.tareas?.some((t) => t.id === id)
    );
};




const factorComplejidad = computed(() => {
    if (!props.complejidad) return 1;
    return Number(props.complejidad.factor ?? 1);
});


const totalMinutos = computed(() => {
    let total = 0;
    fases.value.forEach(fase => {
        fase.tareas?.forEach(tarea => {
            if (tareasSeleccionadas.value.includes(tarea.id)) {
                total += Number(tarea.duracion_minuto);
            }
        });
    });
    return total * factorComplejidad.value;
});

const totalHoras = computed(() =>
    Number((totalMinutos.value / 60).toFixed(2))
);

const horasPorFase = (fase) => {
    let total = 0;
    fase.tareas?.forEach(tarea => {
        if (tareasSeleccionadas.value.includes(tarea.id)) {
            total += Number(tarea.duracion_minuto);
        }
    });
    return Number(((total * factorComplejidad.value) / 60).toFixed(2));
};

const toggleFase = (id) => {
    faseActiva.value = faseActiva.value === id ? null : id;
};

// const continuar = () => {
//     const fasesFiltradas = fases.value.map((fase) => {
//         const tareasFiltradas = fase.tareas.filter((t) =>
//             tareasSeleccionadas.value.includes(t.id)
//         );

//         return {
//             ...fase,
//             tareas: tareasFiltradas,
//         };
//     });

//     emit("next", {
//         fases: fasesFiltradas,
//         totalMinutos: totalMinutos.value,
//         totalHoras: totalHoras.value,
//         tipoImplementacionId: props.tipoImplementacionId,
//         nombreTipoImplementacion: props.nombreProyecto,
//     });
// };

const continuar = () => {
    const fasesFiltradas = fases.value
        .map((fase) => {
            const tareasFiltradas =
                fase.tareas?.filter((tarea) =>
                    tareasSeleccionadas.value.includes(tarea.id)
                ) || [];

            return {
                ...fase,
                tareas: tareasFiltradas,
            };
        })
        // ✅ eliminar fases vacías
        .filter((fase) => fase.tareas.length > 0);

    emit("next", {
        fases: fasesFiltradas,
        totalMinutos: totalMinutos.value,
        totalHoras: totalHoras.value,
        tipoImplementacionId: props.tipoImplementacionId,
        nombreTipoImplementacion: props.nombreProyecto,
    });
};





</script>

<template>
 <FormWrapper v-if="complejidad" title="Fases y tareas de implementación">

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
                    class="w-full px-6 py-4 bg-blue-50 hover:bg-blue-100 transition font-semibold text-blue-700"
                    @click="toggleFase(fase.id)"
                >
                    <div class="flex items-center justify-between w-full">
                        <span>{{ fase.nombre }}</span>

                        <div class="flex items-center gap-4">
                            <span
                                class="text-sm font-semibold"
                                :class="
                                    horasPorFase(fase) == 0
                                        ? 'text-gray-400'
                                        : 'text-blue-600'
                                "
                            >
                                {{ horasPorFase(fase) }} h
                            </span>

                            <span class="text-2xl font-bold">
                                {{ faseActiva === fase.id ? "−" : "+" }}
                            </span>
                        </div>
                    </div>
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
                            <span class="text-gray-800">
                                {{ tarea.titulo }}
                            </span>
                        </div>
                        <div class="text-gray-500 font-medium">
                            {{ tarea.duracion_minuto }} min
                        </div>
                    </div>

                    <!-- ✅ ACCIONES POR FASE -->
                    <div
                        class="px-6 py-3 flex justify-end gap-3 bg-gray-50 border-t"
                    >
                        <button
                            type="button"
                            class="px-3 py-1 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                            @click.stop="seleccionarTodoFase(fase)"
                        >
                            Seleccionar todo
                        </button>

                        <button
                            type="button"
                            class="px-3 py-1 text-sm rounded bg-red-100 text-red-700 hover:bg-red-200 transition"
                            @click.stop="limpiarFase(fase)"
                        >
                            Limpiar
                        </button>
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
            class="col-span-2 mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 shadow-sm"
        >
            <div
                class="flex justify-between items-center text-lg font-bold text-blue-700"
            >
                <span>Total de horas estimadas:</span>
                <strong>{{ totalHoras }} h</strong>
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
