<script setup>
defineProps({
    title: String,
    headers: {
        type: Array,
        default: () => [],
    },
    rows: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div class="bg-white rounded-xl shadow">
        <!-- HEADER -->
        <div class="border-b px-6 py-4 flex items-center justify-between">
            <h3 class="font-semibold text-gray-700">
                {{ title }}
            </h3>
        </div>

        <!-- TABLA -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th
                            v-for="(h, i) in headers"
                            :key="i"
                            class="px-5 py-3 text-left font-medium text-gray-600"
                        >
                            {{ h }}
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <!-- SIN DATOS -->
                    <tr v-if="rows.length === 0">
                        <td
                            :colspan="headers.length"
                            class="px-5 py-6 text-center text-gray-500"
                        >
                            No hay registros
                        </td>
                    </tr>

                    <!-- DATOS -->
                    <tr
                        v-for="(row, i) in rows"
                        :key="i"
                        class="border-t hover:bg-gray-50"
                    >
                        <td v-for="(cell, c) in row" :key="c" class="px-5 py-3">
                            <span
                                v-if="cell === 'Activo'"
                                class="text-green-600 font-medium"
                            >
                                {{ cell }}
                            </span>

                            <span
                                v-else-if="cell === 'Inactivo'"
                                class="text-red-600 font-medium"
                            >
                                {{ cell }}
                            </span>

                            <span v-else>
                                {{ cell }}
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
