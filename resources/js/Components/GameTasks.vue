<template>
    <article class="quest-overview" v-show="!showQuestDetailsModal">

        <h3>Quests</h3>
        <section v-if="isUserOwner" v-for="checkpoint in sortedCheckpoints" :key="checkpoint.id">
            <!-- ... andere details van de checkpoint ... -->
            <p>{{ checkpoint.name }} - {{ checkpoint.order }}</p>
            <!-- Voeg een numeriek invoerveld toe voor de nieuwe volgorde -->
            <input v-if="isUserOwner" type="number" v-model="checkpoint.new_order" placeholder="Nieuwe Volgorde">
            <button v-if="isUserOwner" @click="updateCheckpointOrder(checkpoint.id, checkpoint.new_order)">
                Bijwerken Volgorde
            </button>
        </section>
        <article v-for="checkpoint in sortedCheckpoints" :key="checkpoint.id" class="checkpoint-section">
            <h4>{{ checkpoint.name }}</h4>
            <ul>
                <li v-for="task in sortedTasks.filter(t => t.checkpoint_id === checkpoint.id)" :key="task.id"
                    :class="{ 'inactive-task': task.status === 'completed' }">
                    <button v-if="isUserOwner" @click="assignTaskToCheckpoint(task.id)">Assign Checkpoint</button>

                    <select v-if="isUserOwner" v-model="selectedCheckpointId">
                        <option v-for="checkpoint in checkpoints" :value="checkpoint.id">{{ checkpoint.name }}</option>
                    </select>
                    <!-- Niet de eigenaar van de game  -->
                    <button v-if="!isUserOwner && task.status !== 'completed'" @click="showQuestDetails(task)">Toon
                        Details</button>

                    <p v-if="!isUserOwner">{{ task.name }} - {{ task.status }}</p>
                    <span v-if="task.status === 'pending'" class="task-status completed">Pending</span>
                    <span v-if="task.status === 'completed'" class="task-status complete">Completed</span>
                    <span v-if="task.status === 'reviewing'" class="task-status reviewing">Send for review</span>
                    <span v-else-if="task.status === 'rejected'" class="task-status rejected">Afgewezen</span>

                    <!-- Eigenaar van game  -->

                    <button v-if="isUserOwner" @click="showQuestDetails(task)">Toon Details</button>
                    <p v-if="isUserOwner">{{ task.name }}</p>
                    <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
                </li>
            </ul>
        </article>
    </article>

    <GameQuestDetails ref="questDetails" v-if="showQuestDetailsModal" :gameId="gameId" :isUserOwner="isUserOwner"
        :isEditing="isEditing" :quest="selectedQuest" @showGameDetails="showQuestDetails"
        @hideGameDetails="hideQuestDetails" @gameQuestDetailsShown="$emit('gameQuestDetailsShown', $event, isUserOwner)" />
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
    computed: {
        sortedTasks() {
            // Controleer of initialTasks een array is voordat je verder gaat
            if (!Array.isArray(this.initialTasks)) {
                return []; // Terugkeer van een lege array als initialTasks geen array is
            }
            // Ga verder met de logica als initialTasks wel een array is
            return this.initialTasks.filter(task => task.checkpoint_id !== null)
        },
        tasksPerCheckpoint() {
            let groupedTasks = {};
            this.checkpoints.forEach(checkpoint => {
                groupedTasks[checkpoint.id] = this.initialTasks.filter(task => task.checkpoint_id === checkpoint.id);
            });
            return groupedTasks;
        },
        sortedCheckpoints() {
            return this.checkpoints.slice().sort((a, b) => a.order - b.order);
        },
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
        getCheckpointNameById(checkpointId) {
            const checkpoint = this.checkpoints.find(c => c.id === checkpointId);
            return checkpoint ? checkpoint.name : 'Not assigned';
        },
        assignTaskToCheckpoint(taskId) {
            if (!this.selectedCheckpointId) {
                alert('Selecteer een checkpoint.');
                return;
            }

            axios.post(`/checkpoints/${this.selectedCheckpointId}/assign-task`, { task_id: taskId })
                .then(response => {
                    // Vind de taak in initialTasks en update de checkpoint_id en order
                    const task = this.initialTasks.find(t => t.id === taskId);
                    if (task) {
                        task.checkpoint_id = this.selectedCheckpointId; // Directe toewijzing
                        // Vind het checkpoint om de order te krijgen
                        const checkpoint = this.checkpoints.find(c => c.id === this.selectedCheckpointId);
                        if (checkpoint) {
                            task.order = checkpoint.order; // Wijs de order van het checkpoint toe aan de taak
                        }
                        // Reset de geselecteerde checkpoint ID na toewijzing
                        this.selectedCheckpointId = null;
                    }
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
            axios.post(`tasks/${taskId}/completeTask`)
                .then((response) => {
                    // Update de voltooide status van de taak in de lijst
                    const task = this.initialTasks.find((t) => t.id === taskId);
                    if (task) {
                        task.status = 'completed'; // Update de status naar 'completed'
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
        },
        updateCheckpointOrder(checkpointId, nieuweVolgorde) {
            axios.patch(`/checkpoints/${checkpointId}/update-order`, { new_order: nieuweVolgorde }) // Let op de correcte parameter naam
                .then(response => {
                    // Verwerk de succesvolle update
                    this.fetchCheckpoints(); // Haal de checkpoints opnieuw op om de bijgewerkte volgorde te krijgen
                    alert('Volgorde van checkpoint succesvol bijgewerkt.');
                })
                .catch(error => {
                    // Verwerk eventuele fouten
                    console.error('Fout bij het bijwerken van de volgorde van checkpoints:', error);
                    alert('Fout bij het bijwerken van de volgorde.');
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
    background-color: orange;
    color: white;
}

.complete {
    background-color: green;
}

.rejected {
    background-color: red;
    color: white;
}

.reviewing {
    background-color: purple;
    color: white;
}

input {
    background: var(--background-dark);
    color: var(--font-color-normal);
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

.inactive-task {
    color: grey;
    pointer-events: none;
}


@media screen and (max-width: 900px) {
    .quest-overview {
        margin-bottom: 10rem;
    }
}
</style>