<script setup>
import { computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";
import jsPDF from "jspdf";
import autoTable from "jspdf-autotable";

const props = defineProps({
    estimacion: { type: Object, required: true },
    nombreProyecto: { type: String, default: "" },
    bloquesFases: { type: Array, default: () => [] },
    bloquesIntegraciones: { type: Array, default: () => [] },
    totalHoras: { type: Number, default: 0 },
    totalMinutos: { type: Number, default: 0 },
    isSaving: { type: Boolean, default: false },
    usuariosDisponibles: { type: Array, default: () => [] },
});

const emit = defineEmits(["back", "finish"]);

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

const usuarioSeleccionado = computed(() =>
    props.usuariosDisponibles.find(
        (u) => Number(u.id) === Number(props.estimacion.userId),
    ),
);

const generarPDF = () => {
    // Cargar logos
    const logoWeCollab = new Image();
    logoWeCollab.src = "/img/wecollab.jpg";

    const logoSeti = new Image();
    logoSeti.src = "/img/seti.jpg";

    let logosLoaded = 0;
    const checkReady = () => {
        logosLoaded++;
        if (logosLoaded === 2) {
            renderPDF();
        }
    };

    logoWeCollab.onload = checkReady;
    logoSeti.onload = checkReady;
    logoWeCollab.onerror = checkReady;
    logoSeti.onerror = checkReady;

    const renderPDF = () => {
        const doc = new jsPDF("p", "mm", "letter");
        const pageWidth = 210;
        const pageHeight = 297;

        const margins = {
            top: 32,
            bottom: 38,
            left: 14,
            right: 14,
        };

        let y = margins.top;
        let currentPage = 1;

        const drawHeader = () => {
            const logoSize = 18;
            doc.addImage(
                logoWeCollab,
                "JPEG",
                margins.left,
                9,
                logoSize,
                logoSize,
            );
            doc.addImage(
                logoSeti,
                "JPEG",
                pageWidth - margins.right - logoSize,
                9,
                logoSize,
                logoSize,
            );

            doc.setFont("helvetica", "bold");
            doc.setFontSize(17);
            doc.setTextColor(93, 46, 140);
            doc.text("WE COLLAB", pageWidth / 2, 17, { align: "center" });

            doc.setFont("helvetica", "normal");
            doc.setFontSize(9);
            doc.setTextColor(166, 134, 215);
            doc.text("SynergyFlow™ PlanCore", pageWidth / 2, 23, {
                align: "center",
            });

            doc.setDrawColor(93, 46, 140);
            doc.setLineWidth(1.8);
            doc.line(margins.left, 29, pageWidth - margins.right, 29);
        };

        const drawFooter = () => {
            const footerTop = pageHeight - margins.bottom + 4;
            doc.setDrawColor(200, 200, 200);
            doc.setLineWidth(0.4);
            doc.line(
                margins.left,
                footerTop,
                pageWidth - margins.right,
                footerTop,
            );

            doc.setFont("helvetica", "normal");
            doc.setFontSize(7.5);
            doc.setTextColor(100, 100, 100);
            doc.text(
                "We Collab · Primo Feliciano Velazquez 3930 · Guadalajara, Jalisco",
                margins.left,
                footerTop + 5,
            );
            doc.text(
                "contacto@we-collab.tech · https://we-collab.tech",
                margins.left,
                footerTop + 9.5,
            );
            doc.text(
                `Página ${currentPage}`,
                pageWidth - margins.right,
                footerTop + 5,
                { align: "right" },
            );
        };

        const hasSpaceFor = (height) =>
            y + height <= pageHeight - margins.bottom;
        const addPageIfNeeded = (requiredHeight) => {
            if (!hasSpaceFor(requiredHeight)) {
                doc.addPage();
                currentPage++;
                drawHeader();
                drawFooter();
                y = margins.top;
                return true;
            }
            return false;
        };

        const drawSectionHeader = (title) => {
            addPageIfNeeded(9);
            doc.setFillColor(93, 46, 140);
            doc.rect(
                margins.left,
                y,
                pageWidth - margins.left - margins.right,
                5.5,
                "F",
            );
            doc.setFont("helvetica", "bold");
            doc.setFontSize(9.5);
            doc.setTextColor(255, 255, 255);
            doc.text(title, margins.left + 2.5, y + 3.8);
            y += 8.5;
        };

        drawHeader();
        drawFooter();

        drawSectionHeader("Información General");
        addPageIfNeeded(42);

        autoTable(doc, {
            startY: y,
            theme: "plain",
            body: [
                [
                    "ID Estimación",
                    `#${props.estimacion.id || "-"}`,
                    "Fecha",
                    new Date().toLocaleDateString("es-MX"),
                ],
                [
                    "Compañía",
                    props.estimacion.nombreEmpresa || "-",
                    "ID Negocio",
                    props.estimacion.idNegocio || "-",
                ],
                [
                    "Responsable",
                    usuarioSeleccionado.value?.name || "No asignado",
                    "Tipo Implementación",
                    props.nombreProyecto || "-",
                ],
                [
                    "Complejidad",
                    props.estimacion.complejidad?.nombre || "-",
                    "Total Horas",
                    `${props.totalHoras.toFixed(2)} h`,
                ],
            ],
            styles: { fontSize: 8.5, cellPadding: 1.5 },
            columnStyles: {
                0: { fontStyle: "bold", textColor: 119 },
                1: {},
                2: { fontStyle: "bold", textColor: 119 },
                3: { fontStyle: "bold", textColor: [93, 46, 140] },
            },
            margin: { left: margins.left, right: margins.right },
        });

        y = doc.lastAutoTable.finalY + 7;

        bloquesFasesValidos.value.forEach((fase) => {
            drawSectionHeader(`Fase: ${fase.nombre}`);
            addPageIfNeeded(14 + fase.tareas.length * 4.5);

            autoTable(doc, {
                startY: y,
                head: [["#", "Tarea", "Duración (min)"]],
                body: fase.tareas.map((t, i) => [
                    (i + 1).toString(),
                    t.titulo || "-",
                    t.duracion_minuto?.toString() || "-",
                ]),
                styles: { fontSize: 8.5, cellPadding: 1.5 },
                headStyles: {
                    fillColor: [240, 240, 240],
                    textColor: [0, 0, 0],
                    fontStyle: "bold",
                    fontSize: 8,
                },
                alternateRowStyles: { fillColor: [251, 251, 251] },
                columnStyles: {
                    0: { cellWidth: 12, halign: "center" },
                    2: {
                        cellWidth: 35,
                        halign: "right",
                        fontStyle: "bold",
                        textColor: [93, 46, 140],
                    },
                },
                margin: { left: margins.left, right: margins.right },
            });

            y = doc.lastAutoTable.finalY + 7;
        });

        if (bloquesIntegracionesValidos.value.length > 0) {
            drawSectionHeader("Integraciones");
            bloquesIntegracionesValidos.value.forEach((intg) => {
                addPageIfNeeded(11);
                doc.setFont("helvetica", "bold");
                doc.setFontSize(9.5);
                doc.setTextColor(93, 46, 140);
                doc.text(intg.nombre, margins.left, y);
                y += 7;

                addPageIfNeeded(14 + intg.tareas.length * 4.5);
                autoTable(doc, {
                    startY: y,
                    head: [["#", "Tarea", "Duración (min)"]],
                    body: intg.tareas.map((t, i) => [
                        (i + 1).toString(),
                        t.titulo || "-",
                        t.duracion_minuto?.toString() || "-",
                    ]),
                    styles: { fontSize: 8.5, cellPadding: 1.5 },
                    headStyles: {
                        fillColor: [240, 240, 240],
                        textColor: [0, 0, 0],
                        fontStyle: "bold",
                        fontSize: 8,
                    },
                    alternateRowStyles: { fillColor: [255, 247, 237] },
                    columnStyles: {
                        0: { cellWidth: 12, halign: "center" },
                        2: {
                            cellWidth: 35,
                            halign: "right",
                            fontStyle: "bold",
                            textColor: [245, 158, 11],
                        },
                    },
                    margin: { left: margins.left, right: margins.right },
                });
                y = doc.lastAutoTable.finalY + 7;
            });
        }

        drawSectionHeader("Resumen Final");
        addPageIfNeeded(28);

        const cardWidth = pageWidth - margins.left - margins.right;
        const cardHeight = 22;
        doc.setFillColor(250, 250, 250);
        doc.rect(margins.left, y, cardWidth, cardHeight, "F");
        doc.setDrawColor(220, 220, 220);
        doc.rect(margins.left, y, cardWidth, cardHeight);
        doc.setFont("helvetica", "normal");
        doc.setFontSize(8.5);
        doc.setTextColor(119, 119, 119);
        doc.text("Factor", margins.left + 4, y + 7);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(10);
        doc.setTextColor(93, 46, 140);
        doc.text(
            props.estimacion.complejidad?.factor?.toString() || "-",
            margins.left + 4,
            y + 13,
        );
        doc.setFont("helvetica", "normal");
        doc.setFontSize(8.5);
        doc.setTextColor(119, 119, 119);
        doc.text("Total horas estimadas", margins.left + 70, y + 7);
        doc.setFont("helvetica", "bold");
        doc.setFontSize(13);
        doc.setTextColor(93, 46, 140);
        doc.text(`${props.totalHoras.toFixed(2)} h`, margins.left + 70, y + 15);
        y += cardHeight + 8;

        window.open(doc.output("bloburl"), "_blank");
    };
};
</script>

<template>
    <FormWrapper title="">
        <template #headerExtra>
            <div
                class="bg-gradient-to-r from-blue-50 to-purple-50 border border-blue-200 rounded-lg p-3 shadow-sm mt-2"
            >
                <div
                    class="text-lg font-bold bg-gradient-to-r from-blue-700 to-purple-700 bg-clip-text text-transparent flex items-center gap-2"
                >
                    <span>📊</span> Resumen de Estimación
                </div>
                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-2.5 mt-2.5 text-xs"
                >
                    <div
                        class="bg-white p-2.5 rounded-lg shadow-sm border border-gray-100"
                    >
                        <div class="text-gray-500 font-semibold">
                            Implementación
                        </div>
                        <div class="font-bold text-blue-600 mt-0.5">
                            {{ nombreProyecto }}
                        </div>
                    </div>
                    <div
                        class="bg-white p-2.5 rounded-lg shadow-sm border border-gray-100"
                    >
                        <div class="text-gray-500 font-semibold">
                            Responsable
                        </div>
                        <div class="font-bold text-blue-600 mt-0.5">
                            {{ usuarioSeleccionado?.name || "—" }}
                        </div>
                    </div>
                    <div
                        class="bg-white p-2.5 rounded-lg shadow-sm border border-gray-100"
                    >
                        <div class="text-gray-500 font-semibold">
                            Complejidad
                        </div>
                        <div class="font-bold text-blue-600 mt-0.5">
                            {{ estimacion.complejidad?.nombre || "—" }}
                        </div>
                    </div>
                    <div
                        class="bg-gradient-to-r from-blue-600 to-purple-600 p-2.5 rounded-lg"
                    >
                        <div class="text-white font-semibold">
                            Total Estimado
                        </div>
                        <div class="font-bold text-xl text-white mt-0.5">
                            {{ totalHoras }}h
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-5">
            <div v-if="bloquesFasesValidos.length">
                <h2
                    class="mb-2.5 font-bold text-blue-700 text-base flex items-center gap-1.5"
                >
                    <span>📋</span> Fases de Implementación
                </h2>
                <div class="space-y-2.5">
                    <div
                        v-for="fase in bloquesFasesValidos"
                        :key="fase.id"
                        class="border border-blue-200 rounded-lg bg-white shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div
                            class="px-4 py-2.5 bg-gradient-to-r from-blue-50 to-blue-100 text-blue-800 font-bold flex justify-between items-center rounded-t-lg"
                        >
                            <span>{{ fase.nombre }}</span>
                            <span
                                class="bg-white text-blue-700 px-2.5 py-0.5 rounded-full text-xs font-bold shadow"
                            >
                                {{ horasPorBloque(fase) }} h
                            </span>
                        </div>
                        <div class="divide-y">
                            <div
                                v-for="(t, index) in fase.tareas"
                                :key="t.tarea_id"
                                class="px-4 py-2 flex justify-between items-center hover:bg-blue-50 transition-colors"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-blue-600 font-bold w-5 text-center"
                                        >{{ index + 1 }}</span
                                    >
                                    <span class="text-gray-700 text-sm">{{
                                        t.titulo
                                    }}</span>
                                </div>
                                <span
                                    class="text-gray-600 font-semibold bg-blue-100 px-2.5 py-0.5 rounded text-xs"
                                >
                                    {{ t.duracion_minuto }} min
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="bloquesIntegracionesValidos.length">
                <h2
                    class="mb-2.5 font-bold text-yellow-700 text-base flex items-center gap-1.5"
                >
                    <span>🔗</span> Integraciones Asociadas
                </h2>
                <div class="space-y-2.5">
                    <div
                        v-for="intg in bloquesIntegracionesValidos"
                        :key="intg.id"
                        class="border border-yellow-200 rounded-lg bg-white shadow-sm hover:shadow-md transition-shadow"
                    >
                        <div
                            class="px-4 py-2.5 bg-gradient-to-r from-yellow-50 to-yellow-100 text-yellow-800 font-bold flex justify-between items-center rounded-t-lg"
                        >
                            <span>{{ intg.nombre }}</span>
                            <span
                                class="bg-white text-yellow-700 px-2.5 py-0.5 rounded-full text-xs font-bold shadow"
                            >
                                {{ horasPorBloque(intg) }} h
                            </span>
                        </div>
                        <div class="divide-y">
                            <div
                                v-for="(t, index) in intg.tareas"
                                :key="t.tarea_id"
                                class="px-4 py-2 flex justify-between items-center hover:bg-yellow-50 transition-colors"
                            >
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-yellow-600 font-bold w-5 text-center"
                                        >{{ index + 1 }}</span
                                    >
                                    <span class="text-gray-700 text-sm">{{
                                        t.titulo
                                    }}</span>
                                </div>
                                <span
                                    class="text-gray-600 font-semibold bg-yellow-100 px-2.5 py-0.5 rounded text-xs"
                                >
                                    {{ t.duracion_minuto }} min
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <template #actions>
            <div
                class="flex flex-col gap-2.5 mt-5 md:flex-row md:justify-between"
            >
                <button
                    class="px-5 py-2 bg-gray-300 hover:bg-gray-400 rounded-md font-semibold transition-colors flex items-center justify-center gap-1.5 text-sm"
                    @click="emit('back')"
                >
                    <span>←</span> <span>Regresar</span>
                </button>
                <button
                    class="px-5 py-2 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-md font-semibold shadow-md hover:shadow-lg transition-all flex items-center justify-center gap-1.5 text-sm min-w-[180px]"
                    @click="generarPDF"
                >
                    <span class="text-xl">📄</span>
                    <span>Vista Previa PDF</span>
                </button>
                <button
                    :class="[
                        'px-5 py-2 rounded-md font-semibold shadow-md transition-all flex items-center justify-center gap-1.5 text-sm min-w-[180px]',
                        isSaving
                            ? 'bg-green-400 cursor-not-allowed'
                            : 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white hover:shadow-lg',
                    ]"
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
                    <span class="text-lg">✅</span>
                    <span>{{
                        isSaving ? "Guardando..." : "Confirmar Estimación"
                    }}</span>
                </button>
            </div>
        </template>
    </FormWrapper>
</template>
