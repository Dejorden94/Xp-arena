<template>
    <article class="pincode">
        <figure>
            <img src="images/info-imgs/info-about-graphic.png" alt="Xp arena information graphic.">
        </figure>
        <h2>Join game</h2>
        <form @submit.prevent="submitPincode">
            <input placeholder="Enter game pin" type="text" id="pincode" v-model="pincode" required>
            <button type="submit" @click="$emit('reloadJoinedGames')">GO</button>
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
        fetchFollowedGames() {
            axios.get('/dashboard/games')
                .then(response => {
                    this.followedGames = response.data.games;
                })
                .catch(error => {
                    console.log(error);
                });
        },
    },
}
</script>
<style scoped>
.pincode {
    background: var(--background-super-dark);
    border: 2px solid var(--background-lighter);
    border-radius: 1rem;
    position: absolute;
    display: flex;
    justify-content: center;
    align-items: center;
    top: 60%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80%;
    padding: 0;
}

article {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    margin-bottom: 2rem;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    margin-left: auto;
    margin-right: auto;
}

figure {
    background: var(--background-lighter);
    width: 100%;
    height: 100%;
}


h2 {
    font-size: 150%;
    margin-top: 1rem;
    color: var(--font-white);
}

form {
    padding: 2rem;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    width: 100%;
}

input {
    text-align: center;
    margin: 0 auto 1rem auto;
    background: var(--background-lighter);
    border: none;
    padding: 0.5rem;
    width: 60%;
    border-radius: 5px;
}

button {
    cursor: pointer;
    padding: 0.2rem 2rem;
    background: var(--background-lighter);
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
