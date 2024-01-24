<template>
    <article>
        <h1>{{ questName }}</h1>
        <button v-show="isUserOwner" v-if="criteria.length < 3" @click="toggleAddCriteria">Voeg criteria toe</button>

        <button @click="showInfo">Terug</button>

        <section class="quest-info">
            <img src="images/info-imgs/levelup-bg1.png" alt="Quest background chosen by user.">
            <template v-if="isEditing && isUserOwner">
                <textarea v-if="isEditing" v-model="editedDescription">{{ questDesciption }}</textarea>
                <textarea>{{ quest.experience }}</textarea>
            </template>
            <template v-else>
                <p>{{ questDesciption }}</p>
            </template>
        </section>

        <section class="stars-section">
            <span class="star progress-star" :class="starsStatus[0] ? 'gold-star' : 'gray-star'">&#9733;</span>
            <span class="star progress-star" :class="starsStatus[1] ? 'gold-star' : 'gray-star'">&#9733;</span>
            <span class="star progress-star" :class="starsStatus[2] ? 'gold-star' : 'gray-star'">&#9733;</span>
        </section>

        <section v-if="showAddCriteriaForm">
            <input v-model="newCriterionDescription" placeholder="Omschrijving van criterium">
            <button @click="addCriteria">Opslaan</button>
        </section>

        <ul>
            <li class="criterion" :class="criterion.is_met ? 'gold-background' : 'gray-background'"
                v-for="(criterion, index) in criteria" :key="criterion.id">

                <select v-if="isEditing && isUserOwner" v-model="criterion.difficulty">
                    <option value="makkelijk">Makkelijk</option>
                    <option value="normaal">Normaal</option>
                    <option value="moeilijk">Moeilijk</option>
                </select>
                <span class="star" :class="criterion.is_met ? 'gold-star' : 'gray-star'"
                    @click="toggleCriterionMet(criterion)">&#9733;</span>
                <template v-if="isEditing && isUserOwner">
                    <textarea v-model="editedCriteria[index]" placeholder="Bewerk criterium"></textarea>
                </template>
                <template v-else>
                    <span>{{ criterion.description }}</span>
                </template>
            </li>
        </ul>
        <button v-if="allCriteriaMet && !isUserOwner" @click="sendTaskForReview">
            Verzend Taak voor Controle
        </button>


        <section>
            <h2>Attachments</h2>
        </section>
    </article>
</template>
<script>
import { onBeforeUnmount } from 'vue';

