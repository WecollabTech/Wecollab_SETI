<script setup>
import AppHeader from "@/Components/Layout/AppHeader.vue";
import AppFooter from "@/Components/Layout/AppFooter.vue";
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <!-- HEADER (SIEMPRE FIJO) -->
        <AppHeader />

        <!-- CONTENIDO PRINCIPAL -->
        <main class="flex-1 p-6">
            <!-- TÍTULO DE PÁGINA (OPCIONAL) -->
            <div v-if="$slots.title" class="mb-6">
                <h2 class="text-2xl font-semibold text-[#422782]">
                    <slot name="title" />
                </h2>
            </div>

            <!-- CONTENIDO DE LA PÁGINA -->
            <slot />
        </main>

        <!-- FOOTER -->
        <AppFooter />
    </div>
</template>
