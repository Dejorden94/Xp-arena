<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="register-form-page">
        <div>
            <Link href="/">
            <ApplicationLogo />
            </Link>
        </div>

        <div class="register-form">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.register-form-page {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background: var(--background-darker);
}

.register-form {
    background: var(--background-super-dark);
    width: 20vw;
    padding: 2rem;
    display: flex;
    justify-content: center;
    color: var(--font-color-normal);
}
</style>