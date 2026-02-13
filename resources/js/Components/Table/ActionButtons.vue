<script setup>
import { Link } from "@inertiajs/vue3";
import {
    EyeIcon,
    DocumentTextIcon,
    PencilIcon,
    TrashIcon,
    DocumentDuplicateIcon,
} from "@heroicons/vue/24/solid";

const props = defineProps({
    id: { type: [Number, String], required: true },

    /* VISIBILIDAD */
    showEdit: { type: Boolean, default: true },
    showDelete: { type: Boolean, default: true },
    showDuplicate: { type: Boolean, default: true },
    showViewDetail: { type: Boolean, default: true }, // Ver detalle
    showViewContent: { type: Boolean, default: true }, // Ver contenido

    /* URLS (Inertia) */
    editUrl: { type: String, default: null },
    deleteUrl: { type: String, default: null },
    duplicateUrl: { type: String, default: null },
    viewDetailUrl: { type: String, default: null },
    viewContentUrl: { type: String, default: null },
});

const emit = defineEmits([
    "edit",
    "delete",
    "duplicate",
    "viewDetail",
    "viewContent",
]);

const confirmDelete = () => confirm("¿Estás seguro de eliminar este registro?");
</script>

<template>
    <td class="py-3 px-3 flex justify-center gap-2">
        <!-- EDITAR -->
        <component
            v-if="showEdit"
            :is="editUrl ? Link : 'button'"
            :href="editUrl"
            @click="!editUrl && emit('edit', id)"
            class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm"
            title="Editar"
        >
            <PencilIcon class="w-4 h-4" />
        </component>

        <!-- ELIMINAR -->
        <component
            v-if="showDelete"
            :is="deleteUrl ? Link : 'button'"
            :href="deleteUrl"
            method="delete"
            as="button"
            @click.prevent="deleteUrl ? confirmDelete() : emit('delete', id)"
            class="p-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition shadow-sm"
            title="Eliminar"
        >
            <TrashIcon class="w-4 h-4" />
        </component>

        <!-- DUPLICAR -->
        <component
            v-if="showDuplicate"
            :is="duplicateUrl ? Link : 'button'"
            :href="duplicateUrl"
            @click="!duplicateUrl && emit('duplicate', id)"
            class="p-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm"
            title="Duplicar"
        >
            <DocumentDuplicateIcon class="w-4 h-4" />
        </component>

        <!-- VER DETALLE -->
        <component
            v-if="showViewDetail"
            :is="viewDetailUrl ? Link : 'button'"
            :href="viewDetailUrl"
            @click="!viewDetailUrl && emit('viewDetail', id)"
            class="p-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition shadow-sm"
            title="Ver detalle"
        >
            <EyeIcon class="w-4 h-4" />
        </component>

        <!-- VER CONTENIDO -->
        <component
            v-if="showViewContent"
            :is="viewContentUrl ? Link : 'button'"
            :href="viewContentUrl"
            @click="!viewContentUrl && emit('viewContent', id)"
            class="p-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition shadow-sm"
            title="Ver contenido"
        >
            <DocumentTextIcon class="w-4 h-4" />
        </component>

        <!-- SLOT PERSONALIZADO -->
        <slot :item-id="id"></slot>
    </td>
</template>
