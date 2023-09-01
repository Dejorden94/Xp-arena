<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { Head } from '@inertiajs/vue3';

import GameDetails from '@/Components/GameDetails.vue';
import GameTasks from '@/Components/GameTasks.vue';
import UnverifiedTasks from '@/Components/UnverifiedTasks.vue';

</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ user.username }}</h2>
        </template>

        <article class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">Welkom! {{ user.username }}</div>
                    <div class="p-6 text-gray-900">Current experience points: {{ user.experience }}</div>
                    <div class="p-6 text-gray-900">Level: {{ user.level }}</div>
                </div>
            </div>
        </article>

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

        <GameDetails v-if="gameDetailsVisible && gameData" :gameData="gameData" @hide="hideGameDetails" />

        <GameTasks v-if="gameDetailsVisible && gameData" :initialTasks="tasks" :gameId="gameData.id" :user="user"
            :key="gameData.id" :isUserOwner="gameData.isUserOwner" />

        <article>
            <h2>Je games</h2>
            <ul>
                <li v-on:click="loadGameDetails(game.id)" v-for="game in games" :key="game.id">
                    {{ game.name }}
                </li>
            </ul>
        </article>

        <UnverifiedTasks v-if="unverifiedTasks.length > 0" />

        <article>
            <h2>Voer een game pincode in</h2>
            <form @submit.prevent="submitPincode">
                <div>
                    <label for="pincode">Pincode:</label>
                    <input type="text" id="pincode" v-model="pincode" required>
                </div>
                <button type="submit">Verzenden</button>
            </form>
        </article>
        <article>
            <h2>Followed Games</h2>
            <ul>
                <li v-for="game in followedGames" :key="game.id" @click="loadGameDetails(game.id)">
                    {{ game.name }}
                </li>
            </ul>
        </article>
    </AuthenticatedLayout>
</template>

<script>
export default {
    components: {
        GameDetails,
        GameTasks,
        UnverifiedTasks
    },
    data() {
        return {
            name: '',
            games: [],
            user: {},
            tasks: {},
            gameData: null,
            gameDetailsVisible: false,
            pincode: '',
            validPincodes: [],
            validPincode: false,
            followedGames: [],
            unverifiedTasks: [],
        }
    },
    created() {
        this.user = this.$page.props.auth.user;
        this.refreshGames(this.user.id);
    },
    mounted() {
        this.fetchFollowedGames();

    },
    methods: {
        createGame() {
            axios.post('/games', {
                name: this.name,
                user_id: this.user.id
            })
                .then(response => {
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
        },
        async loadGameDetails(gameId) {
            try {
                const response = await axios.get(`/api/games/${gameId}`);
                if (response.data) {
                    this.gameData = response.data;
                    this.gameData.isUserOwner = this.user.id === this.gameData.user_id;


                    if (this.gameData.isUserOwner) {
                        this.fetchTasks(gameId); // Wacht op het ophalen van taken
                    } else {
                        this.fetchFollowedTasks(gameId); // Wacht op het ophalen van gevolgde taken
                    }

                    this.gameDetailsVisible = true;
                } else {
                    console.error("Geen data ontvangen van de API.");
                }
            } catch (error) {
                console.error(error);
            }
        },
        fetchTasks(gameId) {
            axios.get(`/api/games/${gameId}/tasks`)
                .then(response => {
                    this.tasks = response.data.tasks;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        fetchFollowedTasks(gameId) {
            axios.get(`/users/${this.user.id}/games/${gameId}/followed-tasks`)
                .then(response => {
                    this.tasks = response.data.tasks;
                })
                .catch(error => {
                    console.error(error);
                });
        },
        hideGameDetails() {
            this.gameDetailsVisible = false;
        },
        submitPincode() {
            axios.post('/games/follow', {
                pincode: this.pincode
            })
                .then(response => {
                    this.pincode = '';
                    this.fetchFollowedGames(); // Leeg het invoerveld voor de pincode
                })
                .catch(error => {
                    console.log(error);
                });
        },
        fetchFollowedGames() {
            axios.get('/dashboard/games')
                .then(response => {
                    this.followedGames = response.data.games;
                })
                .catch(error => {
                    console.log(error);
                });
        },
        refreshTasks() {
            this.loadGameDetails(this.gameData.id);
        },

    }
}
</script>

<style scoped>
article,
GameDetails,
GameTasks {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    width: 60%;
    margin-left: auto;
    margin-right: auto;
}


h2 {
    color: #333;
    margin-bottom: 1rem;
}

form {
    margin-top: 1rem;
    width: 100%;
}

form>div {
    margin-bottom: 1rem;
}

input,
textarea {
    padding: 0.5rem;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    background-color: #007BFF;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

ul {
    padding-left: 1rem;
}

li {
    margin-bottom: 0.5rem;
    cursor: pointer;
}

li:hover {
    color: #007BFF;
}
</style>

