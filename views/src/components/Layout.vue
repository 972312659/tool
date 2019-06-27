<template>
    <div>
        <el-container class="container-main">
            <el-header>
                <el-row type="flex" justify="space-between">
                    <el-col :span="4">
                        <span class="logo">Peach Tool</span>
                    </el-col>
                    <el-col :span="1">
                        <el-tooltip :content="$store.state.userinfo.User" placement="left">
                            <el-dropdown>
                                <el-button type="default" icon="el-icon-setting" circle></el-button>
                                <el-dropdown-menu slot="dropdown">
                                    <el-dropdown-item>
                                        <span @click="Logout">退出登录</span>
                                    </el-dropdown-item>
                                </el-dropdown-menu>
                            </el-dropdown>
                        </el-tooltip>
                    </el-col>
                </el-row>
            </el-header>
            <el-container>
                <el-aside width="200px">
                    <el-menu :default-active="$route.path" router>
                        <el-menu-item :index="'/home'">
                            <template slot="title">
                                <i class="el-icon-menu"></i>
                                <span>首页</span>
                            </template>
                        </el-menu-item>
                        <el-menu-item :index="'/menu'">
                            <template slot="title">
                                <i class="el-icon-menu"></i>
                                <span>功能管理</span>
                            </template>
                        </el-menu-item>
                        <el-menu-item :index="'/route'">
                            <template slot="title">
                                <i class="el-icon-menu"></i>
                                <span>路由管理</span>
                            </template>
                        </el-menu-item>
                        <el-menu-item :index="'/cache'">
                            <template slot="title">
                                <i class="el-icon-menu"></i>
                                <span>缓存管理</span>
                            </template>
                        </el-menu-item>
                        <el-menu-item :index="'/config'">
                            <template slot="title">
                                <i class="el-icon-menu"></i>
                                <span>权限配置</span>
                            </template>
                        </el-menu-item>
                    </el-menu>
                </el-aside>
                <el-main>
                    <router-view></router-view>
                </el-main>
            </el-container>
        </el-container>
    </div>
</template>

<script>
export default {
  name: "layout",
  data() {
    return {};
  },
  methods: {
    Logout() {
      this.$store.commit("Logout");
      this.$router.push({
        name: "login"
      });
    }
  }
};
</script>

<style scoped>
.el-header {
  background: #324057;
  line-height: 60px;
}
.logo {
  color: #cbcfd4;
  font-size: 30px;
}
.el-main {
  background: #e5e9f2;
}
.container-main {
  height: 100vh;
  width: 100vw;
}
</style>
