<template>
  <div class="home">
    <el-card v-loading="Loading" element-loading-text="拼命加载中">
      <div slot="header">
        <i class="el-icon-info"></i>
        系统信息
      </div>
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card>
            <div slot="header">操作系统</div>
            {{SystemInfo.OS}}
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card>
            <div slot="header">服务器负载</div>
            {{SystemInfo.LoadAvg}}
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card>
            <div slot="header">服务器环境</div>
            {{SystemInfo.Server}}
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card>
            <div slot="header">运行环境</div>
            {{SystemInfo.PHP}}
          </el-card>
        </el-col>
      </el-row>
      <br>
      <el-row :gutter="20">
        <el-col :span="6">
          <el-card>
            <div slot="header">CPU</div>
            {{SystemInfo.CPU}}
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card>
            <div slot="header">内存</div>
            <el-progress type="circle" :percentage="SystemInfo.MemoryPercentage"></el-progress>
          </el-card>
        </el-col>
        <el-col :span="6">
          <el-card>
            <div slot="header">硬盘</div>
            <el-progress type="circle" :percentage="SystemInfo.DiskPercentage"></el-progress>
          </el-card>
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      Loading: true,
      Times: null,
      SystemInfo: {
        OS: "",
        Server: "",
        PHP: "",
        CPU: "",
        Disk: { Total: "", Free: "" },
        Memory: { Total: "", Available: "" },
        LoadAvg: "",
        DiskPercentage: 0,
        MemoryPercentage: 0
      }
    };
  },
  methods: {
    SetTimer() {
      this.Times = setInterval(() => {
        this.GetSystemInfo();
      }, 1000 * 30);
    },
    GetSystemInfo() {
      this.axios
        .get("/index/index")
        .then(res => {
          this.SystemInfo = Object.assign(this.SystemInfo, res.data);
          this.SystemInfo.DiskPercentage = parseFloat(
            (
              ((this.SystemInfo.Disk.Total - this.SystemInfo.Disk.Free) /
                this.SystemInfo.Disk.Total) *
              100
            ).toFixed(2)
          );
          this.SystemInfo.MemoryPercentage = parseFloat(
            (
              ((this.SystemInfo.Memory.Total -
                this.SystemInfo.Memory.Available) /
                this.SystemInfo.Memory.Total) *
              100
            ).toFixed(2)
          );
          this.Loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    }
  },
  mounted() {
    this.GetSystemInfo();
    this.SetTimer();
  },
  beforeDestroy() {
    clearInterval(this.Times);
  }
};
</script>
