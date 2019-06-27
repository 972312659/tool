<template>
  <div>
    <el-card v-loading="Loading" element-loading-text="拼命加载中">
      <div slot="header">
        <i class="el-icon-info"></i>
        缓存管理
        <div style="float:right">
          <el-button round size="small" type="primary" @click="ClearRedisCache">清空Redis缓存</el-button>
          <el-button round size="small" type="primary" @click="ClearAllCacheData">清空系统缓存</el-button>
        </div>
      </div>
      <el-table :data="CacheList" border size="small">
        <el-table-column type="index"></el-table-column>
        <el-table-column label="缓存时间" width="200">
          <template slot-scope="scope">
            {{scope.row.creation_time | time}}
          </template>
        </el-table-column>
        <el-table-column label="缓存信息" prop="info"></el-table-column>
        <el-table-column label="缓存大小" width="100">
          <template slot-scope="scope">
            {{scope.row.mem_size | memsize}}
          </template>
        </el-table-column>
        <el-table-column label="操作" fixed="right" width="100">
          <template slot-scope="scope">
            <el-button round type="primary" size="small" @click="ClearCacheData(scope.row.info)">清除</el-button>
          </template>
        </el-table-column>
      </el-table>
    </el-card>
  </div>
</template>

<script>
export default {
  data() {
    return {
      Loading: true,
      CacheList: []
    };
  },
  methods: {
    GetCacheList() {
      this.axios
        .get("/cache/listApcu")
        .then(res => {
          this.CacheList = res.data.cache_list;
          this.Loading = false;
        })
        .catch(err => {
          console.log(err);
        });
    },
    ClearCacheData(Key) {
      this.axios
        .post(
          "/cache/delApcuCache",
          Object.assign(
            {},
            {
              Key: Key
            }
          )
        )
        .then(res => {
          this.$message.success("清除缓存成功");
          this.GetCacheList();
        })
        .catch(err => {
          console.log(err);
        });
    },
    ClearAllCacheData() {
      this.axios
        .post("/cache/clearApcuCache")
        .then(res => {
          this.$message.success("清除所有缓存成功");
          this.GetCacheList();
        })
        .catch(err => {
          console.log(err);
        });
    },
    ClearRedisCache() {
      this.$confirm("正在清除Redis缓存,是否继续？", "提示", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          this.axios
            .post("/cache/clearRedisModelCache")
            .then(res => {
              this.$message.success(res.data.message);
            })
            .catch(err => {
              console.log(err);
            });
        })
        .catch(() => {
          this.$message.info("取消操作");
        });
    }
  },
  mounted() {
    this.GetCacheList();
  },
  filters: {
    memsize(val) {
      return (val / 1024).toFixed(0) + "kb";
    },
    time(val) {
      let DateTime = new Date(val * 1000);
      let Day = DateTime.toLocaleDateString();
      let Time = DateTime.toLocaleTimeString();
      return Day + Time;
    }
  }
};
</script>

<style>
</style>
