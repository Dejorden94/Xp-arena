<template>
    <article>
        <h3>Quests</h3>
        <ul>
            <li v-for="task in initialTasks" :key="task.id">
                <input v-if="!isUserOwner" type="checkbox" v-model="task.completed"
                    @change="toggleTaskCompletion(task.id)" />
                {{ task.name }} - {{ task.description }} - {{ task.experience }} - {{ task.status }}
                <span v-if="task.status === 'pending'" class="task-status completed">Pending</span>
                <span v-if="task.status === 'completed'" class="task-status completed">Voltooid</span>
                <span v-else-if="task.status === 'rejected'" class="task-status rejected">Afgewezen</span>
                <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
            </li>
        </ul>
    </article>
</template>

<script>
export default {
    props: {
        initialTasks: {
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
        },
        user: {
            type: Object,
            required: true
        }
    },
    methods: {
        deleteTask(taskId) {
            axios
                .delete(`/api/games/${this.gameId}/tasks/${taskId}`)
                .then((response) => {
                    // Verwijder de taak uit de array
                    const index = this.initialTasks.findIndex((task) => task.id === taskId);
                    if (index !== -1) {
                        this.initialTasks.splice(index, 1);
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        toggleTaskCompletion(taskId) {
            // Stuur een verzoek om de voltooiingsstatus van de taak te wijzigen
            axios
                .post(`/games/${this.gameId}/tasks/${taskId}/completeTask`)
                .then((response) => {
                    // Update de voltooide status van de taak in de lijst
                    const task = this.initialTasks.find((t) => t.id === taskId);
                    if (task) {
                        task.status = 'completed'; // Je kunt de status aanpassen op basis van de serverreactie
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
    }
};
</script>

<style scoped>
/* Voeg stijlen toe aan de statusindicator */
.task-status {
    margin-left: 10px;
    font-size: 12px;
    padding: 2px 5px;
    border-radius: 5px;
}

.completed {
    background-color: green;
    color: white;
}

.rejected {
    background-color: red;
    color: white;
}

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

  