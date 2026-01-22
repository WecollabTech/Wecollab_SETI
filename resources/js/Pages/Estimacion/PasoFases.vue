<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    tipoImplementacionId: Number,
    nombreProyecto: String,
    complejidad: Object,
    integracionesSeleccionadas: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(["next", "back"]);

const fases = ref([]);
const integraciones = ref([]);
const faseActiva = ref(null);
const integracionActiva = ref(null);
const tareasSeleccionadas = ref([]);

// 🔹 Cargar fases + integraciones
onMounted(async () => {
    try {
        // 1️⃣ FASES con tareas (YA FUNCIONA)
        const resFases = await axios.get(
            `/api/tipoimplementacion/${props.tipoImplementacionId}/fases`,
        );

        fases.value = resFases.data.data ?? [];

        // 2️⃣ INTEGRACIONES con tareas (ENDPOINT NUEVO)
        const resIntegraciones = await axios.get(
            `/api/tipoimplementacion/${props.tipoImplementacionId}/integraciones-tareas`,
        );

        const integracionesBackend = resIntegraciones.data.data ?? [];

        // 3️⃣ SOLO dejar las integraciones seleccionadas
        const idsSeleccionados = props.integracionesSeleccionadas.map(
            (i) => i.id,
        );

        integraciones.value = integracionesBackend.filter((i) =>
            idsSeleccionados.includes(i.id),
        );

        // 4️⃣ Preseleccionar todas las tareas (fases + integraciones)
        [...fases.value, ...integraciones.value].forEach((bloque) => {
            bloque.tareas?.forEach((t) => {
                if (!tareasSeleccionadas.value.includes(t.id)) {
                    tareasSeleccionadas.value.push(t.id);
                }
            });
        });
    } catch (error) {
        console.error("Error cargando fases e integraciones:", error);
    }
});

// ✅ Seleccionar todo
const seleccionarTodo = (bloque) => {
    bloque.tareas?.forEach((t) => {
        if (!tareasSeleccionadas.value.includes(t.id))
            tareasSeleccionadas.value.push(t.id);
    });
};

// ✅ Limpiar tareas
const limpiarTareas = (bloque) => {
    tareasSeleccionadas.value = tareasSeleccionadas.value.filter(
        (id) => !bloque.tareas?.some((t) => t.id === id),
    );
};

// Factor de complejidad
const factorComplejidad = computed(() =>
    Number(props.complejidad?.factor ?? 1),
);

// Total minutos
const totalMinutos = computed(() => {
    let total = 0;
    [...fases.value, ...integraciones.value].forEach((b) => {
        b.tareas?.forEach((t) => {
            if (tareasSeleccionadas.value.includes(t.id))
                total += Number(t.duracion_minuto);
        });
    });
    return total * factorComplejidad.value;
});

const totalHoras = computed(() => Number((totalMinutos.value / 60).toFixed(2)));

