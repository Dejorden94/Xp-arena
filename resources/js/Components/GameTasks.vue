<template>
    <article>
        <h3>Quests</h3>
        <ul>
            <li v-for="task in tasks" :key="task.id">
                <template v-if="!task.completion_status && !isTaskCompleted(task)">
                    {{ task.name }} - {{ task.description }} - {{ task.experience }}
                    <input v-if="!isUserOwner && task.approval_status !== 'rejected'" type="checkbox"
                        :disabled="task.approval_status === 'verified'" @change="checkTask(task)">
                    <button v-if="!isUserOwner && task.approval_status === 'rejected'" @click="resubmitTask(task)">Opnieuw
                        indienen</button>
                    <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
                </template>
            </li>
        </ul>
    </article>
</template>

<script>
import { ref, onMounted } from 'vue';

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
        completedTasks: {
            type: Array,
            required: true,
        },
        isUserOwner: {
            type: Boolean,
            default: false,
        }
    },
    emits: ['onHide'],
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
        async isTaskCompleted(task) {
            try {
                const response = await axios.get(`/api/games/${this.gameId}/tasks/${task.id}/completed`);
                return response.data.completed;
            } catch (error) {
                console.log(error);
                return false;
            }
        },
        checkTask(task) {
            axios.post(`/tasks/${task.id}/complete`)
                .then(response => {
                    // Markeer de taak als gecontroleerd
                    task.completion_status = true;

                    // Vind de index van de taak in de takenlijst
                    const index = this.tasks.findIndex(t => t.id === task.id);

                    // Verwijder de taak uit de takenlijst
                    if (index !== -1) {
                        this.tasks.splice(index, 1);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
        },
        resubmitTask(task) {
            axios.post(`/tasks/${task.id}/resubmit`)
                .then(response => {
                    // Zet de status van de taak terug naar 'unverified'
                    task.approval_status = 'unverified';
                })
                .catch(error => {
                    console.log(error);
                });
        }
    },
    setup(props) {
        const tasks = ref([]);

        // Laad de taken van de game wanneer de component wordt geladen
        const loadTasks = async () => {
            try {
                const response = await axios.get(`/api/games/${props.gameId}/tasks`);
                tasks.value = response.data.tasks;
            } catch (error) {
                console.error(error);
            }
        };

        onMounted(() => {
            loadTasks();
        });

        return {
            tasks,
        };
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
