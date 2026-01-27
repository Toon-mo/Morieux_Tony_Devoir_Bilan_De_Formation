<script setup>
import { ref } from "vue";
import axios from "axios";
import { useRouter } from "vue-router";
import { toast } from "vue3-toastify";

const router = useRouter();
const email = ref("");
const password = ref("");
const loading = ref(false);

const handleLogin = async () => {
  if (!email.value || !password.value) {
    toast.error("Veuillez remplir tous les champs");
    return;
  }

  loading.value = true;
  try {
    const url =
      "http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/login.php";
    const response = await axios.post(url, {
      email: email.value,
      password: password.value,
    });

    if (response.data.user) {
      // Stockage de la session
      localStorage.setItem("user", JSON.stringify(response.data.user));

      toast.success("Connexion réussie !");

      // Redirection vers le dashboard
      setTimeout(() => {
        router.push("/dashboard");
      }, 1000);
    }
  } catch (err) {
    console.error(err);
    const message = err.response?.data?.message || "Identifiants incorrects";
    toast.error(message);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <div class="login-page">
    <div class="login-card shadow-card">
      <div class="login-header">
        <div class="logo-circle">InCô</div>
        <h1>Connexion</h1>
        <p>Espace membre Community</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div class="form-group">
          <label>Email professionnel</label>
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
            placeholder="••••••••"
            required
          />
        </div>

        <button type="submit" class="btn-login" :disabled="loading">
          {{ loading ? "Connexion en cours..." : "Se connecter" }}
        </button>
      </form>

      <div class="login-footer">
        <p>
          Pas encore de compte ?
          <router-link to="/register">S'inscrire</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  background-color: #f8f9fa; /* Gris léger comme ton site */
}

.login-card {
  background: white;
  width: 100%;
  max-width: 400px;
  padding: 40px;
  border-radius: 20px;
  border: 1.5px solid #60c4e6; /* Ton bleu InCô */
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
}

.login-header {
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
  font-size: 1.2rem;
}

h1 {
  font-size: 1.8rem;
  color: #1e1e1e;
  margin-bottom: 5px;
}

.login-header p {
  color: #888;
  font-size: 0.9rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

label {
  font-weight: 600;
  font-size: 0.9rem;
  color: #333;
}

input {
  padding: 12px 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  font-size: 1rem;
  transition: 0.3s;
}

input:focus {
  border-color: #60c4e6;
  outline: none;
  box-shadow: 0 0 0 3px rgba(96, 196, 230, 0.15);
}

.btn-login {
  background: #1e1e1e; /* Fond noir pour le contraste */
  color: #60c4e6; /* Texte bleu InCô */
  border: none;
  padding: 14px;
  border-radius: 8px;
  font-weight: bold;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.3s;
  margin-top: 10px;
}

.btn-login:hover {
  background: #333;
  transform: translateY(-2px);
}

.btn-login:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.login-footer {
  margin-top: 25px;
  text-align: center;
  font-size: 0.85rem;
  color: #666;
}

.login-footer a {
  color: #60c4e6;
  font-weight: bold;
  text-decoration: none;
}
</style>
