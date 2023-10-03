<script setup>
</script>
<template>
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
</template>

<script>
export default {
    data() {
        return {
            user: {},
        }
    },
    created() {
        this.refreshGames(this.user.id);
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
    }
}
</script>

<style scoped>
.join-add-button {
    margin-bottom: 10rem;
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

