<template>
  <div class="app-container">
    <!-- Header -->
    <header class="main-header">
      <nav class="nav-content">
        <router-link to="/" class="logo">InCô Laser</router-link>

        <div class="nav-links-left">
          <router-link to="/machines">Machines</router-link>
          <router-link to="/materials">Matières</router-link>
          <router-link to="/tests">Test</router-link>
          <router-link to="astuces/">Astuces</router-link>
        </div>

        <div class="nav-links-right">
          <!-- SI CONNECTÉ -->
          <div v-if="user" class="user-logged-box">
            <router-link to="/create" class="btn-publish"
              >Publier un test</router-link
            >
            <span class="user-name">👤 {{ user.username }}</span>
            <button @click="handleLogout" class="btn-logout">
              Déconnexion
            </button>
          </div>

          <!-- SI NON CONNECTÉ -->
          <router-link v-else to="/login" class="btn-login"
            >Connexion</router-link
          >
        </div>
      </nav>
    </header>

    <!-- Contenu principal -->
    <main class="app-main">
      <router-view />
    </main>

    <!-- Footer -->
    <footer class="main-footer">
      <p>&copy; 2026 InCô Laser Community</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { useRouter, useRoute } from "vue-router";

const router = useRouter();
const route = useRoute();
const user = ref(null);

const checkUser = () => {
  const savedUser = localStorage.getItem("user");
  user.value = savedUser ? JSON.parse(savedUser) : null;
};

onMounted(checkUser);

watch(
  () => route.path,
  () => {
    checkUser();
  },
);

const handleLogout = () => {
  localStorage.removeItem("user");
  user.value = null;
  router.push("/login");
};
</script>

<style>
/* Container full height */
.app-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh; /* prend tout l'espace vertical */
  font-family: "Inter", sans-serif;
  background: #fafafa;
}

/* Main qui prend tout l'espace restant */
.app-main {
  flex-grow: 1; /* pousse le footer en bas */
  padding: 2rem 5%;
}

/* Header */
.main-header {
  background: #1e1e1e;
  color: white;
  padding: 1rem 5%;
}
.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #60c4e6;
  text-decoration: none;
}
.nav-links-left a {
  color: white;
  text-decoration: none;
  margin: 0 40px;
}
.nav-links-right {
  display: flex;
  align-items: center;
}
.nav-links-right a {
  color: white;
  text-decoration: none;
  margin-left: 20px;
}
.btn-publish {
  background: #60c4e6;
  padding: 3px 8px;
  border-radius: 5px;
}

/* Styles utilisateur */
.user-logged-box {
  display: flex;
  align-items: center;
  gap: 15px;
}
.user-name {
  color: #60c4e6;
  font-weight: bold;
}
.btn-logout {
  background: none;
  border: 1px solid #ff4d4d;
  color: #ff4d4d;
  padding: 5px 12px;
  border-radius: 4px;
  cursor: pointer;
  font-weight: bold;
}
.btn-logout:hover {
  background: #ff4d4d;
  color: white;
}
.btn-login {
  border: 1px solid #60c4e6;
  padding: 5px 15px;
  border-radius: 4px;
}

/* Footer */
.main-footer {
  text-align: center;
  padding: 2rem 5%;
  color: #666;
  background: #fafafa;
}
</style>
