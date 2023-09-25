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
    margin-top: 2rem;
    background: var(--background-super-dark);
    max-width: 35rem;
    padding: 4rem;
    display: flex;
    justify-content: center;
    color: var(--font-color-normal);
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
    border: 0.2rem solid var(--background-lighter);
    border-radius: 1rem;
}
</style>