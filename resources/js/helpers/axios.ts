import axios from 'axios'
import { getItem } from "./localStorage";

const token = getItem('token');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;

export default axios
