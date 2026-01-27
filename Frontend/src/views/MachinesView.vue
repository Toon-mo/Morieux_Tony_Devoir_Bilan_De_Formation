<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const machines = ref([]);

onMounted(async () => {
  const res = await axios.get(
    "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/machines.php",
  );
  machines.value = res.data;
});
</script>

<template>
  <main class="page-container">
    <h1 class="section-title">Nos Machines Laser</h1>
    <div class="grid">
      <div v-for="m in machines" :key="m.machine_id" class="info-card">
        <div class="badge">{{ m.laser_type }}</div>
        <h3>{{ m.brand }} {{ m.name }}</h3>
        <p>Modèle : {{ m.model }}</p>
        <p v-if="m.is_mopa">✨ Technologie MOPA</p>
      </div>
    </div>
  </main>
</template>

<style scoped>
.page-container {
  padding: 40px 5%;
}

.test-grid,
.grid {
  display: grid;
  /* On définit 4 colonnes égales par défaut */
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
  max-width: 1400px; /* On limite la largeur totale pour éviter l'étirement */
  margin: 0 auto; /* On centre la grille */
}

.info-card {
  background: white;
  padding: 25px;
  border-radius: 12px;
  border: 1.5px solid #60c4e6;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}
.badge {
  background: #1e1e1e;
  color: #60c4e6;
  padding: 3px 8px;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: bold;
  width: fit-content;
  margin-bottom: 10px;
}

/* Tablette : 2 colonnes maximum */
@media (max-width: 1100px) {
  .test-grid,
  .grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

/* Mobile : 1 seule colonne */
@media (max-width: 600px) {
  .test-grid,
  .grid {
    grid-template-columns: 1fr;
  }
}
</style>
