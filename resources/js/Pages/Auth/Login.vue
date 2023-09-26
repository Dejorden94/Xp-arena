<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <div class="login-styling">
        <GuestLayout :isLoginPage="true">

            <Head title="Log in" />

            <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                {{ status }}
            </div>

            <form @submit.prevent="submit">
                <h2>Login</h2>
                <p>Welcome back!</p>
                <div class="login-field">
                    <InputLabel for="email" value="Email:" />

                    <TextInput class="input" id="email" type="email" v-model="form.email" required autofocus
                        autocomplete="username" />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="login-field">
                    <InputLabel for="password" value="Password:" />

                    <TextInput class="input" id="password" type="password" v-model="form.password" required
                        autocomplete="current-password" />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="remember-section">
                    <label>
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span class="remember-text">Remember me</span>
                    </label>
                </div>

                <div class="submit-section">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="password-forgot">
                    Forgot your password?
                    </Link>

                    <PrimaryButton class="login-button" :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing">
                        Login
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
        <article class="create-account">
            <h2>Create Account</h2>
            <p>Don't have an account yet? No problem, just click the button below.</p>
            <Link href="/register">
            <button>Create Account</button>
            </Link>
        </article>
    </div>
</template>

<style scoped>
.login-styling {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.login-field {
    height: 100%;
    display: flex;
    justify-content: space-between;
}

.input {
    background: var(--background-lighter);
    border: none;
    width: 60%;
    height: 1.8rem;
    margin-left: auto;
    margin-bottom: 1rem;
}

.rember-section {
    display: flex;
    justify-content: flex-start;
}

.remember-text {
    margin-left: 1rem;
}

.submit-section {
    margin-top: 1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.password-forgot {
    text-decoration: underline;
}

h2,
p {
    text-align: center;
}

p {
    margin-bottom: 2rem;
}

.create-account {
    padding: 4rem;
    width: 25vw;
    min-width: 30rem;
    max-width: 60rem;
    background: var(--background-super-dark);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    box-shadow: var(--box-shadow);
    border: 0.2rem solid var(--background-lighter);
    border-radius: 1rem;
}

.login-button {
    margin: 0;
    font-size: 100%;
}
</style>