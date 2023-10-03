<script setup></script>
<template>
    <article>
        <h2>Voer een game pincode in</h2>
        <form @submit.prevent="submitPincode">
            <div>
                <label for="pincode">Pincode:</label>
                <input type="text" id="pincode" v-model="pincode" required>
            </div>
            <button type="submit">Verzenden</button>
        </form>
    </article>
</template>
<script>
export default {

    data() {
        return {
            pincode: '',
            validPincodes: [],
            validPincode: false,
        }
    },
    methods: {
        submitPincode() {
            axios.post('/games/follow', {
                pincode: this.pincode
            })
                .then(response => {
                    this.pincode = '';
                    this.fetchFollowedGames(); // Leeg het invoerveld voor de pincode
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },
}
</script>
<style scoped>
.join-add-button {
    margin-bottom: 10rem;
}

article,
GameDetails,
GameTasks {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 2rem;
    padding: 1rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    width: 60%;
    margin-left: auto;
    margin-right: auto;
}


h2 {
    color: #333;
    margin-bottom: 1rem;
}

form {
    margin-top: 1rem;
    width: 100%;
}

form>div {
    margin-bottom: 1rem;
}

input,
textarea {
    padding: 0.5rem;
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 5px;
    background-color: #007BFF;
    color: #fff;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #0056b3;
}

ul {
    padding-left: 1rem;
}

li {
    margin-bottom: 0.5rem;
    cursor: pointer;
}

li:hover {
    color: #007BFF;
}
</style>
