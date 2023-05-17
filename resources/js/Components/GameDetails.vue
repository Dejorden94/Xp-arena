<template>
    <article>
        <button @click="hideGameDetails">Verberg</button>
        <section>
            <h1>{{ gameData.name }}</h1>
        </section>

        <form @submit.prevent="addTask">
            <input v-model="newTaskName" type="text" placeholder="Titel" required>
            <textarea v-model="newTaskDescription" placeholder="Beschrijving" required></textarea>
            <button type="submit">Toevoegen</button>
        </form>
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
}

form {
    display: flex;
    flex-direction: column;
}
</style>
