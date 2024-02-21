<template>
    <article class="quest-overview" v-show="!showQuestDetailsModal">

        <h3>Quests</h3>
        <section v-if="isUserOwner" v-for="checkpoint in sortedCheckpoints" :key="checkpoint.id">
            <p>{{ checkpoint.name }} - {{ checkpoint.order }}</p>
            <input v-if="isUserOwner" type="number" v-model="checkpoint.new_order" placeholder="Nieuwe Volgorde">
            <button v-if="isUserOwner" @click="updateCheckpointOrder(checkpoint.id, checkpoint.new_order)">
                Bijwerken Volgorde
            </button>
        </section>
        <article v-for="checkpoint in sortedCheckpoints" :key="checkpoint.id" class="checkpoint-section">
            <h4>{{ checkpoint.name }}</h4>
            <ul>
                <li v-for="task in tasksPerCheckpoint[checkpoint.id]" :key="task.id"
                    :class="{ 'inactive-task': task.status === 'completed' || task.status === 'reviewing' }">
                    <button v-if="isUserOwner" @click="assignTaskToCheckpoint(task.id)">Assign Checkpoint</button>

                    <select v-if="isUserOwner" v-model="selectedCheckpointId">
                        <option v-for="checkpoint in checkpoints" :value="checkpoint.id">{{ checkpoint.name }}</option>
                    </select>

                    <!-- Sterren voor voortgang -->
                    <div class="star-section" v-if="!isUserOwner">
                        <span v-for="(starClass, index) in starClasses(task.id)" :key="index" class="star progress-star"
                            :class="starClass">&#9733;</span>
                    </div>

                    <img v-if="isUserOwner || task.status !== 'locked'"
                        :class="{ 'inactive-button': task.status === 'completed' || task.status === 'reviewing' }"
                        @click="showQuestDetails(task)" :src="getQuestImageUrl(task.image)" alt="Quest image">

                    <p>{{ task.name }} - {{ task.status }}</p>
                    <span v-if="task.status === 'pending'" class="task-status completed">Pending</span>
                    <span v-if="task.status === 'completed'" class="task-status complete">Completed</span>
                    <span v-if="task.status === 'reviewing'" class="task-status reviewing">Send for review</span>
                    <span v-else-if="task.status === 'rejected'" class="task-status rejected">Afgewezen</span>

                    <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
                </li>
            </ul>
        </article>
        <div v-show="isUserOwner" class="quest-button-container">
            <button @click="$emit('showQuestCheck')">+</button>
        </div>
    </article>

    <GameQuestDetails ref="questDetails" v-if="showQuestDetailsModal" :gameId="gameId" :isUserOwner="isUserOwner"
        :isEditing="isEditing" :quest="selectedQuest" @criteriaUpdated="updateQuestCriteria"
        @showGameDetails="showQuestDetails" @hideGameDetails="handleHideGameDetails"
        @gameQuestDetailsShown="$emit('gameQuestDetailsShown', $event, isUserOwner)" />
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
        criteria: {
            type: Object,
            required: true
        },
        isEditing: {
            type: Boolean,
            default: false
        },
    },
    mounted() {
        this.fetchCheckpoints();
    },
    computed: {
        sortedCheckpoints() {
            return this.checkpoints.slice().sort((a, b) => a.order - b.order);
        },
        tasksPerCheckpoint() {
            let groupedTasks = {};
            this.checkpoints.forEach(checkpoint => {
                groupedTasks[checkpoint.id] = this.initialTasks.filter(task => task.checkpoint_id === checkpoint.id);
            });
            return groupedTasks;
        },
        starClasses() {
            return (taskId) => {
                const task = this.initialTasks.find(t => t.id === taskId);
                const taskCriteriaInfo = this.criteria.find(c => c.taskId === taskId);

                // Als de taakstatus 'completed' is, geef dan alle sterren een 'gold-star' klasse
                if (task && task.status === 'completed') {
                    return ['gold-star', 'gold-star', 'gold-star'];
                }

                // Als er geen criteria zijn of de taak niet gevonden is, geef dan alle sterren een 'gray-star' klasse
                if (!taskCriteriaInfo || taskCriteriaInfo.criteria.length === 0) {
                    return ['gray-star', 'gray-star', 'gray-star'];
                }

                // Bereken het aantal voldane criteria en pas de sterrenklassen dienovereenkomstig aan
                const metCriteriaCount = taskCriteriaInfo.criteria.filter(criterion => criterion.is_met).length;
                let classes = ['gray-star', 'gray-star', 'gray-star'];
                for (let i = 0; i < metCriteriaCount; i++) {
                    classes[i] = 'gold-star'; // Vervang grijze sterren door gouden sterren voor voldane criteria
                }
                return classes;
            };
        },
    },
    methods: {
        getQuestImageUrl(imagePath) {
            return imagePath ? `${imagePath}` : '/images/default-game-image/default.webp'
        },
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
        showQuestDetails(task) {
            this.selectedQuest = task;
            this.showQuestDetailsModal = true;
        },
        handleHideGameDetails() {
            this.showQuestDetailsModal = false;
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
.quest-overview {
    width: 80%;
    margin: 0 auto 0 auto;
}

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

.inactive-button {
    color: gray;
    cursor: not-allowed;
    pointer-events: none;
}

.star {
    font-size: 250%;
}

.gold-star {
    color: gold;
}

.gray-star {
    color: gray;
}

.progress-star:nth-of-type(2) {
    font-size: 350%;
}

.quest-button-container {
    display: flex;
    justify-content: center;
    width: 100%;
    height: 3rem;

    button {
        border: none;
        background: linear-gradient(90deg, #FDA829, #FF5C00);
        display: block;
        font-weight: 100;
        font-size: 370%;
        width: 8rem;
        height: 8rem;
        border-radius: 50%;
    }
}

img {
    cursor: pointer;
    margin: 2rem auto;
    width: 25rem;
    aspect-ratio: 1/1;
    border-radius: 50%;
    object-fit: cover;
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

@media screen and (max-width: 1280px) {
    .quest-button-container {
        display: none;
    }
}

@media screen and (max-width: 900px) {
    .quest-overview {
        margin-bottom: 10rem;
    }
}
</style>