// Horas por bloque
const horasPorBloque = (bloque) => {
    let total = 0;
    bloque.tareas?.forEach((t) => {
        if (tareasSeleccionadas.value.includes(t.id))
            total += Number(t.duracion_minuto);
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
    const bloques = [...fases.value, ...integraciones.value]
        .map((b) => ({
            id: b.id,
            nombre: b.nombre,
            tipo: integraciones.value.some((i) => i.id === b.id)
                ? "integracion"
                : "fase",
            tareas: b.tareas.filter((t) =>
                tareasSeleccionadas.value.includes(t.id),
            ),
        }))
        .filter((b) => b.tareas.length > 0);

    emit("next", {
        bloques,
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

        <!-- SECCIÓN FASES -->
        <h1 class="mt-4 mb-2 font-bold text-lg">Fases de Implementación</h1>
        <div class="space-y-4 col-span-2">
            <div
                v-for="fase in fases"
                :key="fase.id"
                class="border rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition"
            >
                <button
                    type="button"
                    class="w-full px-6 py-4 bg-blue-50 hover:bg-blue-100 transition font-semibold text-blue-700"
                    @click="toggleFase(fase.id)"
                >
                    <div class="flex items-center justify-between w-full">
                        <span>{{ fase.nombre }}</span>
                        <div class="flex items-center gap-4">
                            <span
                                :class="
                                    horasPorBloque(fase) == 0
                                        ? 'text-gray-400'
                                        : 'text-blue-600'
                                "
                                class="text-sm font-semibold"
                            >
                                {{ horasPorBloque(fase) }} h
                            </span>
                            <span class="text-2xl font-bold">{{
                                faseActiva === fase.id ? "−" : "+"
                            }}</span>
                        </div>
                    </div>
                </button>

                <div v-if="faseActiva === fase.id" class="bg-white divide-y">
                    <div
                        v-for="tarea in fase.tareas"
                        :key="tarea.id"
                        class="px-6 py-3 flex justify-between items-center hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                v-model="tareasSeleccionadas"
                                :value="tarea.id"
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
                        class="px-6 py-3 flex justify-end gap-3 bg-gray-50 border-t"
                    >
                        <button
                            type="button"
                            class="px-3 py-1 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 transition"
                            @click.stop="seleccionarTodo(fase)"
                        >
                            Seleccionar todo
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1 text-sm rounded bg-red-100 text-red-700 hover:bg-red-200 transition"
                            @click.stop="limpiarTareas(fase)"
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

        <!-- SECCIÓN INTEGRACIONES -->
        <h1 class="mt-6 mb-2 font-bold text-lg">Integraciones Asociadas</h1>
        <div class="space-y-4 col-span-2">
            <div
                v-for="intg in integraciones"
                :key="intg.id"
                class="border rounded-2xl overflow-hidden bg-white shadow-sm hover:shadow-md transition"
            >
                <button
                    type="button"
                    class="w-full px-6 py-4 bg-yellow-50 hover:bg-yellow-100 transition font-semibold text-yellow-800"
                    @click="toggleIntegracion(intg.id)"
                >
                    <div class="flex items-center justify-between w-full">
                        <span>{{ intg.nombre }}</span>
                        <div class="flex items-center gap-4">
                            <span
                                :class="
                                    horasPorBloque(intg) == 0
                                        ? 'text-gray-400'
                                        : 'text-yellow-800'
                                "
                                class="text-sm font-semibold"
                            >
                                {{ horasPorBloque(intg) }} h
                            </span>
                            <span class="text-2xl font-bold">{{
                                integracionActiva === intg.id ? "−" : "+"
                            }}</span>
                        </div>
                    </div>
                </button>

                <div
                    v-if="integracionActiva === intg.id"
                    class="bg-white divide-y"
                >
                    <div
                        v-for="tarea in intg.tareas"
                        :key="tarea.id"
                        class="px-6 py-3 flex justify-between items-center hover:bg-gray-50 transition"
                    >
                        <div class="flex items-center gap-3">
                            <input
                                type="checkbox"
                                v-model="tareasSeleccionadas"
                                :value="tarea.id"
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
                        class="px-6 py-3 flex justify-end gap-3 bg-gray-50 border-t"
                    >
                        <button
                            type="button"
                            class="px-3 py-1 text-sm rounded bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition"
                            @click.stop="seleccionarTodo(intg)"
                        >
                            Seleccionar todo
                        </button>
                        <button
                            type="button"
                            class="px-3 py-1 text-sm rounded bg-red-100 text-red-700 hover:bg-red-200 transition"
                            @click.stop="limpiarTareas(intg)"
                        >
                            Limpiar
                        </button>
                    </div>

                    <div
                        v-if="!intg.tareas || intg.tareas.length === 0"
                        class="px-6 py-3 text-sm text-gray-400 italic"
                    >
                        Esta integración no tiene tareas registradas.
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
