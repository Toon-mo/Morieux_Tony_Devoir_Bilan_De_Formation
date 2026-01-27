<script setup>
import { ref, onMounted, computed } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";

// Configuration
const router = useRouter();
const API_BASE =
  "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api";

// --- Fichiers & preview ---
const selectedFile = ref(null);
const imagePreview = ref(null);
const fileInput = ref(null); // Référence pour l'input file

// --- Référentiels ---
const machines = ref([]);
const materials = ref([]);

// --- Utilisateur courant ---
const user = ref(null);
const loadingUser = ref(true);

// Computed pour affichage badge (Plus propre pour le jury)
const displayUserName = computed(() => {
  if (loadingUser.value) return "Chargement...";
  return user.value?.username || "Invité";
});

const displayAvatar = computed(() => {
  if (loadingUser.value) return "…";
  return user.value?.username?.charAt(0).toUpperCase() || "?";
});

// --- Formulaire ---
const formData = ref({
  title: "",
  description: "",
  speed: 800,
  power: 50,
  frequency: 45,
  pulse: 200,
  nb_passes: 1,
  z_offset: 0,
  sweep: 1,
  hatch: 1,
  row_interval: 0.05,
  wobble: 0,
  user_id: null,
  machine_id: null,
  material_id: null,
});

// --- Gestion du fichier image ---
const onFileSelected = (event) => {
  const file = event.target.files[0];
  if (file) {
    selectedFile.value = file;
    imagePreview.value = URL.createObjectURL(file);
  }
};

// --- Chargement des données ---
const loadInitialData = async () => {
  try {
    const [resM, resMat] = await Promise.all([
      axios.get(`${API_BASE}/machines.php`),
      axios.get(`${API_BASE}/materials.php`),
    ]);
    machines.value = resM.data;
    materials.value = resMat.data;
  } catch (err) {
    console.error("Erreur référentiels :", err);
  }
};

const loadUser = () => {
  const savedUser = localStorage.getItem("user");
  if (savedUser) {
    user.value = JSON.parse(savedUser);
    formData.value.user_id = user.value.user_id;
  } else {
    // Sécurité : si pas connecté, on redirige
    router.push("/login");
  }
  loadingUser.value = false;
};

// --- Soumission ---
const handleSubmit = async () => {
  if (!formData.value.title || !selectedFile.value) {
    toast.error("Le titre et la photo sont obligatoires !");
    return;
  }

  try {
    const data = new FormData();
    data.append("image", selectedFile.value);

    Object.keys(formData.value).forEach((key) => {
      if (formData.value[key] !== null) data.append(key, formData.value[key]);
    });

    const res = await axios.post(`${API_BASE}/tests.php`, data, {
      headers: { "Content-Type": "multipart/form-data" },
    });

    if (res.status === 201 || res.data.success) {
      toast.success("Test publié avec succès !");
      setTimeout(() => router.push("/"), 1500);
    }
  } catch (err) {
    toast.error("Erreur lors de la publication");
    console.error(err);
  }
};

onMounted(async () => {
  loadUser();
  await loadInitialData();
});
</script>

<template>
  <div class="page-bg">
    <h1 class="main-title">Partagez vos connaissances</h1>

    <form @submit.prevent="handleSubmit" class="form-layout">
      <!-- COLONNE GAUCHE -->
      <div class="col-left">
        <!-- Zone Upload Image -->
        <div class="visual-card shadow-card" @click="fileInput.click()">
          <img v-if="imagePreview" :src="imagePreview" class="full-preview" />
          <div v-else class="placeholder-content">
            <span class="plus-icon">+</span>
            <p>Ajouter une photo du rendu</p>
          </div>
          <input
            type="file"
            ref="fileInput"
            @change="onFileSelected"
            accept="image/*"
            style="display: none"
          />
        </div>

        <div class="description-card shadow-card">
          <label class="label-title">Titre :</label>
          <input
            v-model="formData.title"
            type="text"
            placeholder="Nommez votre test..."
            class="title-input"
          />
          <label class="label-title">Description :</label>
          <textarea
            v-model="formData.description"
            placeholder="Écrivez vos astuces ici..."
          ></textarea>
        </div>
      </div>

      <!-- COLONNE DROITE -->
      <div class="col-right">
        <div class="main-params-card shadow-card">
          <!-- Badge utilisateur dynamique -->
          <div class="user-chip">
            <div class="avatar">{{ displayAvatar }}</div>
            <span>{{ displayUserName }}</span>
          </div>

          <!-- Sélecteurs -->
          <div class="select-group">
            <label>Machine :</label>
            <select v-model="formData.machine_id" required>
              <option :value="null">Choisir ▼</option>
              <option
                v-for="m in machines"
                :key="m.machine_id"
                :value="m.machine_id"
              >
                {{ m.name }}
              </option>
            </select>
          </div>

          <div class="select-group">
            <label>Matériau :</label>
            <select v-model="formData.material_id" required>
              <option :value="null">Choisir ▼</option>
              <option
                v-for="mat in materials"
                :key="mat.material_id"
                :value="mat.material_id"
              >
                {{ mat.name }}
              </option>
            </select>
          </div>

          <!-- Paramètres scrollables -->
          <div class="container-parameters">
            <div class="parameters-inner-box">
              <h3 class="box-title">Paramètres</h3>
              <div class="scroll-zone">
                <div class="param-row">
                  <label>Puissance (%)</label
                  ><input v-model.number="formData.power" type="number" />
                </div>
                <div class="param-row">
                  <label>Vitesse (mm/s)</label
                  ><input v-model.number="formData.speed" type="number" />
                </div>
                <div class="param-row">
                  <label>Fréquence (kHz)</label
                  ><input v-model.number="formData.frequency" type="number" />
                </div>
                <div class="param-row">
                  <label>Pulse (ns)</label
                  ><input v-model.number="formData.pulse" type="number" />
                </div>
                <div class="param-row">
                  <label>Z-Offset (mm)</label
                  ><input
                    v-model.number="formData.z_offset"
                    type="number"
                    step="0.1"
                  />
                </div>
                <div class="param-row">
                  <label>Nb Passes</label
                  ><input v-model.number="formData.nb_passes" type="number" />
                </div>
                <div class="param-row">
                  <label>Intervalle (mm)</label
                  ><input
                    v-model.number="formData.row_interval"
                    type="number"
                    step="0.001"
                  />
                </div>
              </div>
            </div>
            <button type="button" class="btn-join">Join Files</button>
          </div>

          <div class="footer-actions">
            <button type="submit" class="btn-submit shadow-btn">Submit</button>
            <button
              type="button"
              @click="router.push('/')"
              class="btn-cancel shadow-btn"
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<style scoped>
.page-bg {
  padding: 40px 5%;
  background-color: #f8f9fa;
  min-height: 100vh;
  font-family: "Inter", sans-serif;
}
.main-title {
  text-align: center;
  margin-bottom: 40px;
  border-bottom: 2px solid #60c4e6;
  display: block;
  width: fit-content;
  margin: 0 auto 40px;
  padding-bottom: 10px;
}