export default {
    props: {
        quest: {
            type: Object,
            required: true
        },
        isEditing: {
            type: Boolean,
            default: false
        },
        gameId: {
            type: Object,
            default: false
        },
        isUserOwner: {
            type: Boolean,
            default: false,
        },
    },
    watch: {
        quest: {
            deep: true,
            immediate: true,
            handler(newVal, oldVal) {
                if (newVal && newVal.id) {
                    this.fetchCriteria();
                }
            }
        },
        isEditing(newVal) {
            if (newVal === true) { // Als isEditing verandert naar true
                this.startEditing();
                this.editedDescription = this.questDesciption;
            }
            else {
                this.updateTask();
            }
        }
    },
    data() {
        return {
            criteria: [],
            showAddCriteriaForm: false,
            newCriterionDescription: '',
            editedDescription: '',
            editedCriterion: '',
            editedCriteria: []
        };
    },
    computed: {
        allCriteriaMet() {
            return this.criteria.length > 0 && this.criteria.every(criterion => criterion.is_met);
        },
        questName() {
            return this.quest && this.quest.name ? this.quest.name : 'Loading...';
        },
        questDesciption() {
            return this.quest && this.quest.description ? this.quest.description : 'Loading...';
        },
        checkedCriteriaCount() {
            return this.criteria.filter(criterion => criterion.is_met).length;
        },
        starsStatus() {
            const totalCrtiteria = this.criteria.length;
            const checkedCriteria = this.criteria.filter(criterion => criterion.is_met).length;

            switch (totalCrtiteria) {
                case 1:
                    return [checkedCriteria >= 1, checkedCriteria >= 1, checkedCriteria >= 1];
                case 2:
                    return [checkedCriteria >= 1, checkedCriteria >= 2, checkedCriteria >= 2];
                default:
                    return [checkedCriteria >= 1, checkedCriteria >= 2, checkedCriteria >= 3];
            }
        }
    },
    mounted() {
        this.$emit('gameQuestDetailsShown', true);
        this.fetchCriteria();
    },

    beforeUnmount() {
        this.$emit('gameQuestDetailsShown', false);
    },
    methods: {
        startEditing() {
            this.editedCriteria = this.criteria.map(criterion => criterion.description);
        },
        async addCriteria() {
            try {
                const response = await axios.post(`/task/${this.quest.id}/add-criteria`, {
                    description: this.newCriterionDescription,
                    difficulty: 'normaal'
                });
                this.criteria.push(response.data);  // Voeg het nieuwe criterium toe aan de lijst
                this.newCriterionDescription = '';  // Reset het invoerveld
                this.selectedDifficulty = ''; // Reset de geselecteerde moeilijkheidsgraad
                this.showAddCriteriaForm = false;  // Verberg het formulier

                // Haal de criteria opnieuw op
                this.fetchCriteria();
            } catch (error) {
                console.warn("Er is een fout opgetreden bij het toevoegen van het criterium:", error);
            }
        },

        async fetchCriteria() {
            this.criteria = [];
            if (!this.quest || !this.quest.id) {
                return;
            }

            try {
                let response;
                if (this.isUserOwner) {
                    response = await axios.get(`/task/${this.quest.id}/check-criteria`);
                } else {
                    // Aanname: er is een endpoint /follower-task/{id}/check-criteria
                    response = await axios.get(`/follower-task/${this.quest.id}/check-criteria`);
                }
                this.criteria = response.data;
            } catch (error) {
                console.error("Er is een fout opgetreden bij het ophalen van de criteria:", error);
            }
        },
        async toggleCriterionMet(criterion) {

            try {
                const updatedValue = !criterion.is_met;
                let response;

                if (this.isUserOwner) {
                    // Als de gebruiker de eigenaar is, update de criteria in de 'task' tabel
                    response = await axios.post(`/task/${this.quest.id}/criterion/${criterion.id}/toggle-met`, {
                        is_met: updatedValue
                    });
                } else {
                    // Als de gebruiker geen eigenaar is, update de criteria in een andere tabel, bijvoorbeeld 'follower-task'
                    response = await axios.post(`/follower-task/${this.quest.id}/criterion/${criterion.id}/toggle-met`, {
                        is_met: updatedValue
                    });
                }

                // Update de lokale status van de criterion
                if (response && response.data) {
                    criterion.is_met = updatedValue;
                }
            } catch (error) {
                console.error("Er is een fout opgetreden bij het bijwerken van de criterion:", error);
                if (error.response) {
                    // Het verzoek is gemaakt en de server heeft een statuscode geretourneerd die buiten het bereik van 2xx valt
                    console.error('Response data:', error.response.data);
                    console.error('Response status:', error.response.status);
                    console.error('Response headers:', error.response.headers);
                } else if (error.request) {
                    // Het verzoek is gemaakt, maar er is geen antwoord ontvangen
                    console.error('Request data:', error.request);
                } else {
                    // Iets anders is misgegaan bij het instellen van het verzoek
                    console.error('Error message:', error.message);
                }
            }
        },

        async updateTask() {
            try {
                const taskId = this.quest.id;
                const updatedTaskData = {
                    name: this.quest.name,
                    description: this.editedDescription,
                    experience: this.quest.experience,
                    criteria: this.criteria.map(criterion => ({
                        id: criterion.id,
                        description: criterion.description,
                        difficulty: criterion.difficulty,
                        is_met: criterion.is_met
                    }))
                };

                const response = await axios.put(`/task/${taskId}`, updatedTaskData);

                if (response.data.message === 'Task and associated criteria updated successfully') {
                    console.log('Taak en bijbehorende criteria succesvol bijgewerkt!');

                    // Haal de bijgewerkte gegevens opnieuw op
                    this.fetchCriteria();
                    this.fetchQuestDescription();
                } else {
                    console.log('Er is een fout opgetreden bij het bijwerken van de taak.');
                }
            } catch (error) {
                console.error("Er is een fout opgetreden bij het bijwerken van de taak:", error);
            }
        },
        async fetchQuestDescription() {
            try {
                const response = await axios.get(`/task/${this.quest.id}`);
                this.quest.description = response.data.description;
            } catch (error) {
                console.error("Er is een fout opgetreden bij het ophalen van de taakbeschrijving:", error);
            }
        },

        async sendTaskForReview() {
            try {
                const taskId = this.quest.task_id; // Zorg ervoor dat dit het juiste ID van de taak is
                const response = await axios.post(`/tasks/${taskId}/completeTask`);

                if (response.status === 200) {
                    // Verwerk de succesvolle reactie
                    console.log('Taak succesvol verzonden voor beoordeling:', response.data);
                    // U kunt hier ook een bericht of notificatie aan de gebruiker tonen
                    window.location.reload();
                } else {
                    // Verwerk andere statuscodes
                    console.error('Er is iets misgegaan:', response);
                }
            } catch (error) {
                console.error('Fout bij het verzenden van de taak voor beoordeling:', error);
                // Toon een foutmelding aan de gebruiker
            }
        },

        toggleAddCriteria() {
            this.showAddCriteriaForm = !this.showAddCriteriaForm;
        },
        showInfo() {
            this.$emit('hideGameDetails');
        },
        editDescription() {
            this.editedDescription = this.questDesciption;
            this.isEditing = true;
        },
        saveDescriptionChanges() {
            this.quest.description = this.editedDescription;
            this.isEditing = false;
            // Hier kun je een API-aanroep doen om de wijzigingen op te slaan
        },
        editCriterion(criterion) {
            this.editedCriterion = criterion.description;
            this.isEditing = true;
        },
        saveCriterionChanges(criterion, index) {
            criterion.description = this.editedCriteria[index];
            // Hier kun je een API-aanroep doen om de wijzigingen op te slaan
        },
        setEditedCriterion(criterion) {
            this.editedCriterion = criterion.description;
        },

    }
}
</script>
<style scoped>
h1 {
    font-size: 150%;
    font-weight: 700;
    text-align: center;
}

h2 {
    margin-top: 1rem;
    text-align: center;
}

article {
    margin-bottom: 10rem;
    width: 80vw;
    margin: 0 auto 15rem auto;
}

input,
textarea {
    color: black;
    background: var(--background-super-light);
    border-radius: 1rem;
    border: 1px solid var(--font-color-normal);
}

.progress-star:nth-of-type(2) {
    font-size: 350%;
}

.stars-section {
    text-align: center;
}

.quest-info {
    background: var(--background-super-dark);
    border: 2px solid var(--background-lighter);
    border-radius: 1rem;
}

.quest-info>img {
    height: 80%;
    width: 100%;
}

.quest-info>p {
    margin: 1rem;
}

.criterion {
    width: 100%;
    min-height: 5rem;
    margin-top: 1rem;
    background: var(--background-super-dark);
    border: 2px solid var(--background-lighter);
    border-radius: 1rem;
}

.gold-background {
    background: linear-gradient(var(--color-yellow), var(--color-orange));
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
</style>