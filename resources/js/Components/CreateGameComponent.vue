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
            <button type="submit" @click="$emit('reloadGames')">Save</button>
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
    top: 60%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
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
</style>

