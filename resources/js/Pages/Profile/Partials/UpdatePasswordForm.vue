<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value.focus();
            }
        },
    });
};
</script>

<template>
    <article class="player-edit-section">
        <header>
            <h2>Update Password</h2>
        </header>

        <form @submit.prevent="updatePassword">
            <section>
                <InputLabel for="current_password" value="Password" />
                <TextInput class="input" id="current_password" ref="currentPasswordInput" v-model="form.current_password"
                    type="password" autocomplete="current-password" />
                <InputError :message="form.errors.current_password" />
            </section>

            <section>
                <InputLabel for="password" value="New Password" />
                <TextInput class="input" id="password" ref="passwordInput" v-model="form.password" type="password"
                    autocomplete="new-password" />
                <InputError :message="form.errors.password" />
            </section>

            <section>
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput class="input" id="password_confirmation" v-model="form.password_confirmation" type="password"
                    autocomplete="new-password" />
                <InputError :message="form.errors.password_confirmation" />
            </section>

            <section>
                <PrimaryButton class="save-button" :disabled="form.processing">Save</PrimaryButton>
                <Transition>
                    <p v-if="form.recentlySuccessful">Saved.</p>
                </Transition>
            </section>
        </form>
    </article>
</template>

<style scoped>
section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 1rem;
}

h2 {
    text-align: center;
}

.input {
    width: 50%;
    margin-bottom: 1rem;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
    gap: 1rem;
}

.save-button {
    margin-left: auto;
}
</style>