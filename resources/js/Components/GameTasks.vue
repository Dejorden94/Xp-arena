<template>
    <article>
        <h3>Quests</h3>
        <ul>
            <li v-for="task in tasks" :key="task.id">
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
    data() {
        return {
            tasks: []
        };
    },
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
                    const index = this.tasks.findIndex((task) => task.id === taskId);
                    if (index !== -1) {
                        this.tasks.splice(index, 1);
                    }
                })
                .catch((error) => {
                    console.error(error);
                });
        },
        created() {
            // Haal de taken-data op bij het maken van de component
            this.fetchFollowedTasks(this.gameId);
        }
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

  