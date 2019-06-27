<template>
  <div>
    <el-card v-loading="Loading" element-loading-text="拼命加载中">
      <div slot="header">
        <i class="el-icon-info"></i>
        功能管理
      </div>
      <el-row :gutter="20">
        <el-col :span="24">
          <el-button round type="primary" size="small" @click="Create">新建模块</el-button>
          <br>
          <br>
          <el-tree
            :data="FeatureList"
            ref="tree"
            node-key="Id"
            :props="{label:'Name',key:'Id',children:'Children'}"
            :check-strictly="true"
            default-expand-all
            :expand-on-click-node="false"
          >
            <span class="custom-tree-node" slot-scope="{ node, data }">
              <span>{{ node.label }}{{data.ActionCount?'('+data.ActionCount+')':''}}</span>
              <span>
                <el-button type="text" size="mini" @click="Add(node,data)">添加</el-button>
                <el-button type="text" size="mini" @click="Edit(node,data)">修改</el-button>
                <el-button type="text" size="mini" @click="ShowSort(node,data)">排序({{data.Sort}})</el-button>
                <el-button
                  type="text"
                  size="mini"
                  @click="Remove(node,data)"
                  v-if="!data.Children"
                >删除</el-button>
                <el-button
                  type="text"
                  size="mini"
                  v-if="!data.Children"
                  @click="Transfer(node,data);"
                >转移</el-button>
              </span>
            </span>
          </el-tree>
        </el-col>
      </el-row>
    </el-card>
    <el-dialog title="新建模块" :visible.sync="CreateDialog" center width="30%">
      <el-form :model="CreateData" label-width="80px" size="small" :rules="rules" ref="Create">
        <el-form-item label="模块名称" prop="Name">
          <el-input v-model="CreateData.Name" placeholder="请输入模块名称"></el-input>
        </el-form-item>
        <el-form-item label="唯一标识" prop="Sign">
          <el-input v-model="CreateData.Sign" placeholder="请输入唯一标识"></el-input>
        </el-form-item>
        <el-form-item label>
          <el-button round type="primary" @click="CreateFeature('Create')">确认新建</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog title="添加模块" :visible.sync="AddDialog" center width="30%">
      <el-form :model="AddData" label-width="80px" size="small" :rules="rules" ref="Add">
        <el-form-item label="模块名称" prop="Name">
          <el-input v-model="AddData.Name" placeholder="请输入模块名称"></el-input>
        </el-form-item>
        <el-form-item label="Url">
          <el-input v-model="AddData.Url" placeholder="请输入Url"></el-input>
        </el-form-item>
        <el-form-item label="Icon">
          <el-input v-model="AddData.Icon" placeholder="请输入图标名称"></el-input>
        </el-form-item>
        <el-form-item label="唯一标识" prop="Sign">
          <el-input v-model="AddData.Sign" placeholder="请输入唯一标识"></el-input>
        </el-form-item>
        <el-form-item label>
          <el-button round type="primary" @click="AddFeature('Add')">添加</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog title="修改模块" :visible.sync="EditDialog" center width="30%">
      <el-form :model="EditData" label-width="80px" size="small" :rules="rules" ref="Edit">
        <el-form-item label="模块名称" prop="Name">
          <el-input v-model="EditData.Name" placeholder="请输入模块名称"></el-input>
        </el-form-item>
        <el-form-item label="Url">
          <el-input v-model="EditData.Url" placeholder="请输入Url"></el-input>
        </el-form-item>
        <el-form-item label="Icon">
          <el-input v-model="EditData.Icon" placeholder="请输入图标名称"></el-input>
        </el-form-item>
        <el-form-item label="唯一标识" prop="Sign">
          <el-input v-model="EditData.Sign" placeholder="请输入唯一标识"></el-input>
        </el-form-item>
        <el-form-item label>
          <el-button round type="primary" @click="EditFeature('Edit')">保存</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
    <el-dialog title="转移关联关系" :visible.sync="TransferDialog" center width="30%">
      <el-form :model="TransferData" label-width="80px" size="small" :rules="rules" ref="Edit">
        <el-form-item label="原模块">
          <el-input v-model="TransferData.Name" placeholder="请输入模块名称" disabled></el-input>
        </el-form-item>
        <el-form-item label="转移到">
          <el-cascader
            :options="FeatureList"
            v-model="TransferData.NewPids"
            size="small"
            :props="{value:'Id',label:'Name',children:'Children'}"
            :change-on-select="true"
            style="width:100%"
          ></el-cascader>
        </el-form-item>
        <el-form-item label>
          <el-button type="danger" @click="CancelTransfer(TransferData)">取消</el-button>
          <el-button type="primary" @click="TransferFeature(TransferData)">确定</el-button>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
