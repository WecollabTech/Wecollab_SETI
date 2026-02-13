<script setup>
import { router } from "@inertiajs/vue3";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },

    createText: {
        type: String,
        default: null,
    },

    // 👉 Opción 1: URL directa
    createUrl: {
        type: String,
        default: null,
    },

    // 👉 Opción 2: route('nombre.ruta')
    createRoute: {
        type: String,
        default: null,
    },

    routeParams: {
        type: Object,
        default: () => ({}),
    },

    showCreate: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(["create"]);

const handleCreate = () => {
    // 👉 Prioridad: route() > url > evento
    if (props.createRoute) {
        router.visit(route(props.createRoute, props.routeParams), {
            preserveScroll: true,
            preserveState: true,
        });
        return;
    }

    if (props.createUrl) {
        router.visit(props.createUrl, {
            preserveScroll: true,
            preserveState: true,
        });
        return;
    }

    emit("create");
};
</script>

<template>
    <div class="px-6 py-4 border-b border-gray-200 bg-white">
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <!-- 🔹 TÍTULO -->
            <div class="flex items-center gap-3">
                <slot name="icon" />
                <h2 v-if="title" class="text-xl font-bold text-gray-900">
                    {{ title }}
                </h2>
            </div>

            <!-- 🔹 SLOT IZQUIERDO -->
            <div class="flex-1">
                <slot name="left" />
            </div>

            <!-- 🔹 ACCIONES -->
            <div class="flex items-center gap-2">
                <slot name="actions" />

                <button
                    v-if="showCreate && createText"
                    @click="handleCreate"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition shadow"
                >
                    <slot name="create-icon">
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                            />
                        </svg>
                    </slot>
                    {{ createText }}
                </button>
            </div>
        </div>
    </div>
</template>
