<script setup>
import { ref, computed, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import axios from "axios";

const route = useRoute();
const router = useRouter();

const test = ref(null);
const erreur = ref(null);
const loading = ref(true);

const testId = route.params.id;

// URL de l'image depuis le backend
const imageUrl = computed(() => {
  if (test.value && test.value.image) {
    return `http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/image.php?name=${test.value.image}`;
  }
  return null;
});

// Récupération des détails du test
const fetchDetails = async () => {
  loading.value = true;
  try {
    const response = await axios.get(
      `http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/tests.php?id=${testId}`,
    );
    test.value = response.data;
  } catch (err) {
    console.error(err);
    erreur.value = "Impossible de charger les détails de ce test.";
  } finally {
    loading.value = false;
  }
};

// Télécharger le fichier .lbrn
const downloadLbrn = async () => {
  if (!test.value) return;
  try {
    const res = await axios.get(
      `http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/tests.php?id=${testId}&download=1`,
      { responseType: "blob" },
    );
    const url = window.URL.createObjectURL(new Blob([res.data]));
    const link = document.createElement("a");
    link.href = url;
    link.setAttribute("download", `${test.value.title || "test"}.lbrn`);
    document.body.appendChild(link);
    link.click();
    link.remove();
  } catch (err) {
    console.error(err);
    alert("Erreur lors du téléchargement.");
  }
};

onMounted(fetchDetails);
</script>

<template>
  <div class="detail-container">
    <!-- Bouton Retour -->
    <button @click="router.push('/')" class="btn-back">
      ← Retour aux tests
    </button>

    <!-- Loading / Erreur -->
    <div v-if="loading" class="loading">Chargement des données techniques…</div>
    <div v-else-if="erreur" class="error-msg">{{ erreur }}</div>

    <!-- Contenu -->
    <div v-else-if="test" class="detail-layout">
      <!-- Colonne gauche -->
      <div class="visual-column">
        <h1>{{ test?.title || "Test sans titre" }}</h1>
        <div class="image-placeholder">
          <img
            v-if="imageUrl"
            :src="imageUrl"
            :alt="test?.title || 'Rendu du test'"
          />
        </div>
        <div class="description-box">
          <h3>Description / Conseils :</h3>
          <p>{{ test?.description || "Aucune description fournie." }}</p>
        </div>
      </div>

      <!-- Colonne droite -->
      <div class="settings-column">
        <div class="author-card">
          <p>
            Posté par : <strong>{{ test?.author || "Inconnu" }}</strong>
          </p>
          <p>
            Machine : <span class="tag">{{ test?.machine_name || "N/A" }}</span>
          </p>
          <p>
            Matière :
            <span class="tag">{{ test?.material_name || "N/A" }}</span>
          </p>
        </div>

        <div class="parameters-grid">
          <h3>Paramètres de gravure :</h3>
          <div class="param-row">
            <span>Vitesse :</span> <strong>{{ test?.speed || 0 }} mm/s</strong>
          </div>
          <div class="param-row">
            <span>Puissance :</span> <strong>{{ test?.power || 0 }} %</strong>
          </div>
          <div class="param-row">
            <span>Fréquence :</span>
            <strong>{{ test?.frequency || 0 }} kHz</strong>
          </div>
          <div v-if="test?.pulse" class="param-row">
            <span>Pulse :</span> <strong>{{ test.pulse }} ns</strong>
          </div>
          <div class="param-row">
            <span>Passes :</span> <strong>{{ test?.nb_passes || 1 }}</strong>
          </div>
          <div class="param-row">
            <span>Z-Offset :</span>
            <strong>{{ test?.z_offset || 0 }} mm</strong>
          </div>
        </div>

        <button class="btn-download" @click="downloadLbrn">
          Télécharger les paramètres (.lbrn)
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.detail-container {
  padding: 2rem 5%;
  max-width: 1200px;
  margin: 0 auto;
}
.btn-back {
  background: none;
  border: none;
  color: #60c4e6;
  cursor: pointer;
  font-weight: bold;
  margin-bottom: 20px;
}
.detail-layout {
  display: flex;
  gap: 40px;
  align-items: flex-start;
}
.visual-column {
  flex: 2;
}
.settings-column {
  flex: 1;
  background: white;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
  border: 1px solid #eee;
}
.image-placeholder {
  width: 100%;
  height: 400px;
  background: #ddd;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
  overflow: hidden;
}
.image-placeholder img {
  height: 100%;
}
.description-box {
  background: #eef9fd;
  padding: 15px;
  border-radius: 8px;
  border-left: 4px solid #60c4e6;
}
.author-card {
  border-bottom: 1px solid #eee;
  padding-bottom: 15px;
  margin-bottom: 15px;
}
.tag {
  background: #1e1e1e;
  color: white;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 0.8rem;
}
.param-row {
  display: flex;
  justify-content: space-between;
  padding: 8px 0;
  border-bottom: 1px solid #f9f9f9;
}
.btn-download {
  width: 100%;
  margin-top: 20px;
  background: #60c4e6;
  color: #1e1e1e;
  border: none;
  padding: 12px;
  border-radius: 6px;
  font-weight: bold;
  cursor: pointer;
}
.loading,
.error-msg {
  text-align: center;
  font-size: 1.2rem;
  margin-top: 50px;
}
.error-msg {
  color: #e74c3c;
}
@media (max-width: 768px) {
  .detail-layout {
    flex-direction: column;
    gap: 20px;
  }
  .image-placeholder {
    height: 250px;
  }
  .settings-column {
    padding: 15px;
  }
}
</style>
