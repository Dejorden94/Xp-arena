<template>
    <section>
        <form @submit.prevent="addTask">
            <input class="task-input" v-model="newTaskName" type="text" placeholder="Titel" required>
            <textarea class="task-input" v-model="newTaskDescription" placeholder="Beschrijving" required></textarea>

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
                description: this.newTaskDescription
            })
                .then(response => {
                    this.newTaskName = "",
                        this.newTaskDescription = ""
                })
                .catch(error => {
                    console.error(error);
                });
        },
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
