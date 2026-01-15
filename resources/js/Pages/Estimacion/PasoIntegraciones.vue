<script setup>
import { ref, onMounted, watch } from "vue";
import axios from "axios";

const props = defineProps({
    tipoImplementacionId: Number,
});

const model = defineModel();
const emit = defineEmits(["next", "prev"]);

const integraciones = ref([]);

onMounted(async () => {
    const res = await axios.get(
        `/api/integraciones/${props.tipoImplementacionId}`
    );
    integraciones.value = res.data;
});
</script>

<template>
    <div class="col-span-2">
        <label class="font-semibold">Integraciones</label>

        <div class="grid grid-cols-2 gap-2 mt-2">
            <label
                v-for="i in integraciones"
                :key="i.id"
                class="flex items-center gap-2"
            >
                <input type="checkbox" :value="i.id" v-model="model" />
                {{ i.nombre }}
            </label>
        </div>

        <div class="flex justify-between mt-6">
            <button type="button" class="btn-secondary" @click="emit('prev')">
                Anterior
            </button>
            <button type="button" class="btn-primary" @click="emit('next')">
                Siguiente
            </button>
        </div>
    </div>
</template>
