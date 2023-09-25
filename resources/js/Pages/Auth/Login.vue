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
                    <InputLabel for="email" value="Email" />

                    <TextInput id="email" type="email" v-model="form.email" required autofocus autocomplete="username" />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="login-field">
                    <InputLabel for="password" value="Password" />

                    <TextInput id="password" type="password" v-model="form.password" required
                        autocomplete="current-password" />

                    <InputError class="mt-2" :message="form.errors.password" />
                </div>

                <div class="login-field">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" />
                        <span>Remember me</span>
                    </label>
                </div>

                <div class="">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="">
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
    height: 100vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.login {
    background: var(--background-lighter);
}

.login-field {
    height: 100%;
    display: flex;
    /* flex-direction: column; */
}

h2,
p {
    text-align: center;
}

p {
    margin-bottom: 2rem;
}

.create-account {
    padding: 4rem 2rem;
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
    font-size: 100%;
}
</style>