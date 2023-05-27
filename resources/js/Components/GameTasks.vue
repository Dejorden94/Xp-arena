<template>
    <article>
        <h3>Taken</h3>
        <ul>
            <li v-for="task in tasks" :key="task.id">
                {{ task.name }} - {{ task.description }} - {{ task.experience }}
                <input v-if="!isUserOwner" type="checkbox" @change="checkTask(task.id)">
                <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
            </li>
        </ul>
    </article>
</template>
  
<script>
export default {
    props: {
        tasks: {
            type: Array,
            required: true,
        },
        gameId: {
            type: Number,
            required: true,
        },
        isUserOwner: {
            type: Boolean,
            default: false,
        }
    },
    methods: {
        deleteTask(taskId) {
            axios
                .delete(`/api/games/${this.gameId}/tasks/${taskId}`)
                .then((response) => {
                    // Verwijder de taak uit de array
                    const index = this.tasks.findIndex((task) => task.id === taskId);
                    if (index !== -1) {
                        this.tasks.splice(index, 1);
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        checkTask(taskId) {
            axios.post(`/api/tasks/${task.id}/complete`)
                .then(response => {
                    // Handle response
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },
};
</script>
  
<style scoped>
article {
    border: 1px solid #ccc;
    padding: 1rem;
    margin-bottom: 1rem;
    background-color: #f9f9f9;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}

h3 {
    margin-top: 0;
}

ul {
    list-style: none;
    padding: 0;
}

li {
    margin-bottom: 0.5rem;
    padding: 0.5rem;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
}
</style>

  