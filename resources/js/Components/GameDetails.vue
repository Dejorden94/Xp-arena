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
            <h3>Tasks</h3>
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
            axios.post(`/api/games/${this.gameData.id}/tasks`, {
                name: this.newTaskName,
                description: this.newTaskDescription
            })
                .then(response => {
                    this.gameData.tasks.push(response.data.task);
                    this.newTaskName = '';
                    this.newTaskDescription = '';
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
    display: flex;
    justify-content: center;
    flex-direction: column;
}



form {
    display: flex;
    flex-direction: column;
}
</style>
