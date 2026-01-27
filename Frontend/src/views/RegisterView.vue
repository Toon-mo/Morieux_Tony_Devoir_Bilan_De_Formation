<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";

const router = useRouter();
const username = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const loading = ref(false);

const handleRegister = async () => {
  // 1. Validation de base
  if (!username.value || !email.value || !password.value) {
    toast.error("Veuillez remplir tous les champs");
    return;
  }

  // 2. Vérification de la correspondance des mots de passe
  if (password.value !== confirmPassword.value) {
    toast.error("Les mots de passe ne correspondent pas");
    return;
  }

  loading.value = true;
  try {
    const url =
      "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/register.php";

    const response = await axios.post(url, {
      username: username.value,
      email: email.value,
      password: password.value,
    });

    // Si le PHP renvoie un succès (Code 201)
    toast.success("Compte créé ! Vous pouvez vous connecter.");

    // Redirection vers le login après 2 secondes
    setTimeout(() => {
      router.push("/login");
    }, 2000);
  } catch (err) {
    console.error(err);
    const message =
      err.response?.data?.message || "Erreur lors de l'inscription";
    toast.error(message);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="register-page">
    <div class="register-card shadow-card">
      <div class="register-header">
        <div class="logo-circle">InCô</div>
        <h1>Créer un compte</h1>
        <p>Rejoignez la communauté laser</p>
      </div>

      <form @submit.prevent="handleRegister" class="register-form">
        <div class="form-group">
          <label>Nom d'utilisateur</label>
          <input
            v-model="username"
            type="text"
            placeholder="Ex: Paul_Laser"
            required
          />
        </div>

        <div class="form-group">
          <label>Email</label>
          <input
            v-model="email"
            type="email"
            placeholder="votre@email.fr"
            required
          />
        </div>

        <div class="form-group">
          <label>Mot de passe</label>
          <input
            v-model="password"
            type="password"
            placeholder="6 caractères minimum"
            required
          />
        </div>

        <div class="form-group">
          <label>Confirmer le mot de passe</label>
          <input
            v-model="confirmPassword"
            type="password"
            placeholder="••••••••"
            required
          />
        </div>

        <button type="submit" class="btn-register" :disabled="loading">
          {{ loading ? "Traitement..." : "Créer mon compte" }}
        </button>
      </form>

      <div class="register-footer">
        <p>Déjà membre ? <router-link to="/login">Se connecter</router-link></p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.register-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 90vh;
  background-color: #f8f9fa;
  padding: 20px;
}

.register-card {
  background: white;
  width: 100%;
  max-width: 450px;
  padding: 40px;
  border-radius: 20px;
  border: 1.5px solid #60c4e6;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
}

.register-header {
  text-align: center;
  margin-bottom: 30px;
}

.logo-circle {
  width: 60px;
  height: 60px;
  background: #1e1e1e;
  color: #60c4e6;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 15px;
  font-weight: bold;
}

h1 {
  font-size: 1.8rem;
  color: #1e1e1e;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

label {
  font-weight: 600;
  font-size: 0.85rem;
  color: #333;
}

input {
  padding: 10px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 0.95rem;
}

input:focus {
  border-color: #60c4e6;
  outline: none;
}

.btn-register {
  background: #1e1e1e;
  color: #60c4e6;
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-weight: bold;
  cursor: pointer;
  margin-top: 15px;
}

.btn-register:hover {
  background: #333;
}

.register-footer {
  margin-top: 20px;
  text-align: center;
  font-size: 0.85rem;
  color: #666;
}

.register-footer a {
  color: #60c4e6;
  font-weight: bold;
  text-decoration: none;
}
</style>
