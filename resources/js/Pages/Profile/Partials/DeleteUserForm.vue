<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.reset();
};
</script>

<template>
    <article class="player-edit-section delete-section">
        <header>
            <h2>Delete Account</h2>
            <p>
                Wanneer je je account verwijdert dan worden alle data verwijdert. Denk dus goed na voor da je de knop
                hieronder drukt!
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Delete Account</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <article>
                <h2>Are you sure you want to delete your account?</h2>
                <p>
                    Once your account is deleted, all of its resources and data will be permanently deleted. Please
                    enter your password to confirm you would like to permanently delete your account.
                </p>

                <section>
                    <InputLabel for="password" value="Password" />
                    <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                        placeholder="Password" @keyup.enter="deleteUser" />
                    <InputError :message="form.errors.password" />
                </section>

                <section>
                    <SecondaryButton @click="closeModal"> Cancel </SecondaryButton>
                    <DangerButton :disabled="form.processing" @click="deleteUser">
                        Delete Account
                    </DangerButton>
                </section>
            </article>
        </Modal>
    </article>
</template>

<style scoped>
.delete-section {
    margin-bottom: 10rem;
}
</style>