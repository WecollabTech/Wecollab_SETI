<script setup>
import { computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable"; // Import correcto para autoTable

const props = defineProps({
    nombreProyecto: { type: String, default: "" },
    bloquesFases: { type: Array, default: () => [] },
    bloquesIntegraciones: { type: Array, default: () => [] },
    totalHoras: { type: Number, default: 0 },
});

const emit = defineEmits(["back", "finish"]);

// Calcular horas por bloque (solo para mostrar)
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

// Generar PDF Vista Previa
const generarPDF = () => {
    const doc = new jsPDF();

    // Título principal
    doc.setFontSize(18);
    doc.setFont("helvetica", "bold");
    doc.text("Resumen de Estimación", 105, 15, { align: "center" });

    // Proyecto
    doc.setFontSize(12);
    doc.setFont("helvetica", "normal");
    doc.text(`Proyecto: ${props.nombreProyecto}`, 14, 25);

    let yOffset = 35;

    // --- Fases de Implementación ---
    bloquesFasesValidos.value.forEach((fase) => {
        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text(
            `Fase: ${fase.nombre} (${horasPorBloque(fase)} h)`,
            14,
            yOffset,
        );
        yOffset += 6;

        const tablaFase = fase.tareas.map((t) => [
            t.titulo,
            `${t.duracion_minuto} min`,
        ]);

        autoTable(doc, {
            startY: yOffset,
            head: [["Tarea", "Duración"]],
            body: tablaFase,
            theme: "grid",
            styles: { fontSize: 10 },
            headStyles: { fillColor: [52, 152, 219], textColor: 255 },
            margin: { left: 14, right: 14 },
        });

        yOffset = doc.lastAutoTable.finalY + 10;
    });

    // --- Integraciones Asociadas ---
    bloquesIntegracionesValidos.value.forEach((intg) => {
        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text(
            `Integración: ${intg.nombre} (${horasPorBloque(intg)} h)`,
            14,
            yOffset,
        );
        yOffset += 6;

        const tablaIntg = intg.tareas.map((t) => [
            t.titulo,
            `${t.duracion_minuto} min`,
        ]);

        autoTable(doc, {
            startY: yOffset,
            head: [["Tarea", "Duración"]],
            body: tablaIntg,
            theme: "grid",
            styles: { fontSize: 10 },
            headStyles: { fillColor: [241, 196, 15], textColor: 0 },
            margin: { left: 14, right: 14 },
        });

        yOffset = doc.lastAutoTable.finalY + 10;
    });

    // Total de horas
    doc.setFont("helvetica", "bold");
    doc.setFontSize(14);
    doc.text(
        `Total de horas estimadas: ${props.totalHoras} h`,
        14,
        yOffset + 5,
    );

    // Abrir PDF en nueva ventana como vista previa
    doc.output("dataurlnewwindow");
};
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
                    v-if="bloquesIntegracionesValidos.length"
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
                    @click="generarPDF"
                >
                    Vista Previa PDF
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
