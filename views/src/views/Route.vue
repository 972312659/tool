<template>
  <div>
    <el-card v-loading="Loading" element-loading-text="拼命加载中">
      <div slot="header">
        <i class="el-icon-info"></i>
        路由管理
      </div>
      <table style="width:100%" cellspacing="0" cellpadding="5" border="0">
        <tr>
          <th>控制器</th>
          <th>动作</th>
          <th>功能</th>
          <th>状态</th>
          <th>操作</th>
        </tr>
        <template v-for="(item,key) in RouteList">
          <tr v-for="(scope, index) in item" :key="scope.Id">
            <td :rowspan="item.length" v-if="index==0">{{key}}</td>
            <td>{{scope.Action}}</td>
            <td>
              <el-tag type="primary" size="small" v-if="scope.Path">{{scope.Path}}</el-tag>
              <span v-else>&nbsp;&nbsp;&nbsp;&nbsp;</span>
            </td>
            <td>
              <el-tag :type="scope.Discard===1?'danger':'success'" size="small">{{scope.Discard===1?'已废弃':'正常'}}</el-tag>
            </td>
            <td>
              <el-button round type="primary" size="small" v-if="scope.Type===1&&scope.FeatureId===null" @click="BindMenu(scope)">绑定</el-button>
              <el-button round type="danger" size="small" v-if="scope.FeatureId" @click="UnBindMenu(scope)">解绑</el-button>
            </td>
          </tr>
        </template>
      </table>
    </el-card>
    <el-dialog title="所属功能" :visible.sync="Dialog" center width="30%">
      <el-form :model="Data" ref="Feature" label-width="80px" :rules="rules">
        <el-form-item label="选择功能" prop="FeatureId">
          <el-cascader :options="FeatureList" :props="{value:'Id',label:'Name',children:'Children'}" clearable filterable v-model="Data.FeatureId" style="width:100%"></el-cascader>
        </el-form-item>
        <el-form-item label="">
          <el-button round type="primary" @click="Save('Feature')">保存</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      RouteList: [],
      FeatureList: [],
      Loading: true,
      Dialog: false,
      Data: {
        ActionId: "",
        FeatureId: []
      },
      rules: {
        FeatureId: [
          {
            required: true,
            type: "array",
            message: "请选择要绑定的功能",
            trigger: "change"
          }
        ]
      }
    };
  },
  methods: {
    GetRouteList() {
      this.axios
        .get("/route/all")
        .then(res => {
          this.RouteList = res.data;
          this.Loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    GetFeatureList() {
      this.axios
        .get("/feature/all")
        .then(res => {
          this.FeatureList = res.data;
        })
        .catch(err => {
          console.log(err);
        });
    },
    BindMenu(row) {
      this.Dialog = true;
      this.Data.ActionId = row.Id;
      this.Data.FeatureId = [];
    },
    Save(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          let Data = Object.assign({}, this.Data);
          Data.FeatureId = Data.FeatureId[Data.FeatureId.length - 1];
          this.axios.post("/route/bind", Data).then(res => {
            this.$message.success(res.data.message);
            this.$refs[formName].resetFields();
            this.Dialog = false;
            this.Loading = true;
            this.GetRouteList();
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    UnBindMenu(row) {
      this.$confirm("正在解绑该路由与功能关系,是否继续?", "提示", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          this.axios
            .post("/route/unbind", {
              ActionId: row.Id
            })
            .then(res => {
              this.$message.success(res.data.message);
              this.GetRouteList();
            })
            .catch(err => {
              console.log(err);
            });
        })
        .catch(() => {
          this.$message.info("已取消操作");
        });
    }
  },
  mounted() {
    this.GetFeatureList();
    this.GetRouteList();
  }
};
</script>

<style>
th,
td {
  border-top: 0;
  border-right: 1px #ebeef5 solid;
  border-bottom: 1px #ebeef5 solid;
  border-left: 0;
}
table {
  border-top: 1px #ebeef5 solid;
  border-right: 0;
  border-bottom: 0;
  border-left: 1px #ebeef5 solid;
}
</style>
