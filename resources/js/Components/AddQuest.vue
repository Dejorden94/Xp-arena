<template>
    <section>
        <form @submit.prevent="addTask">
            <input class="task-input" v-model="newTaskName" type="text" placeholder="Titel" required>
            <textarea class="task-input" v-model="newTaskDescription" placeholder="Beschrijving" required></textarea>
            <input class="task-input" v-model="newTaskExperience" type="number" placeholder="Experience" required>
            <button type="submit" @click="$emit('refreshTasks')">Toevoegen</button>
        </form>
    </section>
</template>

<script>
export default {
    props: {
        gameData: {
            type: Object,
            required: true
        }
    },
    methods: {
        addTask() {
            axios.post(`/games/${this.gameData.id}/add-task`, { // gebruik de nieuwe route
                name: this.newTaskName,
                description: this.newTaskDescription,
                experience: this.newTaskExperience
            })
                .then(response => {

                })
                .catch(error => {
                    console.error(error);
                });
        },
    }
}
</script>

<style scoped>
form {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    margin-top: 1rem;
}

.task-input {
    margin-top: 1rem;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    border-radius: 5px;
}
</style>