export default {
  data() {
    return {
      Loading: true,
      CreateDialog: false,
      AddDialog: false,
      EditDialog: false,
      TransferDialog: false,
      FeatureList: [],
      FeatureListCache: [],
      CreateData: {
        PId: 0,
        Name: "",
        Icon: "",
        Sign: ""
      },
      TransferData: {},
      EditData: {},
      AddData: {
        Pid: "",
        Name: "",
        Url: "",
        Icon: "",
        Sign: ""
      },
      rules: {
        Name: [
          {
            required: true,
            message: "名称不能为空",
            trigger: "blur"
          }
        ],
        Sign: [
          {
            required: true,
            message: "唯一标识不能为空",
            trigger: "blur"
          }
        ]
      }
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
    CreateFeature(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.axios
            .post("/feature/create", Object.assign({}, this.CreateData))
            .then(res => {
              this.$message.success(res.data.message);
              this.$refs[formName].resetFields();
              this.GetFeatureList();
              this.CreateDialog = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    AddFeature(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.axios
            .post("/feature/create", Object.assign({}, this.AddData))
            .then(res => {
              this.$message.success(res.data.message);
              this.$refs[formName].resetFields();
              this.GetFeatureList();
              this.AddDialog = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    EditFeature(formName) {
      this.$refs[formName].validate(valid => {
        if (valid) {
          this.axios
            .post("/feature/update", Object.assign({}, this.EditData))
            .then(res => {
              this.$message.success(res.data.message);
              this.$refs[formName].resetFields();
              this.GetFeatureList();
              this.EditDialog = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    TransferFeature(data) {
      let params = {
        Id: data.Id,
        NewPId: data.NewPids[data.NewPids.length - 1]
      };
      this.axios.post("/feature/transfer", params).then(res => {
        this.$message.success(res.data.message);
        this.GetFeatureList();
        this.TransferDialog = false;
        this.TransferData = {};
      });
    },
    Create() {
      this.CreateDialog = true;
    },
    Add(node, data) {
      this.AddData.PId = data.Id;
      this.AddDialog = true;
    },
    Edit(node, data) {
      this.EditData = Object.assign({}, data);
      delete this.EditData.Children;
      this.EditDialog = true;
    },
    ShowSort(node, data) {
      this.$prompt("请输入" + data.Name + "的序号", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        closeOnClickModal: false,
        closeOnPressEscape: false,
        inputType: "number",
        inputValue: data.Sort,
        center: true,
        roundButton: true
      })
        .then(({ value }) => {
          let params = {
            Id: data.Id,
            Sort: value
          };
          this.UpdateSort(params);
        })
        .catch(() => {
          this.$message.info("取消操作");
        });
    },
    UpdateSort(data) {
      let params = {
        Id: data.Id,
        Sort: data.Sort
      };
      this.axios.post("/feature/sort", params).then(res => {
        this.$message.success(res.data.message);
        this.GetFeatureList();
      });
    },
    Remove(node, data) {
      if (data.Children) {
        this.$message({
          message: "该模块下面存在子模块，不能直接删除",
          type: "error"
        });
      } else {
        this.$confirm("正在删除该模块，是否继续?", "提示", {
          confirmButtonText: "确认",
          cancelButtonText: "取消",
          type: "warning"
        })
          .then(() => {
            let params = {
              Id: data.Id,
              RoleCount: data.RoleCount
            };
            this.axios.post("/feature/delete", params).then(res => {
              this.$message.success(res.data.message);
              this.GetFeatureList();
            });
          })
          .catch(() => {
            this.$message({
              message: "已取消本次操作",
              type: "info"
            });
          });
      }
    },
    Transfer(node, data) {
      this.TransferData = Object.assign({}, data);
      this.TransferDialog = true;
    },
    CancelTransfer(data) {
      this.GetFeatureList();
    }
  },
  mounted() {
    this.GetFeatureList();
  }
};
</script>

<style scoped>
.custom-tree-node {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 14px;
  padding-right: 8px;
}
.router-name {
  color: #c40000;
}
</style>
