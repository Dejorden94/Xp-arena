<template>
    <article>
        <h3>Quests</h3>
        <ul>
            <li v-for="task in initialTasks" :key="task.id">
                <input v-if="task.status === 'pending' && !isUserOwner || task.status === 'rejected'" type="checkbox"
                    v-model="task.completed" @change="toggleTaskCompletion(task.id)" />
                <p v-if="!isUserOwner">{{ task.name }} - {{ task.description }} - {{ task.experience }} - {{ task.status
                }}</p>

                <p v-if="isUserOwner">{{ task.name }} - {{ task.description }} - {{ task.experience }}</p>

                <span v-if="task.status === 'pending'" class="task-status completed">Pending</span>
                <span v-if="task.status === 'completed'" class="task-status reviewd">Reviewd</span>
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
                .post(`tasks/${taskId}/completeTask`)
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

.reviewd {
    background-color: orange;
}

.rejected {
    background-color: red;
    color: white;
}

h3 {
    margin: 0 auto;
    font-size: 200%;
    font-weight: 700;
    margin-top: 0;
}

ul {
    padding: 1rem;
    background-color: var(--background-super-dark);
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    border: 2px solid var(--background-lighter);
    margin-bottom: 1rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    text-align: center;
    list-style: none;
    padding: 1rem;
}

li {
    width: 50%;
    margin-bottom: 0.5rem;
    padding: 0.5rem;
}
</style>

  