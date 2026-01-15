<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const emit = defineEmits(["next", "prev"]);
const fases = ref([]);

onMounted(async () => {
    const res = await axios.get("/api/estimaciones/fases");
    fases.value = res.data.map((f) => ({
        ...f,
        horas: 0,
    }));
});

const continuar = async () => {
    await axios.post("/api/estimaciones/paso-3", fases.value);
    emit("next");
};
</script>

<template>
    <h2 class="font-bold mb-4">Horas por fase</h2>

    <div v-for="fase in fases" :key="fase.id" class="mb-3">
        <label>{{ fase.nombre }}</label>
        <input type="number" class="input" v-model="fase.horas" min="0" />
    </div>

    <div class="flex justify-between mt-6">
        <button @click="emit('prev')">Anterior</button>
        <button @click="continuar">Siguiente</button>
    </div>
</template>
