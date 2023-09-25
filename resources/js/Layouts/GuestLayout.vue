<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    isLoginPage: Boolean
});
</script>

<template>
    <div class="register-form-page">
        <div>
            <Link href="/">
            <ApplicationLogo />
            </Link>
        </div>

        <div :class="isLoginPage ? 'login-form' : 'register-form'">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.register-form-page {
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
    box-shadow: var(--box-shadow);
    border: 0.2rem solid var(--background-lighter);
    border-radius: 1rem;
}

.login-form {
    background: var(--background-super-light);
    height: 20rem;
    border: none;
    margin: 2rem 0;
    border-radius: 1rem;
    width: 25vw;
    min-width: 30rem;
    max-width: 60rem;
    padding: 4rem 2rem;
}
</style>