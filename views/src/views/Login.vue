<template>
  <div class="page">
    <div class="box">
      <el-card>
        <div slot="header">
          用户登录
        </div>
        <el-form :model="Form" label-width="80px" ref='login' :rules="rules">
          <el-form-item label="用户名" prop="User">
            <el-input v-model="Form.User" placeholder="请输入用户名"></el-input>
          </el-form-item>
          <el-form-item label="密码" prop="Password">
            <el-input type="password" v-model="Form.Password" placeholder="请输入密码"></el-input>
          </el-form-item>
          <el-form-item label="">
            <el-button type="primary" @click="Login('login')">登录</el-button>
          </el-form-item>
        </el-form>
      </el-card>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      Form: {
        User: "",
        Password: ""
      },
      rules: {
        User: [
          {
            required: true,
            message: "请输入用户名",
            trigger: "blur"
          }
        ],
        Password: [
          {
            required: true,
            message: "请输入密码",
            trigger: "blur"
          }
        ]
      }
    };
  },
  methods: {
    Login(form) {
      this.$refs[form].validate(valid => {
        if (valid) {
          this.axios
            .post("/auth/login", this.Form)
            .then(res => {
              this.$message.success(res.data.message);
              this.$store.commit(
                "Login",
                Object.assign({}, { User: this.Form.User })
              );
              this.$router.push({
                name: "home"
              });
            })
            .catch(err => {
              console.log(err);
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    }
  }
};
</script>

<style scoped>
.page {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
  width: 100vw;
}
.box {
  width: 400px;
}
</style>
