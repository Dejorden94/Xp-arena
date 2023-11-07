<template>
    <article class="quest-overview" v-show="!showQuestDetailsModal">

        <h3>Quests</h3>
        <ul>
            <li v-for="task in initialTasks" :key="task.id">
                <button v-if="isUserOwner" @click="assignTaskToCheckpoint(task.id)">Assign Checkpoint</button>

                <select v-model="selectedCheckpointId">
                    <option v-for="checkpoint in checkpoints" :value="checkpoint.id">{{ checkpoint.name }}</option>
                </select>
                <!-- Niet de eigenaar van de game  -->
                <button v-if="!isUserOwner" @click="showQuestDetails(task)">Toon Details</button>

                <!-- <input v-if="task.status === 'pending' && !isUserOwner || task.status === 'rejected'" type="checkbox"
                    v-model="task.completed" @change="toggleTaskCompletion(task.id)" /> -->
                <p v-if="!isUserOwner">{{ task.name }} - {{ task.description }} - {{ task.experience }} - {{ task.status
                }}</p>
                <span v-if="task.status === 'pending'" class="task-status completed">Pending</span>
                <span v-if="task.status === 'completed'" class="task-status reviewd">Reviewd</span>
                <span v-else-if="task.status === 'rejected'" class="task-status rejected">Afgewezen</span>

                <!-- Eigenaar van game  -->

                <button v-if="isUserOwner" @click="showQuestDetails(task)">Toon Details</button>
                <p v-if="isUserOwner">{{ task.name }} - {{ task.description }} - {{ task.experience }}</p>
                <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
            </li>
        </ul>
    </article>
    <GameQuestDetails ref="questDetails" v-if="showQuestDetailsModal" :gameId="gameId" :isUserOwner="isUserOwner"
        :isEditing="isEditing" :quest="selectedQuest" @showGameDetails="showQuestDetails"
        @hideGameDetails="hideQuestDetails" @gameQuestDetailsShown="$emit('gameQuestDetailsShown', $event)" />
</template>

<script>
import GameQuestDetails from '@/Components/GameQuestDetails.vue';

export default {
    components: {
        GameQuestDetails
    },
    data() {
        return {
            currenTaskId: null,
            showQuestDetailsModal: false,
            selectedQuest: null,
            checkpoints: [],
            selectedCheckpointId: null,
        }
    },
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
        },
        quest: {
            type: Object,
            required: true
        },
        isEditing: {
            type: Boolean,
            default: false
        }
    },
    mounted() {
        this.fetchCheckpoints();
    },
    methods: {
        fetchCheckpoints() {
            axios.get(`/games/${this.gameId}/checkpoints`)
                .then(response => {
                    this.checkpoints = response.data;
                })
                .catch(error => {
                    console.error('Error fetching checkpoints:', error);
                });
        },
        assignTaskToCheckpoint(taskId) {
            if (!this.selectedCheckpointId) {
                alert('Selecteer een checkpoint.');
                return;
            }

            axios.post(`/checkpoints/${this.selectedCheckpointId}/assign-task`, { task_id: taskId })
                .then(response => {
                    console.log('Task assigned to checkpoint:', response.data);
                    // U kunt hier de UI bijwerken om de wijziging te reflecteren
                })
                .catch(error => {
                    console.error('Error assigning task to checkpoint:', error);
                });
        },
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
        showQuestDetails(task) {
            this.selectedQuest = task;
            this.showQuestDetailsModal = true;
            this.$emit('hideAll');
            this.$emit('togglePlayerInfo');

            // Gebruik nextTick om ervoor te zorgen dat de GameQuestDetails component volledig is gemount
            this.$nextTick(() => {
                this.$refs.questDetails.fetchCriteria();
            });
        },
        hideQuestDetails() {
            this.$emit('hideAll');
            this.showQuestDetailsModal = false;
            this.$emit('togglePlayerInfo');
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

.reviewd {
    background-color: orange;
}

.rejected {
    background-color: red;
    color: white;
}

select {
    color: var(--font-color-normal);
    background: var(--background-lighter);
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


@media screen and (max-width: 900px) {
    .quest-overview {
        margin-bottom: 10rem;
    }
}
</style>

  