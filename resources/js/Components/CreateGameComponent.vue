<script setup>
</script>
<template>
    <article class="create-game">
        <figure>
            <img src="images/Logo-Xp-Arena.png" alt="">
        </figure>
        <h2>Create game</h2>
        <form @submit.prevent="createGame">
            <input placeholder="Enter game name" type="text" id="name" v-model="name" required>
            <input type="file" @change="handleFileUpload" id="game_image">
            <button type="submit">Save</button>
        </form>
    </article>
</template>

<script>
export default {
    data() {
        return {
            user: {},
            name: '',
            gameImage: null
        }
    },
    created() {
        this.refreshGames(this.user.id);
    },
    methods: {
        handleFileUpload(event) {
            this.gameImage = event.target.files[0];
        },
        async createGame() {
    let formData = new FormData();
    formData.append('name', this.name);
    if (this.gameImage) {
        formData.append('game_image', this.gameImage);
    }
    try {
        const gameResponse = await axios.post('/games', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });

        // Reset het invoerveld voor de naam van de game
        this.name = '';

        // Haal de ID van de nieuw aangemaakte game op
        const gameId = gameResponse.data.id;

        // Maak automatisch een standaard checkpoint aan
        await this.createDefaultCheckpoint(gameId);

        // Herlaad de games en sluit het formulier
        this.$emit('reloadGames');
        this.refreshGames();

    } catch (error) {
        if (error.response.status === 422 && error.response.data.errors && error.response.data.errors.game_image) {
            // Toon de foutmelding voor het geval van ongeldig bestandsformaat
            alert(error.response.data.errors.game_image[0]);
        } else {
            console.error('Fout bij het aanmaken van de game:', error);
        }
    }
},

        async createDefaultCheckpoint(gameId) {
            try {
                await axios.post('/checkpoints', {
                    name: 'Checkpoint 1',
                    game_id: gameId
                });
                console.log('Standaard checkpoint aangemaakt voor game ID:', gameId);
            } catch (error) {
                console.error('Fout bij het aanmaken van het standaard checkpoint:', error);
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
    }
}

</script>

<style scoped>
.create-game {
    background: var(--background-super-dark);
    border: 2px solid var(--background-lighter);
    border-radius: 1rem;
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    top: 70%;
    left: 60%;
    transform: translate(-50%, -50%);
    width: 50%;
    height: 40rem;
    padding: 0;
}

h2 {
    font-size: 150%;
    margin-top: 1rem;
    color: var(--font-white);
}

form {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    width: 100%;
}

input {
    text-align: center;
    margin: 0 auto 1rem auto;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    width: 60%;
}

button {
    background: var(--background-lighter);
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

@media screen and (max-width:1280px) {
    .create-game {
        top: 60%;
        left: 50%;
        width: 80%;
    }
}
</style>

