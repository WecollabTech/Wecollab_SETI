<template>
    <div
        v-if="show"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
    >
        <div
            class="bg-white rounded-lg shadow-lg w-3/4 max-w-3xl p-4 max-h-[90vh] overflow-auto"
        >
            <h3 class="text-lg font-bold mb-4">{{ title }}</h3>

            <div class="mb-4">
                <!-- Texto simple -->
                <p v-if="type === 'text'">{{ content }}</p>

                <!-- PDF -->
                <iframe
                    v-else-if="type === 'pdf'"
                    :src="content"
                    class="w-full h-96"
                    frameborder="0"
                ></iframe>

                <!-- Video mp4 -->
                <video
                    v-else-if="type === 'video'"
                    controls
                    autoplay
                    muted
                    loop
                    class="w-full h-64"
                >
                    <source :src="content" type="video/mp4" />
                    Tu navegador no soporta video.
                </video>

                <!-- Imagen -->
                <img
                    v-else-if="type === 'image'"
                    :src="content"
                    class="w-full h-auto"
                />

                <!-- YouTube -->
                <iframe
                    v-else-if="type === 'youtube'"
                    class="w-full h-64"
                    :src="getYoutubeEmbedUrl(content)"
                    frameborder="0"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                ></iframe>

                <!-- Contenido no disponible -->
                <p v-else class="text-center text-gray-500">
                    Contenido no disponible
                </p>
            </div>

            <button
                @click="closeModal"
                class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700"
            >
                Cerrar
            </button>
        </div>
    </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: "Contenido" },
    content: { type: String, default: "" },
    type: { type: String, default: "text" }, // 'text' | 'video' | 'youtube' | 'pdf' | 'image'
});

const emit = defineEmits(["update:show"]);

const closeModal = () => emit("update:show", false);

// Función para generar embed de YouTube con autoplay
const getYoutubeEmbedUrl = (url) => {
    let videoId = "";
    if (url.includes("youtu.be")) {
        videoId = url.split("/").pop();
    } else {
        videoId = new URL(url).searchParams.get("v");
    }
    return `https://www.youtube.com/embed/${videoId}?autoplay=1`;
};
</script>
