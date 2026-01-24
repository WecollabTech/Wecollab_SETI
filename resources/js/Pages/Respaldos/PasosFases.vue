<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    tipoImplementacionId: Number,
    nombreProyecto: String,
    complejidad: Object,
    integracionesSeleccionadas: { type: Array, required: true },
});

const emit = defineEmits(["next", "back"]);

const fases = ref([]);
const integraciones = ref([]);
const faseActiva = ref(null);
const integracionActiva = ref(null);

// Mantener selección separada
const tareasSeleccionadasFases = ref([]);
const tareasSeleccionadasIntegraciones = ref([]);

// Cargar fases + integraciones
onMounted(async () => {
    try {
        const resFases = await axios.get(
            `/api/tipoimplementacion/${props.tipoImplementacionId}/fases`,
        );
        fases.value = resFases.data.data ?? [];

        const resIntegraciones = await axios.get(
            `/api/tipoimplementacion/${props.tipoImplementacionId}/integraciones-tareas`,
        );
        const integracionesBackend = resIntegraciones.data.data ?? [];

        const idsSeleccionados = props.integracionesSeleccionadas.map(
            (i) => i.id,
        );
        integraciones.value = integracionesBackend.filter((i) =>
            idsSeleccionados.includes(i.id),
        );

        // Preseleccionar tareas de fases
        fases.value.forEach((fase) => {
            fase.tareas?.forEach((t) => {
                tareasSeleccionadasFases.value.push(t.id);
            });
        });

        // Preseleccionar tareas de integraciones
        integraciones.value.forEach((intg) => {
            intg.tareas?.forEach((t) => {
                tareasSeleccionadasIntegraciones.value.push(t.id);
            });
        });
    } catch (error) {
        console.error("Error cargando fases e integraciones:", error);
    }
});

// Seleccionar / limpiar tareas
const seleccionarTodo = (bloque, tipo) => {
    const sel =
        tipo === "fase"
            ? tareasSeleccionadasFases
            : tareasSeleccionadasIntegraciones;
    bloque.tareas?.forEach((t) => {
        if (!sel.value.includes(t.id)) sel.value.push(t.id);
    });
};
const limpiarTareas = (bloque, tipo) => {
    const sel =
        tipo === "fase"
            ? tareasSeleccionadasFases
            : tareasSeleccionadasIntegraciones;
    sel.value = sel.value.filter(
        (id) => !bloque.tareas?.some((t) => t.id === id),
    );
};

// Factor de complejidad
const factorComplejidad = computed(() =>
    Number(props.complejidad?.factor ?? 1),
);

const totalMinutos = computed(() => {
    let total = 0;
    fases.value.forEach((b) => {
        b.tareas?.forEach((t) => {
            if (tareasSeleccionadasFases.value.includes(t.id))
                total += Number(t.duracion_minuto);
        });
    });
    integraciones.value.forEach((b) => {
        b.tareas?.forEach((t) => {
            if (tareasSeleccionadasIntegraciones.value.includes(t.id))
                total += Number(t.duracion_minuto);
        });
    });
    return total * factorComplejidad.value;
});
const totalHoras = computed(() => Number((totalMinutos.value / 60).toFixed(2)));

// Horas por bloque
const horasPorBloque = (bloque, tipo) => {
    const sel =
        tipo === "fase"
            ? tareasSeleccionadasFases.value
            : tareasSeleccionadasIntegraciones.value;
    let total = 0;
    bloque.tareas?.forEach((t) => {
        if (sel.includes(t.id)) total += Number(t.duracion_minuto);
    });
    return Number(((total * factorComplejidad.value) / 60).toFixed(2));
};

// Alternar bloques
const toggleFase = (id) =>
    (faseActiva.value = faseActiva.value === id ? null : id);
const toggleIntegracion = (id) =>
    (integracionActiva.value = integracionActiva.value === id ? null : id);

// Continuar
const continuar = () => {
    const bloquesFases = fases.value
        .map((b) => ({
            id: b.id,
            nombre: b.nombre,
            tareas: b.tareas.filter((t) =>
                tareasSeleccionadasFases.value.includes(t.id),
            ),
        }))
        .filter((b) => b.tareas.length > 0);

    const bloquesIntegraciones = integraciones.value
        .map((b) => ({
            id: b.id,
            nombre: b.nombre,
            tareas: b.tareas.filter((t) =>
                tareasSeleccionadasIntegraciones.value.includes(t.id),
            ),
        }))
        .filter((b) => b.tareas.length > 0);

    emit("next", {
        bloquesFases,
        bloquesIntegraciones,
        totalMinutos: totalMinutos.value,
        totalHoras: totalHoras.value,
    });
};
</script>

