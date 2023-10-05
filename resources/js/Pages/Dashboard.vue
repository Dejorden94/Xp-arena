<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import GameDetails from '@/Components/GameDetails.vue';
import GameTasks from '@/Components/GameTasks.vue';
import UnverifiedTasks from '@/Components/UnverifiedTasks.vue';
import AddGameComponent from '@/Components/AddGameComponent.vue';
import PinComponent from '@/Components/PinComponent.vue';
import CreateGameComponent from '@/Components/CreateGameComponent.vue';
</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>

        <CreateGameComponent v-show="createGame" />

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

        <UnverifiedTasks v-if="unverifiedTasks.length > 0" :unverifiedTasks="unverifiedTasks" />

        <article>
            <h2>Followed Games</h2>
            <ul>
                <li v-for="game in followedGames" :key="game.id" @click="loadGameDetails(game.id)">
                    {{ game.name }}
                </li>
            </ul>
        </article>
        <PinComponent v-show="showJoin" />
        <AddGameComponent v-show="showAddGame" @showJoin="handleShowJoin" @showCreate="handletoggleCreate" />

    </AuthenticatedLayout>
    <button class="join-add-button" @click="toggleJoinGame"></button>
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
            tasks: {},
            gameData: null,
            gameDetailsVisible: false,
            followedGames: [],
            unverifiedTasks: [],
            showAddGame: false,
            showJoin: false,
            createGame: false,
        }
    },
    created() {
        this.user = this.$page.props.auth.user;
        this.fetchUnverifiedTasks(); // Haal ongeverifieerde taken op bij het laden van de pagina
    },
    mounted() {
        this.fetchFollowedGames();
    },
    methods: {
        async fetchUnverifiedTasks() {
            try {
                const response = await axios.get(`/unverified-tasks`);
                this.unverifiedTasks = response.data;
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
        toggleJoinGame() {
            this.showAddGame = !this.showAddGame;
        },
        handleShowJoin() {
            this.showJoin = !this.showJoin;
        },
        handletoggleCreate() {
            console.log("Create");
            this.createGame = !this.createGame;
        }
    }
}
</script>

<style scoped>
.join-add-button {
    position: absolute;
    bottom: 0;
    left: 50%;
}

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

