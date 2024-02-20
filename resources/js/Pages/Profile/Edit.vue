<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>

        <div class="edit-player-form">
            <div>
                <UpdateProfileInformationForm :must-verify-email="mustVerifyEmail" :status="status" />
            </div>

            <div>
                <UpdatePasswordForm />
            </div>

            <div>
                <DeleteUserForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.head {
    padding: 0 2rem;
}

.edit-player-form {
    margin-left: auto;
    margin-right: 1%;
    width: 90%;
    padding: 0 2rem;
}

@media screen and (max-width:1280px) {
    .edit-player-form {
        width: 100%;
        margin-left: 0;
        margin-right: 0;
    }
}
</style>
