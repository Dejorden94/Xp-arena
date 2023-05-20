<template>
    <article>
        <h3>Taken</h3>
        <ul>
            <li v-for="task in tasks" :key="task.id">
                {{ task.name }} - {{ task.description }}
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
    },
};
</script>
  
<style scoped>
/* Optionele stijlen voor de weergave van taken */
ul {
    list-style: none;
    padding: 0;
}

li {
    margin-bottom: 0.5rem;
}
</style>
  