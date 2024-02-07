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
                <li v-for="task in accessibleTasks[checkpoint.id]" :key="task.id"
                    :class="{ 'inactive-task': !task.isAccessible && !isUserOwner }">
                    <button v-if="isUserOwner" @click="assignTaskToCheckpoint(task.id)">Assign Checkpoint</button>

                    <select v-if="isUserOwner" v-model="selectedCheckpointId">
                        <option v-for="checkpoint in checkpoints" :value="checkpoint.id">{{ checkpoint.name }}</option>
                    </select>
                    <!-- Niet de eigenaar van de game  -->
                    <section v-if="!isUserOwner">
                        <div class="star-section">
                            <span v-for="(starClass, index) in starClasses(task.id)" :key="index" class="star progress-star"
                                :class="starClass">&#9733;</span>
                        </div>
                    </section>

                    <img v-if="(isUserOwner || (!isUserOwner && !isTaskInLockedCheckpoint(task.id)))"
                        :class="{ 'inactive-button': task.status === 'completed' }" @click="showQuestDetails(task)"
                        :src="getQuestImageUrl(task.image)" alt="Quest image">

                    <p v-if="!isUserOwner">{{ task.name }} - {{ task.status }}</p>
                    <span v-if="task.status === 'pending'" class="task-status completed">Pending</span>
                    <span v-if="task.status === 'completed'" class="task-status complete">Completed</span>
                    <span v-if="task.status === 'reviewing'" class="task-status reviewing">Send for review</span>
                    <span v-else-if="task.status === 'rejected'" class="task-status rejected">Afgewezen</span>

                    <!-- Eigenaar van game  -->

                    <p v-if="isUserOwner">{{ task.name }}</p>
                    <button v-if="isUserOwner" @click="deleteTask(task.id)">Verwijderen</button>
                </li>
            </ul>
        </article>
    </article>

    <GameQuestDetails ref="questDetails" v-if="showQuestDetailsModal" :gameId="gameId" :isUserOwner="isUserOwner"
        :isEditing="isEditing" :quest="selectedQuest" @criteriaUpdated="updateQuestCriteria"
        @showGameDetails="showQuestDetails" @hideGameDetails="hideQuestDetails"
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
        this.checkCheckpointsStatus();
    },
    watch: {
        criteria(newCriteria) {
            console.log(newCriteria); // Dit zou de geüpdatete criteria moeten loggen
            // Voer hier eventuele extra logica uit die afhankelijk is van de bijgewerkte criteria
        }
    },

    computed: {
        totalGames() {
            return this.criteria.length;
        },
        gamesWithCriteria() {
            return this.criteria.filter(c => c.hasCriteria).length;
        },
        criteriaMetCount() {
            return this.criteria.filter(criterion => criterion.is_met).length;
        },
        starClasses() {
            return (taskId) => {
                const taskCriteriaInfo = this.criteria.find(c => c.taskId === taskId);
                if (!taskCriteriaInfo || taskCriteriaInfo.criteria.length === 0) {
                    return ['gray-star', 'gray-star', 'gray-star'];
                }
                const metCriteriaCount = taskCriteriaInfo.criteria.filter(criterion => criterion.is_met).length;
                let classes = ['gray-star', 'gray-star', 'gray-star'];
                for (let i = 0; i < metCriteriaCount; i++) {
                    classes[i] = 'gold-star'; // Vervang grijze sterren door gouden sterren voor voldane criteria
                }
                return classes;
            };
        },
        isCheckpointCompleted() {
            let checkpointCompletionStatus = {};
            this.checkpoints.forEach(checkpoint => {
                const tasks = this.tasksPerCheckpoint[checkpoint.id];
                checkpointCompletionStatus[checkpoint.id] = tasks.every(task => task.status === 'reviewing' || task.status === 'rejected');
            });
            return checkpointCompletionStatus;
        },
        accessibleTasks() {
            const accessibleTasks = {};

            this.sortedCheckpoints.forEach((checkpoint, index) => {
                const isCurrentCheckpointAccessible = index === 0 || this.isCheckpointCompleted[this.checkpoints[index - 1]?.id];
                accessibleTasks[checkpoint.id] = this.tasksPerCheckpoint[checkpoint.id].map(task => {
                    return { ...task, isAccessible: isCurrentCheckpointAccessible };
                });
            });

            return accessibleTasks;
        },
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
        checkedCriteriaCount() {
            return this.criteria.filter(criterion => criterion.is_met).length;
        },
    },
    methods: {
        updateQuestCriteria(questId, updatedCriteria) {
            const quest = this.initialTasks.find(task => task.id === questId);
            if (quest) {
                quest.criteria = updatedCriteria;
                this.$forceUpdate();
            }
        },
        getQuestImageUrl(imagePath) {
            return imagePath ? `${imagePath}` : '/images/default-game-image/default.webp'
        },
        fetchCheckpoints() {
            axios.get(`/games/${this.gameId}/checkpoints`)
                .then(response => {
                    this.checkpoints = response.data.map((checkpoint, index) => ({
                        ...checkpoint,
                        isLocked: index !== 0 // De eerste checkpoint is niet vergrendeld, de rest wel
                    }));
                    this.checkCheckpointsStatus(); // Controleer en update de status na het ophalen
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
        checkCheckpointsStatus() {
            // Controleer de status van elke checkpoint en update de status
            this.checkpoints.forEach((checkpoint, index) => {
                const tasks = this.tasksPerCheckpoint[checkpoint.id];
                const isCompletedOrReviewing = tasks.every(task => task.status === 'completed' || task.status === 'reviewing');
                const isRejected = tasks.some(task => task.status === 'rejected');

                checkpoint.isCompleted = isCompletedOrReviewing;
                checkpoint.isLocked = isRejected;

                // Ontgrendel de volgende checkpoint als de huidige voltooid is en geen afgewezen quests heeft
                if (checkpoint.isCompleted && !isRejected && this.checkpoints[index + 1]) {
                    this.checkpoints[index + 1].isLocked = false;
                }
            });
        },
        isTaskInLockedCheckpoint(taskId) {
            const task = this.initialTasks.find(t => t.id === taskId);
            const checkpointIndex = this.checkpoints.findIndex(c => c.id === task.checkpoint_id);

            if (checkpointIndex === -1) return false;

            // Controleer of alle vorige checkpoints zijn voltooid
            for (let i = 0; i < checkpointIndex; i++) {
                const isCompleted = this.isCheckpointCompleted[this.checkpoints[i].id];
                if (!isCompleted) {
                    return true; // Als een vorig checkpoint niet voltooid is, is dit checkpoint vergrendeld
                }
            }

            return this.checkpoints[checkpointIndex].isLocked;
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
        onTaskCompletion(taskId) {
            // Update de taakstatus en controleer de checkpointstatus
            this.toggleTaskCompletion(taskId);
            this.checkCheckpointsStatus();
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


@media screen and (max-width: 900px) {
    .quest-overview {
        margin-bottom: 10rem;
    }
}
</style>