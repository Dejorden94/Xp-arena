<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

import GameDetails from '@/Components/GameDetails.vue';
import GameTasks from '@/Components/GameTasks.vue';
import UnverifiedTasks from '@/Components/UnverifiedTasks.vue';
import AddGameComponent from '@/Components/AddGameComponent.vue';
import PinComponent from '@/Components/PinComponent.vue';
import CreateGameComponent from '@/Components/CreateGameComponent.vue';
import AddQuest from '@/Components/AddQuest.vue'
import AddCheckpoint from '@/Components/AddCheckpoint.vue'
import AddQuestCheckComponent from '@/Components/AddQuestCheckComponent.vue'

</script>


<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout @showJoin="handleJoinGame" @showTaskCheck="handleQuestCheck" :show-player-info="showPlayerInfo"
        :showTaskCheck="showTaskCheck" :showAddJoin="showAddJoin" @showQuestCheck="toggleQuestCheckMenu"
        :isGameQuestDetailsShown="isGameQuestDetailsShown" @isEditing="toggleEdit">


        <GameDetails v-if="gameDetailsVisible && gameData" :gameData="gameData" @hide="hideGameDetails"
            @handleGame="toggleGames" @refreshTasks="refreshTasks" @hideButton="toggleQuestCheck" />

        <GameTasks ref="gameTaskComponent" v-if="gameQuestVisible && gameData" :initialTasks="tasks" :gameId="gameData.id"
            :user="user" :key="gameData.id" :isUserOwner="gameData.isUserOwner" :isEditing="isEditing" @hideAll="hideAll"
            @togglePlayerInfo="showPlayerInfo = !showPlayerInfo" @gameQuestDetailsShown="handleGameQuestDetailsShown"
            @reloadGames="loadGameDetails" />

        <article v-show="showGames" class="games-overview">
            <h2>Je games</h2>
            <ul>
                <li class="game" @click="loadGameDetails(game.id); toggleGames();" v-for="game in games" :key="game.id">
                    {{ game.name }}
                </li>

            </ul>
        </article>

        <UnverifiedTasks v-if="unverifiedTasks.length > 0" :unverifiedTasks="unverifiedTasks" />

        <article v-show="showGames" class="games-overview">
            <h2>Followed Games</h2>
            <ul>
                <li class="game" v-for="game in followedGames" :key="game.id"
                    @click="loadGameDetails(game.id); toggleGames();">
                    {{ game.name }}
                </li>
            </ul>
        </article>

        <div v-if="showJoin" class="overlay"></div>
        <PinComponent v-show="showJoin" @reloadJoinedGames="fetchFollowedGames(); setFalse();" />

        <div v-if="createGame" class="overlay"></div>
        <CreateGameComponent v-show="createGame" @reloadGames="refreshGames(); setFalse();" />

        <div v-if="showAddGame" class="overlay"></div>
        <AddGameComponent class="add-game-component" v-show="showAddGame" @showCreate="handletoggleCreate"
            @showJoin="handleShowJoin" />

        <div v-if="showAddQuesTaskMenu" class="overlay"></div>
        <AddQuestCheckComponent v-show="showAddQuesTaskMenu" @showQuest="toggleAddQuest" @showCheck="toggleCheck" />

        <div v-if="showAddQuest" class="overlay"></div>
        <AddQuest v-show="showAddQuest" :gameData="gameData" @refreshTasks="refreshTasks(); setFalseQuestCheck();" />

        <div v-if="showCheckpoint" class="overlay"></div>
        <AddCheckpoint v-show="showCheckpoint" :gameData="gameData" @hideAddCheck="showCheckpoint = false"
            @reloadCheck="callCheckpointReload" />

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
            tasks: {},
            gameData: null,
            gameDetailsVisible: false,
            gameQuestVisible: false,
            followedGames: [],
            unverifiedTasks: [],
            showAddGame: false,
            showJoin: false,
            createGame: false,
            showGames: true,
            showTaskCheck: false,
            showAddJoin: true,
            showAddQuest: false,
            showAddQuesTaskMenu: false,
            showCheckpoint: false,
            showPlayerInfo: true,
            isGameQuestDetailsShown: false,
            isEditing: false,
        }
    },

    created() {
        this.user = this.$page.props.auth.user;
        this.fetchUnverifiedTasks(); // Haal ongeverifieerde taken op bij het laden van de pagina
        this.refreshGames(this.user.id);
        this.fetchFollowedGames(this.user.id)
    },
    mounted() {
        this.fetchFollowedGames();
        this.levelCheck();
    },
    methods: {
        callCheckpointReload() {
            console.log("call checkpoint reload");
            this.$refs.gameTaskComponent.fetchCheckpoints();
        },
        levelCheck() {
            const response = axios.get(`user/${this.user.id}/checkLevel`)
                .then(response => {
                })
                .catch(error => {
                    console.log('Error during level check', error);
                });
        },
        async fetchUnverifiedTasks() {
            try {
                const response = await axios.get(`/unverified-tasks`);
                this.unverifiedTasks = response.data;
            } catch (error) {
                console.error(error);
            }
        },
        async refreshGames() {
            try {
                const response = await axios.get(`/users/${this.user.id}/games`);
                this.games = response.data;
            } catch (error) {
                console.log(error);
            }
        },
        async loadGameDetails(gameId, preserveTaskCheck = false) {
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
                    this.gameQuestVisible = true;
                    if (!preserveTaskCheck) {
                        this.toggleQuestCheck();
                    }
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
            this.gameQuestVisible = false;
            this.showTaskCheck = false;
            this.showAddJoin = true;
            this.is
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
            this.loadGameDetails(this.gameData.id, true);
            this.showTaskCheck = true;
        },
        handleGameQuestDetailsShown(isShown, isUserOwner) {
            if (isUserOwner) {
                this.isGameQuestDetailsShown = isShown;
            } else {
                this.isGameQuestDetailsShown = false;
            }
        },
        handleJoinGame() {
            this.showAddGame = !this.showAddGame;
            this.createGame = false;
            this.showJoin = false;
        },
        handleShowJoin() {
            this.showJoin = !this.showJoin;
            this.showAddGame = !this.showAddGame;
        },
        handleQuestCheck() {
            this.showAddQuest = !this.showAddQuest;
            this.showAddCheck = !this.showAddCheck;
        },
        handletoggleCreate() {
            this.createGame = !this.createGame;
            this.showAddGame = !this.showAddGame;
        },
        setFalse() {
            this.showJoin = false;
            this.showAddGame = false;
            this.createGame = false;
            this.showAddQuesTaskMenu = false;
        },
        toggleGames() {
            this.showGames = !this.showGames;
        },
        toggleQuestCheck() {
            if (this.gameData) {
                if (this.gameData.isUserOwner) {
                    this.showTaskCheck = !this.showTaskCheck;
                    this.showAddJoin = !this.showAddJoin;
                } else {
                    this.showTaskCheck = false;
                    this.showAddJoin = false;
                }
            } else {
                this.showTaskCheck = false;
                this.showAddJoin = true;
            }
        },

        toggleQuestCheckMenu() {
            this.showAddQuesTaskMenu = !this.showAddQuesTaskMenu;
            this.showCheckpoint = false;
        },
        toggleAddQuest() {
            this.showAddQuest = !this.showAddQuest;
            this.showAddQuesTaskMenu = !this.showAddQuesTaskMenu;
        },
        setFalseQuestCheck() {
            this.showAddQuesTaskMenu = false;
            this.showAddQuest = false;

            // Toevoegen wanneer checkpoint component en functionaliteit is gemaakt
            // this.showCheckpoint = false;
        },
        hideAll() {
            this.gameDetailsVisible = !this.gameDetailsVisible;
        },
        toggleEdit() {
            this.isEditing = !this.isEditing;
        },
        toggleCheck() {
            this.showCheckpoint = !this.showCheckpoint;
            this.showAddQuesTaskMenu = false;
        }
    }
}
</script>

<style scoped>
article {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 2rem;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
}


h2 {
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
    width: 100%;
    height: 100%;
}

li {
    margin-bottom: 0.5rem;
    cursor: pointer;
}

li:hover {
    color: #007BFF;
}

.game {
    padding-left: 1rem;
    display: flex;
    align-items: center;
    background: linear-gradient(#4DA9FF, #3770A6);
    width: 100%;
    height: 10rem;
    font-size: 150%;
    font-weight: 700;
    margin-bottom: 2rem;
    border-radius: 1rem;
}

.games-overview {
    /* background: var(--background-lighter); */
    color: var(--font-color-normal);
}

.overlay {
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 0;
}
</style>

