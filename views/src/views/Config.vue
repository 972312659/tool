<template>
    <div>
        <el-card v-loading="Loading" element-loading-text="拼命加载中">
            <el-tabs v-model="ActiveName" @tab-click="SwitchTab">
                <el-tab-pane label="医院" name="hospital">
                    <el-button type="primary" @click="Save">保存</el-button>
                    <br>
                    <br>
                    <el-tree :data="FeatureList" :default-checked-keys="DefaultCheckedArray" default-expand-all ref="onetree" node-key="Id" :props="{label:'Name',key:'Id',children:'Children'}" show-checkbox @check-change="GetCheckedNodes('onetree')">
                        <span slot-scope="{ node,data }">
                            <span>{{data.Name}}</span>
                            <span v-if="data.ActionCount">({{data.ActionCount}})</span>
                        </span>
                    </el-tree>
                </el-tab-pane>
                <el-tab-pane label="网点" name="slave">
                    <el-button type="primary" @click="Save">保存</el-button>
                    <br>
                    <br>
                    <el-tree :data="FeatureList" :default-checked-keys="DefaultCheckedArray" default-expand-all ref="twotree" node-key="Id" :props="{label:'Name',key:'Id',children:'Children'}" show-checkbox @check-change="GetCheckedNodes('twotree')">
                        <span slot-scope="{ node,data }">
                            <span>{{data.Name}}</span>
                            <span v-if="data.ActionCount">({{data.ActionCount}})</span>
                        </span>
                    </el-tree>
                </el-tab-pane>
                <el-tab-pane label="供应商" name="producer">
                    <el-button type="primary" @click="Save">保存</el-button>
                    <br>
                    <br>
                    <el-tree :data="FeatureList" :default-checked-keys="DefaultCheckedArray" default-expand-all ref="threetree" node-key="Id" :props="{label:'Name',key:'Id',children:'Children'}" show-checkbox @check-change="GetCheckedNodes('threetree')">
                        <span slot-scope="{ node,data }">
                            <span>{{data.Name}}</span>
                            <span v-if="data.ActionCount">({{data.ActionCount}})</span>
                        </span>
                    </el-tree>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>

<script>
export default {
  data() {
    return {
      Loading: true,
      FeatureList: [],
      DefaultCheckedArray: [],
      CheckedArray: [],
      ActiveName: "hospital",
      ActiveType: 1
    };
  },
  methods: {
    GetFeatureList() {
      this.axios
        .get("/feature/all")
        .then(res => {
          this.FeatureList = res.data;
          this.Loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    GetDefaultType() {
      this.axios
        .post("/feature/default", { Type: this.ActiveType })
        .then(res => {
          let keys = [];
          for (const key in res.data) {
            keys.push(res.data[key].FeatureId);
          }
          this.DefaultCheckedArray = keys;
          if (this.ActiveType === 1) {
            this.$refs["onetree"].setCheckedKeys(this.DefaultCheckedArray);
          } else if (this.ActiveType === 2) {
            this.$refs["twotree"].setCheckedKeys(this.DefaultCheckedArray);
          } else if (this.ActiveType === 3) {
            this.$refs["threetree"].setCheckedKeys(this.DefaultCheckedArray);
          }
          this.Loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    GetDefaultAttay() {
      this.DefaultCheckedArray = [];
    },
    GetCheckedNodes(refs) {
      let nodes = this.$refs[refs].getCheckedNodes();
      let keys = [];
      for (const key in nodes) {
        if (nodes.hasOwnProperty(key)) {
          const element = nodes[key];
          if (!element.Children) {
            keys.push(element.Id);
          }
        }
      }
      this.CheckedArray = keys;
    },
    SwitchTab(tab, event) {
      this.Loading = true;
      if (tab.name === "hospital") {
        this.ActiveType = 1;
      } else if (tab.name === "slave") {
        this.ActiveType = 2;
      } else if (tab.name === "producer") {
        this.ActiveType = 3;
      }
      this.GetDefaultType();
    },
    Save() {
      let data = Object.assign(
        {},
        { Features: this.CheckedArray },
        { Type: this.ActiveType }
      );
      if (data.Features.length < 1) {
        this.$message.error("请选择要分配的权限");
        return;
      }
      this.$confirm("确认配置权限，是否继续?", "提示", {
        confirmButtonText: "确认",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          this.axios
            .post("/feature/setDefault", data)
            .then(res => {
              this.$message({
                message: "操作成功",
                type: "success"
              });
            })
            .catch(error => {
              console.log(error);
            });
        })
        .catch(() => {
          this.$message({
            message: "取消操作",
            type: "info"
          });
        });
    }
  },
  mounted() {
    this.GetFeatureList();
    this.GetDefaultType();
  }
};
</script>

<style>
</style>
