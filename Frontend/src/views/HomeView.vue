<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import HomeCard from "../components/HomeCard.vue";

const tests = ref([]);
const loading = ref(true);

// Récupération des tests
const fetchTests = async () => {
  try {
    const response = await axios.get(
      "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/tests.php",
    );
    tests.value = response.data;
    console.log("✅ Tests récupérés:", response.data.length);
  } catch (err) {
    console.error("❌ Erreur lors de la récupération des tests:", err);
  } finally {
    loading.value = false;
  }
};

// Afficher uniquement les 2 derniers tests
const filteredTests = computed(() => {
  return tests.value.slice(0, 2);
});

onMounted(fetchTests);
</script>

<template>
  <main class="home-container">
    <div class="welcome-header">
      <h2 class="section-title">Derniers tests de la communauté</h2>
    </div>

    <!-- Grille des tests -->
    <div v-if="loading" class="info-msg">
      <div class="loader"></div>
      Chargement des tests...
    </div>

    <div v-else-if="filteredTests.length" class="test-container">
      <HomeCard v-for="t in filteredTests" :key="t.test_id" :test="t" />
    </div>

    <div v-else class="info-msg">
      <p>Aucun test disponible pour le moment.</p>
    </div>
  </main>
</template>

<style scoped>
.home-container {
  padding: 40px 5%;
  background-color: #e8e8e8;
  min-height: 80vh;
}

.welcome-header {
  text-align: center;
  margin-bottom: 40px;
}

.section-title {
  font-size: 2rem;
  margin-bottom: 30px;
  color: #1a1a1a;
  font-weight: 600;
}

/* GRILLE */
.test-container {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  gap: 30px;
  max-width: 1240px;
  margin: 0 auto;
}
.card-img {
}

@media (max-width: 1200px) {
  .test-container {
    grid-template-columns: repeat(3, 1fr);
  }
}

@media (max-width: 900px) {
  .test-container {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 600px) {
  .test-container {
    grid-template-columns: 1fr;
  }
}

/* Messages informatifs */
.info-msg {
  text-align: center;
  color: #666;
  margin-top: 60px;
  font-size: 1.1rem;
}

.info-msg p {
  margin-bottom: 20px;
}

/* Loader */
.loader {
  width: 50px;
  height: 50px;
  border: 4px solid #f0f0f0;
  border-top: 4px solid #60c4e6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin: 0 auto 20px;
}

@keyframes spin {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
</style>
