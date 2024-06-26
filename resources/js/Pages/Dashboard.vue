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
        :showTaskCheck="showTaskCheck" @toggleAddButton="toggleAddButton" @toggleQuestAddButton="toggleQuestAddButton"
        :showAddJoin="showAddJoin" @showQuestCheck="toggleQuestCheckMenu"
        :isGameQuestDetailsShown="isGameQuestDetailsShown" @isEditing="toggleEdit" :isButtonClicked="isButtonClicked"
        :isQuestButtonClicked="isQuestButtonClicked">


        <GameDetails v-if="gameDetailsVisible && gameData" :gameData="gameData" @hide="hideGameDetails"
            @handleGame="toggleGames" @refreshTasks="refreshTasks" @hideButton="toggleQuestCheck"
            @setUserDetailsFalse="setUserDetailsFalse" @setUserDetailsTrue="setUserDetailsTrue" />

        <GameTasks ref="gameTaskComponent" v-if="gameQuestVisible && gameData" :initialTasks="tasks"
            :gameId="gameData.id" :user="user" :key="gameData.id" :isUserOwner="gameData.isUserOwner"
            :isEditing="isEditing" @hideAll="hideAll" @togglePlayerInfo="showPlayerInfo = !showPlayerInfo"
            @gameQuestDetailsShown="handleGameQuestDetailsShown" @reloadGames="loadGameDetails"
            @showQuestCheck="toggleQuestCheckMenu" @reloadCriteria="fetchCriteria(this.tasks)"
            @hideAddButton="togglAddButton" :criteria="criteria" />

        <article v-show="showGames" class="games-overview">
            <h2>Mijn games</h2>
            <ul>
                <li class="game game-owner" @click="loadGameDetails(game.id); toggleGames();" v-for="game in games"
                    :key="game.id">
                    {{ game.name }}
                    <img class="game-image" :src="getImageUrl(game.image)" alt="Game image">
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
                    <img class="game-image" :src="getImageUrl(game.image)" alt="Game image">
                </li>
            </ul>
        </article>

        <div v-if="showJoin" class="overlay" @click="setFalse()"></div>
        <PinComponent v-show="showJoin" @reloadJoinedGames="toggleAddButton(); fetchFollowedGames(); setFalse();" />

        <div v-if="createGame" class="overlay" @click="setFalse()"></div>
        <CreateGameComponent v-show="createGame" @reloadGames="toggleAddButton(); refreshGames(); setFalse();" />

        <div v-if="showAddGame" class="overlay" @click="handleJoinGame()"></div>
        <AddGameComponent class="add-game-component" v-show="showAddGame" @showCreate="handletoggleCreate"
            @showJoin="handleShowJoin" />

        <div v-if="showAddQuesTaskMenu" class="overlay" @click="setQuestFalse"></div>
        <AddQuestCheckComponent v-show="showAddQuesTaskMenu" @showQuest="toggleAddQuest" @showCheck="toggleCheck" />

        <div v-if="showAddQuest" class="overlay" @click="setQuestFalse"></div>
        <AddQuest v-show="showAddQuest" :gameData="gameData" @refreshTasks="refreshTasks(); setFalseQuestCheck();" />

        <div v-if="showCheckpoint" class="overlay" @click="setQuestFalse"></div>
        <AddCheckpoint v-show="showCheckpoint" :gameData="gameData" @hideAddCheck="showCheckpoint = false"
            @reloadCheck="callCheckpointReload" />

        <div v-show="showGames" class="game-button-container">
            <button @click="handleJoinGame()">+</button>
        </div>

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
            criteria: [],
            isButtonClicked: false,
            isQuestButtonClicked: false
        }
    },
    watch: {
        createGame(newVal) {
            if (newVal === false) {
                this.showAddGame = false;
            }
        },
        showJoin(newVal) {
            if (newVal === false) {
                this.showAddGame = false;
            }
        },
        showAddQuest(newVal) {
            if (newVal === false) {
                this.showAddQuesTaskMenu = false;
            }
        },
        showCheckpoint(newVal) {
            if (newVal === false) {
                this.showAddQuesTaskMenu = false;
            }
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
        setUserDetailsFalse() {
            this.showPlayerInfo = false;
        },
        setUserDetailsTrue() {
            this.showPlayerInfo = true;
        },
        setQuestFalse() {
            this.showAddQuest = false;
            this.showCheckpoint = false;
            this.showAddQuesTaskMenu = false;
            this.isQuestButtonClicked = false;
        },
        toggleAddButton() {
            this.isButtonClicked = !this.isButtonClicked;
        },
        toggleQuestAddButton() {
            this.isQuestButtonClicked = !this.isQuestButtonClicked;
            this.showAddQuest = false;
            if (this.isGameQuestDetailsShown === true) {
                this.showCheckpoint = false;
            }
        },
        getImageUrl(imagePath) {
            return imagePath ? `${imagePath}` : '/images/default-game-image/default.webp'
        },
        callCheckpointReload() {
            this.$refs.gameTaskComponent.fetchCheckpoints();
            this.isQuestButtonClicked = !this.isQuestButtonClicked;
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
                    this.fetchCriteria(this.tasks);
                })
                .catch(error => {
                    console.error(error);
                });
        },
        async fetchCriteria(tasks) {
            const criteriaPromises = tasks.map(task =>
                axios.get(`/follower-task/${task.id}`).then(response => ({
                    taskId: task.id,
                    criteria: response.data,
                    hasCriteria: response.data.length > 0
                })).catch(error => {
                    console.error('Probleem bij het ophalen van criteria voor taak:', task.id, error);
                    return null;
                })
            );

            try {
                const criteriaResponses = await Promise.all(criteriaPromises);
                this.criteria = criteriaResponses.filter(response => response !== null);
            } catch (error) {
                console.error('Probleem bij het verwerken van criteria:', error);
            }
        },
        hideGameDetails() {
            this.gameDetailsVisible = false;
            this.gameQuestVisible = false;
            this.showTaskCheck = false;
            this.showAddJoin = true;
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
            this.isQuestButtonClicked = !this.isQuestButtonClicked;
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
            isButtonClicked = false;
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
    justify-content: flex-end;
    flex-direction: column;
    width: 80%;
    margin-left: auto;
    margin-right: auto;
    flex-wrap: wrap;
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
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
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
    width: 49%;
    height: 10rem;
    font-size: 150%;
    font-weight: 700;
    margin-bottom: 2rem;
    border-radius: 1rem;
    position: relative;
}



