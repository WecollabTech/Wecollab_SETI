<script setup>
import { ref, computed } from "vue";
import FormWrapper from "@/Components/Formulario/FormWrapper.vue";

const props = defineProps({
    fases: { type: Array, required: true },
    tipoImplementacionId: { type: Number, required: true },
    nombreTipoImplementacion: { type: String, required: true },
    complejidad: { type: Object, required: true },
});

const emit = defineEmits(["back", "finish"]);
const showModal = ref(false);
const tareasSeleccionadas = ref([]);

// Precargar todas las tareas
props.fases.forEach((fase) => {
    fase.tareas?.forEach((t) => tareasSeleccionadas.value.push(t.id));
});

const factorComplejidad = computed(() => Number(props.complejidad.factor ?? 1));

const totalMinutos = computed(() => {
    let total = 0;
    props.fases.forEach((fase) => {
        fase.tareas?.forEach((tarea) => {
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

const finalizar = () => {
    const resumen = {
        tipoImplementacionId: props.tipoImplementacionId,
        nombreTipoImplementacion: props.nombreTipoImplementacion,
        complejidad: props.complejidad,
        totalMinutos: totalMinutos.value,
        totalHoras: totalHoras.value,
        fases: props.fases.map((fase) => ({
            id: fase.id,
            nombre: fase.nombre,
            tareas: fase.tareas?.filter((t) =>
                tareasSeleccionadas.value.includes(t.id)
            ),
        })),
    };
    console.log("✅ RESUMEN FINAL DE ESTIMACIÓN:", resumen);
    emit("finish", resumen);
    showModal.value = true;
};
</script>

<template>
  <FormWrapper title="Revisión final de tareas">
    <!-- 📦 GRID: 1 columna en móvil, 2 en pantallas medianas+ -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
      <div
        v-for="fase in fases"
        :key="fase.id"
        class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition-shadow overflow-hidden"
      >
        <!-- Header de la fase -->
        <div class="px-5 py-4 bg-gray-50 border-b border-gray-100">
          <h3 class="font-semibold text-blue-800 text-lg">{{ fase.nombre }}</h3>
        </div>

        <!-- Lista de tareas -->
        <div class="p-4">
          <div
            v-for="tarea in fase.tareas"
            :key="tarea.id"
            class="flex items-start gap-3 py-2.5 rounded hover:bg-gray-50 transition-colors"
          >
            <input
              type="checkbox"
              v-model="tareasSeleccionadas"
              :value="tarea.id"
              class="mt-0.5 h-4 w-4 text-blue-600 rounded focus:ring-blue-500 focus:ring-2"
            />
            <div class="flex-1 min-w-0">
              <p class="text-gray-800 text-sm leading-relaxed break-words">
                {{ tarea.titulo }}
              </p>
            </div>
            <span class="text-gray-500 text-xs font-medium bg-gray-100 px-2 py-1 rounded whitespace-nowrap">
              {{ tarea.duracion_minuto }} min
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- 🧭 RESUMEN: siempre en su propia fila completa -->
    <div class="w-full max-w-md mx-auto mt-6">
      <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 text-center shadow-sm">
        <p class="text-blue-700 font-medium">Total estimado</p>
        <p class="text-xl font-bold text-blue-800 mt-1">{{ totalHoras }} <span class="text-base font-normal">horas</span></p>
      </div>
    </div>

    <!-- 💡 BOTONES -->
    <template #actions>
      <div class="flex flex-col sm:flex-row justify-between gap-3 w-full mt-6">
        <button
          @click="emit('back')"
          class="px-5 py-2.5 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors font-medium"
        >
          ← Anterior
        </button>
        <button
          @click="finalizar"
          class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium shadow-sm"
        >
          Finalizar estimación
        </button>
      </div>
    </template>

    <!-- ✅ MODAL -->
    <div
      v-if="showModal"
      class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
    >
      <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-sm text-center animate-fade-in">
        <div class="text-5xl mb-4">✅</div>
        <h2 class="text-xl font-bold text-gray-800 mb-2">¡Listo!</h2>
        <p class="text-gray-600 mb-5">La estimación ha sido generada con éxito.</p>
        <button
          @click="showModal = false"
          class="w-full py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
        >
          Ver resumen
        </button>
      </div>
    </div>
  </FormWrapper>
</template>

<style scoped>
.animate-fade-in {
  animation: fadeIn 0.3s ease-out;
}
@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
</style>