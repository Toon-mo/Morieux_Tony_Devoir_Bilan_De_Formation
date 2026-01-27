<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import TestCard from "../components/TestCard.vue";

const tests = ref([]);
const loading = ref(true);
const searchTerm = ref("");

// Filtre sélectionné
const activeFilter = ref("Tous");

// Liste des catégories (correspond à la colonne `category` de la table materials)
const categories = [
  "Tous",
  "Bois",
  "Métal",
  "Plastique",
  "Fibre",
  "Diode",
  "CO2",
];

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

// Changer le filtre
const setFilter = (cat) => {
  activeFilter.value = cat;
  console.log("🔍 Filtre actif:", cat);
};

// Filtrage CORRIGÉ : utilise material_category au lieu de material_name
const filteredTests = computed(() => {
  return tests.value.filter((t) => {
    // 1. Vérifier la catégorie
    let matchesCategory = true;

    if (activeFilter.value !== "Tous") {
      const filter = activeFilter.value.toLowerCase();

      // ✅ CORRECTION : Utiliser material_category pour les matériaux
      const materialCategory = (t.material_category || "").toLowerCase();
      const laserType = (t.laser_type || "").toLowerCase();

      // Le filtre correspond à la catégorie de matériau OU au type de laser
      matchesCategory =
        materialCategory.includes(filter) || laserType.includes(filter);
    }

    // 2. Vérifier la recherche textuelle
    let matchesSearch = true;

    if (searchTerm.value.trim() !== "") {
      const search = searchTerm.value.toLowerCase();
      const title = (t.title || "").toLowerCase();
      const machine = (t.machine_name || "").toLowerCase();
      const materialName = (t.material_name || "").toLowerCase(); // On cherche dans le nom (ex: "Inox 304L")
      const materialCategory = (t.material_category || "").toLowerCase(); // Et dans la catégorie (ex: "Métal")

      matchesSearch =
        title.includes(search) ||
        machine.includes(search) ||
        materialName.includes(search) ||
        materialCategory.includes(search);
    }

    return matchesCategory && matchesSearch;
  });
});

onMounted(fetchTests);
</script>

<template>
  <main class="tests-container">
    <div class="tests-header">
      <h2 class="section-title">Galerie de tests</h2>

      <!-- BARRE DE RECHERCHE (Optionnelle comme sur ton visuel) -->
      <div class="search-container">
        <input
          type="text"
          placeholder="Rechercher un matériau, une machine..."
          class="search-input"
        />
      </div>

      <!-- 4. LES BOUTONS DE CATÉGORIES -->
      <div class="filter-bar">
        <button
          v-for="cat in categories"
          :key="cat"
          @click="activeFilter = cat"
          :class="['filter-btn', { active: activeFilter === cat }]"
        >
          {{ cat }}
        </button>
      </div>
    </div>

    <!-- GRILLE LIMITÉE À 4 CARTES PAR LIGNE -->
    <div v-if="filteredTests.length > 0" class="test-grid">
      <TestCard v-for="t in filteredTests" :key="t.test_id" :test="t" />
    </div>

    <div v-else class="info-msg">
      Aucun test ne correspond à cette catégorie.
    </div>
  </main>
</template>

<style scoped>
.tests-container {
  padding: 40px 5%;
  background-color: #f8f9fa;
  min-height: 80vh;
}

.tests-header {
  text-align: center;
  margin-bottom: 40px;
}

.section-title {
  font-size: 2rem;
  margin-bottom: 30px;
}

/* Style de la barre de recherche */
.search-container {
  margin-bottom: 25px;
}
.search-input {
  width: 100%;
  max-width: 500px;
  padding: 12px 20px;
  border-radius: 25px;
  border: 1px solid #eee;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

/* STYLE DES BOUTONS DE FILTRES (Tes Chips bleus) */
.filter-bar {
  display: flex;
  justify-content: center;
  gap: 15px;
  flex-wrap: wrap;
  margin-bottom: 40px;
}

.filter-btn {
  background-color: #60c4e6; /* Ton bleu InCô */
  color: white;
  border: none;
  padding: 8px 20px;
  border-radius: 8px;
  font-family: "JetBrains Mono", monospace; /* Style technique */
  cursor: pointer;
  transition: all 0.2s ease;
  font-size: 0.9rem;
  box-shadow: 0 4px 6px rgba(96, 196, 230, 0.2);
}

.filter-btn:hover {
  background-color: #4da9c8;
  transform: translateY(-2px);
}

/* Style quand le bouton est sélectionné */
.filter-btn.active {
  background-color: #1e1e1e; /* On passe en noir pour montrer la sélection */
  color: #60c4e6;
  font-weight: bold;
}

/* GRILLE : MAX 4 CARTES PAR LIGNE */
.test-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr); /* Force 4 colonnes */
  gap: 25px;
  max-width: 1300px;
  margin: 0 auto;
}

/* Adaptabilité tablette et mobile */
@media (max-width: 1100px) {
  .test-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}
@media (max-width: 650px) {
  .test-grid {
    grid-template-columns: 1fr;
  }
}

.info-msg {
  text-align: center;
  color: #999;
  margin-top: 50px;
}
</style>
