<script setup>
import { computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

const props = defineProps({
    estimacion: Object,
    nombreProyecto: { type: String, default: "" },
    bloquesFases: { type: Array, default: () => [] },
    bloquesIntegraciones: { type: Array, default: () => [] },
    totalHoras: { type: Number, default: 0 },
    totalMinutos: { type: Number, default: 0 },
    isSaving: { type: Boolean, default: false },
    usuariosDisponibles: { type: Array, default: () => [] },
});

const emit = defineEmits(["back", "finish"]);

/* ================= HELPERS ================= */
const horasPorBloque = (bloque) => {
    if (!bloque?.tareas?.length) return 0;
    const total = bloque.tareas.reduce(
        (acc, t) => acc + Number(t.duracion_minuto || 0),
        0,
    );
    return Number((total / 60).toFixed(2));
};

const bloquesFasesValidos = computed(() =>
    props.bloquesFases.filter((b) => b.tareas?.length),
);

const bloquesIntegracionesValidos = computed(() =>
    props.bloquesIntegraciones.filter((b) => b.tareas?.length),
);

// ✅ Computed para obtener el usuario seleccionado
const usuarioSeleccionado = computed(() =>
    props.usuariosDisponibles.find(
        (u) => Number(u.id) === Number(props.estimacion.userId),
    ),
);

/* ================= PDF ================= */
const generarPDF = () => {
    const logo = new Image();
    logo.src = "/img/logo.png";

    logo.onload = () => {
        const doc = new jsPDF();

        // HEADER
        doc.addImage(logo, "PNG", 14, 12, 36, 18);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        doc.text("Resumen de Estimación", 105, 22, { align: "center" });
        doc.setDrawColor(200);
        doc.line(14, 36, 196, 36);

        // PROYECTO
        doc.setFont("helvetica", "normal");
        doc.setFontSize(12);
        doc.text(`Proyecto: ${props.nombreProyecto}`, 14, 45);

        let yOffset = 55;

        // FASES
        bloquesFasesValidos.value.forEach((fase) => {
            doc.setFont("helvetica", "bold");
            doc.setFontSize(14);
            doc.text(`${fase.nombre} (${horasPorBloque(fase)} h)`, 14, yOffset);
            yOffset += 6;

            autoTable(doc, {
                startY: yOffset,
                head: [["Tarea", "Duración"]],
                body: fase.tareas.map((t) => [
                    t.titulo,
                    `${t.duracion_minuto} min`,
                ]),
                theme: "grid",
                styles: { fontSize: 10 },
                headStyles: { fillColor: [52, 152, 219], textColor: 255 },
                margin: { left: 14, right: 14 },
            });

            yOffset = doc.lastAutoTable.finalY + 10;
        });

        // INTEGRACIONES
        bloquesIntegracionesValidos.value.forEach((intg) => {
            doc.setFont("helvetica", "bold");
            doc.setFontSize(14);
            doc.text(`${intg.nombre} (${horasPorBloque(intg)} h)`, 14, yOffset);
            yOffset += 6;

            autoTable(doc, {
                startY: yOffset,
                head: [["Tarea", "Duración"]],
                body: intg.tareas.map((t) => [
                    t.titulo,
                    `${t.duracion_minuto} min`,
                ]),
                theme: "grid",
                styles: { fontSize: 10 },
                headStyles: { fillColor: [241, 196, 15], textColor: 0 },
                margin: { left: 14, right: 14 },
            });

            yOffset = doc.lastAutoTable.finalY + 10;
        });

        // TOTAL
        doc.rect(14, yOffset, 182, 18);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(16);
        doc.text(`Total: ${props.totalHoras} h`, 105, yOffset + 12, {
            align: "center",
        });

        doc.output("bloburl", { filename: "estimacion.pdf" });
        window.open(doc.output("bloburl"), "_blank");
    };
};
</script>

<template>
    <FormWrapper title="">
        <!-- HEADER -->
        <template #headerExtra>
            <div class="bg-gray-50 border rounded-xl p-4 shadow-sm mt-2">
                <div class="text-lg font-bold text-blue-700">
                    Resumen de Estimación
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    Tipo de implementación:
                    <span class="font-semibold text-blue-600">
                        {{ nombreProyecto }}
                    </span>
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    Responsable:
                    <span class="font-semibold text-blue-600">
                        {{ usuarioSeleccionado?.name || "No asignado" }}
                    </span>
                </div>
            </div>
        </template>

        <!-- CONTENIDO -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
            <!-- FASES -->
            <div v-if="bloquesFasesValidos.length">
                <h2 class="mb-3 font-bold text-lg">Fases de Implementación</h2>

                <div class="space-y-4">
                    <div
                        v-for="fase in bloquesFasesValidos"
                        :key="fase.id"
                        class="border rounded-2xl bg-white shadow"
                    >
                        <div
                            class="px-6 py-4 bg-blue-50 text-blue-700 font-semibold flex justify-between rounded-t-2xl"
                        >
                            <span>{{ fase.nombre }}</span>
                            <span>{{ horasPorBloque(fase) }} h</span>
                        </div>

                        <div class="divide-y">
                            <div
                                v-for="t in fase.tareas"
                                :key="t.tarea_id"
                                class="px-6 py-3 flex justify-between"
                            >
                                <span>{{ t.titulo }}</span>
                                <span class="text-gray-500">
                                    {{ t.duracion_minuto }} min
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- INTEGRACIONES -->
            <div v-if="bloquesIntegracionesValidos.length">
                <h2 class="mb-3 font-bold text-lg">Integraciones Asociadas</h2>

                <div class="space-y-4">
                    <div
                        v-for="intg in bloquesIntegracionesValidos"
                        :key="intg.id"
                        class="border rounded-2xl bg-white shadow"
                    >
                        <div
                            class="px-6 py-4 bg-yellow-50 text-yellow-800 font-semibold flex justify-between rounded-t-2xl"
                        >
                            <span>{{ intg.nombre }}</span>
                            <span>{{ horasPorBloque(intg) }} h</span>
                        </div>

                        <div class="divide-y">
                            <div
                                v-for="t in intg.tareas"
                                :key="t.tarea_id"
                                class="px-6 py-3 flex justify-between"
                            >
                                <span>{{ t.titulo }}</span>
                                <span class="text-gray-500">
                                    {{ t.duracion_minuto }} min
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ACCIONES -->
        <template #actions>
            <div
                class="flex flex-col gap-3 mt-6 md:flex-row md:justify-between"
            >
                <button
                    class="px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
                    @click="emit('back')"
                >
                    Anterior
                </button>

                <button
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    @click="generarPDF"
                >
                    Vista Previa PDF
                </button>

                <button
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50"
                    :disabled="isSaving"
                    @click="
                        emit('finish', {
                            bloquesFases,
                            bloquesIntegraciones,
                            totalHoras,
                            totalMinutos,
                        })
                    "
                >
                    Confirmar Estimación
                </button>
            </div>
        </template>
    </FormWrapper>
</template>
