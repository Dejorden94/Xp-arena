<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ user.username }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Welkom! {{ user.username }}</div>
                    <div class="p-6 text-gray-900">Current experience points: {{ user.experience }}</div>
                    <div class="p-6 text-gray-900">Level: {{ user.level }}</div>
                </div>
            </div>
        </div>
        <article>
            <h2>Maak een nieuwe game aan</h2>
            <form @submit.prevent="createGame">
                <div>
                    <label for="name">Naam Game:</label>
                    <input type="text" id="name" v-model="name" required>
                </div>
                <button type="submit">Aanmaken</button>
            </form>
        </article>
        <article>
            <h2>Je games</h2>
            <ul>
                <li v-for="game in games" :key="game.id">
                    {{ game.name }}
                </li>
            </ul>
        </article>
    </AuthenticatedLayout>
</template>

<script>
export default {
    data() {
        return {
            name: '',
            games: [],
            user: {}
        }
    },
    created() {
        this.user = this.$page.props.auth.user;
        this.refreshGames(this.user.id);
    },
    methods: {
        createGame() {
            axios.post('/games', {
                name: this.name,
                user_id: this.user.id
            })
                .then(response => {
                    // Game is aangemaakt, navigeer naar de pagina van de nieuwe game
                    // this.$router.push(`/games/${response.data.id}`);
                    this.name = ''; // Leeg het invoerveld voor de naam van de game
                    this.refreshGames(); // Laad de lijst met games opnieuw
                })
                .catch(error => {
                    console.log(error);
                });
        },
        async refreshGames() {
            try {
                const response = await axios.get(`/users/${this.user.id}/games`);
                this.games = response.data;
            } catch (error) {
                console.log(error);
            }
        }

    }
}
</script>