.game-button-container {
    display: flex;
    justify-content: center;
    width: 100%;
    height: 3rem;

    button {
        border: none;
        background: linear-gradient(90deg, #FDA829, #FF5C00);
        display: block;
        font-weight: 100;
        font-size: 370%;
        width: 8rem;
        height: 8rem;
        border-radius: 50%;
    }
}

.join-add-button {
    border: none;
    background: linear-gradient(90deg, #FDA829, #FF5C00);
    display: block;
    font-weight: 100;
    font-size: 500%;
    width: 10rem;
    height: 10rem;
    border-radius: 50%;
    position: absolute;
    bottom: 2rem;
    left: 50%;
    margin-left: -5rem;
}

.join-add-button.clicked {
    background: linear-gradient(90deg, #4DA9FF, #3770A6);

}

.criterion-edit {
    display: flex;
    justify-content: center;
    align-items: center;
}

.fa-pen,
.fa-floppy-disk {
    font-size: 5rem;
}

.quest-check-button {
    border: none;
    background: linear-gradient(90deg, #FDA829, #FF5C00);
    display: block;
    font-weight: 100;
    font-size: 500%;
    width: 10rem;
    height: 10rem;
    border-radius: 50%;
    position: absolute;
    bottom: 2rem;
    left: 50%;
    margin-left: -5rem;
}

.game-owner {
    background: var(--background-super-dark);
}

.game-image {
    position: absolute;
    width: 10rem;
    height: 10rem;
    right: 0;
    border-radius: 0 1rem 1rem 0;
    object-fit: cover;
}

.games-overview {
    color: var(--font-color-normal);
}

.games-overview:first-child {
    margin-top: 8rem;
}

.games-overview:nth-child(2) {
    margin-bottom: 10rem;
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

@media screen and (max-width: 1280px) {
    .games-overview:first-child {
        margin-top: 0;
    }

    .game-button-container {
        display: none;
    }

    article {
        margin-bottom: 2rem;
    }

    .game {
        width: 100%;
    }

    ul {
        display: block;
        flex-wrap: nowrap;
    }
}
</style>
