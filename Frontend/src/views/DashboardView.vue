<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";

const router = useRouter();
const user = ref(null);
const countdown = ref(3); // Timer de 3 secondes

onMounted(() => {
  const storedUser = localStorage.getItem("user");

  if (!storedUser) {
    router.push("/login");
    return;
  }

  user.value = JSON.parse(storedUser);

  // Lancement du compte à rebours pour la redirection
  const timer = setInterval(() => {
    countdown.value--;

    if (countdown.value <= 0) {
      clearInterval(timer);
      router.push("/"); // Redirection vers l'accueil
    }
  }, 1000);
});
</script>

<template>
  <div class="dashboard-container" v-if="user">
    <div class="dashboard-card shadow-card">
      <div class="welcome-content">
        <div class="check-icon">✓</div>
        <h1>Bienvenue, {{ user.username }} !</h1>
        <p class="status-text">Connexion réussie.</p>

        <div class="loader-box">
          <p>Préparation de votre espace...</p>
          <div class="progress-bar">
            <div class="progress-fill"></div>
          </div>
          <p class="timer-text">
            Redirection automatique dans <span>{{ countdown }}s</span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.dashboard-container {
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 80vh;
  background-color: #f8f9fa;
}

.dashboard-card {
  background: white;
  padding: 50px;
  border-radius: 20px;
  width: 100%;
  max-width: 450px;
  text-align: center;
  border: 1.5px solid #60c4e6;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
}

.check-icon {
  width: 60px;
  height: 60px;
  background: #60c4e6;
  color: #1e1e1e;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2rem;
  margin: 0 auto 20px;
}

h1 {
  color: #1e1e1e;
  font-size: 1.8rem;
  margin-bottom: 10px;
}

.status-text {
  color: #666;
  margin-bottom: 30px;
}

.loader-box {
  margin-top: 20px;
  color: #888;
  font-size: 0.9rem;
}

/* Animation de la barre de progression */
.progress-bar {
  width: 100%;
  height: 6px;
  background: #eee;
  border-radius: 10px;
  margin: 15px 0;
  overflow: hidden;
}

.progress-fill {
  width: 100%;
  height: 100%;
  background: #60c4e6;
  animation: loading 3s linear forwards;
}

@keyframes loading {
  from {
    width: 0%;
  }
  to {
    width: 100%;
  }
}

.timer-text span {
  color: #60c4e6;
  font-weight: bold;
}
</style>
