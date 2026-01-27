<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";

const materials = ref([]);

onMounted(async () => {
  const res = await axios.get(
    "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/materials.php",
  );
  materials.value = res.data;
});
</script>

<template>
  <main class="page-container">
    <h1 class="section-title">Référentiel Matières</h1>
    <div class="grid">
      <div v-for="mat in materials" :key="mat.material_id" class="info-card">
        <h3>{{ mat.name }}</h3>
        <p>
          Catégorie : <strong>{{ mat.category }}</strong>
        </p>
        <p>Épaisseur : {{ mat.thickness }} mm</p>
      </div>
    </div>
  </main>
</template>

<style scoped>
/* On réutilise les styles de MachinesView ou on les met dans style.css global */
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
