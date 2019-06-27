import Vue from "vue";
import Router from "vue-router";
import Layout from "@/components/Layout.vue";
import store from "./store";
Vue.use(Router);
const router = new Router({
  routes: [
    {
      path: "/",
      name: "Main",
      meta: {
        title: "桃子系统管理",
        auth: true
      },
      component: Layout,
      redirect: "/home",
      children: [
        {
          path: "home",
          name: "home",
          meta: {
            title: "首页",
            auth: true
          },
          component: () => import("@/views/Home.vue")
        },
        {
          path: "menu",
          name: "menu",
          meta: {
            title: "功能管理",
            auth: true
          },
          component: () => import("@/views/Menu.vue")
        },
        {
          path: "route",
          name: "route",
          meta: {
            title: "路由管理",
            auth: true
          },
          component: () => import("@/views/Route.vue")
        },
        {
          path: "cache",
          name: "cache",
          meta: {
            title: "缓存管理",
            auth: true
          },
          component: () => import("@/views/Cache.vue")
        },
        {
          path: "config",
          name: "config",
          meta: {
            title: "权限配置",
            auth: true
          },
          component: () => import("@/views/Config.vue")
        }
      ]
    },
    {
      path: "/login",
      name: "login",
      meta: {
        title: "登录",
        auth: false
      },
      component: () => import("@/views/Login.vue")
    },
    {
      path: "*",
      name: "notfound",
      redirect: "/login"
    }
  ]
});
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.auth)) {
    if (!store.state.userinfo.User) {
      store.commit("Logout");
      next({
        path: "/login",
        query: { redirect: to.fullPath }
      });
    } else {
      next();
    }
  } else {
    next();
  }
});
export default router;