.form-layout {
  display: flex;
  gap: 200px;
  max-width: 1226px;
  margin: 0 auto;
  align-items: flex-start;
}
.col-left {
  flex: 1.2;
  display: flex;
  flex-direction: column;
  gap: 25px;
}
.col-right {
  flex: 1;
  width: 786px;
  position: sticky;
  top: 20px;
}

.shadow-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
}

/* Upload Zone */
.visual-card {
  height: 320px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: 2px dashed #ddd;
  overflow: hidden;
}
.full-preview {
  width: 100%;
  height: 100%;
  object-fit: cover;
}
.placeholder-content {
  text-align: center;
  color: #999;
}
.plus-icon {
  font-size: 3rem;
  display: block;
}

/* Desc card */
.description-card {
  padding: 25px;
  border: 1.5px solid #60c4e6;
}
.title-input {
  width: 100%;
  padding: 10px;
  margin-bottom: 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
}
textarea {
  width: 100%;
  height: 100px;
  border: 1px solid #f0f0f0;
  outline: none;
  resize: none;
  font-family: inherit;
}

/* Right Card */
.main-params-card {
  padding: 25px;
  border: 1.5px solid #60c4e6;
}
.user-chip {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #60c4e6;
  color: #1e1e1e;
  padding: 8px 15px;
  border-radius: 25px;
  width: fit-content;
  margin: 0 auto 20px;
  font-weight: bold;
}
.avatar {
  width: 25px;
  height: 25px;
  background: white;
  border-radius: 50%;
  text-align: center;
  line-height: 25px;
}

.select-group {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
select {
  width: 60%;
  padding: 8px;
  border-radius: 6px;
  border: 1px solid #ddd;
}

/* Param zone SCROLLABLE */

.container-parameters {
  display: flex;
  justify-content: space-around;
  gap: 20px;
  align-items: center;
}
.parameters-inner-box {
  width: 250px;
  display: flex;
  flex-direction: column;
  background: #fcfcfc;
  border-radius: 12px;
  padding: 20px;
  border: 1px solid #f0f0f0;
}
.box-title {
  text-align: center;
}

.scroll-zone {
  max-height: 220px;
  overflow-y: auto;
  padding-right: 10px;
}
.scroll-zone::-webkit-scrollbar {
  width: 5px;
}
.scroll-zone::-webkit-scrollbar-thumb {
  background: #60c4e6;
  border-radius: 10px;
}

.param-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}
.param-row input {
  width: 70px;
  padding: 5px;
  text-align: center;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.btn-join {
  width: 20%;
  height: 45px;
  margin-top: 15px;
  background: #60c4e6;
  border: none;
  padding: 10px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}

/* Actions */
.footer-actions {
  display: flex;
  justify-content: space-between;
  gap: 120px;
  margin-top: 25px;
}
.btn-submit {
  flex: 1;
  background: #60c4e6;
  border: none;
  padding: 12px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}
.btn-cancel {
  flex: 1;
  background: white;
  border: 1.5px solid #60c4e6;
  padding: 12px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
}

.shadow-btn,
.btn-join {
  box-shadow: 0 4px 0 rgba(0, 0, 0, 0.1);
  transition: 0.1s;
}
.shadow-btn:active,
.btn-join:active {
  transform: translateY(2px);
  box-shadow: 0 1px 0 rgba(0, 0, 0, 0.1);
}

@media (max-width: 800px) {
  .form-layout {
    flex-direction: column;
  }
}
</style>
