<template>
    <section>
        <form @submit.prevent="addCheck">
            <input class="task-input" type="text" v-model="name" placeholder="Name" required>
            <button type="submit">Toevoegen</button>
        </form>

    </section>
</template>

<script>
import axios from 'axios';

export default {
    props: {
        gameData: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            name: ''
        };
    },
    methods: {
        async addCheck() {
            try {
                // Aanpassen aan uw API-endpoint en datastructuur
                const response = await axios.post('/checkpoints', {
                    name: this.name,
                    game_id: this.gameData.id
                });
                console.log('Checkpoint toegevoegd:', response.data);
                this.title = '';  // Reset het invoerveld
                this.$emit('hideAddCheck');
                this.$emit('reloadCheck');
            } catch (error) {
                console.error('Er is een fout opgetreden:', error);
            }
        }
    }
}
</script>

<style scoped>
section {
    position: fixed;
    z-index: 1;
    width: 80%;
    left: 50%;
    bottom: 0;
    transform: translate(-50%, -100%);
}

form {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-bottom: 3rem;
}

.task-input {
    margin-top: 1rem;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
}
</style>