<template>
    <article>
        <h1>Quest details van {{ questName }}</h1>

        <button @click="toggleAddCriteria">Voeg criteria toe</button>

        <section v-if="showAddCriteriaForm">
            <input v-model="newCriterionDescription" placeholder="Omschrijving van criterium">
            <button @click="addCriteria">Opslaan</button>
        </section>

        <ul>
            <li v-for="criterion in criteria" :key="criterion.id">{{ criterion.name }}</li>
        </ul>
    </article>
</template>
<script>
export default {
    props: {
        quest: {
            type: Object,
            required: true
        }
    },
    data() {
        return {
            criteria: [],
            showAddCriteriaForm: false,
            newCriterionDescription: ''
        };
    },
    computed: {
        questName() {
            return this.quest && this.quest.name ? this.quest.name : 'Loading...';
        }
    },
    mounted() {
        this.fetchCriteria();
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
                console.error("Er is een fout opgetreden bij het toevoegen van het criterium:", error);
            }
        },
        async fetchCriteria() {
            if (!this.quest || !this.quest.id) {
                console.warn("Quest of quest.id is niet beschikbaar");
                return;
            }

            try {
                const response = await axios.get(`/task/${this.quest.id}/check-criteria`);
                this.criteria = response.data;
            } catch (error) {
                console.error("Er is een fout opgetreden bij het ophalen van de criteria:", error);
            }
        },
        toggleAddCriteria() {
            this.showAddCriteriaForm = !this.showAddCriteriaForm;
        }

    }
}
</script>
<style scoped>
article {
    height: 5rem;
    width: 80vw;
    margin: 0 auto;
    background: var(--background-super-dark);
    border: 2px solid var(--background-lighter);
    border-radius: 1rem;
}
</style>