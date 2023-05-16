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

        <ul>
            <li v-for="task in gameData.tasks" :key="task.id">
                {{ task.name }} - {{ task.description }}
            </li>
        </ul>

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
    data() {
        return {
            newTaskName: '',
            newTaskDescription: ''
        };
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
