<script setup>
import axios from "axios";
import { router } from "@inertiajs/vue3";

const emit = defineEmits(["prev"]);

const guardar = async () => {
    await axios.post("/api/estimaciones/finalizar");
    router.get("/estimaciones");
};
</script>

<template>
    <h2 class="font-bold mb-4">Resumen final</h2>

    <p>Revisa los datos antes de guardar.</p>

    <div class="flex justify-between mt-6">
        <button @click="emit('prev')">Anterior</button>
        <button @click="guardar" class="btn-primary">Guardar estimación</button>
    </div>
</template>
