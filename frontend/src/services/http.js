import axios from "axios";

const API_URL = process.env.VUE_APP_API_URL;

const _axios = axios.create({
    baseURL: API_URL,
});

export default _axios;