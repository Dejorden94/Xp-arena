<template>
    <article v-if="gameData.isUserOwner"> <!--Compnent wanneer owner de game laad-->
        <button @click="hideGameDetails">Verberg</button>
        <section>
            <h1>{{ gameData.name }}</h1>
            <h2>{{ gameData.pin_code }}</h2>
        </section>

        <form @submit.prevent="addTask">
            <input v-model="newTaskName" type="text" placeholder="Titel" required>
            <textarea v-model="newTaskDescription" placeholder="Beschrijving" required></textarea>
            <input v-model="newTaskExperience" type="number" placeholder="Experience" required>
            <button type="submit">Toevoegen</button>
        </form>
    </article>

    <article v-if="!gameData.isUserOwner"> <!--Component wanneer NIET de owner de game laad-->
        <button @click="hideGameDetails">Verberg</button>
        <section>
            <h1>{{ gameData.name }}</h1>
            <h2>{{ gameData.description }}</h2>
            <p>{{ gameData.owner }}</p>
        </section>

        <section v-if="gameData.tasks">
            <h3>Quests</h3>
            <ul>
                <li v-for="task in gameData.tasks" :key="task.id">
                    {{ task.name }}
                </li>
            </ul>
        </section>
    </article>
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
        hideGameDetails() {
            this.$emit('hide');
        },
        addTask() {
            axios.post(`/games/${this.gameData.id}/add-task`, { // gebruik de nieuwe route
                name: this.newTaskName,
                description: this.newTaskDescription,
                experience: this.newTaskExperience
            })
                .then(response => {
                    // ... je huidige logica om taken toe te voegen ...
                })
                .catch(error => {
                    console.error(error);
                });
        },
        fetchTasks() {
            if (this.gameData.id) {
                axios.get(`/api/games/${this.gameData.id}/tasks`)
                    .then(response => {
                        this.gameData.tasks = response.data.tasks;
                    })
                    .catch(error => {
                        console.error(error);
                    });
            }
        },
        deleteTask(taskId) {
            axios.delete(`/api/games/${this.gameData.id}/tasks/${taskId}`)
                .then(response => {
                    // Verwijder de taak uit de array
                    const index = this.gameData.tasks.findIndex(task => task.id === taskId);
                    if (index !== -1) {
                        this.gameData.tasks.splice(index, 1);
                    }
                })
                .catch(error => {
                    console.error(error);
                });
        }
    },
    created() {
        this.fetchTasks();
    }
};
</script>

<style scoped>
article {
    width: 100vw;
    padding: 1rem;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    margin-bottom: 1rem;
}

form {
    display: flex;
    flex-direction: column;
    margin-top: 1rem;
}

input,
textarea {
    padding: 0.5rem;
    margin-bottom: 0.5rem;
    border: 1px solid #ccc;
    border-radius: 5px;
}
</style>

