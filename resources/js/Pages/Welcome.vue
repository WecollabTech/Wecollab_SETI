<script setup>
import { Head, Link } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import HeroSection from "@/Components/Hero/HeroSection.vue";
</script>

<template>
    <Head title="Welcome" />
    <AppLayout>
        <!-- HERO SOLO AQUÍ -->
        <HeroSection />
    </AppLayout>
</template>
