<script setup>
import { computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable"; // Import correcto

const props = defineProps({
    nombreProyecto: { type: String, default: "" },
    bloquesFases: { type: Array, default: () => [] },
    bloquesIntegraciones: { type: Array, default: () => [] },
    totalHoras: { type: Number, default: 0 },
    totalMinutos: { type: Number, default: 0 },
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

// Generar PDF vistoso
const generarPDF = () => {
    const logo = new Image();
    logo.src = "/img/logo.png"; // public/img/logo.png

    logo.onload = () => {
        const doc = new jsPDF();

        /* ================= HEADER ================= */
        // Logo (más ancho, elegante)
        doc.addImage(logo, "PNG", 14, 12, 36, 18);

        // Título alineado con el logo
        doc.setFont("helvetica", "bold");
        doc.setFontSize(18);
        doc.text("Resumen de Estimación", 105, 22, { align: "center" });

        // Línea separadora
        doc.setDrawColor(200);
        doc.line(14, 36, 196, 36);

        /* ================= PROYECTO ================= */
        doc.setFont("helvetica", "normal");
        doc.setFontSize(12);
        doc.text(`Proyecto: ${props.nombreProyecto}`, 14, 45);

        let yOffset = 55;

        /* ================= FASES ================= */
        if (bloquesFasesValidos.value.length) {
            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.text("Fases de Implementación", 14, yOffset);
            yOffset += 6;

            doc.setDrawColor(220);
            doc.line(14, yOffset, 196, yOffset);
            yOffset += 6;

            bloquesFasesValidos.value.forEach((fase) => {
                doc.setFontSize(14);
                doc.text(
                    `${fase.nombre} (${horasPorBloque(fase)} h)`,
                    14,
                    yOffset,
                );
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
                    headStyles: {
                        fillColor: [52, 152, 219],
                        textColor: 255,
                    },
                    margin: { left: 14, right: 14 },
                });

                yOffset = doc.lastAutoTable.finalY + 10;
            });
        }

        /* ================= INTEGRACIONES ================= */
        if (bloquesIntegracionesValidos.value.length) {
            doc.setFont("helvetica", "bold");
            doc.setFontSize(16);
            doc.text("Integraciones Asociadas", 14, yOffset);
            yOffset += 6;

            doc.setDrawColor(220);
            doc.line(14, yOffset, 196, yOffset);
            yOffset += 6;

            bloquesIntegracionesValidos.value.forEach((intg) => {
                doc.setFontSize(14);
                doc.text(
                    `${intg.nombre} (${horasPorBloque(intg)} h)`,
                    14,
                    yOffset,
                );
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
                    headStyles: {
                        fillColor: [241, 196, 15],
                        textColor: 0,
                    },
                    margin: { left: 14, right: 14 },
                });

                yOffset = doc.lastAutoTable.finalY + 10;
            });
        }

        /* ================= TOTAL ================= */
        yOffset += 8;

        doc.setDrawColor(180);
        doc.rect(14, yOffset, 182, 18);

        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text("Total de horas estimadas", 105, yOffset + 7, {
            align: "center",
        });

        doc.setFontSize(16);
        doc.text(`${props.totalHoras} h`, 105, yOffset + 14, {
            align: "center",
        });

        /* ================= FOOTER ================= */
        doc.setFont("helvetica", "normal");
        doc.setFontSize(9);
        doc.setTextColor(120);
        doc.text(
            `Generado automáticamente · ${new Date().toLocaleDateString()}`,
            105,
            290,
            { align: "center" },
        );

        /* ================= ABRIR PDF ================= */
        const blobUrl = doc.output("bloburl");
        window.open(blobUrl, "_blank");
    };

    logo.onerror = () => {
        alert("No se pudo cargar el logo");
    };
};
</script>

<template>
    <FormWrapper title="">
        <!-- HEADER -->
        <template #headerExtra>
            <div class="bg-gray-50 border rounded-xl p-4 shadow-sm w-full mt-2">
                <div class="text-lg font-bold text-blue-700">
                    Resumen de Estimación
                </div>
                <div class="text-sm text-gray-700 mt-1">
                    Tipo de implementación:
                    <span class="font-semibold text-blue-600">
                        {{ nombreProyecto }}
                    </span>
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

        <!-- GRID PRINCIPAL -->
        <div
            class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 auto-rows-max items-start"
        >
            <!-- COLUMNA IZQUIERDA: FASES -->
            <div
                v-if="bloquesFasesValidos.length"
                class="md:row-start-1 md:col-start-1"
            >
                <h2 class="mb-3 font-bold text-lg">Fases de Implementación</h2>

                <div class="space-y-4">
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
                                <span class="text-gray-500">
                                    {{ tarea.duracion_minuto }} min
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: INTEGRACIONES -->
            <div
                v-if="bloquesIntegracionesValidos.length"
                class="md:row-start-1 md:col-start-2"
            >
                <h2 class="mb-3 font-bold text-lg">Integraciones Asociadas</h2>

                <div class="space-y-4">
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
                                <span class="text-gray-500">
                                    {{ tarea.duracion_minuto }} min
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESUMEN -->
            <div
                class="md:col-span-2 md:row-start-2 mt-6 max-w-md mx-auto border rounded-xl bg-gray-50 p-6 shadow-sm text-center text-blue-700"
            >
                <div class="text-base md:text-lg font-bold">
                    Total de horas estimadas
                </div>

                <div class="text-xl md:text-2xl font-semibold">
                    {{ totalHoras }} h
                </div>
            </div>
        </div>

        <!-- ACCIONES -->
        <template #actions>
            <div
                class="flex flex-col gap-3 w-full mt-4 md:flex-row md:justify-between md:items-center"
            >
                <button
                    class="w-full md:w-auto px-6 py-2 bg-gray-300 rounded-lg hover:bg-gray-400"
                    @click="emit('back')"
                >
                    Anterior
                </button>

                <button
                    class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                    @click="generarPDF"
                >
                    Vista Previa PDF
                </button>

                <button
                    class="w-full md:w-auto px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
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
