<script setup>
import { ref, computed } from "vue";
import SortableHeader from "./SortableHeader.vue";
import ActionButtons from "./ActionButtons.vue";

const props = defineProps({
    data: {
        type: Array,
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    showActions: {
        type: Boolean,
        default: true,
    },
    actionConfig: {
        type: Object,
        default: () => ({
            showEdit: true,
            showDelete: true,
            showDuplicate: true,
            showView: true,
        }),
    },
});

const emit = defineEmits([
    "sort-change",
    "edit",
    "delete",
    "duplicate",
    "view",
]);

const sortBy = ref("id");
const sortDirection = ref("asc");

const sortedData = computed(() => {
    if (!props.data || props.data.length === 0) return [];

    return [...props.data].sort((a, b) => {
        const aValue = a[sortBy.value];
        const bValue = b[sortBy.value];

        if (aValue === null || aValue === undefined) return 1;
        if (bValue === null || bValue === undefined) return -1;

        if (typeof aValue === "string" && typeof bValue === "string") {
            return sortDirection.value === "asc"
                ? aValue.localeCompare(bValue, "es", { sensitivity: "base" })
                : bValue.localeCompare(aValue, "es", { sensitivity: "base" });
        }

        return sortDirection.value === "asc"
            ? aValue - bValue
            : bValue - aValue;
    });
});

const changeSort = (column) => {
    if (sortBy.value === column) {
        sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
    } else {
        sortBy.value = column;
        sortDirection.value = "asc";
    }
    emit("sort-change", {
        sortBy: sortBy.value,
        sortDirection: sortDirection.value,
    });
};

const handleEdit = (id) => emit("edit", id);
const handleDelete = (id) => emit("delete", id);
const handleDuplicate = (id) => emit("duplicate", id);
const handleView = (id) => emit("view", id);
</script>

<template>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <SortableHeader
                        v-for="column in columns"
                        :key="column.key"
                        :label="column.label"
                        :column="column.key"
                        :sort-by="sortBy"
                        :sort-direction="sortDirection"
                        @click="changeSort"
                    />
                    <th
                        v-if="showActions"
                        class="py-3 px-4 text-center border-b-2 border-gray-200"
                    >
                        <span
                            class="font-semibold text-gray-700 text-sm uppercase tracking-wider"
                            >Acciones</span
                        >
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr
                    v-for="item in sortedData"
                    :key="item.id"
                    class="hover:bg-gray-50/50 transition-colors"
                >
                    <td
                        v-for="column in columns"
                        :key="column.key"
                        class="py-3 px-4"
                        :class="column.class || ''"
                    >
                        <template v-if="column.key === 'id'">
                            <span
                                class="font-mono font-semibold text-indigo-700"
                                >#{{ item.id }}</span
                            >
                        </template>
                        <template v-else-if="column.key === 'descripcion'">
                            <div class="line-clamp-2 text-gray-700">
                                {{ item[column.key] ?? "Sin descripción" }}
                            </div>
                        </template>
                        <template v-else>
                            <span
                                :class="
                                    column.textClass ||
                                    'text-gray-900 font-medium'
                                "
                            >
                                {{ item[column.key] }}
                            </span>
                        </template>
                    </td>
                    <ActionButtons
                        v-if="showActions"
                        :id="item.id"
                        v-bind="actionConfig"
                        @edit="handleEdit"
                        @delete="handleDelete"
                        @duplicate="handleDuplicate"
                        @view="handleView"
                    />
                </tr>

                <!-- LOADING -->
                <tr v-if="loading">
                    <td
                        :colspan="columns.length + (showActions ? 1 : 0)"
                        class="text-center py-8"
                    >
                        <div
                            class="flex flex-col items-center justify-center gap-3 text-gray-600"
                        >
                            <div class="relative">
                                <div
                                    class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"
                                ></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700"
                                >Cargando datos...</span
                            >
                        </div>
                    </td>
                </tr>

                <!-- EMPTY -->
                <tr v-if="!loading && sortedData.length === 0">
                    <td
                        :colspan="columns.length + (showActions ? 1 : 0)"
                        class="text-center py-10"
                    >
                        <div
                            class="flex flex-col items-center gap-3 text-gray-500"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-10 w-10 opacity-75"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
                                />
                            </svg>
                            <div class="text-center">
                                <span class="text-lg font-medium text-gray-900"
                                    >No hay registros</span
                                >
                                <p class="text-sm mt-1">
                                    No se encontraron resultados
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
