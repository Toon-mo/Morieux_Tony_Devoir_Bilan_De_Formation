<script setup>
import { computed } from "vue";

// Props reçues du parent
const props = defineProps({
  test: {
    type: Object,
    required: true,
  },
});

// URL de l'image depuis le backend
const imageUrl = computed(() => {
  if (props.test.image) {
    return `http://localhost/Morieux_Tony_Devoir_Bilan_DE_Formation/Backend/api/image.php?name=${props.test.image}`;
  }
  return "/assets/default-image.png";
});
</script>

<template>
  <div class="test-card">
    <div class="img-container">
      <img
        :src="imageUrl"
        :alt="props.test.title || 'Rendu du test'"
        class="card-img"
      />
    </div>

    <div class="card-content">
      <h3 class="card-title">{{ props.test.title || "Test InCô" }}</h3>

      <span class="machine-badge">
        {{ props.test.machine_name || "Machine inconnue" }}
      </span>

      <div class="params-info">
        <span class="param-item">
          <strong>{{ props.test.speed || "N/A" }}</strong> mm/s
        </span>
        <span class="separator">|</span>
        <span class="param-item">
          <strong>{{ props.test.power || "N/A" }}</strong
          >%
        </span>
      </div>

      <router-link :to="'/test/' + props.test.test_id" class="btn-details">
        Voir les réglages
      </router-link>
    </div>
  </div>
</template>

<style scoped>
.test-card {
  background: #fff;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  border: 2px solid #60c4e6;
  transition:
    transform 0.3s ease,
    box-shadow 0.3s ease;
}

.test-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 8px 25px rgba(96, 196, 230, 0.3);
}

.img-container {
  height: 220px;
  background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;
}

.card-img {
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.test-card:hover .card-img {
  transform: scale(1.05);
}

.card-content {
  display: flex;
  flex-direction: column;
  padding: 20px;
  gap: 12px;
}

.card-title {
  text-align: center;
  color: #1a1a1a;
  font-size: 1.1rem;
  font-weight: 600;
  margin: 0;
  min-height: 50px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.machine-badge {
  margin: 0 auto;
  background: #60c4e6;
  color: #1a1a1a;
  padding: 6px 16px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
  display: inline-block;
  width: fit-content;
}

.params-info {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 0.9rem;
  color: #555;
  padding: 8px 0;
}

.param-item {
  display: flex;
  align-items: center;
  gap: 4px;
}

.param-item strong {
  color: #1a1a1a;
  font-size: 1rem;
}

.separator {
  color: #d0d0d0;
  font-weight: 300;
}

.btn-details {
  display: block;
  text-align: center;
  color: #60c4e6;
  text-decoration: none;
  font-weight: 600;
  padding: 12px 0;
  border-top: 2px solid #f0f0f0;
  transition: all 0.2s;
  font-size: 0.95rem;
}

.btn-details:hover {
  color: #1a1a1a;
  background: #f8f9fa;
  border-top-color: #60c4e6;
}

/* Responsive */
@media (max-width: 650px) {
  .card-content {
    padding: 15px;
  }

  .card-title {
    font-size: 1rem;
    min-height: 40px;
  }
}
</style>
