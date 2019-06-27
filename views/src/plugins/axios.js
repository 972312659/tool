"use strict";

import Vue from "vue";
import axios from "axios";
import qs from "qs";
import store from "@/store";
import router from "@/router";
import { getHost } from "@/plugins/config";
import { Message, Notification } from "element-ui";
// Full config:  https://github.com/axios/axios#request-config
// axios.defaults.baseURL = process.env.baseURL || process.env.apiUrl || '';
// axios.defaults.headers.common['Authorization'] = AUTH_TOKEN;
axios.defaults.headers.post["Content-Type"] =
  "application/x-www-form-urlencoded";
let config = {
  baseURL: getHost(),
  timeout: 60 * 1000, // Timeout
  withCredentials: true, // Check cross-site Access-Control
  transformRequest: [
    function(data) {
      return qs.stringify(data, {
        arrayFormat: "brackets"
      });
    }
  ]
};
const _axios = axios.create(config);
_axios.interceptors.request.use(
  function(config) {
    return config;
  },
  function(error) {
    return Promise.reject(error);
  }
);
_axios.interceptors.response.use(
  function(response) {
    if (typeof response.data === "object") {
      return response;
    } else {
      Notification.error({
        title: "错误",
        message: "接口返回数据结构出错了,详细信息请查看控制台"
      });
      console.error(response.data);
      return response;
    }
  },
  function(error) {
    switch (error.response.status) {
      case 400:
        Message.error(error.response.data.message);
        break;
      case 401:
        Message.error("用户未登录~");
        store.commit("Logout");
        router.push({
          name: "login"
        });
        break;
      case 403:
        Message.error("没有访问权限哦~");
        break;
      case 404:
        Message.error("接口不存在~");
        break;
      case 405:
        Message.error("请求方式出错啦~");
        break;
      case 500:
        Message.error("服务器错误~");
        break;
      default:
        Message.error("出错了~");
        break;
    }
    return Promise.reject(error);
  }
);

Plugin.install = function(Vue, options) {
  Vue.axios = _axios;
  window.axios = _axios;
  Object.defineProperties(Vue.prototype, {
    axios: {
      get() {
        return _axios;
      }
    },
    $axios: {
      get() {
        return _axios;
      }
    }
  });
};

Vue.use(Plugin);

export default Plugin;
