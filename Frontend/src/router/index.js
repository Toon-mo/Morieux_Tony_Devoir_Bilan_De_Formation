import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import TestDetailView from "../views/TestDetailView.vue";
import LoginView from "../views/LoginView.vue";
import RegisterView from "../views/RegisterView.vue";
import CreateTestView from "../views/CreateTestView.vue";
import TestsView from "../views/TestsView.vue";
import MachinesView from "../views/MachinesView.vue";
import MaterialsView from "../views/MaterialsView.vue";
import AstucesView from "../views/AstucesView.vue";

const routes = [
  { path: "/", name: "home", component: HomeView },
  {
    path: "/test/:id",
    name: "test-detail",
    component: TestDetailView,
    props: true,
  },
  { path: "/login", name: "login", component: LoginView },
  {
    path: "/create",
    name: "create",
    component: CreateTestView,
    meta: { requiresAuth: true }, // Seuls les connectés peuvent créer
  },
  {
    path: "/dashboard",
    name: "dashboard",
    component: () => import("../views/DashboardView.vue"), // Chargement paresseux (pro)
    meta: { requiresAuth: true },
  },
  { path: "/register", name: "register", component: RegisterView },
  { path: "/tests", name: "all-tests", component: TestsView },
  { path: "/machines", name: "machines", component: MachinesView },
  { path: "/materials", name: "materials", component: MaterialsView },
  { path: "/astuces", name: "tips", component: AstucesView },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach((to, from, next) => {
  const user = localStorage.getItem("user");
  if (to.meta.requiresAuth && !user) {
    next("/login");
  } else {
    next();
  }
});

export default router;
