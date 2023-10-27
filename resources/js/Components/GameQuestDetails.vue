<template>
    <article>
        <h1>{{ questName }}</h1>
        <button v-if="criteria.length < 3" @click="toggleAddCriteria">Voeg criteria toe</button>

        <button @click="showInfo">Terug</button>

        <section class="quest-info">
            <img src="images/info-imgs/levelup-bg1.png" alt="Quest background chosen by user.">
            <template v-if="isEditing">
                <textarea v-model="editedDescription">{{ questDesciption }}</textarea>
                <button @click="saveDescriptionChanges">Opslaan</button>
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
                v-for="criterion in criteria" :key="criterion.id">
                <span class="star" :class="criterion.is_met ? 'gold-star' : 'gray-star'"
                    @click="toggleCriterionMet(criterion)">&#9733;</span>
                <template v-if="isEditing">
                    <input v-model="editedCriterion" placeholder="Bewerk criterium">
                    <button @click="saveCriterionChanges(criterion)">Opslaan</button>
                </template>
                <template v-else>
                    <span>{{ criterion.description }}</span>
                </template>
            </li>
        </ul>

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
        }
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
        }
    },
    data() {
        return {
            criteria: [],
            showAddCriteriaForm: false,
            newCriterionDescription: '',
            editedDescription: '',
            editedCriterion: '',
        };
    },
    computed: {
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
            const checkedCount = this.criteria.filter(criterion => criterion.is_met).length;
            return [
                checkedCount > 0,  // Eerste ster (links)
                checkedCount > 2,  // Middelste ster
                checkedCount > 1   // Laatste ster (rechts)
            ];
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
        async addCriteria() {
            try {
                const response = await axios.post(`/task/${this.quest.id}/add-criteria`, {
                    description: this.newCriterionDescription
                });
                this.criteria.push(response.data);  // Voeg het nieuwe criterium toe aan de lijst
                this.newCriterionDescription = '';  // Reset het invoerveld
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
                console.log("Quest of quest.id is niet beschikbaar");
                return;
            }

            try {
                const response = await axios.get(`/task/${this.quest.id}/check-criteria`);
                this.criteria = response.data;
            } catch (error) {
                console.error("Er is een fout opgetreden bij het ophalen van de criteria:", error);
            }
        },
        async toggleCriterionMet(criterion) {
            try {
                // Update de waarde van is_met
                const updatedValue = !criterion.is_met;

                // Doe een API-aanroep om de waarde in de database bij te werken
                const response = await axios.post(`/task/${this.quest.id}/criterion/${criterion.id}/toggle-met`, {
                    is_met: updatedValue
                });

                // Update de waarde in de lokale data
                criterion.is_met = updatedValue;
            } catch (error) {
                console.error("Er is een fout opgetreden bij het bijwerken van de criterion:", error);
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
        saveCriterionChanges(criterion) {
            criterion.description = this.editedCriterion;
            this.isEditing = false;
            // Hier kun je een API-aanroep doen om de wijzigingen op te slaan
        }

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

.progress-star:nth-of-type(2) {
    font-size: 350%;
}

.stars-section {
    text-align: center;
}

.quest-info {
    height: 20rem;
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