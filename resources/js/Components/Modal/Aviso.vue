<script setup>
import { ref } from "vue";

// Estado del modal
const showModal = ref(false);
const modalMessage = ref("");

// Función para mostrar contenido de la tarea
const verContenidoTarea = (item) => {
    if (item.url_contenido) {
        window.open(item.url_contenido, "_blank"); // Abre en nueva pestaña
    } else {
        modalMessage.value = "Esta tarea no tiene contenido disponible.";
        showModal.value = true;
    }
};

// Función para cerrar modal
const cerrarModal = () => {
    showModal.value = false;
};
</script>
<template>
    <!-- Botón ejemplo para abrir contenido -->
    <button
        @click="verContenidoTarea(item)"
        class="p-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition shadow-sm"
        title="Ver contenido"
    >
        Ver contenido
    </button>

    <!-- Modal sencillo inline -->
    <div
        v-if="showModal"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-96 max-w-full">
            <h3 class="text-lg font-semibold mb-2">Aviso</h3>
            <p class="text-gray-700">{{ modalMessage }}</p>
            <div class="mt-4 text-right">
                <button
                    @click="cerrarModal"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                >
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</template>