<template>
    <FormWrapper v-if="complejidad" title="Fases e Integraciones">
        <template #headerExtra>
            <span class="text-blue-600 font-semibold">{{
                nombreProyecto
            }}</span>
        </template>

        <!-- FASES -->
        <h1 class="mt-4 mb-2 font-bold text-lg">Fases de Implementación</h1>
        <div class="space-y-4 col-span-2">
            <div
                v-for="fase in fases"
                :key="fase.id"
                class="border rounded-2xl bg-white shadow-sm hover:shadow-md transition"
            >
                <button
                    type="button"
                    class="w-full px-6 py-4 bg-blue-50 hover:bg-blue-100 font-semibold text-blue-700"
                    @click="toggleFase(fase.id)"
                >
                    <div class="flex justify-between items-center w-full">
                        <span>{{ fase.nombre }}</span>
                        <span>{{ horasPorBloque(fase, "fase") }} h</span>
                    </div>
                </button>

                <div v-if="faseActiva === fase.id" class="divide-y">
                    <div
                        v-for="tarea in fase.tareas"
                        :key="tarea.id"
                        class="flex justify-between px-6 py-3 items-center hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                v-model="tareasSeleccionadasFases"
                                :value="tarea.id"
                                class="rounded border-gray-300"
                            />
                            <span>{{ tarea.titulo }}</span>
                        </div>
                        <span class="text-gray-500"
                            >{{ tarea.duracion_minuto }} min</span
                        >
                    </div>

                    <div
                        class="flex justify-end gap-3 px-6 py-3 bg-gray-50 border-t"
                    >
                        <button
                            @click.stop="seleccionarTodo(fase, 'fase')"
                            class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200"
                        >
                            Seleccionar todo
                        </button>
                        <button
                            @click.stop="limpiarTareas(fase, 'fase')"
                            class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                        >
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- INTEGRACIONES -->
        <h1 class="mt-6 mb-2 font-bold text-lg">Integraciones Asociadas</h1>
        <div class="space-y-4 col-span-2">
            <div
                v-for="intg in integraciones"
                :key="intg.id"
                class="border rounded-2xl bg-white shadow-sm hover:shadow-md transition"
            >
                <button
                    type="button"
                    class="w-full px-6 py-4 bg-yellow-50 hover:bg-yellow-100 font-semibold text-yellow-800"
                    @click="toggleIntegracion(intg.id)"
                >
                    <div class="flex justify-between items-center w-full">
                        <span>{{ intg.nombre }}</span>
                        <span>{{ horasPorBloque(intg, "integracion") }} h</span>
                    </div>
                </button>

                <div v-if="integracionActiva === intg.id" class="divide-y">
                    <div
                        v-for="tarea in intg.tareas"
                        :key="tarea.id"
                        class="flex justify-between px-6 py-3 items-center hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                v-model="tareasSeleccionadasIntegraciones"
                                :value="tarea.id"
                                class="rounded border-gray-300"
                            />
                            <span>{{ tarea.titulo }}</span>
                        </div>
                        <span class="text-gray-500"
                            >{{ tarea.duracion_minuto }} min</span
                        >
                    </div>

                    <div
                        class="flex justify-end gap-3 px-6 py-3 bg-gray-50 border-t"
                    >
                        <button
                            @click.stop="seleccionarTodo(intg, 'integracion')"
                            class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded hover:bg-yellow-200"
                        >
                            Seleccionar todo
                        </button>
                        <button
                            @click.stop="limpiarTareas(intg, 'integracion')"
                            class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200"
                        >
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RESUMEN -->
        <div
            class="col-span-2 mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 shadow-sm text-center font-bold text-blue-700"
        >
            Total de horas estimadas: {{ totalHoras }} h
        </div>

        <!-- ACCIONES -->
        <template #actions>
            <div class="flex justify-between w-full mt-4">
                <button
                    class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
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
