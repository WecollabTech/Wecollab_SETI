<script setup>
import { ref, watch, onMounted } from "vue"; // ✅ AQUÍ ESTÁ LA CORRECCIÓN
import axios from "axios";
import FormEstimacion from "@/Components/Formulario/FormEstimacion.vue";

const props = defineProps({
    modelValue: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(["update:modelValue"]);

const update = (key, value) => {
    emit("update:modelValue", {
        ...props.modelValue,
        [key]: value,
    });
};

// --- LISTAS ---
const tiposImplementacion = ref([]);
const integracionesDisponibles = ref([]);

// --- Cargar tipos ---
onMounted(async () => {
    try {
        const res = await axios.get("/api/tipoimplementacion");
        tiposImplementacion.value = res.data.data ?? [];
    } catch (e) {
        console.error(e);
    }
});

// --- Cargar integraciones según tipo ---
watch(
    () => props.modelValue.tipoImplementacion,
    async (tipoId) => {
        integracionesDisponibles.value = [];
        update("integraciones", []);

        if (!tipoId) return;

        try {
            const res = await axios.get(
                `/api/tipoimplementacion/${tipoId}/integraciones`
            );
            integracionesDisponibles.value = res.data.data ?? [];
        } catch (e) {
            console.error(e);
        }
    }
);
</script>

<template>
    <!-- Tipo implementación -->
    <FormEstimacion
        label="Tipo de implementación"
        type="select"
        :options="
            tiposImplementacion.map((t) => ({
                value: t.id,
                label: t.nombre,
            }))
        "
        :model-value="modelValue.tipoImplementacion"
        @update:modelValue="update('tipoImplementacion', $event)"
    />

    <!-- Integraciones -->
    <div class="mt-4">
        <label class="block font-medium mb-1">Integraciones</label>

        <FormEstimacion
            v-if="modelValue.tipoImplementacion"
            type="checkbox"
            :options="
                integracionesDisponibles.map((i) => ({
                    value: i.id,
                    label: i.nombre,
                }))
            "
            :model-value="modelValue.integraciones"
            @update:modelValue="update('integraciones', $event)"
        />

        <p v-else class="text-gray-400 text-sm italic">
            Selecciona un tipo de implementación primero
        </p>
    </div>

    <!-- Complejidad -->
    <div class="mt-4">
        <label class="block font-medium mb-2">Nivel de complejidad</label>
        <div class="flex gap-2">
            <button
                v-for="nivel in ['Baja', 'Media', 'Alta']"
                :key="nivel"
                type="button"
                @click="update('complejidad', nivel)"
                class="px-4 py-2 rounded border transition"
                :class="
                    modelValue.complejidad === nivel
                        ? 'bg-blue-600 text-white'
                        : 'bg-white'
                "
            >
                {{ nivel }}
            </button>
        </div>
    </div>

    <!-- Comentarios -->
    <FormEstimacion
        label="Comentarios"
        type="textarea"
        :model-value="modelValue.comentarios"
        @update:modelValue="update('comentarios', $event)"
    />
</template>
